<?php

namespace App\Livewire\MealPlans;

use App\Models\WeeklyMealPlan;
use App\Models\Recipe;
use App\Models\User;
use App\Models\Favorite;
use App\Services\WeeklyMealPlanService;
use App\Services\WeatherService;
use App\Services\OpenAiService;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class WeeklyMealPlanPage extends Component
{
    use WithPagination;

    public $currentMealPlan;
    public $selectedWeek;
    public $mealTypes;
    public $days;

    public $selectedDay;
    public $selectedMealType;
    public $statistics = [];

    protected $listeners = ['mealPlanUpdated' => 'refreshMealPlan'];

    public function mount()
    {
        $this->mealTypes = WeeklyMealPlan::getMealTypes();
        $this->days = [
            'monday' => 'Thứ 2',
            'tuesday' => 'Thứ 3',
            'wednesday' => 'Thứ 4',
            'thursday' => 'Thứ 5',
            'friday' => 'Thứ 6',
            'saturday' => 'Thứ 7',
            'sunday' => 'Chủ nhật'
        ];

        $this->selectedWeek = now()->startOfWeek()->format('Y-m-d');
        $this->loadCurrentMealPlan();
    }

    public function loadCurrentMealPlan()
    {
        $user = Auth::user();
        $weekStart = Carbon::parse($this->selectedWeek);

        $this->currentMealPlan = app(WeeklyMealPlanService::class)
            ->getMealPlanForWeek($user, $weekStart);

        if ($this->currentMealPlan) {
            $this->statistics = app(WeeklyMealPlanService::class)
                ->getStatistics($this->currentMealPlan);
        }
    }

    public function createMealPlan()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                \Log::error('No authenticated user found');
                return;
            }

            $weekStart = Carbon::parse($this->selectedWeek);

            \Log::info('Creating meal plan', [
                'user_id' => $user->id,
                'week_start' => $weekStart->format('Y-m-d'),
                'selected_week' => $this->selectedWeek
            ]);

            $this->currentMealPlan = app(WeeklyMealPlanService::class)
                ->createMealPlan($user, 'Kế hoạch tuần ' . $weekStart->format('d/m/Y'), $weekStart);

            \Log::info('Meal plan created successfully', [
                'meal_plan_id' => $this->currentMealPlan->id ?? 'null'
            ]);

            $this->dispatch('mealPlanCreated');
        } catch (\Exception $e) {
            \Log::error('Error creating meal plan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function addMeal($day, $mealType, $recipeId)
    {
        if (!$this->currentMealPlan) {
            return;
        }

        $success = app(WeeklyMealPlanService::class)
            ->addMeal($this->currentMealPlan, $day, $mealType, $recipeId);

        if ($success) {
            $this->refreshMealPlan();
            $this->dispatch('mealAdded');
        }
    }

    public function removeMeal($day, $mealType, $recipeId)
    {
        if (!$this->currentMealPlan) {
            return;
        }

        app(WeeklyMealPlanService::class)
            ->removeMeal($this->currentMealPlan, $day, $mealType, $recipeId);

        $this->refreshMealPlan();
        $this->dispatch('mealRemoved');
    }



    public function generateWeeklyMeals()
    {
        try {
            if (!$this->currentMealPlan) {
                session()->flash('error', 'Không có kế hoạch bữa ăn để tạo món ăn theo tuần.');
                return redirect()->route('weekly-meal-plan');
            }

            $weeklyMeals = app(WeeklyMealPlanService::class)
                ->generateWeeklyMeals($this->currentMealPlan);

            // Chuyển đến trang hiển thị món ăn theo tuần
            return redirect()->route('weekly-meals.show', [
                'mealPlan' => $this->currentMealPlan->id
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi tạo món ăn theo tuần: ' . $e->getMessage());
            return redirect()->route('weekly-meal-plan');
        }
    }

    public function duplicateForNextWeek()
    {
        if (!$this->currentMealPlan) {
            return;
        }

        $newMealPlan = app(WeeklyMealPlanService::class)
            ->duplicateForNextWeek($this->currentMealPlan);

        $this->dispatch('mealPlanDuplicated', $newMealPlan);
    }

    public function refreshMealPlan()
    {
        $this->loadCurrentMealPlan();
    }

    public function updatedSelectedWeek()
    {
        $this->loadCurrentMealPlan();
    }

    public function getMealForDay($day, $mealType)
    {
        if (!$this->currentMealPlan) {
            return null;
        }

        return $this->currentMealPlan->getMealForDay($day, $mealType);
    }

    public function getMealsForDay($day, $mealType)
    {
        if (!$this->currentMealPlan) {
            return [];
        }

        return $this->currentMealPlan->getMealsForDay($day, $mealType);
    }

    public function saveMealPlan()
    {
        if (!$this->currentMealPlan) {
            session()->flash('error', 'Không có kế hoạch bữa ăn để lưu.');
            return;
        }

        try {
            // Lưu meal plan
            $this->currentMealPlan->save();

            // Cập nhật thống kê
            $this->currentMealPlan->updateTotals();

            session()->flash('success', 'Kế hoạch bữa ăn đã được lưu thành công!');

            // Refresh meal plan
            $this->loadCurrentMealPlan();

        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi lưu kế hoạch bữa ăn: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $mealPlans = WeeklyMealPlan::where('user_id', $user->id)
            ->orderBy('week_start', 'desc')
            ->paginate(5);

        return view('livewire.meal-plans.weekly-meal-plan-page', [
            'mealPlans' => $mealPlans
        ]);
    }
}
