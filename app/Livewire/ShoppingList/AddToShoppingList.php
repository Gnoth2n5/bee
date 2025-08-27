<?php

namespace App\Livewire\ShoppingList;

use App\Models\Recipe;
use App\Models\ShoppingList;
use App\Services\ShoppingListService;
use Livewire\Component;

class AddToShoppingList extends Component
{
    public Recipe $recipe;
    public $shoppingLists = [];
    public $selectedShoppingListId = null;
    public $showCreateForm = false;
    public $newShoppingListName = '';

    public function mount(Recipe $recipe)
    {
        $this->recipe = $recipe;
        $this->loadShoppingLists();
    }

    public function loadShoppingLists()
    {
        $this->shoppingLists = ShoppingList::where('user_id', auth()->id())
            ->where('is_active', true)
            ->whereNull('completed_at')
            ->get();
    }

    public function addToShoppingList()
    {
        if (!$this->selectedShoppingListId) {
            $this->dispatch('flash-message', [
                'message' => 'Vui lòng chọn một shopping list!',
                'type' => 'error'
            ]);
            return;
        }

        $shoppingList = ShoppingList::find($this->selectedShoppingListId);
        if (!$shoppingList || $shoppingList->user_id !== auth()->id()) {
            $this->dispatch('flash-message', [
                'message' => 'Shopping list không hợp lệ!',
                'type' => 'error'
            ]);
            return;
        }

        $service = app(ShoppingListService::class);
        $service->generateFromRecipe($this->recipe, auth()->user(), $shoppingList);

        $this->dispatch('flash-message', [
            'message' => 'Đã thêm ingredients vào shopping list!',
            'type' => 'success'
        ]);

        $this->selectedShoppingListId = null;
    }

    public function createAndAdd()
    {
        if (empty($this->newShoppingListName)) {
            $this->dispatch('flash-message', [
                'message' => 'Vui lòng nhập tên shopping list!',
                'type' => 'error'
            ]);
            return;
        }

        $service = app(ShoppingListService::class);
        $shoppingList = $service->createShoppingList(auth()->user(), [
            'name' => $this->newShoppingListName,
            'description' => 'Tạo từ recipe: ' . $this->recipe->title
        ]);

        $service->generateFromRecipe($this->recipe, auth()->user(), $shoppingList);

        $this->loadShoppingLists();
        $this->newShoppingListName = '';
        $this->showCreateForm = false;

        $this->dispatch('flash-message', [
            'message' => 'Đã tạo shopping list mới và thêm ingredients!',
            'type' => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.shopping-list.add-to-shopping-list');
    }
}
