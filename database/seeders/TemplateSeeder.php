<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\TemplateCategory;
use Illuminate\Database\Seeder;

/**
 * 10 base templates (one per layout) + variants built on the same layouts
 * with different branding and section order.
 */
class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $categories = TemplateCategory::query()->pluck('id', 'slug');

        $templates = [
            ['Corporate Blue', 'corporate-blue', 'corporate', 'corporate', true, []],
            ['Modern Business', 'modern-business', 'modern-business', 'corporate', true, []],
            ['Technology', 'technology', 'technology', 'technology', true, []],
            ['Construction', 'construction', 'construction', 'construction', false, []],
            ['Manufacturing', 'manufacturing', 'manufacturing', 'manufacturing', false, []],
            ['Consulting', 'consulting', 'consulting', 'professional', false, []],
            ['Creative Agency', 'creative-agency', 'creative-agency', 'creative', true, []],
            ['Professional Services', 'professional-services', 'professional-services', 'professional', false, []],
            ['Minimal', 'minimal', 'minimal', 'other', false, []],
            ['Executive', 'executive', 'executive', 'finance', true, []],
            // Variants
            ['Corporate Emerald', 'corporate-emerald', 'corporate', 'corporate', false, [
                'primary_color' => '#059669', 'secondary_color' => '#022c22', 'heading_font' => 'Montserrat', 'body_font' => 'Inter',
            ]],
            ['Healthcare Clinic', 'healthcare-clinic', 'professional-services', 'healthcare', false, [
                'primary_color' => '#0d9488', 'secondary_color' => '#0f766e', 'heading_font' => 'Poppins', 'body_font' => 'Inter', 'button_style' => 'pill', 'border_radius' => 'lg',
                'sections' => ['hero', 'services', 'team', 'about', 'testimonials', 'gallery', 'products', 'projects', 'cta', 'contact'],
            ]],
            ['Education Campus', 'education-campus', 'modern-business', 'education', false, [
                'primary_color' => '#2563eb', 'secondary_color' => '#facc15', 'heading_font' => 'Poppins', 'body_font' => 'DM Sans',
                'sections' => ['hero', 'about', 'services', 'team', 'gallery', 'testimonials', 'projects', 'products', 'cta', 'contact'],
            ]],
        ];

        foreach ($templates as $i => [$name, $slug, $layout, $category, $featured, $settings]) {
            Template::query()->updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'layout' => $layout,
                'template_category_id' => $categories[$category] ?? null,
                'description' => config("website-templates.layouts.{$layout}.description"),
                'status' => Template::STATUS_PUBLISHED,
                'is_featured' => $featured,
                'settings' => $settings,
                'sort_order' => $i + 1,
            ]);
        }
    }
}
