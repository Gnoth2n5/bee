<?php

namespace App\Livewire\Collections;

use App\Models\Collection;
use App\Services\CollectionService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class CollectionDetail extends Component
{
    use WithPagination, WithFileUploads;

    public Collection $collection;
    public $isOwner = false;
    public $perPage = 12;
    public $isEditing = false;

    // Thuộc tính cho form chỉnh sửa
    public $editName;
    public $editDescription;
    public $editIsPublic;
    public $editCoverImage;
    public $editCoverImagePreview;

    public function mount(Collection $collection)
    {
        $this->collection = $collection->load(['user.profile', 'recipes.categories', 'recipes.images', 'recipes.user.profile']);
        $this->isOwner = Auth::check() && Auth::id() === $collection->user_id;
        $this->resetEditFields();
    }

    public function showEdit()
    {
        $this->isEditing = true;
        $this->resetEditFields();
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->resetEditFields();
        $this->resetValidation();
    }

    public function updatedEditCoverImage()
    {
        if ($this->editCoverImage) {
            $this->editCoverImagePreview = $this->editCoverImage->temporaryUrl();
        }
    }

    public function resetEditFields()
    {
        $this->editName = $this->collection->name;
        $this->editDescription = $this->collection->description;
        $this->editIsPublic = $this->collection->is_public;
        $this->editCoverImage = null;
        $this->editCoverImagePreview = $this->collection->cover_image ? Storage::url($this->collection->cover_image) : null;
    }

    public function resetEditCoverImage()
    {
        $this->editCoverImage = null;
        $this->editCoverImagePreview = $this->collection->cover_image ? Storage::url($this->collection->cover_image) : null;
        $this->resetErrorBag('editCoverImage');
    }

    public function updateCollection()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editDescription' => 'nullable|string|max:1000',
            'editIsPublic' => 'boolean',
            'editCoverImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'editName.required' => 'Tên bộ sưu tập là bắt buộc.',
            'editName.max' => 'Tên bộ sưu tập không được vượt quá 255 ký tự.',
            'editDescription.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'editCoverImage.image' => 'File phải là hình ảnh.',
            'editCoverImage.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'editCoverImage.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ]);

        $data = [
            'name' => $this->editName,
            'description' => $this->editDescription,
            'is_public' => $this->editIsPublic,
        ];
        if ($this->editCoverImage) {
            $data['cover_image'] = $this->editCoverImage;
        }

        $collectionService = app(CollectionService::class);
        $collectionService->update($this->collection, $data);

        // Load lại collection mới nhất
        $this->collection = Collection::with(['user.profile', 'recipes.categories', 'recipes.images', 'recipes.user.profile'])
            ->find($this->collection->id);
        $this->isEditing = false;
        $this->resetEditFields();
        session()->flash('success', 'Cập nhật bộ sưu tập thành công!');
        $this->dispatch('flash-message', message: 'Cập nhật bộ sưu tập thành công!', type: 'success');
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
            // Refresh collection data realtime
            $this->collection = \App\Models\Collection::with(['user.profile', 'recipes.categories', 'recipes.images', 'recipes.user.profile'])
                ->find($this->collection->id);
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

    /**
     * Toggle favorite status for a recipe
     */
    public function toggleFavorite($recipeId)
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            session()->flash('message', 'Vui lòng đăng nhập để thêm vào yêu thích.');
            return redirect()->route('login');
        }

        $recipe = \App\Models\Recipe::findOrFail($recipeId);
        $favoriteService = app(\App\Services\FavoriteService::class);
        $result = $favoriteService->toggle($recipe, \Illuminate\Support\Facades\Auth::user());

        session()->flash('success', $result['message']);
        $this->dispatch('favorite-toggled', recipeId: $recipeId);
        $this->dispatch('flash-message', message: $result['message'], type: 'success');

        // Refresh component để cập nhật UI
        $this->dispatch('$refresh');
    }

    /**
     * Confirm toggle favorite with confirmation dialog
     */
    public function confirmToggleFavorite($recipeId)
    {
        $recipe = \App\Models\Recipe::findOrFail($recipeId);
        $isFavorited = $recipe->isFavoritedBy(\Illuminate\Support\Facades\Auth::user());

        if ($isFavorited) {
            $this->removeFavorite($recipe->slug);
        } else {
            $this->toggleFavorite($recipeId);
        }
    }

    /**
     * Remove favorite (fallback method)
     */
    public function removeFavorite($recipeSlug)
    {
        if (!Auth::check()) {
            session()->flash('message', 'Vui lòng đăng nhập để thực hiện thao tác này.');
            return;
        }

        $recipe = \App\Models\Recipe::where('slug', $recipeSlug)->first();
        if ($recipe) {
            $favoriteService = app(FavoriteService::class);
            $favoriteService->removeFavorite($recipe, Auth::user());

            session()->flash('success', 'Đã xóa khỏi danh sách yêu thích.');
            $this->dispatch('favorite-toggled', recipeId: $recipe->id);
            $this->dispatch('flash-message', message: 'Đã xóa khỏi danh sách yêu thích.', type: 'success');

            // Refresh component để cập nhật UI
            $this->dispatch('$refresh');
        }
    }

    public function render()
    {
        return view('livewire.collections.collection-detail', [
            'recipes' => $this->recipes,
        ]);
    }
}