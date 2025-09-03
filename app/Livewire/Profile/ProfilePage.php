<?php

namespace App\Livewire\Profile;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Recipe;
use App\Models\Collection;
use App\Models\Favorite;
use App\Services\CollectionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProfilePage extends Component
{
    use WithFileUploads;

    public $user;
    public $profile;
    public $isEditing = false;
    public $avatar;

    // Profile fields
    public $name;
    public $email;
    public $province;
    public $bio;
    public $phone;
    public $address;
    public $city;
    public $country;
    public $cooking_experience;
    public $dietary_preferences = [];
    public $allergies = '';
    public $health_conditions = '';

    // Stats
    public $recipesCount = 0;
    public $collectionsCount = 0;
    public $favoritesCount = 0;

    // Tabs
    public $activeTab = 'recipes';

    // Collection creation
    public $showCreateModal = false;
    public $newName = '';
    public $newDescription = '';
    public $newIsPublic = false;
    public $newCoverImage;
    public $newCoverImagePreview;

    // Dietary options
    public $dietaryOptions = [
        'vegan' => 'Thuần chay',
        'vegetarian' => 'Ăn chay',
        'pescatarian' => 'Ăn cá',
        'gluten_free' => 'Không gluten',
        'dairy_free' => 'Không sữa',
        'keto' => 'Keto',
        'paleo' => 'Paleo',
        'low_carb' => 'Ít carb',
        'low_sodium' => 'Ít muối',
        'halal' => 'Halal',
        'kosher' => 'Kosher'
    ];

    // Experience options
    public $experienceOptions = [
        'beginner' => 'Mới bắt đầu',
        'intermediate' => 'Trung bình',
        'advanced' => 'Nâng cao'
    ];

    // Location properties
    public $userLatitude = null;
    public $userLongitude = null;
    public $nearestCity = null;

    public function mount()
    {
        $this->user = auth()->user();
        $this->profile = $this->user->profile;

        // Create profile if it doesn't exist
        if (!$this->profile) {
            $this->profile = UserProfile::create([
                'user_id' => $this->user->id,
                'country' => 'Vietnam',
                'cooking_experience' => 'beginner',
                'dietary_preferences' => [],
                'allergies' => [],
                'health_conditions' => []
            ]);
        }

        // Set active tab from session if available
        if (session('activeTab')) {
            $this->activeTab = session('activeTab');
            session()->forget('activeTab');
        }

        // Kiểm tra xem có thông tin vị trí từ session không
        if (session('user_location')) {
            $userLocation = session('user_location');
            $this->userLatitude = $userLocation['latitude'];
            $this->userLongitude = $userLocation['longitude'];
            $this->nearestCity = \App\Models\VietnamCity::where('code', $userLocation['nearest_city_code'])->first();

            Log::info('Loaded user location from session: ' . $userLocation['nearest_city_name'] . ' (' . $userLocation['nearest_city_code'] . ')');
        } else {
            // Tự động lấy vị trí khi component được load
            $this->dispatch('auto-get-location');
        }

        $this->loadProfileData();
        $this->loadStats();
    }

    public function loadProfileData()
    {
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->province = $this->user->province ?? '';
        $this->bio = $this->user->bio ?? '';

        if ($this->profile) {
            $this->phone = $this->profile->phone ?? '';
            $this->address = $this->profile->address ?? '';
            $this->city = $this->profile->city ?? '';
            $this->country = $this->profile->country ?? 'Vietnam';
            $this->cooking_experience = $this->profile->cooking_experience ?? 'beginner';
            $this->dietary_preferences = $this->profile->dietary_preferences ?? [];
            $this->allergies = is_array($this->profile->allergies) ? implode(', ', $this->profile->allergies) : ($this->profile->allergies ?? '');
            $this->health_conditions = is_array($this->profile->health_conditions) ? implode(', ', $this->profile->health_conditions) : ($this->profile->health_conditions ?? '');
        } else {
            // Default values if profile doesn't exist
            $this->phone = '';
            $this->address = '';
            $this->city = '';
            $this->country = 'Vietnam';
            $this->cooking_experience = 'beginner';
            $this->dietary_preferences = [];
            $this->allergies = '';
            $this->health_conditions = '';
        }
    }

    public function loadStats()
    {
        $this->recipesCount = Recipe::where('user_id', $this->user->id)->count();
        $this->collectionsCount = Collection::where('user_id', $this->user->id)->count();
        $this->favoritesCount = Favorite::where('user_id', $this->user->id)->count();
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        if ($this->isEditing) {
            // When entering edit mode, switch to settings tab
            $this->activeTab = 'settings';
        } else {
            $this->loadProfileData(); // Reset form
            $this->avatar = null; // Reset avatar
            // When exiting edit mode, switch back to recipes tab if currently on settings
            if ($this->activeTab === 'settings') {
                $this->activeTab = 'recipes';
            }
        }
    }

    public function saveProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'province' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'cooking_experience' => 'required|in:beginner,intermediate,advanced',
            'dietary_preferences' => 'array',
            'allergies' => 'nullable|string|max:500',
            'health_conditions' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'name.required' => 'Họ tên là bắt buộc.',
            'name.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'bio.max' => 'Giới thiệu bản thân không được vượt quá 500 ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự.',
            'city.max' => 'Thành phố không được vượt quá 100 ký tự.',
            'country.max' => 'Quốc gia không được vượt quá 100 ký tự.',
            'cooking_experience.required' => 'Kinh nghiệm nấu ăn là bắt buộc.',
            'cooking_experience.in' => 'Kinh nghiệm nấu ăn không hợp lệ.',
            'allergies.max' => 'Dị ứng thực phẩm không được vượt quá 500 ký tự.',
            'health_conditions.max' => 'Tình trạng sức khỏe không được vượt quá 500 ký tự.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'avatar.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ]);

        try {
            // Log trước khi update
            $oldUserData = [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'province' => $this->user->province,
                'bio' => $this->user->bio,
            ];

            $oldProfileData = [
                'city' => $this->profile->city ?? null,
                'country' => $this->profile->country ?? null,
            ];

            Log::info('📝 [ProfilePage] Saving profile changes', [
                'component' => 'ProfilePage',
                'user_id' => $this->user->id,
                'old_user_data' => $oldUserData,
                'new_user_data' => [
                    'name' => $this->name,
                    'email' => $this->email,
                    'province' => $this->province,
                    'bio' => $this->bio,
                ],
                'old_profile_data' => $oldProfileData,
                'new_profile_data' => [
                    'city' => $this->city,
                    'country' => $this->country,
                ],
                'location_changed' => ($oldUserData['province'] !== $this->province || ($oldProfileData['city'] ?? null) !== $this->city)
            ]);

            // Handle avatar upload first
            if ($this->avatar) {
                // Delete old avatar if exists (only if it's a local file)
                if ($this->user->hasLocalAvatar() && Storage::disk('public')->exists($this->user->avatar)) {
                    Storage::disk('public')->delete($this->user->avatar);
                }

                // Store new avatar
                $avatarPath = $this->avatar->store('avatars', 'public');

                // Update user with avatar
                $this->user->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'province' => $this->province,
                    'bio' => $this->bio,
                    'avatar' => $avatarPath,
                ]);
            } else {
                // Update user without avatar
                $this->user->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'province' => $this->province,
                    'bio' => $this->bio,
                ]);
            }

            // Convert allergies and health_conditions to array
            $allergiesArray = !empty($this->allergies) ? array_map('trim', explode(',', $this->allergies)) : [];
            $healthConditionsArray = !empty($this->health_conditions) ? array_map('trim', explode(',', $this->health_conditions)) : [];

            // Update or create profile
            if ($this->profile) {
                $this->profile->update([
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'city' => $this->city,
                    'country' => $this->country,
                    'cooking_experience' => $this->cooking_experience,
                    'dietary_preferences' => $this->dietary_preferences,
                    'allergies' => $allergiesArray,
                    'health_conditions' => $healthConditionsArray,
                ]);
            } else {
                // Create new profile if it doesn't exist
                $this->profile = UserProfile::create([
                    'user_id' => $this->user->id,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'city' => $this->city,
                    'country' => $this->country,
                    'cooking_experience' => $this->cooking_experience,
                    'dietary_preferences' => $this->dietary_preferences,
                    'allergies' => $allergiesArray,
                    'health_conditions' => $healthConditionsArray,
                ]);
            }

            // Nếu location thay đổi, update session
            if ($oldUserData['province'] !== $this->province || ($oldProfileData['city'] ?? null) !== $this->city) {
                $oldSession = session('user_location');

                // Tìm city trong database
                $cityInDb = \App\Models\VietnamCity::where('name', 'LIKE', '%' . $this->province . '%')
                    ->orWhere('name', 'LIKE', '%' . $this->city . '%')
                    ->first();

                if ($cityInDb) {
                    $newSessionData = [
                        'latitude' => $cityInDb->latitude,
                        'longitude' => $cityInDb->longitude,
                        'nearest_city_code' => $cityInDb->code,
                        'nearest_city_name' => $cityInDb->name,
                        'detected_at' => now()->toDateTimeString(),
                        'detection_method' => 'profile_save',
                        'component' => 'ProfilePage',
                        'updated_via_profile' => true
                    ];

                    session(['user_location' => $newSessionData]);

                    Log::info('💾 [ProfilePage] Session updated due to profile location change', [
                        'old_session' => $oldSession,
                        'new_session' => $newSessionData,
                        'matched_city' => $cityInDb->name,
                        'trigger' => 'profile_save'
                    ]);
                } else {
                    Log::warning('❌ [ProfilePage] Could not find city in database for profile update', [
                        'province' => $this->province,
                        'city' => $this->city,
                        'session_not_updated' => true
                    ]);
                }
            }

            // Refresh user data
            $this->user->refresh();
            $this->profile->refresh();

            $this->isEditing = false;
            $this->avatar = null; // Reset avatar after save
            // Switch back to recipes tab after successful save
            $this->activeTab = 'recipes';
            $this->dispatch('profile-updated');
            session()->flash('success', 'Hồ sơ đã được cập nhật thành công!');

            Log::info('✅ [ProfilePage] Profile saved successfully', [
                'user_id' => $this->user->id,
                'location_updated' => ($oldUserData['province'] !== $this->province || ($oldProfileData['city'] ?? null) !== $this->city)
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('❌ [ProfilePage] Profile save error', [
                'component' => 'ProfilePage',
                'user_id' => $this->user->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Có lỗi xảy ra khi cập nhật hồ sơ: ' . $e->getMessage());

            // Reset editing state on error
            $this->isEditing = true;
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;

        // Tự động vào chế độ edit khi click vào tab settings
        if ($tab === 'settings') {
            $this->isEditing = true;
        }
    }

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'avatar.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ]);
    }

    public function removeAvatar()
    {
        // Chỉ xóa file local nếu avatar là file local (không phải URL)
        if ($this->user->hasLocalAvatar() && Storage::disk('public')->exists($this->user->avatar)) {
            Storage::disk('public')->delete($this->user->avatar);
        }

        $this->user->update(['avatar' => null]);
        $this->user->refresh();
        $this->avatar = null;

        session()->flash('success', 'Ảnh đại diện đã được xóa!');
    }

    public function getRecipesProperty()
    {
        return Recipe::where('user_id', $this->user->id)
            ->with(['categories', 'tags', 'images'])
            ->latest()
            ->paginate(12);
    }

    public function getCollectionsProperty()
    {
        return Collection::where('user_id', $this->user->id)
            ->withCount('recipes')
            ->latest()
            ->paginate(12);
    }

    public function getFavoritesProperty()
    {
        return Favorite::where('user_id', $this->user->id)
            ->with(['recipe.categories', 'recipe.images', 'recipe.user.profile', 'recipe.favorites'])
            ->latest()
            ->paginate(12);
    }

    // Xác nhận xóa công thức yêu thích
    public function confirmRemoveFavorite($recipeSlug)
    {
        $this->removeFavorite($recipeSlug);
    }

    // Xóa công thức yêu thích qua Livewire
    public function removeFavorite($recipeSlug)
    {
        $user = $this->user;
        $recipe = \App\Models\Recipe::where('slug', $recipeSlug)->first();
        if ($recipe) {
            app(\App\Services\FavoriteService::class)->removeFavorite($recipe, $user);
            // Cập nhật lại số lượng yêu thích
            $this->favoritesCount = Favorite::where('user_id', $user->id)->count();
            session()->flash('success', 'Đã xóa công thức khỏi danh sách yêu thích!');
            $this->dispatch('flash-message', message: 'Đã xóa công thức khỏi danh sách yêu thích!', type: 'success');
        }
    }

    // Collection creation methods
    public function updatedNewCoverImage()
    {
        $this->validate([
            'newCoverImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'newCoverImage.image' => 'File phải là hình ảnh.',
            'newCoverImage.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'newCoverImage.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ]);

        if ($this->newCoverImage) {
            $this->newCoverImagePreview = $this->newCoverImage->temporaryUrl();
        }
    }

    public function createCollection()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newDescription' => 'nullable|string|max:1000',
            'newIsPublic' => 'boolean',
            'newCoverImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'newName.required' => 'Tên bộ sưu tập là bắt buộc.',
            'newName.max' => 'Tên bộ sưu tập không được vượt quá 255 ký tự.',
            'newDescription.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'newCoverImage.image' => 'File phải là hình ảnh.',
            'newCoverImage.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'newCoverImage.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ]);

        try {
            $collectionService = app(CollectionService::class);

            $collectionData = [
                'name' => $this->newName,
                'description' => $this->newDescription,
                'is_public' => $this->newIsPublic,
            ];

            if ($this->newCoverImage) {
                $collectionData['cover_image'] = $this->newCoverImage;
            }

            $collection = $collectionService->create($collectionData, $this->user);

            // Reset form
            $this->resetCollectionForm();

            // Cập nhật số lượng collections
            $this->collectionsCount = Collection::where('user_id', $this->user->id)->count();

            session()->flash('success', 'Đã tạo bộ sưu tập "' . $collection->name . '" thành công!');
            $this->dispatch('flash-message', message: 'Đã tạo bộ sưu tập "' . $collection->name . '" thành công!', type: 'success');
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi tạo bộ sưu tập: ' . $e->getMessage());
            $this->dispatch('flash-message', message: 'Có lỗi xảy ra khi tạo bộ sưu tập: ' . $e->getMessage(), type: 'error');
        }
    }

    public function resetCollectionForm()
    {
        $this->newName = '';
        $this->newDescription = '';
        $this->newIsPublic = false;
        $this->newCoverImage = null;
        $this->newCoverImagePreview = null;
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    // Location methods
    public function getUserLocationFromBrowser()
    {
        Log::info('getUserLocationFromBrowser called');
        $this->dispatch('get-user-location');
    }

    /**
     * Random chọn thành phố khi người dùng không cho phép vị trí
     */
    public function randomCity()
    {
        Log::info('randomCity called - user denied location permission');

        // Lấy danh sách tất cả thành phố có dữ liệu thời tiết
        $citiesWithWeather = \App\Models\WeatherData::select('city_code')
            ->distinct()
            ->whereNotNull('temperature')
            ->pluck('city_code')
            ->toArray();

        if (empty($citiesWithWeather)) {
            // Nếu không có thành phố nào có dữ liệu thời tiết, lấy tất cả thành phố
            $randomCity = \App\Models\VietnamCity::active()->inRandomOrder()->first();
        } else {
            // Random chọn từ các thành phố có dữ liệu thời tiết
            $randomCityCode = $citiesWithWeather[array_rand($citiesWithWeather)];
            $randomCity = \App\Models\VietnamCity::where('code', $randomCityCode)->first();
        }

        if ($randomCity) {
            Log::info('Random city selected: ' . $randomCity->name . ' (' . $randomCity->code . ')');
            $this->nearestCity = $randomCity;

            // Lưu vào session để dùng ở trang khác
            session([
                'user_location' => [
                    'latitude' => $randomCity->latitude,
                    'longitude' => $randomCity->longitude,
                    'nearest_city_code' => $randomCity->code,
                    'nearest_city_name' => $randomCity->name,
                    'is_random' => true
                ]
            ]);

            $this->dispatch('alert', message: 'Đã chọn ngẫu nhiên thành phố: ' . $randomCity->name);
        } else {
            Log::info('No random city found');
            $this->dispatch('alert', message: 'Không thể chọn thành phố ngẫu nhiên');
        }
    }

    public function setUserLocation($latitude, $longitude)
    {
        Log::info('🎯 [ProfilePage] setUserLocation called', [
            'component' => 'ProfilePage',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'user_id' => auth()->id() ?? 'guest',
            'user_email' => auth()->user()?->email ?? 'guest',
            'timestamp' => now()->toDateTimeString(),
            'session_id' => session()->getId()
        ]);

        $this->userLatitude = $latitude;
        $this->userLongitude = $longitude;

        // Tìm thành phố gần nhất
        $this->nearestCity = $this->findNearestCity($latitude, $longitude);

        if ($this->nearestCity) {
            Log::info('✅ [ProfilePage] Location detection successful', [
                'component' => 'ProfilePage',
                'detected_city' => $this->nearestCity->name,
                'detected_code' => $this->nearestCity->code,
                'city_coordinates' => [$this->nearestCity->latitude, $this->nearestCity->longitude],
                'input_coordinates' => [$latitude, $longitude],
                'will_update_profile' => true
            ]);

            // Tự động điền thông tin địa chỉ
            $oldCity = $this->city;
            $oldCountry = $this->country;

            $this->city = $this->nearestCity->name;
            $this->country = 'Vietnam';

            Log::info('📝 [ProfilePage] Profile fields updated', [
                'old_city' => $oldCity,
                'new_city' => $this->city,
                'old_country' => $oldCountry,
                'new_country' => $this->country
            ]);

            // Lưu vào session với logging
            $oldSession = session('user_location');
            $sessionData = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'nearest_city_code' => $this->nearestCity->code,
                'nearest_city_name' => $this->nearestCity->name,
                'detected_at' => now()->toDateTimeString(),
                'detection_method' => 'profile_page_gps',
                'component' => 'ProfilePage',
                'auto_filled_profile' => true
            ];

            session(['user_location' => $sessionData]);

            Log::info('💾 [ProfilePage] Session location updated', [
                'component' => 'ProfilePage',
                'old_session' => $oldSession,
                'new_session' => $sessionData,
                'profile_auto_filled' => true
            ]);
        } else {
            Log::warning('❌ [ProfilePage] No nearest city found', [
                'component' => 'ProfilePage',
                'latitude' => $latitude,
                'longitude' => $longitude,
                'total_cities_in_db' => \App\Models\VietnamCity::count(),
                'profile_update' => false
            ]);
        }
    }

    public function findNearestCity($latitude, $longitude)
    {
        $cities = \App\Models\VietnamCity::all();
        $nearestCity = null;
        $shortestDistance = PHP_FLOAT_MAX;

        foreach ($cities as $city) {
            $distance = $this->calculateDistance($latitude, $longitude, $city->latitude, $city->longitude);
            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestCity = $city;
            }
        }

        return $nearestCity;
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Bán kính trái đất tính bằng km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function render()
    {
        return view('livewire.profile.profile-page');
    }
}
