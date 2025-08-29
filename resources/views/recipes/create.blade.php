@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tạo công thức mới</h1>
                        <p class="text-gray-600 mt-1">Chia sẻ công thức nấu ăn của bạn với cộng đồng</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('recipes.my') }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Quay lại
                        </a>
                    </div>
                </div>

                <!-- Create Form -->
                <form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data" id="recipeForm">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tên công thức <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}"
                                           placeholder="Ví dụ: Phở bò Hà Nội"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                           required>
                                    @error('title')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Mô tả <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="description" 
                                              name="description" 
                                              rows="3"
                                              placeholder="Mô tả ngắn gọn về công thức..."
                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="cooking_time" class="block text-sm font-medium text-gray-700 mb-2">
                                        Thời gian nấu (phút) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="cooking_time" 
                                           name="cooking_time" 
                                           value="{{ old('cooking_time') }}"
                                           min="1"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                           required>
                                    @error('cooking_time')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="preparation_time" class="block text-sm font-medium text-gray-700 mb-2">
                                        Thời gian chuẩn bị (phút)
                                    </label>
                                    <input type="number" 
                                           id="preparation_time" 
                                           name="preparation_time" 
                                           value="{{ old('preparation_time') }}"
                                           min="0"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                                    @error('preparation_time')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="servings" class="block text-sm font-medium text-gray-700 mb-2">
                                        Khẩu phần (người) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="servings" 
                                           name="servings" 
                                           value="{{ old('servings') }}"
                                           min="1"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                           required>
                                    @error('servings')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">
                                        Độ khó <span class="text-red-500">*</span>
                                    </label>
                                    <select id="difficulty" 
                                            name="difficulty" 
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                            required>
                                        <option value="">Chọn độ khó</option>
                                        <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Dễ</option>
                                        <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                        <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Khó</option>
                                    </select>
                                    @error('difficulty')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Categories and Tags -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Danh mục và Tags</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="categories" class="block text-sm font-medium text-gray-700 mb-2">
                                        Danh mục <span class="text-red-500">*</span>
                                    </label>
                                    <select id="categories" 
                                            name="categories[]" 
                                            multiple
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                            required>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categories')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tags
                                    </label>
                                    <select id="tags" 
                                            name="tags[]" 
                                            multiple
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                                        @foreach($tags as $tag)
                                            <option value="{{ $tag->id }}" 
                                                    {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                                #{{ $tag->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tags')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ingredients -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Nguyên liệu <span class="text-red-500">*</span></h3>
                            
                            <div id="ingredients-container">
                                <div class="ingredient-item grid grid-cols-1 md:grid-cols-4 gap-3 mb-3">
                                    <div class="md:col-span-2">
                                        <input type="text" 
                                               name="ingredients[0][name]" 
                                               placeholder="Tên nguyên liệu"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                               required>
                                    </div>
                                    <div>
                                        <input type="text" 
                                               name="ingredients[0][amount]" 
                                               placeholder="Số lượng"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                               required>
                                    </div>
                                    <div>
                                        <input type="text" 
                                               name="ingredients[0][unit]" 
                                               placeholder="Đơn vị"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" 
                                    onclick="addIngredient()"
                                    class="mt-3 px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                                + Thêm nguyên liệu
                            </button>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cách làm <span class="text-red-500">*</span></h3>
                            
                            <div id="instructions-container">
                                <div class="instruction-item grid grid-cols-1 md:grid-cols-6 gap-3 mb-3">
                                    <div class="md:col-span-1">
                                        <input type="number" 
                                               name="instructions[0][step]" 
                                               value="1"
                                               min="1"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                               required>
                                    </div>
                                    <div class="md:col-span-5">
                                        <textarea name="instructions[0][instruction]" 
                                                  placeholder="Mô tả bước làm..."
                                                  rows="2"
                                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                                  required></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" 
                                    onclick="addInstruction()"
                                    class="mt-3 px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                                + Thêm bước
                            </button>
                        </div>

                        <!-- Images -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Hình ảnh</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                                        Ảnh bìa
                                    </label>
                                    <input type="file" 
                                           id="cover_image" 
                                           name="cover_image"
                                           accept="image/*"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                                    @error('cover_image')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                                        Ảnh khác
                                    </label>
                                    <input type="file" 
                                           id="images" 
                                           name="images[]"
                                           accept="image/*"
                                           multiple
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                                    @error('images')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('recipes.my') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Hủy
                            </a>
                            <button type="submit" 
                                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Tạo công thức
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let ingredientCount = 1;
let instructionCount = 1;

function addIngredient() {
    const container = document.getElementById('ingredients-container');
    const newItem = document.createElement('div');
    newItem.className = 'ingredient-item grid grid-cols-1 md:grid-cols-4 gap-3 mb-3';
    newItem.innerHTML = `
        <div class="md:col-span-2">
            <input type="text" 
                   name="ingredients[${ingredientCount}][name]" 
                   placeholder="Tên nguyên liệu"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                   required>
        </div>
        <div>
            <input type="text" 
                   name="ingredients[${ingredientCount}][amount]" 
                   placeholder="Số lượng"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                   required>
        </div>
        <div class="flex space-x-2">
            <input type="text" 
                   name="ingredients[${ingredientCount}][unit]" 
                   placeholder="Đơn vị"
                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
            <button type="button" 
                    onclick="removeIngredient(this)"
                    class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                Xóa
            </button>
        </div>
    `;
    container.appendChild(newItem);
    ingredientCount++;
}

function removeIngredient(button) {
    button.closest('.ingredient-item').remove();
}

function addInstruction() {
    const container = document.getElementById('instructions-container');
    const newItem = document.createElement('div');
    newItem.className = 'instruction-item grid grid-cols-1 md:grid-cols-6 gap-3 mb-3';
    newItem.innerHTML = `
        <div class="md:col-span-1">
            <input type="number" 
                   name="instructions[${instructionCount}][step]" 
                   value="${instructionCount + 1}"
                   min="1"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                   required>
        </div>
        <div class="md:col-span-4">
            <textarea name="instructions[${instructionCount}][instruction]" 
                      placeholder="Mô tả bước làm..."
                      rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                      required></textarea>
        </div>
        <div class="md:col-span-1">
            <button type="button" 
                    onclick="removeInstruction(this)"
                    class="w-full px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                Xóa
            </button>
        </div>
    `;
    container.appendChild(newItem);
    instructionCount++;
}

function removeInstruction(button) {
    button.closest('.instruction-item').remove();
    // Cập nhật lại số thứ tự các bước
    const steps = document.querySelectorAll('input[name*="[step]"]');
    steps.forEach((step, index) => {
        step.value = index + 1;
    });
}
</script>
@endsection

