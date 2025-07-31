@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa bộ sưu tập</h1>
            <p class="text-gray-600 mt-2">Cập nhật thông tin bộ sưu tập "{{ $collection->name }}"</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('collections.update', $collection) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên bộ sưu tập <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name"
                        name="name"
                        value="{{ old('name', $collection->name) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror"
                        placeholder="Nhập tên bộ sưu tập"
                        required
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Mô tả (tùy chọn)
                    </label>
                    <textarea 
                        id="description"
                        name="description"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('description') border-red-500 @enderror"
                        placeholder="Mô tả về bộ sưu tập của bạn"
                    >{{ old('description', $collection->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Hình ảnh bìa (tùy chọn)
                    </label>
                    
                    @if($collection->cover_image)
                        <div class="mb-3">
                            <img src="{{ Storage::url($collection->cover_image) }}" 
                                 alt="{{ $collection->name }}" 
                                 class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                        </div>
                    @endif
                    
                    <input 
                        type="file" 
                        id="cover_image"
                        name="cover_image"
                        accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('cover_image') border-red-500 @enderror"
                    >
                    <p class="text-xs text-gray-500 mt-1">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB</p>
                    @error('cover_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_public"
                            value="1"
                            {{ old('is_public', $collection->is_public) ? 'checked' : '' }}
                            class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"
                        >
                        <span class="ml-2 text-sm text-gray-700">Công khai bộ sưu tập</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Bộ sưu tập công khai sẽ hiển thị cho tất cả mọi người</p>
                </div>

                <div class="flex gap-4">
                    <button 
                        type="submit"
                        class="flex-1 bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
                    >
                        Cập nhật bộ sưu tập
                    </button>
                    <a href="{{ route('collections.show', $collection) }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-semibold transition-colors">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 