<?php

namespace Database\Factories\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Database\Seeders\LocalImages;

trait CanCreateProductImage
{
    protected static $unusedProductImages = null;

    public function CanCreateProductImage(string $productName): ?string
    {
        $productImages = [
            'Stylish Floral Hoodie for Cats' => 'cat_clothes.jpg',
            'Red Bowtie Cat Collar with Bell' => 'cat_collar.jpg',
            'Interactive Laser Toy for Cats and Dogs' => 'cat_dog_toy.jpg',
            'Adjustable Red Nylon Collar for Pets' => 'collar_for_dog_or_cat.png',
            'Outdoor Adventure Jacket for Dogs' => 'dog_clothes.jpg',
            'Heavy-Duty Red Dog Leash with Brass Hook' => 'dog_leash_enhanced.jpg',
            '9 Lives Crunchy Cat Treats' => 'cat_treats.png',
            'Vegan Duo Dog and Cat Food'=> 'dog_food_&_cat_food.jpg',
        ];

        $filename = $productImages[$productName] ?? null;

        if (!$filename) {
         
            return null;
        }

        // Get the path to the image file
        $filePath = database_path('seeders/local_images/products/' . $filename);

        // Use file_get_contents to read the image file
        $image = file_get_contents($filePath);

         // Get original extension (e.g., jpg or png)
         $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $newFilename = Str::slug($productName) . '_' . Str::uuid() . '.' . $extension;

        // Store the image in the storage disk
        Storage::disk('public')->put($newFilename, $image);

        return $newFilename;
    }
}
