<?php

namespace App\Livewire;

use App\Services\GeminiService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageSearch extends Component
{
    use WithFileUploads;

    public $searchImage = null;
    public $isAnalyzingImage = false;
    public $imageAnalysisResult = null;
    public $searchQuery = '';

    protected $listeners = ['imageAnalyzed' => 'handleImageAnalysis'];

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
                $this->searchQuery = $result['keywords'][0] ?? '';
                $this->imageAnalysisResult = [
                    'success' => true,
                    'keywords' => $result['keywords'],
                    'message' => 'Đã phân tích ảnh thành công! Tìm thấy: ' . implode(', ', $result['keywords'])
                ];

                // Emit event để parent component có thể xử lý
                $this->dispatch('imageAnalyzed', [
                    'keywords' => $result['keywords'],
                    'searchQuery' => $this->searchQuery
                ]);
            } else {
                $this->imageAnalysisResult = [
                    'success' => false,
                    'message' => $result['error'] ?? 'Không thể phân tích ảnh'
                ];
            }
        } catch (\Exception $e) {
            $this->imageAnalysisResult = [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi phân tích ảnh: ' . $e->getMessage()
            ];
        }

        $this->isAnalyzingImage = false;
        $this->searchImage = null;
    }

    public function handleImageAnalysis($data)
    {
        $this->searchQuery = $data['searchQuery'] ?? '';
        $this->imageAnalysisResult = [
            'success' => true,
            'keywords' => $data['keywords'] ?? [],
            'message' => 'Đã phân tích ảnh thành công!'
        ];
    }

    public function clearImageSearch()
    {
        $this->searchImage = null;
        $this->imageAnalysisResult = null;
        $this->searchQuery = '';
        $this->dispatch('imageSearchCleared');
    }

    public function performSearch()
    {
        if (!empty($this->searchQuery)) {
            $this->dispatch('performSearch', searchQuery: $this->searchQuery);
        }
    }

    public function updatedSearchImage()
    {
        $this->validate([
            'searchImage' => 'image|max:5120',
        ]);
    }

    public function render()
    {
        return view('livewire.image-search');
    }
}