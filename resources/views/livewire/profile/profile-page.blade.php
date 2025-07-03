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
                        <x-profile.collections-tab :collections="$this->collections" />
                    @endif

                    @if($activeTab === 'favorites')
                        <x-profile.favorites-tab :favorites="$this->favorites" />
                    @endif

                    @if($activeTab === 'settings' && $isEditing)
                        <x-profile.settings-tab 
                            :name="$name"
                            :email="$email"
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