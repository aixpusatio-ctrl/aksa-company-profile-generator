<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\TemplateCategory;
use App\Support\Website\TemplateLibrary;
use Illuminate\Database\Seeder;

/**
 * Seeds the 50 built-in templates from App\Support\Website\TemplateLibrary:
 * 10 hand-crafted Blade themes + 40 templates composed from the component
 * library, each with its own design system and demo company.
 */
class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $categories = TemplateCategory::query()->pluck('id', 'slug');

        foreach (TemplateLibrary::all() as $i => $definition) {
            Template::query()->updateOrCreate(['slug' => $definition['slug']], [
                'name' => $definition['name'],
                'layout' => $definition['layout'],
                'demo' => $definition['demo'],
                'template_category_id' => $categories[$definition['category']] ?? null,
                'description' => $definition['description'],
                'style' => $definition['style'],
                'status' => Template::STATUS_PUBLISHED,
                'is_featured' => $definition['featured'],
                'settings' => $definition['settings'],
                'config' => $definition['config'],
                'sort_order' => $i + 1,
            ]);
        }
    }
}
