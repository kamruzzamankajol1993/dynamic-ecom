<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'product_code' => strtoupper(\Illuminate\Support\Str::random(8)),
            // These will automatically create a new Category, Brand, and Unit for each product
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'unit_id' => Unit::factory(),
            'base_price' => $this->faker->randomFloat(2, 100, 1000),
            'purchase_price' => $this->faker->randomFloat(2, 50, 500),
            'description' => $this->faker->paragraph,
            'status' => 1,
        ];
    }
}