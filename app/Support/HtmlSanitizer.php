<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Cleans rich text (Trix editor output) so it can be rendered safely with {!! !!}.
 */
class HtmlSanitizer
{
    private static ?HTMLPurifier $purifier = null;

    public static function clean(?string $html): ?string
    {
        if (blank($html)) {
            return null;
        }

        return trim(self::purifier()->purify($html)) ?: null;
    }

    private static function purifier(): HTMLPurifier
    {
        if (self::$purifier) {
            return self::$purifier;
        }

        $config = HTMLPurifier_Config::createDefault();
        $cache = storage_path('framework/cache/purifier');
        if (! is_dir($cache)) {
            @mkdir($cache, 0755, true);
        }
        $config->set('Cache.SerializerPath', $cache);
        $config->set('HTML.Allowed', 'p,br,strong,b,em,i,u,s,del,a[href|title|target],ul,ol,li,h1,h2,h3,h4,blockquote,pre,code,img[src|alt|width|height],hr,div,span');
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'tel' => true]);
        $config->set('HTML.TargetNoopener', true);
        $config->set('AutoFormat.RemoveEmpty', true);

        return self::$purifier = new HTMLPurifier($config);
    }
}
