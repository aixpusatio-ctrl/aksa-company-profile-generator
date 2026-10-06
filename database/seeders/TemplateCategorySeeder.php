<?php

namespace Database\Seeders;

use App\Models\TemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TemplateCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Corporate' => 'Perusahaan, holding dan grup usaha.',
            'Technology' => 'Software house, startup dan perusahaan IT.',
            'Construction' => 'Kontraktor, arsitek dan developer properti.',
            'Finance' => 'Investasi, asuransi dan jasa keuangan.',
            'Healthcare' => 'Klinik, rumah sakit dan layanan kesehatan.',
            'Education' => 'Sekolah, kampus dan lembaga pelatihan.',
            'Manufacturing' => 'Pabrik, industri dan distributor.',
            'Creative' => 'Agensi kreatif, studio desain dan media.',
            'Professional' => 'Konsultan, firma hukum dan akuntan.',
            'Other' => 'Kategori lainnya.',
        ];

        $order = 1;
        foreach ($categories as $name => $description) {
            TemplateCategory::query()->updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'description' => $description,
                'sort_order' => $order++,
            ]);
        }
    }
}
