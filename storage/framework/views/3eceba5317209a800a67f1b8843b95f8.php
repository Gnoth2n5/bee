<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'recipes',
    'title' => 'Công thức mới nhất',
    'subtitle' => null,
    'viewMode' => 'grid',
    'hasActiveFilters' => false,
    'difficulty' => '',
    'cookingTime' => ''
]));

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

foreach (array_filter(([
    'recipes',
    'title' => 'Công thức mới nhất',
    'subtitle' => null,
    'viewMode' => 'grid',
    'hasActiveFilters' => false,
    'difficulty' => '',
    'cookingTime' => ''
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900"><?php echo e($title); ?></h2>
                <!--[if BLOCK]><![endif]--><?php if($subtitle): ?>
                    <p class="text-gray-600 mt-2"><?php echo e($subtitle); ?></p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-wrap gap-4">
                <!-- Sort Dropdown -->
                <select 
                    class="border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    wire:model.live="sort"
                >
                    <option value="latest">Mới nhất</option>
                    <option value="popular">Phổ biến</option>
                    <option value="rating">Đánh giá cao</option>
                    <option value="oldest">Cũ nhất</option>
                </select>

                <!-- Difficulty Filter -->
                <div class="relative">
                    <select 
                        class="form-select border border-gray-300 rounded-lg px-3 pr-10 py-2 bg-white focus:ring-2 focus:ring-orange-500 focus:border-transparent appearance-none"
                        wire:model.live="difficulty"
                    >
                        <option value="">Tất cả độ khó</option>
                        <option value="easy">Dễ</option>
                        <option value="medium">Trung bình</option>
                        <option value="hard">Khó</option>
                    </select>
                    
                </div>

                <!-- Cooking Time Filter -->
                <select 
                    class="border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    wire:model.live="cookingTime"
                >
                    <option value="">Tất cả thời gian</option>
                    <option value="quick">Nhanh (< 30 phút)</option>
                    <option value="medium">Trung bình (30-60 phút)</option>
                    <option value="long">Lâu (> 60 phút)</option>
                </select>

                <!-- View Toggle -->
                <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                    <button 
                        class="px-3 py-2 <?php echo e($viewMode === 'grid' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'); ?> transition-colors"
                        wire:click="$set('viewMode', 'grid')"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button 
                        class="px-3 py-2 <?php echo e($viewMode === 'list' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'); ?> transition-colors"
                        wire:click="$set('viewMode', 'list')"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Active Filters -->
        <!--[if BLOCK]><![endif]--><?php if($hasActiveFilters): ?>
            <div class="mb-6 flex flex-wrap gap-2">
                <span class="text-sm text-gray-600">Bộ lọc:</span>
                <!--[if BLOCK]><![endif]--><?php if($difficulty): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 text-orange-700">
                        Độ khó: <?php echo e(ucfirst($difficulty)); ?>

                        <button wire:click="$set('difficulty', '')" class="ml-2 hover:text-orange-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><?php if($cookingTime): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 text-orange-700">
                        Thời gian: <?php echo e($cookingTime); ?>

                        <button wire:click="$set('cookingTime', '')" class="ml-2 hover:text-orange-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <button 
                    wire:click="clearFilters"
                    class="text-sm text-orange-600 hover:text-orange-700 underline"
                >
                    Xóa tất cả
                </button>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- Recipe Cards Grid -->
        <!--[if BLOCK]><![endif]--><?php if($recipes->count() > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginal9ff93d1775529871158937c5c88e7f2b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ff93d1775529871158937c5c88e7f2b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.recipe-card','data' => ['recipe' => $recipe]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('recipe-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['recipe' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($recipe)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ff93d1775529871158937c5c88e7f2b)): ?>
<?php $attributes = $__attributesOriginal9ff93d1775529871158937c5c88e7f2b; ?>
<?php unset($__attributesOriginal9ff93d1775529871158937c5c88e7f2b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ff93d1775529871158937c5c88e7f2b)): ?>
<?php $component = $__componentOriginal9ff93d1775529871158937c5c88e7f2b; ?>
<?php unset($__componentOriginal9ff93d1775529871158937c5c88e7f2b); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!-- Pagination -->
            <div class="mt-8" wire:loading.class="opacity-50">
                <?php if (isset($component)) { $__componentOriginala16d4863d0b2e04e2725366f368adf16 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala16d4863d0b2e04e2725366f368adf16 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.basic-pagination','data' => ['paginator' => $recipes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('basic-pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['paginator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($recipes)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala16d4863d0b2e04e2725366f368adf16)): ?>
<?php $attributes = $__attributesOriginala16d4863d0b2e04e2725366f368adf16; ?>
<?php unset($__attributesOriginala16d4863d0b2e04e2725366f368adf16); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala16d4863d0b2e04e2725366f368adf16)): ?>
<?php $component = $__componentOriginala16d4863d0b2e04e2725366f368adf16; ?>
<?php unset($__componentOriginala16d4863d0b2e04e2725366f368adf16); ?>
<?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Không tìm thấy công thức</h3>
                <p class="mt-1 text-sm text-gray-500">
                    <!--[if BLOCK]><![endif]--><?php if($hasActiveFilters): ?>
                        Thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác.
                    <?php else: ?>
                        Chưa có công thức nào được đăng tải.
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </p>
                <!--[if BLOCK]><![endif]--><?php if($hasActiveFilters): ?>
                    <div class="mt-6">
                        <button 
                            wire:click="clearFilters"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700"
                        >
                            Xóa bộ lọc
                        </button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</section> <?php /**PATH D:\DuAn1\test\bee\resources\views/components/recipe-grid.blade.php ENDPATH**/ ?>