<?php

namespace Database\Factories\Ecommerce;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ecommerce\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected static $prod_namesIndex =0;

     protected static $prod_names = [
        'Stylish Floral Hoodie for Cats',
        'Red Bowtie Cat Collar with Bell',
        'Interactive Laser Toy for Cats and Dogs',
        'Adjustable Red Nylon Collar for Pets',
        'Outdoor Adventure Jacket for Dogs',
        'Heavy-Duty Red Dog Leash with Brass Hook',

     ];
    public function definition(): array
    {
        if(self::$prod_namesIndex >= count(self::$prod_names)){
            throw new \Exception("No Product available.");
        
        }

        $prod_name = self::$prod_names[self::$prod_namesIndex];
            self::$prod_namesIndex++;

        $prod_desc = [];
        for ($i = 0; $i < 3; $i++) {
            $text = $this->faker->paragraph(3,true); // Generate 2 paragraphs of lorem
            $words = array_slice(explode(' ', $text), 0, 170); // Get first 170 words
            $paragraphText = implode(' ',$words);

            $prod_desc[] = "<p>$paragraphText</p>";
           
        }
        $prod_desc_content = implode(' ', $prod_desc);
        return [
            'prod_name' => $prod_name,
            'prod_slug' => Str::slug($prod_name),
            'prod_price' => $this->faker->randomFloat(2, 100, 2000), // price between 100 and 2000
            'prod_description' => $prod_desc_content,
            'prod_quantity' => $this->faker->numberBetween(10, 100),
            'prod_sku' => strtoupper($this->faker->bothify('SKU-##??##')),
            'prod_unit' => $this->faker->randomElement(['pc', 'kg']),
            'prod_requires_shipping' => $this->faker->boolean(80), // 80% chance of true
            'prod_weight' => $this->faker->randomFloat(2, 0.2, 5), // 0.2kg to 5kg
            'prod_short_description' => $this->faker->sentence(10),
            'prod_old_price' => $this->faker->randomFloat(2, 100, 2000),
            'is_visible_to_market' => $this->faker->boolean(90), // 90% chance of true
        ];
    }
}
