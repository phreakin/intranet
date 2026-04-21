<?php

declare(strict_types=1);

namespace Intranet\Core;

final class Helpers
{
    public static function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }

    public static function svgs(): array
    {
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/svg/icons/color/';

        if (!is_dir($dir)) {
            return [];
        }

        $svgs = [];

        foreach (scandir($dir) as $file) {
            if ($file[0] === '.' || pathinfo($file, PATHINFO_EXTENSION) !== 'svg') {
                continue;
            }

            // Normalize key: Google.svg → google
            $key = strtolower(pathinfo($file, PATHINFO_FILENAME));

            // Public URL
            $svgs[$key] = '/assets/svg/icons/color/' . $file;
        }

        return $svgs;
    }

    public static function svgsColor(): array
    {
        return self::loadSvgSet('/assets/svg/icons/color/');
    }

    public static function svgsBlack(): array
    {
        return self::loadSvgSet('/assets/svg/icons/black/');
    }

    public static function svgsWhite(): array
    {
        return self::loadSvgSet('/assets/svg/icons/white/');
    }

    public static function svgsNav(): array
    {
        return self::loadSvgSet('/assets/svg/icons/nav/');
    }



    private static function loadSvgSet(string $relativePath): array
    {
        $dir = $_SERVER['DOCUMENT_ROOT'] . $relativePath;

        if (!is_dir($dir)) {
            return [];
        }

        $svgs = [];

        foreach (scandir($dir) as $file) {
            if ($file[0] === '.' || pathinfo($file, PATHINFO_EXTENSION) !== 'svg') {
                continue;
            }

            // Normalize key: Google.svg → google
            $key = strtolower(pathinfo($file, PATHINFO_FILENAME));

            // Public URL
            $svgs[$key] = $relativePath . $file;
        }

        return $svgs;
    }





}
