<div class="min-h-screen bg-gray-50">
    <!-- Profile Info Section -->
    <div class="relative px-4 sm:px-6 lg:px-8 pt-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header Component -->
                <x-profile.header 
                    :user="$user" 
                    :profile="$profile" 
                    :isEditing="$isEditing" 
                    :avatar="$avatar" 
                    :experienceOptions="$experienceOptions"
                    :nearestCity="$nearestCity"
                />

                <!-- Stats Component -->
                <x-profile.stats 
                    :recipesCount="$recipesCount" 
                    :collectionsCount="$collectionsCount" 
                    :favoritesCount="$favoritesCount" 
                />

                <!-- Tabs Component -->
                <x-profile.tabs 
                    :activeTab="$activeTab" 
                    :isEditing="$isEditing" 
                />

                <!-- Tab Content -->
                <div class="p-6">
                    @if($activeTab === 'recipes')
                        <x-profile.recipes-tab :recipes="$this->recipes" />
                    @endif

                    @if($activeTab === 'collections')
                        <x-profile.collections-tab 
                            :collections="$this->collections"
                            :showCreateModal="$showCreateModal"
                            :newName="$newName"
                            :newDescription="$newDescription"
                            :newIsPublic="$newIsPublic"
                            :newCoverImage="$newCoverImage"
                            :newCoverImagePreview="$newCoverImagePreview"
                        />
                    @endif

                    @if($activeTab === 'favorites')
                        <x-profile.favorites-tab :favorites="$this->favorites" />
                    @endif

                    @if($activeTab === 'settings')
                        <x-profile.settings-tab 
                            :name="$name"
                            :email="$email"
                            :province="$province"
                            :bio="$bio"
                            :phone="$phone"
                            :address="$address"
                            :city="$city"
                            :country="$country"
                            :cooking_experience="$cooking_experience"
                            :dietary_preferences="$dietary_preferences"
                            :allergies="$allergies"
                            :health_conditions="$health_conditions"
                            :experienceOptions="$experienceOptions"
                            :dietaryOptions="$dietaryOptions"
                        />
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <x-flash-message />
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('get-user-location', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    console.log('Location obtained:', latitude, longitude);
                    
                    // Gửi tọa độ về Livewire component
                    @this.setUserLocation(latitude, longitude);
                },
                (error) => {
                    console.error('Geolocation error:', error);
                    alert('Không thể lấy vị trí của bạn. Vui lòng kiểm tra quyền truy cập vị trí.');
                }
            );
        } else {
            alert('Trình duyệt của bạn không hỗ trợ định vị địa lý.');
        }
    });
});
</script> 