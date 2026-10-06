<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            // Component composition + design system (navbar, hero, ..., theme, typography, ...).
            $table->json('config')->nullable()->after('settings');
            // Short style keywords shown in the gallery, e.g. "Enterprise · Clean · Premium".
            $table->string('style')->nullable()->after('description');
            $table->string('mobile_thumbnail')->nullable()->after('thumbnail');
            // Demo dataset used for previews (App\Support\DemoContent).
            $table->string('demo', 60)->nullable()->after('layout');
        });

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('hero_video', 500)->nullable()->after('hero_image');
            // Key figures shown in stats sections: [{value, label}, ...]
            $table->json('highlights')->nullable()->after('company_values');
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['config', 'style', 'mobile_thumbnail', 'demo']);
        });

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn(['hero_video', 'highlights']);
        });
    }
};
