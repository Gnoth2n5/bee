<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use App\Services\RatingService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RecipeRating extends Component
{
    public Recipe $recipe;
    public $userRating = null;
    public $showRatingModal = false;
    public $rating = 0;
    public $isSubmitting = false;

    protected $listeners = [
        'refreshRating' => 'refreshRating',
        'openRatingModal' => 'openRatingModal'
    ];

    public function mount(Recipe $recipe)
    {
        $this->recipe = $recipe;
        $this->loadUserRating();
    }

    public function loadUserRating()
    {
        if (Auth::check()) {
            $ratingService = app(RatingService::class);
            $this->userRating = $ratingService->getUserRating($this->recipe, Auth::user());
            if ($this->userRating) {
                $this->rating = $this->userRating->rating;
            }
        }
    }

    public function setRating($value)
    {
        $this->rating = $value;
    }

    public function openRatingModal()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Vui lòng đăng nhập để đánh giá công thức.');
            return redirect()->route('login');
        }

        $this->showRatingModal = true;
    }

    public function closeRatingModal()
    {
        $this->showRatingModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->rating = $this->userRating ? $this->userRating->rating : 0;
        $this->isSubmitting = false;
    }

    public function submitRating()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Vui lòng đăng nhập để đánh giá công thức.');
            return;
        }

        if ($this->rating < 1) {
            session()->flash('error', 'Vui lòng chọn số sao đánh giá.');
            return;
        }

        $this->isSubmitting = true;

        try {
            $ratingService = app(RatingService::class);
            $data = [
                'rating' => $this->rating
            ];

            if ($this->userRating) {
                // Update existing rating
                $ratingService->update($this->userRating, $data);
                $message = 'Đánh giá đã được cập nhật thành công!';
            } else {
                // Create new rating
                $ratingService->create($data, $this->recipe, Auth::user());
                $message = 'Đánh giá đã được gửi thành công!';
            }

            // Refresh recipe data
            $this->recipe->refresh();
            $this->loadUserRating();
            
            $this->closeRatingModal();
            session()->flash('success', $message);
            $this->dispatch('flash-message', message: $message, type: 'success');
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function deleteRating()
    {
        if (!$this->userRating) {
            return;
        }

        try {
            $ratingService = app(RatingService::class);
            $ratingService->delete($this->userRating);
            
            $this->userRating = null;
            $this->recipe->refresh();
            $this->resetForm();
            
            session()->flash('success', 'Đánh giá đã được xóa thành công!');
            $this->dispatch('flash-message', message: 'Đánh giá đã được xóa thành công!', type: 'success');
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function refreshRating()
    {
        $this->recipe->refresh();
        $this->loadUserRating();
    }

    public function render()
    {
        $ratingService = app(RatingService::class);
        $ratingDistribution = $ratingService->getRatingDistribution($this->recipe);
        
        return view('livewire.recipes.recipe-rating', [
            'ratingDistribution' => $ratingDistribution,
            'averageRating' => $this->recipe->average_rating,
            'ratingCount' => $this->recipe->rating_count,
        ]);
    }
} 