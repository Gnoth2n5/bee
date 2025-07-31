<?php

namespace App\Livewire\Admin;

use App\Models\Recipe;
use App\Services\ModerationService;
use Livewire\Component;
use Livewire\WithPagination;

class ModerationTest extends Component
{
    use WithPagination;

    public $selectedRecipe = null;
    public $moderationResults = [];
    public $showResults = false;

    public function mount()
    {
        // Component initialization
    }

    public function testRecipe($recipeId)
    {
        $this->selectedRecipe = Recipe::findOrFail($recipeId);
        $moderationService = app(ModerationService::class);
        $this->moderationResults = $moderationService->testRecipeModeration($this->selectedRecipe);
        $this->showResults = true;
    }

    public function runAutoModeration()
    {
        $moderationService = app(ModerationService::class);
        $results = $moderationService->autoModeratePendingRecipes();

        session()->flash('success', 'Đã chạy tự động phê duyệt thành công!');
        $this->dispatch('flash-message', message: 'Đã chạy tự động phê duyệt thành công!', type: 'success');

        return $results;
    }

    public function render()
    {
        $pendingRecipes = Recipe::where('status', 'pending')
            ->with(['user', 'categories'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.moderation-test', [
            'pendingRecipes' => $pendingRecipes
        ]);
    }
}