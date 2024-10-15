<?php

namespace App\Models;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            // Check if the category has associated posts
            if ($category->posts()->count() > 0) {
                // Check if the 'Undefined' category exists, and if not, create it
                $undefinedCategory = Category::firstOrCreate(
                    ['slug' => 'uncategorized'],   // Search criteria
                    ['name' => 'Uncategorized']    // Attributes to create if not found
                );

                // Update all posts associated with this category to the 'Undefined' category
                $category->posts()->update(['category_id' => $undefinedCategory->id]);
            }
        });
    }

    public function posts() {
        return $this->hasMany(Post::class);
    }


    public function proposals()
    {
        return $this->belongsToMany(Proposal::class, 'category_proposal', 'category_id', 'proposal_id')
                    ->withTimestamps();
    }
}
