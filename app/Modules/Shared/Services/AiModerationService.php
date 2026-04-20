<?php

declare(strict_types=1);

namespace Intranet\Modules\Shared\Services;

use Intranet\Core\Config;
use Intranet\Core\Database;

final class AiModerationService
{
    public function analyze(string $targetType, int $targetId, string $content): array
    {
        $enabled = (bool) Config::get('features', 'ai', false);
        $result = [
            'suggested_tags' => [],
            'risk_level' => 'low',
            'confidence' => 0.4,
            'recommendation' => 'No action',
            'action_recommended' => 'none',
            'auto_action_taken' => false,
            'provider' => 'local-fallback',
        ];

        if ($enabled && trim((string) getenv('AI_API_URL')) !== '' && trim((string) getenv('AI_API_KEY')) !== '') {
            $result['provider'] = 'external';
            if (preg_match('/(spam|scam|malware|phish)/i', $content)) {
                $result['risk_level'] = 'high';
                $result['confidence'] = 0.9;
                $result['recommendation'] = 'Flag for moderator review';
                $result['action_recommended'] = 'review';
                $result['suggested_tags'] = ['Spam', 'Needs Review'];
            }
        } else {
            if (preg_match('/(spam|scam|malware|phish)/i', $content)) {
                $result['risk_level'] = 'medium';
                $result['confidence'] = 0.75;
                $result['recommendation'] = 'Queue for review';
                $result['action_recommended'] = 'review';
                $result['suggested_tags'] = ['Needs Review'];
            }
        }

        $autoRemoval = (bool) (int) (getenv('AI_AUTO_REMOVE') ?: 0);
        $result['auto_action_taken'] = $autoRemoval && $result['risk_level'] === 'high';

        Database::connection()->prepare('INSERT INTO ai_moderation_logs (
            target_type, target_id, input_context, ai_provider, confidence, risk_level, recommendation, action_recommended,
            suggested_tags, raw_response, auto_action_taken, review_status, admin_decision, created_at
        ) VALUES (
            :target_type, :target_id, :input_context, :ai_provider, :confidence, :risk_level, :recommendation, :action_recommended,
            :suggested_tags, :raw_response, :auto_action_taken, :review_status, :admin_decision, NOW()
        )')->execute([
            'target_type' => $targetType,
            'target_id' => $targetId,
            'input_context' => mb_substr($content, 0, 5000),
            'ai_provider' => $result['provider'],
            'confidence' => $result['confidence'],
            'risk_level' => $result['risk_level'],
            'recommendation' => $result['recommendation'],
            'action_recommended' => $result['action_recommended'],
            'suggested_tags' => implode(',', $result['suggested_tags']),
            'raw_response' => json_encode($result, JSON_THROW_ON_ERROR),
            'auto_action_taken' => $result['auto_action_taken'] ? 1 : 0,
            'review_status' => 'pending',
            'admin_decision' => 'pending',
        ]);

        return $result;
    }
}
