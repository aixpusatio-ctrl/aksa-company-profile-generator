<?php

namespace Database\Seeders;

use App\Services\SettingService;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(SettingService $settings): void
    {
        $settings->set([
            'app_name' => 'ProfilKu',
            'app_tagline' => 'Company Profile Generator',
            'default_template' => 'corporate-blue',
            'support_email' => 'support@example.com',
            'registration_enabled' => '1',
            'media_disk' => 'public',
            'max_upload_kb' => 4096,
        ]);
    }
}
