<?php

namespace App\Livewire\ShoppingList;

use App\Models\ShoppingList;
use App\Models\Recipe;
use App\Models\WeeklyMealPlan;
use App\Services\ShoppingListService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ShoppingListDashboard extends Component
{
    use WithPagination;

    public $shoppingLists;
    public $showCreateForm = false;
    public $newShoppingList = [
        'name' => '',
        'description' => ''
    ];

    public $selectedMealPlanId = null;
    public $selectedRecipeId = null;
    public $mealPlans = [];
    public $recipes = [];

    protected $rules = [
        'newShoppingList.name' => 'required|string|max:255',
        'newShoppingList.description' => 'nullable|string'
    ];

    public function mount()
    {
        $this->loadShoppingLists();
        $this->loadMealPlansAndRecipes();
    }

    public function loadShoppingLists()
    {
        $this->shoppingLists = ShoppingList::where('user_id', auth()->id())
            ->with([
                'items' => function ($query) {
                    $query->orderBy('sort_order');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function loadMealPlansAndRecipes()
    {
        $this->mealPlans = WeeklyMealPlan::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();

        $this->recipes = Recipe::published()->get();
    }

    public function createShoppingList()
    {
        $this->validate();

        $shoppingList = ShoppingList::create([
            'user_id' => auth()->id(),
            'name' => $this->newShoppingList['name'],
            'description' => $this->newShoppingList['description'],
            'is_active' => true
        ]);

        $this->reset('newShoppingList', 'showCreateForm');
        $this->loadShoppingLists();

        $this->dispatch('flash-message', [
            'message' => 'Shopping list đã được tạo thành công!',
            'type' => 'success'
        ]);
    }

    public function generateFromMealPlan()
    {
        if (!$this->selectedMealPlanId) {
            $this->dispatch('flash-message', [
                'message' => 'Vui lòng chọn một meal plan!',
                'type' => 'error'
            ]);
            return;
        }

        $mealPlan = WeeklyMealPlan::find($this->selectedMealPlanId);
        if ($mealPlan && $mealPlan->user_id == auth()->id()) {
            $service = app(ShoppingListService::class);
            $shoppingList = $service->generateFromMealPlan($mealPlan);

            $this->loadShoppingLists();
            $this->selectedMealPlanId = null;

            $this->dispatch('flash-message', [
                'message' => 'Shopping list đã được tạo từ meal plan!',
                'type' => 'success'
            ]);
        }
    }

    public function generateFromRecipe()
    {
        if (!$this->selectedRecipeId) {
            $this->dispatch('flash-message', [
                'message' => 'Vui lòng chọn một recipe!',
                'type' => 'error'
            ]);
            return;
        }

        $recipe = Recipe::find($this->selectedRecipeId);
        if ($recipe) {
            $service = app(ShoppingListService::class);
            $shoppingList = $service->generateFromRecipe($recipe, auth()->user());

            $this->loadShoppingLists();
            $this->selectedRecipeId = null;

            $this->dispatch('flash-message', [
                'message' => 'Shopping list đã được tạo từ recipe!',
                'type' => 'success'
            ]);
        }
    }

    public function deleteShoppingList($id)
    {
        $shoppingList = ShoppingList::where('user_id', auth()->id())->find($id);
        if ($shoppingList) {
            $shoppingList->delete();
            $this->loadShoppingLists();

            $this->dispatch('flash-message', [
                'message' => 'Shopping list đã được xóa!',
                'type' => 'success'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.shopping-list.shopping-list-dashboard');
    }
}
