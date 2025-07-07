<?php

namespace App\Livewire\Collections;

use App\Models\Collection;
use App\Services\CollectionService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class CollectionDetail extends Component
{
    use WithPagination;

    public Collection $collection;
    public $isOwner = false;
    public $perPage = 12;

    public function mount(Collection $collection)
    {
        $this->collection = $collection->load(['user.profile', 'recipes.categories', 'recipes.images', 'recipes.user.profile']);
        $this->isOwner = Auth::check() && Auth::id() === $collection->user_id;
    }

    public function removeRecipe($recipeId)
    {
        if (!$this->isOwner) {
            session()->flash('error', 'Bạn không có quyền xóa công thức khỏi bộ sưu tập này.');
            $this->dispatch('flash-message', message: 'Bạn không có quyền xóa công thức khỏi bộ sưu tập này.', type: 'error');
            return;
        }

        $recipe = $this->collection->recipes()->find($recipeId);
        if (!$recipe) {
            session()->flash('error', 'Không tìm thấy công thức trong bộ sưu tập.');
            $this->dispatch('flash-message', message: 'Không tìm thấy công thức trong bộ sưu tập.', type: 'error');
            return;
        }

        $collectionService = app(CollectionService::class);
        $result = $collectionService->removeRecipe($this->collection, $recipe);

        if ($result) {
            // Refresh collection data
            $this->collection->refresh();
            $this->collection->load(['user.profile', 'recipes.categories', 'recipes.images', 'recipes.user.profile']);
            
            session()->flash('success', 'Đã xóa công thức "' . $recipe->title . '" khỏi bộ sưu tập!');
            $this->dispatch('flash-message', message: 'Đã xóa công thức "' . $recipe->title . '" khỏi bộ sưu tập!', type: 'success');
        } else {
            session()->flash('error', 'Không thể xóa công thức khỏi bộ sưu tập.');
            $this->dispatch('flash-message', message: 'Không thể xóa công thức khỏi bộ sưu tập.', type: 'error');
        }
    }

    public function deleteCollection()
    {
        if (!$this->isOwner) {
            session()->flash('error', 'Bạn không có quyền xóa bộ sưu tập này.');
            $this->dispatch('flash-message', message: 'Bạn không có quyền xóa bộ sưu tập này.', type: 'error');
            return;
        }

        $collectionService = app(CollectionService::class);
        $result = $collectionService->delete($this->collection);

        if ($result) {
            session()->flash('success', 'Đã xóa bộ sưu tập "' . $this->collection->name . '" thành công!');
            $this->dispatch('flash-message', message: 'Đã xóa bộ sưu tập "' . $this->collection->name . '" thành công!', type: 'success');
            return redirect()->route('profile', ['activeTab' => 'collections']);
        } else {
            session()->flash('error', 'Không thể xóa bộ sưu tập.');
            $this->dispatch('flash-message', message: 'Không thể xóa bộ sưu tập.', type: 'error');
        }
    }

    public function getRecipesProperty()
    {
        return $this->collection->recipes()
            ->with(['categories', 'images', 'user.profile'])
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.collections.collection-detail', [
            'recipes' => $this->recipes,
        ]);
    }
} 