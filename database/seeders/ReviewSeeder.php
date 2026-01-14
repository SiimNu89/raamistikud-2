<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        Product::all()->each(function ($product) {
            Review::factory()
                ->count(rand(5, 8))
                ->create([
                    'product_id' => $product->id
                ]);
        });
    }
}
