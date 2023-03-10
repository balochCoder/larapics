<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $images = Storage::allFiles('images');

        foreach ($images as $image) {
            Image::factory()->create([
                'file' => $image,
                'dimension' => Image::getDimension($image)
            ]);

        }
    }


}
