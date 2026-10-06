<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            UserSeeder::class,
            TemplateCategorySeeder::class,
            TemplateSeeder::class,
            DemoCompanySeeder::class,
        ]);
    }
}
