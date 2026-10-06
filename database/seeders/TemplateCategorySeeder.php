<?php

namespace Database\Seeders;

use App\Models\TemplateCategory;
use App\Support\Website\TemplateLibrary;
use Illuminate\Database\Seeder;

class TemplateCategorySeeder extends Seeder
{
    public function run(): void
    {
        $order = 1;
        foreach (TemplateLibrary::CATEGORIES as $slug => [$name, $description]) {
            TemplateCategory::query()->updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'description' => $description,
                'sort_order' => $order++,
            ]);
        }
    }
}
