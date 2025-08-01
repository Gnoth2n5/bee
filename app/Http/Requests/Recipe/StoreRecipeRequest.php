<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Recipe::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'summary' => ['required', 'string', 'max:500'],
            'cooking_time' => ['required', 'integer', 'min:5', 'max:1440'], // max 24 hours
            'preparation_time' => ['required', 'integer', 'min:0', 'max:1440'],
            'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'servings' => ['required', 'integer', 'min:1', 'max:50'],
            'calories_per_serving' => ['nullable', 'integer', 'min:0', 'max:5000'],
            'ingredients' => ['required', 'array', 'min:2'],
            'ingredients.*.name' => ['required', 'string', 'max:255'],
            'ingredients.*.amount' => ['required', 'string', 'max:50'],
            'ingredients.*.unit' => ['required', 'string', 'max:50'],
            'instructions' => ['required', 'array', 'min:2'],
            'instructions.*.step' => ['required', 'integer', 'min:1'],
            'instructions.*.instruction' => ['required', 'string', 'min:5', 'max:1000'],
            'tips' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['exists:categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:tags,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề công thức là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.required' => 'Mô tả công thức là bắt buộc.',
            'description.min' => 'Mô tả phải có ít nhất 10 ký tự.',
            'summary.required' => 'Tóm tắt công thức là bắt buộc.',
            'summary.max' => 'Tóm tắt không được vượt quá 500 ký tự.',
            'cooking_time.required' => 'Thời gian nấu là bắt buộc.',
            'cooking_time.min' => 'Thời gian nấu phải ít nhất 1 phút.',
            'preparation_time.required' => 'Thời gian chuẩn bị là bắt buộc.',
            'difficulty.required' => 'Độ khó là bắt buộc.',
            'difficulty.in' => 'Độ khó phải là: dễ, trung bình, hoặc khó.',
            'servings.required' => 'Số khẩu phần là bắt buộc.',
            'servings.min' => 'Số khẩu phần phải ít nhất 1.',
            'ingredients.required' => 'Danh sách nguyên liệu là bắt buộc.',
            'ingredients.min' => 'Phải có ít nhất 2 nguyên liệu.',
            'ingredients.*.name.required' => 'Tên nguyên liệu là bắt buộc.',
            'ingredients.*.amount.required' => 'Số lượng nguyên liệu là bắt buộc.',
            'ingredients.*.unit.required' => 'Đơn vị nguyên liệu là bắt buộc.',
            'instructions.required' => 'Hướng dẫn nấu là bắt buộc.',
            'instructions.min' => 'Phải có ít nhất 2 bước hướng dẫn.',
            'instructions.*.instruction.required' => 'Nội dung hướng dẫn là bắt buộc.',
            'instructions.*.instruction.min' => 'Hướng dẫn phải có ít nhất 5 ký tự.',
            'category_ids.required' => 'Danh mục là bắt buộc.',
            'category_ids.min' => 'Phải chọn ít nhất 1 danh mục.',
            'featured_image.image' => 'File phải là hình ảnh.',
            'featured_image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'featured_image.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            'video_url.url' => 'URL video không hợp lệ.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'tiêu đề',
            'description' => 'mô tả',
            'summary' => 'tóm tắt',
            'cooking_time' => 'thời gian nấu',
            'preparation_time' => 'thời gian chuẩn bị',
            'difficulty' => 'độ khó',
            'servings' => 'số khẩu phần',
            'calories_per_serving' => 'calo mỗi khẩu phần',
            'ingredients' => 'nguyên liệu',
            'instructions' => 'hướng dẫn',
            'tips' => 'mẹo',
            'notes' => 'ghi chú',
            'featured_image' => 'hình ảnh chính',
            'video_url' => 'URL video',
            'category_ids' => 'danh mục',
            'tag_ids' => 'tags',
        ];
    }
}