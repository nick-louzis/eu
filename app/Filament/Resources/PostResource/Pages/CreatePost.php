<?php

namespace App\Filament\Resources\PostResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Check if 'category_id' is not set (null or empty)
    if (empty($data['category_id'])) {
        // Check if the 'Uncategorized' category exists or create it
        $category = \App\Models\Category::firstOrCreate(
            ['name' => 'Uncategorized'], // Find by the 'name' column
            [
                'description' => 'This category is for uncategorized posts',//description not used cause no such field in db
                'slug' => Str::slug('Uncategorized'), // Create a slug for the category
            ]
        );

        // Assign the 'Uncategorized' category ID to 'category_id'
        $data['category_id'] = $category->id;
    }

        return $data;
    }
}
