<?php

namespace App\Livewire\ShoppingList;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\Recipe;
use App\Models\WeeklyMealPlan;
use App\Services\ShoppingListService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ShoppingListManager extends Component
{
    use WithPagination;

    public $shoppingLists;
    public $selectedShoppingList;
    public $showCreateForm = false;
    public $showAddItemForm = false;
    public $newItem = [
        'ingredient_name' => '',
        'amount' => '',
        'unit' => '',
        'notes' => '',
        'category' => ''
    ];

    public $newShoppingList = [
        'name' => '',
        'description' => ''
    ];

    public $categories = [
        'Rau củ' => 'Rau củ',
        'Thịt cá' => 'Thịt cá',
        'Sữa và bơ sữa' => 'Sữa và bơ sữa',
        'Ngũ cốc' => 'Ngũ cốc',
        'Gia vị' => 'Gia vị',
        'Trái cây' => 'Trái cây',
        'Trứng' => 'Trứng',
        'Khác' => 'Khác'
    ];

    protected $rules = [
        'newShoppingList.name' => 'required|string|max:255',
        'newShoppingList.description' => 'nullable|string',
        'newItem.ingredient_name' => 'required|string|max:255',
        'newItem.amount' => 'nullable|numeric|min:0',
        'newItem.unit' => 'nullable|string|max:50',
        'newItem.notes' => 'nullable|string',
        'newItem.category' => 'nullable|string|max:100'
    ];

    public function mount()
    {
        $this->loadShoppingLists();
    }

    public function loadShoppingLists()
    {
        $this->shoppingLists = ShoppingList::where('user_id', auth()->id())
            ->where('is_active', true)
            ->whereNull('completed_at')
            ->with([
                'items' => function ($query) {
                    $query->orderBy('sort_order');
                }
            ])
            ->get();
    }

    public function selectShoppingList($id)
    {
        $this->selectedShoppingList = ShoppingList::with([
            'items' => function ($query) {
                $query->orderBy('sort_order');
            }
        ])->find($id);
    }

    public function createShoppingList()
    {
        $this->validate([
            'newShoppingList.name' => 'required|string|max:255',
            'newShoppingList.description' => 'nullable|string'
        ]);

        $shoppingList = ShoppingList::create([
            'user_id' => auth()->id(),
            'name' => $this->newShoppingList['name'],
            'description' => $this->newShoppingList['description'],
            'is_active' => true
        ]);

        $this->reset('newShoppingList', 'showCreateForm');
        $this->loadShoppingLists();
        $this->selectShoppingList($shoppingList->id);

        $this->dispatch('flash-message', [
            'message' => 'Shopping list đã được tạo thành công!',
            'type' => 'success'
        ]);
    }

    public function addItem()
    {
        if (!$this->selectedShoppingList) {
            $this->dispatch('flash-message', [
                'message' => 'Vui lòng chọn một shopping list!',
                'type' => 'error'
            ]);
            return;
        }

        $this->validate([
            'newItem.ingredient_name' => 'required|string|max:255',
            'newItem.amount' => 'nullable|numeric|min:0',
            'newItem.unit' => 'nullable|string|max:50',
            'newItem.notes' => 'nullable|string',
            'newItem.category' => 'nullable|string|max:100'
        ]);

        ShoppingListItem::create([
            'shopping_list_id' => $this->selectedShoppingList->id,
            'ingredient_name' => $this->newItem['ingredient_name'],
            'amount' => $this->newItem['amount'] ?: null,
            'unit' => $this->newItem['unit'] ?: null,
            'notes' => $this->newItem['notes'] ?: null,
            'category' => $this->newItem['category'] ?: 'Khác',
            'sort_order' => $this->selectedShoppingList->items()->max('sort_order') + 1
        ]);

        $this->reset('newItem');
        $this->selectShoppingList($this->selectedShoppingList->id);

        $this->dispatch('flash-message', [
            'message' => 'Item đã được thêm vào shopping list!',
            'type' => 'success'
        ]);
    }

    public function toggleItemChecked($itemId)
    {
        $item = ShoppingListItem::find($itemId);
        if ($item && $item->shopping_list_id == $this->selectedShoppingList->id) {
            $item->toggleChecked();
            $this->selectShoppingList($this->selectedShoppingList->id);
        }
    }

    public function deleteItem($itemId)
    {
        $item = ShoppingListItem::find($itemId);
        if ($item && $item->shopping_list_id == $this->selectedShoppingList->id) {
            $item->delete();
            $this->selectShoppingList($this->selectedShoppingList->id);

            $this->dispatch('flash-message', [
                'message' => 'Item đã được xóa!',
                'type' => 'success'
            ]);
        }
    }

    public function clearCheckedItems()
    {
        if ($this->selectedShoppingList) {
            // Get checked items and delete them individually
            $checkedItems = $this->selectedShoppingList->checkedItems()->get();
            $deletedCount = 0;

            // Debug info
            $totalItems = $this->selectedShoppingList->items()->count();
            $checkedCount = $checkedItems->count();

            foreach ($checkedItems as $item) {
                $item->delete();
                $deletedCount++;
            }

            $this->selectShoppingList($this->selectedShoppingList->id);

            if ($deletedCount > 0) {
                $this->dispatch('flash-message', [
                    'message' => "Đã xóa {$deletedCount} items đã check!",
                    'type' => 'success'
                ]);
            } else {
                $this->dispatch('flash-message', [
                    'message' => "Không có items nào được check để xóa!",
                    'type' => 'warning'
                ]);
            }
        }
    }

    public function markAsCompleted()
    {
        if ($this->selectedShoppingList) {
            $this->selectedShoppingList->markAsCompleted();
            $this->loadShoppingLists();
            $this->selectedShoppingList = null;

            $this->dispatch('flash-message', [
                'message' => 'Shopping list đã được đánh dấu hoàn thành!',
                'type' => 'success'
            ]);
        }
    }

    public function generateFromMealPlan($mealPlanId)
    {
        $mealPlan = WeeklyMealPlan::find($mealPlanId);
        if ($mealPlan && $mealPlan->user_id == auth()->id()) {
            $service = app(ShoppingListService::class);
            $shoppingList = $service->generateFromMealPlan($mealPlan);

            $this->loadShoppingLists();
            $this->selectShoppingList($shoppingList->id);

            $this->dispatch('flash-message', [
                'message' => 'Shopping list đã được tạo từ meal plan!',
                'type' => 'success'
            ]);
        }
    }

    public function generateFromRecipe($recipeId)
    {
        $recipe = Recipe::find($recipeId);
        if ($recipe) {
            $service = app(ShoppingListService::class);
            $shoppingList = $service->generateFromRecipe($recipe, auth()->user());

            $this->loadShoppingLists();
            $this->selectShoppingList($shoppingList->id);

            $this->dispatch('flash-message', [
                'message' => 'Shopping list đã được tạo từ recipe!',
                'type' => 'success'
            ]);
        }
    }

    public function render()
    {
        $mealPlans = WeeklyMealPlan::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();

        $recipes = Recipe::published()->get();

        return view('livewire.shopping-list.shopping-list-manager', [
            'mealPlans' => $mealPlans,
            'recipes' => $recipes
        ]);
    }
}
