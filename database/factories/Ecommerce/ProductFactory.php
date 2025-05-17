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
    if (self::$prod_namesIndex >= count(self::$prod_names)) {
        throw new \Exception("No Product available.");
    }

    $prod_name = self::$prod_names[self::$prod_namesIndex];
    self::$prod_namesIndex++;

    $prod_desc = [];
    // First 3 paragraphs
    for ($i = 0; $i < 3; $i++) {
        $text = $this->faker->paragraph(3, true);
        $words = array_slice(explode(' ', $text), 0, 170);
        $paragraphText = implode(' ', $words);
        $prod_desc[] = "<p>$paragraphText</p>";
    }

    // Bullet points
    $prod_desc[] = <<<HTML

        <ul>
            <br>
            <li>- Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
            <li>- Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
            <li>- Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</li>
            <li>- Duis aute irure dolor in reprehenderit in voluptate velit esse cillum.</li>
            <li>- Excepteur sint occaecat cupidatat non proident.</li>
        </ul>
        <br>
    HTML;

    // Final paragraph with about 10 sentences
    $finalParagraph = $this->faker->paragraphs(10, true);
    $prod_desc[] = "<p>$finalParagraph</p> <br>";

    $prod_desc_content = implode(' ', $prod_desc);

    // Generate formatted SKU
    $sku = 'SKU-' . rand(1000, 9999) . '-' . strtoupper(Str::random(4));

    return [
        'prod_name' => $prod_name,
        'prod_slug' => Str::slug($prod_name),
        'prod_price' => $this->faker->randomFloat(2, 100, 2000),
        'prod_description' => $prod_desc_content,
        'prod_quantity' => $this->faker->numberBetween(10, 100),
        'prod_sku' => $sku,
        'prod_unit' => $this->faker->randomElement(['pc', 'kg']),
        'prod_requires_shipping' => $this->faker->boolean(80),
        'prod_weight' => $this->faker->randomFloat(2, 0.2, 5),
        'prod_short_description' => $this->faker->sentence(10),
        'prod_old_price' => $this->faker->randomFloat(2, 100, 2000),
        'is_visible_to_market' => $this->faker->boolean(90),
    ];
}

}
