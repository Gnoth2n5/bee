<?php

namespace App\Livewire\Recipes;

use App\Http\Requests\Recipe\StoreRecipeRequest;
use App\Http\Requests\Recipe\UpdateRecipeRequest;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use App\Services\RecipeService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

class RecipeForm extends Component
{
    use WithFileUploads;

    public ?Recipe $recipe = null;
    public $isEditing = false;

    // Basic information
    #[Rule('required|string|max:255|regex:/^[\p{L}\p{N}\s\-\.\,\!\?\(\)\:\;\'\"]+$/u')]
    public $title = '';

    #[Rule('required|string|min:10')]
    public $description = '';

    #[Rule('required|string|max:500')]
    public $summary = '';

    // Cooking details
    #[Rule('required|integer|min:1|max:1440')]
    public $cookingTime = '';

    #[Rule('required|integer|min:0|max:1440')]
    public $preparationTime = '';

    #[Rule('required|in:easy,medium,hard')]
    public $difficulty = '';

    #[Rule('required|integer|min:1|max:50')]
    public $servings = '';

    #[Rule('nullable|integer|min:0|max:5000')]
    public $caloriesPerServing = '';

    // Content
    #[Rule('required|array|min:1')]
    public $ingredients = [];

    #[Rule('required|array|min:1')]
    public $instructions = [];

    #[Rule('nullable|string|max:1000')]
    public $tips = '';

    #[Rule('nullable|string|max:1000')]
    public $notes = '';

    // Media
    #[Rule('nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048')]
    public $featuredImage = null;

    #[Rule('nullable|url|max:500')]
    public $videoUrl = '';

    // Categories and tags
    #[Rule('required|array|min:1')]
    public $categoryIds = [];

    #[Rule('nullable|array')]
    public $tagIds = [];

    // Form state
    public $showPreview = false;
    public $isSubmitting = false;

    protected $listeners = ['refreshForm' => 'refreshForm'];

    public function mount(?Recipe $recipe = null)
    {
        if ($recipe) {
            $this->recipe = $recipe;
            $this->isEditing = true;
            $this->loadRecipeData();
        } else {
            $this->initializeForm();
        }
    }

    public function loadRecipeData()
    {
        $this->title = $this->recipe->title;
        $this->description = $this->recipe->description;
        $this->summary = $this->recipe->summary;
        $this->cookingTime = $this->recipe->cooking_time;
        $this->preparationTime = $this->recipe->preparation_time;
        $this->difficulty = $this->recipe->difficulty;
        $this->servings = $this->recipe->servings;
        $this->caloriesPerServing = $this->recipe->calories_per_serving;
        $this->ingredients = $this->recipe->ingredients ?? [];
        $this->instructions = $this->recipe->instructions ?? [];
        $this->tips = $this->recipe->tips;
        $this->notes = $this->recipe->notes;
        $this->videoUrl = $this->recipe->video_url;
        $this->categoryIds = $this->recipe->categories->pluck('id')->toArray();
        $this->tagIds = $this->recipe->tags->pluck('id')->toArray();
    }

    public function initializeForm()
    {
        $this->ingredients = [
            ['name' => '', 'amount' => '', 'unit' => '']
        ];
        $this->instructions = [
            ['step' => 1, 'instruction' => '']
        ];
        $this->difficulty = 'medium';
        $this->servings = 2;
    }

    public function addIngredient()
    {
        $this->ingredients[] = ['name' => '', 'amount' => '', 'unit' => ''];
    }

    public function removeIngredient($index)
    {
        if (count($this->ingredients) > 1) {
            unset($this->ingredients[$index]);
            $this->ingredients = array_values($this->ingredients);
        }
    }

    public function addInstruction()
    {
        $nextStep = count($this->instructions) + 1;
        $this->instructions[] = ['step' => $nextStep, 'instruction' => ''];
    }

    public function removeInstruction($index)
    {
        if (count($this->instructions) > 1) {
            unset($this->instructions[$index]);
            $this->instructions = array_values($this->instructions);

            // Reorder steps
            foreach ($this->instructions as $key => $instruction) {
                $this->instructions[$key]['step'] = $key + 1;
            }
        }
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    public function save()
    {
        $this->isSubmitting = true;

        try {
            $this->validate();

            $data = [
                'title' => $this->title,
                'description' => $this->description,
                'summary' => $this->summary,
                'cooking_time' => $this->cookingTime,
                'preparation_time' => $this->preparationTime,
                'difficulty' => $this->difficulty,
                'servings' => $this->servings,
                'calories_per_serving' => $this->caloriesPerServing,
                'ingredients' => $this->ingredients,
                'instructions' => $this->instructions,
                'tips' => $this->tips,
                'notes' => $this->notes,
                'video_url' => $this->videoUrl,
                'category_ids' => $this->categoryIds,
                'tag_ids' => $this->tagIds,
            ];

            if ($this->featuredImage) {
                $data['featured_image'] = $this->featuredImage;
            }

            $recipeService = app(RecipeService::class);

            if ($this->isEditing) {
                $recipe = $recipeService->update($this->recipe, $data);
                $message = 'Công thức đã được cập nhật thành công.';
            } else {
                $recipe = $recipeService->create($data, auth()->user());
                $message = 'Công thức đã được tạo thành công và đang chờ duyệt.';
            }

            $this->dispatch('recipe-saved', [
                'recipe' => $recipe,
                'message' => $message,
                'isEditing' => $this->isEditing
            ]);

            return redirect()->route('recipes.show', $recipe)
                ->with('success', $message);

        } catch (\Exception $e) {
            $this->addError('general', $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function refreshForm()
    {
        $this->resetValidation();
        $this->reset(['featuredImage']);
    }

    protected function messages()
    {
        return [
            'title.required' => 'Tiêu đề công thức là bắt buộc.',
            'title.min' => 'Tiêu đề phải có ít nhất 5 ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'title.regex' => 'Tiêu đề không được chứa ký hiệu đặc biệt.',
            'description.required' => 'Mô tả công thức là bắt buộc.',
            'description.min' => 'Mô tả phải có ít nhất 10 ký tự.',
            'summary.required' => 'Tóm tắt công thức là bắt buộc.',
            'summary.max' => 'Tóm tắt không được vượt quá 500 ký tự.',
            'cookingTime.required' => 'Thời gian nấu là bắt buộc.',
            'cookingTime.min' => 'Thời gian nấu phải ít nhất 1 phút.',
            'preparationTime.required' => 'Thời gian chuẩn bị là bắt buộc.',
            'difficulty.required' => 'Độ khó là bắt buộc.',
            'difficulty.in' => 'Độ khó phải là: dễ, trung bình, hoặc khó.',
            'servings.required' => 'Số khẩu phần là bắt buộc.',
            'servings.min' => 'Số khẩu phần phải ít nhất 1.',
            'ingredients.required' => 'Danh sách nguyên liệu là bắt buộc.',
            'ingredients.min' => 'Phải có ít nhất 2 nguyên liệu.',
            'instructions.required' => 'Hướng dẫn nấu là bắt buộc.',
            'instructions.min' => 'Phải có ít nhất 2 bước hướng dẫn.',
            'categoryIds.required' => 'Danh mục là bắt buộc.',
            'categoryIds.min' => 'Phải chọn ít nhất 1 danh mục.',
        ];
    }

    public function render()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('livewire.recipes.recipe-form', [
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
}