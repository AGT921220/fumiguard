<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $name = fake()->company();
        $slug = Str::slug($name).'-'.fake()->unique()->randomNumber(4);

        return [
            'name' => $name,
            'slug' => $slug,
        ];
    }
}

