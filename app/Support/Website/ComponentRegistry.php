<?php

namespace App\Support\Website;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Discovers website component variants from
 * resources/views/components/company/{slot}/{variant}.blade.php.
 *
 * Adding a new Blade file automatically makes the variant available to
 * templates (and selectable in Admin → Templates).
 */
class ComponentRegistry
{
    /** @var array<string, array<int, string>>|null */
    private static ?array $cache = null;

    /** @return array<string, array<int, string>> slot => variants */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $base = resource_path('views/components/company');
        $map = [];

        foreach (array_keys(DesignSystem::SLOTS) as $slot) {
            $dir = $base.'/'.$slot;
            $map[$slot] = is_dir($dir)
                ? collect(File::files($dir))
                    ->map(fn ($file) => Str::before($file->getFilename(), '.blade.php'))
                    ->filter(fn ($name) => ! str_starts_with($name, '_'))
                    ->sort()
                    ->values()
                    ->all()
                : [];
        }

        return self::$cache = $map;
    }

    public static function variants(string $slot): array
    {
        return self::all()[$slot] ?? [];
    }

    public static function exists(string $slot, string $variant): bool
    {
        return in_array($variant, self::variants($slot), true);
    }

    public static function label(string $slot, string $variant): string
    {
        return Str::of($variant)->replace('-', ' ')->title()->toString();
    }

    /** Options for <select>: variant => label. */
    public static function options(string $slot): array
    {
        return collect(self::variants($slot))->mapWithKeys(fn ($v) => [$v => self::label($slot, $v)])->all();
    }

    public static function count(): int
    {
        return collect(self::all())->flatten()->count();
    }

    public static function flush(): void
    {
        self::$cache = null;
    }
}
