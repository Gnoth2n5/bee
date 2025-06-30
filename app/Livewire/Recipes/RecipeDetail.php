<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RecipeDetail extends Component
{
    public Recipe $recipe;

    public function mount(Recipe $recipe)
    {
        $this->recipe = $recipe->load(['user.profile', 'categories', 'tags', 'images', 'ratings.user']);
    }

    public function render()
    {
        return view('livewire.recipes.recipe-detail', [
            'recipe' => $this->recipe,
        ]);
    }
} 