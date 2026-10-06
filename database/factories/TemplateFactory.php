<?php

namespace Database\Factories;

use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Template>
 */
class TemplateFactory extends Factory
{
    protected $model = Template::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'layout' => fake()->randomElement(array_keys(config('website-templates.layouts'))),
            'description' => fake()->sentence(),
            'status' => Template::STATUS_PUBLISHED,
            'is_featured' => false,
            'settings' => [],
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => Template::STATUS_DRAFT]);
    }

    public function layout(string $layout): static
    {
        return $this->state(fn () => ['layout' => $layout]);
    }
}
