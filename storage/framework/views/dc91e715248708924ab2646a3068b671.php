<?php if(session()->has('message') || session()->has('success') || session()->has('error')): ?>
    <div 
        x-data="{ show: true }" 
        x-init="setTimeout(() => show = false, 4000)" 
        x-show="show"
        class="fixed bottom-4 right-4 z-50"
        x-transition
    >
        <?php if(session()->has('message') || session()->has('success')): ?>
            <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?php echo e(session('message') ?? session('success')); ?></span>
                <button @click="show = false" class="ml-2 hover:text-green-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        <?php endif; ?>
        <?php if(session()->has('error')): ?>
            <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?php echo e(session('error')); ?></span>
                <button @click="show = false" class="ml-2 hover:text-red-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?> <?php /**PATH D:\DuAn1\test\bee\resources\views/components/flash-message.blade.php ENDPATH**/ ?>