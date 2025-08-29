<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['recipe']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['recipe']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="recipe-card bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group" data-recipe-slug="<?php echo e($recipe->slug); ?>" data-recipe-id="<?php echo e($recipe->id); ?>">
    <div class="aspect-video bg-gray-200 relative overflow-hidden">
        <!--[if BLOCK]><![endif]--><?php if($recipe->featured_image): ?>
            <img src="<?php echo e(Storage::url($recipe->featured_image)); ?>" 
                 alt="<?php echo e($recipe->title); ?>" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-orange-100 to-red-100">
                <svg class="w-12 h-12 text-orange-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h6" />
                    <circle cx="12" cy="12" r="10" />
                </svg>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        
        <!-- Toggle Favorite Button -->
        <div class="absolute top-3 right-3">
            <button 
                class="w-10 h-10 flex items-center justify-center bg-white rounded-full shadow-sm hover:bg-gray-50 transition-colors p-0"
                wire:click="confirmToggleFavorite(<?php echo e($recipe->id); ?>)"
                wire:loading.attr="disabled"
                aria-label="Yêu thích"
            >
                <!--[if BLOCK]><![endif]--><?php if(\Illuminate\Support\Facades\Auth::check() && $recipe->isFavoritedBy(\Illuminate\Support\Facades\Auth::user())): ?>
                    <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                <?php else: ?>
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </button>
        </div>

        <!-- Difficulty Badge -->
        <div class="absolute top-2 left-2">
            <?php
                $difficultyColors = [
                    'easy' => 'bg-green-500',
                    'medium' => 'bg-yellow-500', 
                    'hard' => 'bg-red-500'
                ];
                $difficultyText = [
                    'easy' => 'Dễ',
                    'medium' => 'Trung bình',
                    'hard' => 'Khó'
                ];
            ?>
            <span class="px-3 py-1 text-xs font-medium text-white rounded-full <?php echo e($difficultyColors[$recipe->difficulty]); ?>">
                <?php echo e($difficultyText[$recipe->difficulty]); ?>

            </span>
        </div>
    </div>

    <div class="p-4">
        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-orange-600 transition-colors">
            <a href="<?php echo e(route('recipes.show', $recipe)); ?>"><?php echo e($recipe->title); ?></a>
        </h3>

        <p class="text-sm text-gray-600 mb-3 line-clamp-2"><?php echo e($recipe->summary); ?></p>

        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-2">
                <!--[if BLOCK]><![endif]--><?php if($recipe->user->hasAvatar()): ?>
                    <img src="<?php echo e($recipe->user->getAvatarUrl()); ?>" 
                         alt="<?php echo e($recipe->user->name); ?>" 
                         class="w-6 h-6 rounded-full object-cover" />
                <?php else: ?>
                    <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center">
                        <span class="text-xs font-medium text-orange-600">
                            <?php echo e(strtoupper(substr($recipe->user->name, 0, 1))); ?>

                        </span>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <span class="text-sm text-gray-600"><?php echo e($recipe->user->name); ?></span>
            </div>

            <div class="flex items-center space-x-1">
                <svg class="h-4 w-4 text-yellow-400" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="text-sm text-gray-600"><?php echo e(number_format($recipe->average_rating, 1)); ?></span>
                <span class="text-xs text-gray-400">(<?php echo e($recipe->rating_count); ?>)</span>
            </div>
        </div>

        <div class="flex items-center justify-between text-sm text-gray-500">
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?php echo e($recipe->cooking_time); ?> phút
            </span>
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <?php echo e($recipe->servings); ?> người
            </span>
        </div>

        <!-- Categories -->
        <!--[if BLOCK]><![endif]--><?php if($recipe->categories->count() > 0): ?>
            <div class="mt-3 flex flex-wrap gap-1">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recipe->categories->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">
                        <?php echo e($category->name); ?>

                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($recipe->categories->count() > 2): ?>
                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                        +<?php echo e($recipe->categories->count() - 2); ?>

                    </span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div> <?php /**PATH D:\DuAn1\test\bee\resources\views/components/recipe-card.blade.php ENDPATH**/ ?>