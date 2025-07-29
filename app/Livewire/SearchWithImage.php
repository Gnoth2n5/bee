<?php

namespace App\Livewire;

use App\Services\GeminiService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

class SearchWithImage extends Component
{
    use WithFileUploads;

    public $search = '';
    public $searchImage = null;
    public $isAnalyzingImage = false;
    public $imageAnalysisResult = null;

    protected $listeners = ['searchUpdated' => 'updateSearch'];

    public function mount()
    {
        // Lấy search từ URL nếu có
        $this->search = request()->get('search', '');
    }

    public function updateSearch($search)
    {
        $this->search = $search;
    }

    public function performSearch()
    {
        $this->dispatch('search-performed', search: $this->search);
    }

    public function analyzeImage()
    {
        $this->validate([
            'searchImage' => 'required|image|max:5120', // 5MB max
        ]);

        $this->isAnalyzingImage = true;
        $this->imageAnalysisResult = null;

        try {
            $geminiService = app(GeminiService::class);
            $result = $geminiService->searchRecipesByImage($this->searchImage);

            if ($result['success']) {
                // Sử dụng từ khóa đầu tiên để tìm kiếm
                $this->search = $result['keywords'][0] ?? '';
                $this->performSearch();

                $this->imageAnalysisResult = [
                    'success' => true,
                    'keywords' => $result['keywords'],
                    'message' => 'Đã tìm kiếm với từ khóa: ' . implode(', ', $result['keywords'])
                ];
            } else {
                $this->imageAnalysisResult = [
                    'success' => false,
                    'message' => $result['error']
                ];
            }
        } catch (\Exception $e) {
            Log::error('Image analysis error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->imageAnalysisResult = [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi phân tích ảnh.'
            ];
        }

        $this->isAnalyzingImage = false;
        $this->searchImage = null;
    }

    public function clearImageSearch()
    {
        $this->searchImage = null;
        $this->imageAnalysisResult = null;
    }

    protected function rules()
    {
        return [
            'searchImage' => 'nullable|image|max:5120', // 5MB max
        ];
    }

    public function updatedSearchImage()
    {
        $this->validate([
            'searchImage' => 'image|max:5120',
        ]);
    }

    public function render()
    {
        return view('livewire.search-with-image');
    }
}