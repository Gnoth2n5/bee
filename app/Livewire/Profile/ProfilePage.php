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

    public function mount()
    {
        $this->user = Auth::user();
        $this->profile = $this->user->profile;
        $this->loadProfileData();
        $this->loadStats();
    }

    public function loadProfileData()
    {
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->bio = $this->user->bio ?? '';
        $this->phone = $this->profile->phone ?? '';
        $this->address = $this->profile->address ?? '';
        $this->city = $this->profile->city ?? '';
        $this->country = $this->profile->country ?? 'Vietnam';
        $this->cooking_experience = $this->profile->cooking_experience ?? 'beginner';
        $this->dietary_preferences = $this->profile->dietary_preferences ?? [];
        $this->allergies = is_array($this->profile->allergies) ? implode(', ', $this->profile->allergies) : ($this->profile->allergies ?? '');
        $this->health_conditions = is_array($this->profile->health_conditions) ? implode(', ', $this->profile->health_conditions) : ($this->profile->health_conditions ?? '');
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
        if (!$this->isEditing) {
            $this->loadProfileData(); // Reset form
            $this->avatar = null; // Reset avatar
        }
    }

    public function saveProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
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
            // Handle avatar upload first
            if ($this->avatar) {
                // Delete old avatar if exists
                if ($this->user->avatar && Storage::disk('public')->exists($this->user->avatar)) {
                    Storage::disk('public')->delete($this->user->avatar);
                }
                
                // Store new avatar
                $avatarPath = $this->avatar->store('avatars', 'public');
                
                // Update user with avatar
                $this->user->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'bio' => $this->bio,
                    'avatar' => $avatarPath,
                ]);
            } else {
                // Update user without avatar
                $this->user->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'bio' => $this->bio,
                ]);
            }

            // Convert allergies and health_conditions to array
            $allergiesArray = !empty($this->allergies) ? array_map('trim', explode(',', $this->allergies)) : [];
            $healthConditionsArray = !empty($this->health_conditions) ? array_map('trim', explode(',', $this->health_conditions)) : [];

            // Update profile
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

            // Refresh user data
            $this->user->refresh();
            $this->profile->refresh();

            $this->isEditing = false;
            $this->avatar = null; // Reset avatar after save
            $this->dispatch('profile-updated');
            session()->flash('success', 'Hồ sơ đã được cập nhật thành công!');

        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi cập nhật hồ sơ: ' . $e->getMessage());
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
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
        if ($this->user->avatar && Storage::disk('public')->exists($this->user->avatar)) {
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
            ->with(['recipe.categories', 'recipe.images', 'recipe.user.profile'])
            ->latest()
            ->paginate(12);
    }

    // Xác nhận xóa công thức yêu thích
    public function confirmRemoveFavorite($recipeSlug)
    {
        $this->dispatch('confirm-remove-favorite', recipeSlug: $recipeSlug, componentId: $this->getId());
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

    public function render()
    {
        return view('livewire.profile.profile-page');
    }
} 