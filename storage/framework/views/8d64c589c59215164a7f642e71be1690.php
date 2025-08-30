<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <?php if (! empty(trim($__env->yieldContent('meta')))): ?>
            <?php echo $__env->yieldContent('meta'); ?>
        <?php endif; ?>

        <title><?php if (! empty(trim($__env->yieldContent('title')))): ?> <?php echo $__env->yieldContent('title'); ?> <?php else: ?> <?php echo e(config('app.name', 'Laravel')); ?> <?php endif; ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('layout.navigation', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-4024056885-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

            <!-- Flash Messages -->
            <?php if (isset($component)) { $__componentOriginalbb0843bd48625210e6e530f88101357e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbb0843bd48625210e6e530f88101357e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.flash-message','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flash-message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbb0843bd48625210e6e530f88101357e)): ?>
<?php $attributes = $__attributesOriginalbb0843bd48625210e6e530f88101357e; ?>
<?php unset($__attributesOriginalbb0843bd48625210e6e530f88101357e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbb0843bd48625210e6e530f88101357e)): ?>
<?php $component = $__componentOriginalbb0843bd48625210e6e530f88101357e; ?>
<?php unset($__componentOriginalbb0843bd48625210e6e530f88101357e); ?>
<?php endif; ?>

            <!-- Page Heading -->
            <?php if(isset($header)): ?>
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <?php echo e($header); ?>

                    </div>
                </header>
            <?php endif; ?>

            <!-- Page Content -->
            <main>
                <?php if (! empty(trim($__env->yieldContent('content')))): ?>
                    <?php echo $__env->yieldContent('content'); ?>
                <?php else: ?>
                    <?php echo e($slot); ?>

                <?php endif; ?>
            </main>
            <?php if (isset($component)) { $__componentOriginal8a8716efb3c62a45938aca52e78e0322 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8a8716efb3c62a45938aca52e78e0322 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8a8716efb3c62a45938aca52e78e0322)): ?>
<?php $attributes = $__attributesOriginal8a8716efb3c62a45938aca52e78e0322; ?>
<?php unset($__attributesOriginal8a8716efb3c62a45938aca52e78e0322); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8a8716efb3c62a45938aca52e78e0322)): ?>
<?php $component = $__componentOriginal8a8716efb3c62a45938aca52e78e0322; ?>
<?php unset($__componentOriginal8a8716efb3c62a45938aca52e78e0322); ?>
<?php endif; ?>

            <!-- OpenAI Quick Chat Button (show on all pages except AI chat) -->
            <?php if (! (request()->routeIs('openai.*'))): ?>
                <?php if (isset($component)) { $__componentOriginal7df88d8a2bb6a82e95b34d21b8ce23bf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7df88d8a2bb6a82e95b34d21b8ce23bf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.openai-quick-chat','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('openai-quick-chat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7df88d8a2bb6a82e95b34d21b8ce23bf)): ?>
<?php $attributes = $__attributesOriginal7df88d8a2bb6a82e95b34d21b8ce23bf; ?>
<?php unset($__attributesOriginal7df88d8a2bb6a82e95b34d21b8ce23bf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7df88d8a2bb6a82e95b34d21b8ce23bf)): ?>
<?php $component = $__componentOriginal7df88d8a2bb6a82e95b34d21b8ce23bf; ?>
<?php unset($__componentOriginal7df88d8a2bb6a82e95b34d21b8ce23bf); ?>
<?php endif; ?>
            <?php endif; ?>
        </div>

        <script>
        document.addEventListener('livewire:init', () => {

            // Handle flash message
            Livewire.on('flash-message', (event) => {
                const message = event.message;
                const type = event.type || 'success';
                
                // Tạo flash message element
                const flashElement = document.createElement('div');
                flashElement.className = 'fixed bottom-4 right-4 z-50';
                flashElement.innerHTML = `
                    <div class="bg-${type === 'success' ? 'green' : 'red'}-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-2 hover:text-${type === 'success' ? 'green' : 'red'}-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                
                document.body.appendChild(flashElement);
                
                // Tự động xóa sau 3 giây
                setTimeout(() => {
                    if (flashElement.parentElement) {
                        flashElement.remove();
                    }
                }, 3000);
            });

            // Handle show message (for post actions)
            Livewire.on('show-message', (data) => {
                const message = data.message;
                const type = data.type || 'success';
                
                // Tạo flash message element
                const flashElement = document.createElement('div');
                flashElement.className = 'fixed bottom-4 right-4 z-50';
                flashElement.innerHTML = `
                    <div class="bg-${type === 'success' ? 'green' : type === 'warning' ? 'yellow' : 'red'}-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-2 hover:text-${type === 'success' ? 'green' : type === 'warning' ? 'yellow' : 'red'}-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                
                document.body.appendChild(flashElement);
                
                // Tự động xóa sau 3 giây
                setTimeout(() => {
                    if (flashElement.parentElement) {
                        flashElement.remove();
                    }
                }, 3000);
            });

            // Handle copy to clipboard
            Livewire.on('copy-to-clipboard', (event) => {
                const url = event.url;
                navigator.clipboard.writeText(url).then(() => {
                    console.log('URL copied to clipboard:', url);
                }).catch(err => {
                    console.error('Failed to copy URL:', err);
                });
            });

        });
        </script>

		<?php echo $__env->yieldPushContent('scripts'); ?>

        <!-- Ingredient Substitute Modal -->
        <?php if (isset($component)) { $__componentOriginalb8aca06a8590d2bad66ab9deb276cede = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8aca06a8590d2bad66ab9deb276cede = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ingredient-substitute-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ingredient-substitute-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb8aca06a8590d2bad66ab9deb276cede)): ?>
<?php $attributes = $__attributesOriginalb8aca06a8590d2bad66ab9deb276cede; ?>
<?php unset($__attributesOriginalb8aca06a8590d2bad66ab9deb276cede); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb8aca06a8590d2bad66ab9deb276cede)): ?>
<?php $component = $__componentOriginalb8aca06a8590d2bad66ab9deb276cede; ?>
<?php unset($__componentOriginalb8aca06a8590d2bad66ab9deb276cede); ?>
<?php endif; ?>
        
        <!-- Ingredient Substitute JavaScript -->
        <script src="<?php echo e(asset('js/ingredient-substitute.js')); ?>"></script>

        <!-- Scroll to Top Button -->
        <button id="scrollToTop" class="fixed bottom-6 left-6 bg-white/80 hover:bg-white text-black rounded-full p-3 shadow-lg transition-all duration-300 opacity-0 invisible z-40 backdrop-blur-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
            </svg>
        </button>

        <!-- Scroll to Top JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const scrollToTopBtn = document.getElementById('scrollToTop');
                
                // Hiển thị nút khi scroll xuống 300px
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 300) {
                        scrollToTopBtn.classList.remove('opacity-0', 'invisible');
                        scrollToTopBtn.classList.add('opacity-100', 'visible');
                    } else {
                        scrollToTopBtn.classList.add('opacity-0', 'invisible');
                        scrollToTopBtn.classList.remove('opacity-100', 'visible');
                    }
                });
                
                // Scroll to top khi click
                scrollToTopBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });
        </script>
    </body>

</html>
<?php /**PATH D:\DuAn1\test\bee\resources\views/layouts/app.blade.php ENDPATH**/ ?>