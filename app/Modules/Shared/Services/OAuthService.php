<?php

declare(strict_types=1);

namespace Intranet\Modules\Shared\Services;

use Intranet\Core\Config;

final class OAuthService
{
    public function providers(): array
    {
        return Config::get('oauth');
    }

    public function authorizeUrl(string $provider, string $state): ?string
    {
        $cfg = $this->providers()[$provider] ?? null;
        if (!$cfg || empty($cfg['enabled'])) {
            return null;
        }

        $query = [
            'client_id' => $cfg['client_id'],
            'redirect_uri' => $cfg['redirect_uri'],
            'response_type' => 'code',
            'scope' => implode(' ', $cfg['scopes']),
            'state' => $state,
        ];

        return $cfg['authorize_url'] . '?' . http_build_query($query);
    }

    public function fetchUser(string $provider, string $code): ?array
    {
        $cfg = $this->providers()[$provider] ?? null;
        if (!$cfg || empty($cfg['enabled'])) {
            return null;
        }

        $tokenResponse = $this->http($cfg['token_url'], [
            'client_id' => $cfg['client_id'],
            'client_secret' => $cfg['client_secret'],
            'code' => $code,
            'redirect_uri' => $cfg['redirect_uri'],
            'grant_type' => 'authorization_code',
        ], ['Accept: application/json']);

        $tokenData = json_decode($tokenResponse, true);
        $token = $tokenData['access_token'] ?? null;
        if (!$token) {
            return null;
        }

        $userResponse = $this->http($cfg['user_url'], null, [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'User-Agent: IntranetPromptOAuth/1.0',
        ]);

        $raw = json_decode($userResponse, true);
        if (!is_array($raw)) {
            return null;
        }

        return [
            'provider_user_id' => (string) ($raw['sub'] ?? $raw['id'] ?? ''),
            'email' => (string) ($raw['email'] ?? ''),
            'name' => (string) ($raw['name'] ?? $raw['login'] ?? 'OAuth User'),
            'avatar' => (string) ($raw['picture'] ?? $raw['avatar_url'] ?? $raw['picture']['data']['url'] ?? ''),
        ];
    }

    private function http(string $url, ?array $post = null, array $headers = []): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

        if ($post !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        if ($headers !== []) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return is_string($response) ? $response : '{}';
    }
}
