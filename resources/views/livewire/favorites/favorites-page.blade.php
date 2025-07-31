<div class="min-h-screen bg-gray-50" data-component="favorites-page">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Công thức yêu thích</h1>
                        <p class="mt-2 text-gray-600">Những công thức bạn đã lưu để tham khảo sau</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">
                            {{ $this->favorites->total() }} công thức
                        </span>

                        <a href="{{ route('recipes.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Khám phá thêm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($this->favorites->count() > 0)
            <!-- Recipe Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($this->favorites as $favorite)
                    <x-favorite-recipe-card :recipe="$favorite->recipe" />
                @endforeach
            </div>

            <!-- Pagination -->
            @if($this->favorites->hasPages())
                <div class="mt-8">
                    {{ $this->favorites->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có công thức yêu thích</h3>
                <p class="text-gray-500 mb-4">Khám phá và lưu công thức yêu thích để xem lại sau!</p>
                <a href="{{ route('recipes.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Khám phá công thức
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function confirmRemoveRecipe(recipeId, recipeTitle) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
    modal.innerHTML = `
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 mx-4 transform transition-all">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Xác nhận xóa</h3>
            <p class="text-gray-600 text-center mb-6">Xóa công thức <strong>"${recipeTitle}"</strong> khỏi danh sách yêu thích?</p>
            <div class="flex gap-3">
                <button id="cancelBtn" class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                    Hủy
                </button>
                <button id="confirmBtn" class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors">
                    Xóa
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    const cancelBtn = modal.querySelector('#cancelBtn');
    const confirmBtn = modal.querySelector('#confirmBtn');
    const removeModal = () => { modal.remove(); };
    modal.addEventListener('click', (e) => { if (e.target === modal) { removeModal(); } });
    const handleEsc = (e) => { if (e.key === 'Escape') { removeModal(); document.removeEventListener('keydown', handleEsc); } };
    document.addEventListener('keydown', handleEsc);
    cancelBtn.addEventListener('click', () => { removeModal(); document.removeEventListener('keydown', handleEsc); });
    confirmBtn.addEventListener('click', () => { 
        removeModal(); 
        document.removeEventListener('keydown', handleEsc); 
        
        // Debug log
        console.log('Confirm button clicked for recipe:', recipeId);
        
        // Tìm component FavoritesPage - cách trực tiếp
        const favoritesPageElement = document.querySelector('div[wire\\:id]');
        console.log('Favorites page element:', favoritesPageElement);
        
        let favoritesComponent = null;
        if (favoritesPageElement) {
            const componentId = favoritesPageElement.getAttribute('wire:id');
            favoritesComponent = Livewire.find(componentId);
            console.log('Found component:', componentId, favoritesComponent);
            
            // Debug: hiển thị tất cả properties của component
            if (favoritesComponent) {
                console.log('Component properties:', Object.getOwnPropertyNames(favoritesComponent));
                console.log('Component methods:', Object.getOwnPropertyNames(Object.getPrototypeOf(favoritesComponent)));
            }
        }
        
        if (favoritesComponent && typeof favoritesComponent.removeFavorite === 'function') {
            // Tìm recipe slug từ recipe ID
            const recipeElement = document.querySelector(`[data-recipe-id="${recipeId}"]`);
            if (recipeElement) {
                const recipeSlug = recipeElement.getAttribute('data-recipe-slug');
                console.log('Recipe slug found:', recipeSlug);
                if (recipeSlug) {
                    // Gọi trực tiếp method
                    try {
                        favoritesComponent.call('removeFavorite', recipeSlug);
                        console.log('Method called successfully');
                    } catch (error) {
                        console.error('Error calling method:', error);
                        // Fallback: sử dụng Livewire.dispatch
                        Livewire.dispatch('remove-favorite', { recipeSlug: recipeSlug });
                    }
                }
            } else {
                console.error('Recipe element not found for ID:', recipeId);
            }
        } else {
            console.error('FavoritesPage component not found or removeFavorite method not available');
            console.log('Component:', favoritesComponent);
            if (favoritesComponent) {
                console.log('Available methods:', Object.getOwnPropertyNames(favoritesComponent));
            }
        }
    });
}
</script> 