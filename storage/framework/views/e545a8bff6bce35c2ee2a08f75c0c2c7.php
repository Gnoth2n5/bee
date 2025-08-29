<!--[if BLOCK]><![endif]--><?php if($paginator->hasPages()): ?>
    <div class="flex items-center justify-center space-x-2">
        
        <!--[if BLOCK]><![endif]--><?php if($paginator->onFirstPage()): ?>
            <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded cursor-not-allowed">
                Trước
            </span>
        <?php else: ?>
            <button wire:click="previousPage" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">
                Trước
            </button>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <span class="px-3 py-2 text-sm text-gray-700">
            Trang <?php echo e($paginator->currentPage()); ?> / <?php echo e($paginator->lastPage()); ?>

        </span>

        
        <!--[if BLOCK]><![endif]--><?php if($paginator->hasMorePages()): ?>
            <button wire:click="nextPage" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">
                Sau
            </button>
        <?php else: ?>
            <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded cursor-not-allowed">
                Sau
            </span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->
<?php /**PATH D:\DuAn1\test\bee\resources\views/components/basic-pagination.blade.php ENDPATH**/ ?>