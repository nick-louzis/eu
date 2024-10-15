<?php

namespace App\Filament\Widgets;

use Log;
use App\Models\Post;
use DebugBar\DebugBar;
use App\Models\Category;
use Flowframe\Trend\Trend;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Collection;

class ChartTest extends ChartWidget
{
    protected static ?string $heading = 'Projects';

    protected static ?int $sort = 2;
    
    protected static ?string $pollingInterval = null;

    protected Collection $selectedCollection;

    public ?string $filter = 'posts';

    protected function getFilters(): ?array
    {
        return [
            'posts' => 'Projects',
            'proposals' => 'Proposals',
        ];
    }

    public function mount(): void
    {
        // Populate the categoriesWithPosts property
        $this->selectedCollection = Category::has('posts')->get();
    }


    protected function generateCategoryColors(): array
    {
        // Get the total number of categories that are posts from the Category model
        $categoryCount = $this->selectedCollection->count();

        $colors = [];

        // Loop through the number of categories and generate random RGB colors
        for ($i = 0; $i < $categoryCount; $i++) {
            $colors[] = 'rgba(' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ', 0.7)';
        }

        return $colors;
    }
    protected function getData(): array
    {

        $this->selectedCollection = Category::has($this->filter)->get();
        self::$heading = $this->filter === 'posts' ? 'Projects' : "Proposals"; 


        return [
            'datasets' => [
                [
                    'label' => 'Projects belonging to Categories',
                    'data' => $this->selectedCollection->map(fn($category) => Post::where('category_id', $category->id)->count())->toArray(),
                    'backgroundColor'=> $this->generateCategoryColors(),
                    'borderColor' => "rgba(255, 255, 255, 0.1)"
                ],
            ],
            'labels' => $this->selectedCollection->pluck('name')->toArray()
            
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
