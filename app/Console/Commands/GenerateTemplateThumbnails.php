<?php

namespace App\Console\Commands;

use App\Models\Template;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

/**
 * Generates desktop & mobile screenshot thumbnails for templates so the
 * gallery can show static images instead of live previews.
 */
class GenerateTemplateThumbnails extends Command
{
    protected $signature = 'templates:thumbnails
        {slugs?* : Only these template slugs}
        {--base-url= : URL where the app is running (default APP_URL)}';

    protected $description = 'Screenshot every template preview (desktop + mobile) and store them as thumbnails';

    public function handle(): int
    {
        $templates = Template::query()
            ->when($this->argument('slugs'), fn ($q, $slugs) => $q->whereIn('slug', $slugs))
            ->orderBy('sort_order')
            ->get();

        if ($templates->isEmpty()) {
            $this->warn('No templates found.');

            return self::FAILURE;
        }

        $disk = Storage::disk('public');
        $directory = 'template-thumbnails';

        $result = Process::timeout(1800)->run(array_merge([
            'node', base_path('scripts/template-thumbnails.mjs'),
            '--base='.($this->option('base-url') ?: config('app.url')),
            '--out='.$disk->path($directory),
        ], $templates->pluck('slug')->all()), fn ($type, $output) => $this->output->write($output));

        if (! $result->successful()) {
            $this->error('Thumbnail generation failed.');

            return self::FAILURE;
        }

        foreach ($templates as $template) {
            $desktop = "{$directory}/{$template->slug}.jpg";
            $mobile = "{$directory}/{$template->slug}-mobile.jpg";

            $template->update([
                'thumbnail' => $disk->exists($desktop) ? $desktop : $template->thumbnail,
                'mobile_thumbnail' => $disk->exists($mobile) ? $mobile : $template->mobile_thumbnail,
            ]);
        }

        $this->info($templates->count().' template thumbnails updated.');

        return self::SUCCESS;
    }
}
