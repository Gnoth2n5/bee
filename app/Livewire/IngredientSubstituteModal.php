<?php

namespace App\Livewire;

use App\Services\IngredientSubstituteService;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class IngredientSubstituteModal extends Component
{
    protected $ingredientSubstituteService;

    /**
     * Modal hiển thị/ẩn
     */
    public $showModal = false;

    /**
     * Nguyên liệu nhập bằng tiếng Việt
     */
    #[Rule('required|string|min:1|max:100')]
    public $ingredientVi = '';

    /**
     * Danh sách nguyên liệu thay thế
     */
    public $substitutes = [];

    /**
     * Trạng thái loading
     */
    public $loading = false;

    /**
     * Error message
     */
    public $error = null;

    /**
     * Success message
     */
    public $success = null;

    /**
     * Search history
     */
    public $searchHistory = [];

    /**
     * Max history items
     */
    public $maxHistory = 5;

    public function boot(IngredientSubstituteService $ingredientSubstituteService)
    {
        $this->ingredientSubstituteService = $ingredientSubstituteService;
    }

    public function mount()
    {
        $this->loadSearchHistory();
    }

    /**
     * Mở modal từ navigation
     */
    #[On('open-ingredient-substitute-modal')]
    public function openModal()
    {
        $this->showModal = true;
        $this->resetMessages();

        // Focus vào input khi modal mở
        $this->dispatch('focus-ingredient-input');
    }

    /**
     * Đóng modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Tìm kiếm nguyên liệu thay thế
     */
    public function findSubstitutes()
    {
        // Reset trạng thái
        $this->resetMessages();
        $this->substitutes = [];

        // Validate input
        $this->validate();

        if (empty(trim($this->ingredientVi))) {
            $this->error = 'Vui lòng nhập tên nguyên liệu.';
            return;
        }

        $this->loading = true;

        try {
            Log::info('Modal search for ingredient substitutes', [
                'ingredient' => $this->ingredientVi,
                'user_ip' => request()->ip()
            ]);

            // Gọi service để tìm nguyên liệu thay thế
            $result = $this->ingredientSubstituteService->getSubstitutes($this->ingredientVi);

            if ($result['success']) {
                $this->substitutes = $result['substitutes'];
                $this->success = 'Tìm thấy ' . count($this->substitutes) . ' nguyên liệu có thể thay thế cho "' . $this->ingredientVi . '".';

                // Lưu vào lịch sử tìm kiếm
                $this->addToSearchHistory($this->ingredientVi);

                Log::info('Modal ingredient substitutes found successfully', [
                    'ingredient' => $this->ingredientVi,
                    'substitutes_count' => count($this->substitutes),
                    'from_cache' => $result['from_cache'] ?? false
                ]);
            } else {
                $this->error = $result['error'] ?? 'Không thể tìm thấy nguyên liệu thay thế.';

                Log::warning('Modal failed to find ingredient substitutes', [
                    'ingredient' => $this->ingredientVi,
                    'error' => $this->error
                ]);
            }
        } catch (\Exception $e) {
            $this->error = 'Có lỗi xảy ra. Vui lòng thử lại sau.';

            Log::error('Exception in IngredientSubstituteModal', [
                'ingredient' => $this->ingredientVi,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Reset form và kết quả
     */
    public function resetForm()
    {
        $this->reset(['ingredientVi', 'substitutes', 'error', 'success']);
    }

    /**
     * Search với ví dụ nguyên liệu
     */
    public function searchExample($ingredient)
    {
        $this->ingredientVi = $ingredient;
        $this->findSubstitutes();
    }

    /**
     * Search từ lịch sử
     */
    public function searchFromHistory($ingredient)
    {
        $this->ingredientVi = $ingredient;
        $this->findSubstitutes();
    }

    /**
     * Xóa lịch sử tìm kiếm
     */
    public function clearSearchHistory()
    {
        $this->searchHistory = [];
        session()->forget('ingredient_search_history');
        $this->success = 'Đã xóa lịch sử tìm kiếm.';
    }

    /**
     * Thêm vào lịch sử tìm kiếm
     */
    protected function addToSearchHistory($ingredient)
    {
        $ingredient = trim($ingredient);

        // Loại bỏ nếu đã tồn tại
        $this->searchHistory = array_filter($this->searchHistory, fn($item) => $item !== $ingredient);

        // Thêm vào đầu danh sách
        array_unshift($this->searchHistory, $ingredient);

        // Giới hạn số lượng
        $this->searchHistory = array_slice($this->searchHistory, 0, $this->maxHistory);

        // Lưu vào session
        session(['ingredient_search_history' => $this->searchHistory]);
    }

    /**
     * Load lịch sử tìm kiếm từ session
     */
    protected function loadSearchHistory()
    {
        $this->searchHistory = session('ingredient_search_history', []);
    }

    /**
     * Reset error và success messages
     */
    protected function resetMessages()
    {
        $this->error = null;
        $this->success = null;
    }

    /**
     * Kiểm tra validation realtime
     */
    public function updated($propertyName)
    {
        if ($propertyName === 'ingredientVi') {
            $this->resetMessages();
            // Validate từng ký tự để show error realtime
            $this->validateOnly($propertyName);
        }
    }

    /**
     * Handle keyboard shortcuts
     */
    #[On('handle-keyboard-shortcut')]
    public function handleKeyboardShortcut($key)
    {
        if ($key === 'Escape') {
            $this->closeModal();
        } elseif ($key === 'Enter' && !empty(trim($this->ingredientVi))) {
            $this->findSubstitutes();
        }
    }

    public function render()
    {
        return view('livewire.ingredient-substitute-modal');
    }
}
