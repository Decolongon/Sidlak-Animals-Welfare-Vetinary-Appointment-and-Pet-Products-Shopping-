<?php

namespace Database\Seeders\Ecommerce;

use Illuminate\Database\Seeder;
use App\Models\Ecommerce\Product;
use App\Models\Ecommerce\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\Concerns\CanCreateProductImage;
use Faker\Factory as faker;

class ProductSeeder extends Seeder
{
    use CanCreateProductImage;

    public function run(): void
    {
        $products = 6;
        $progressBar = $this->command->getOutput()->createProgressBar($products);
        $progressBar->setFormat("CREATING Products\n %current%/%max% [%bar%] %percent:3s%% ");
        $progressBar->start();

        for ($i = 0; $i < $products; $i++) {
            // Create the product using the factory
            $product = Product::factory()->create();

            // Create 1–3 images per product
            $imageCount = rand(1, 3);

            for ($j = 0; $j < $imageCount; $j++) {
                $filename = $this->CanCreateProductImage($product->prod_name);

                if ($filename) {
                    $product->images()->create([
                        'url' => $filename,
                        'product_id' => $product->id,
                        // 'is_primary' =>$faker->boolean(50),
                    ]);
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->line('');
    }
}
