<?php

namespace Database\Factories;

use App\Models\CompanyProfile;
use App\Models\Template;
use App\Models\User;
use App\Services\CompanyProfileService;
use App\Services\MenuService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CompanyProfile>
 */
class CompanyProfileFactory extends Factory
{
    protected $model = CompanyProfile::class;

    public function definition(): array
    {
        $name = 'PT '.fake()->unique()->company();

        return [
            'user_id' => User::factory(),
            'template_id' => Template::factory(),
            'name' => $name,
            'slug' => Str::limit(Str::slug($name), 40, '').'-'.Str::lower(Str::random(5)),
            'tagline' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'email' => fake()->companyEmail(),
            'phone' => '021-'.fake()->numerify('#######'),
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'status' => CompanyProfile::STATUS_DRAFT,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => CompanyProfile::STATUS_PUBLISHED, 'published_at' => now()]);
    }

    /**
     * Create the default sections & menus like the real creation flow.
     */
    public function withDefaults(): static
    {
        return $this->afterCreating(function (CompanyProfile $company) {
            app(CompanyProfileService::class)->syncSections($company, $company->template);
            app(MenuService::class)->createDefaults($company);
        });
    }
}
