<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use App\Models\Collection;
use App\Services\CollectionService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;

class RecipeCollectionManager extends Component
{
    public Recipe $recipe;
    public $showModal = false;
    public $showCreateModal = false;
    public $collections = [];
    public $selectedCollectionId = null;

    #[Rule('required|string|max:255')]
    public $newCollectionName = '';

    #[Rule('nullable|string|max:1000')]
    public $newCollectionDescription = '';

    public $newCollectionIsPublic = false;

    public function mount(Recipe $recipe)
    {
        try {
            $this->recipe = $recipe;
            $this->loadCollections();

            // Debug: Log để kiểm tra

        } catch (\Exception $e) {
            \Log::error('Error mounting RecipeCollectionManager', [
                'recipe_id' => $recipe->id,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Có lỗi xảy ra khi khởi tạo component: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi khởi tạo component: ' . $e->getMessage(), type: 'error');
        }
    }

    public function getRecipeCollectionsProperty()
    {
        try {
            if (!Auth::check()) {
                \Log::info('User not authenticated for recipe collections');
                return collect();
            }

            $collections = $this->recipe->getUserCollections(Auth::user());


            return $collections;
        } catch (\Exception $e) {
            \Log::error('Error loading recipe collections', [
                'recipe_id' => $this->recipe->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Có lỗi xảy ra khi tải thông tin bộ sưu tập: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi tải thông tin bộ sưu tập: ' . $e->getMessage(), type: 'error');
            return collect();
        }
    }

    public function loadCollections()
    {
        try {
            if (Auth::check()) {
                $this->collections = Collection::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();


            } else {
                \Log::info('User not authenticated for loading collections');
            }
        } catch (\Exception $e) {
            \Log::error('Error loading collections', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Có lỗi xảy ra khi tải danh sách bộ sưu tập: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi tải danh sách bộ sưu tập: ' . $e->getMessage(), type: 'error');
        }
    }

    public function openModal()
    {
        try {
            if (!Auth::check()) {
                session()->flash('message', 'Vui lòng đăng nhập để thêm vào bộ sưu tập.');
                $this->dispatch('flash-message', message: 'Vui lòng đăng nhập để thêm vào bộ sưu tập.', type: 'error');
                return redirect()->route('login');
            }

            $this->showModal = true;
            $this->loadCollections();


        } catch (\Exception $e) {
            \Log::error('Error opening modal', ['error' => $e->getMessage()]);
            session()->flash('error', 'Có lỗi xảy ra khi mở modal: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi mở modal: ' . $e->getMessage(), type: 'error');
        }
    }

    public function closeModal()
    {
        try {
            $this->showModal = false;
            $this->selectedCollectionId = null;
            $this->resetValidation();
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi đóng modal: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi đóng modal: ' . $e->getMessage(), type: 'error');
        }
    }

    public function openCreateModal()
    {
        try {
            $this->showCreateModal = true;
            $this->newCollectionName = '';
            $this->newCollectionDescription = '';
            $this->newCollectionIsPublic = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi mở modal tạo bộ sưu tập: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi mở modal tạo bộ sưu tập: ' . $e->getMessage(), type: 'error');
        }
    }

    public function closeCreateModal()
    {
        try {
            $this->showCreateModal = false;
            $this->resetValidation();
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi đóng modal tạo bộ sưu tập: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi đóng modal tạo bộ sưu tập: ' . $e->getMessage(), type: 'error');
        }
    }

    public function addToCollection()
    {
        try {


            if (!$this->selectedCollectionId) {
                $this->addError('selectedCollectionId', 'Vui lòng chọn một bộ sưu tập.');
                return;
            }

            $collection = Collection::find($this->selectedCollectionId);

            if (!$collection || $collection->user_id !== Auth::id()) {
                $this->addError('selectedCollectionId', 'Bộ sưu tập không hợp lệ.');
                return;
            }

            $collectionService = app(CollectionService::class);
            $result = $collectionService->addRecipe($collection, $this->recipe);



            if ($result) {
                $this->closeModal();
                $this->loadCollections();
                $this->selectedCollectionId = null;
                session()->flash('success', 'Đã thêm công thức vào bộ sưu tập "' . $collection->name . '" thành công!');
                $this->dispatch('flash-message', message: 'Đã thêm công thức vào bộ sưu tập "' . $collection->name . '" thành công!', type: 'success');
            } else {
                $this->addError('selectedCollectionId', 'Công thức đã có trong bộ sưu tập này.');
                session()->flash('error', 'Công thức đã có trong bộ sưu tập này.');
                $this->dispatch('flash-message', message: 'Công thức đã có trong bộ sưu tập này.', type: 'error');
            }
        } catch (\Exception $e) {
            \Log::error('Error in addToCollection', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Có lỗi xảy ra khi thêm công thức vào bộ sưu tập: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi thêm công thức vào bộ sưu tập: ' . $e->getMessage(), type: 'error');
        }
    }

    public function createCollection()
    {
        try {
            $this->validate();

            $collectionService = app(CollectionService::class);

            $collectionData = [
                'name' => $this->newCollectionName,
                'description' => $this->newCollectionDescription,
                'is_public' => $this->newCollectionIsPublic,
            ];

            $collection = $collectionService->create($collectionData, Auth::user());

            // Tự động thêm công thức vào bộ sưu tập mới
            $collectionService->addRecipe($collection, $this->recipe);

            $this->closeCreateModal();
            $this->loadCollections();
            $this->selectedCollectionId = null;

            session()->flash('success', 'Đã tạo bộ sưu tập "' . $collection->name . '" và thêm công thức thành công!');
            $this->dispatch('flash-message', message: 'Đã tạo bộ sưu tập "' . $collection->name . '" và thêm công thức thành công!', type: 'success');
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi tạo bộ sưu tập: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi tạo bộ sưu tập: ' . $e->getMessage(), type: 'error');
        }
    }

    public function removeFromCollection($collectionId)
    {
        try {
            $collection = Collection::find($collectionId);

            if (!$collection || $collection->user_id !== Auth::id()) {
                session()->flash('error', 'Bộ sưu tập không hợp lệ.');
                $this->dispatch('flash-message', message: 'Bộ sưu tập không hợp lệ.', type: 'error');
                return;
            }

            $collectionService = app(CollectionService::class);
            $result = $collectionService->removeRecipe($collection, $this->recipe);

            if ($result) {
                $this->loadCollections();
                $this->selectedCollectionId = null;
                session()->flash('success', 'Đã xóa công thức khỏi bộ sưu tập "' . $collection->name . '"!');
                $this->dispatch('flash-message', message: 'Đã xóa công thức khỏi bộ sưu tập "' . $collection->name . '"!', type: 'success');
            } else {
                session()->flash('error', 'Không thể xóa công thức khỏi bộ sưu tập.');
                $this->dispatch('flash-message', message: 'Không thể xóa công thức khỏi bộ sưu tập.', type: 'error');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi xóa công thức khỏi bộ sưu tập: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi xóa công thức khỏi bộ sưu tập: ' . $e->getMessage(), type: 'error');
        }
    }



    public function render()
    {
        try {
            return view('livewire.recipes.recipe-collection-manager');
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi render component: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi render component: ' . $e->getMessage(), type: 'error');
            return view('livewire.recipes.recipe-collection-manager');
        }
    }
}