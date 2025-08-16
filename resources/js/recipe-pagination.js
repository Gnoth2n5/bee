// Recipe Pagination Enhancement
document.addEventListener('DOMContentLoaded', function () {
    // Listen for Livewire events
    window.addEventListener('scroll-to-top', function () {
        // Smooth scroll to top
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Add loading states for pagination
    window.addEventListener('livewire:loading', function () {
        // Add loading class to pagination container
        const paginationContainer = document.querySelector('[wire\\:loading\\.class]');
        if (paginationContainer) {
            paginationContainer.classList.add('opacity-50');
        }
    });

    window.addEventListener('livewire:loaded', function () {
        // Remove loading class
        const paginationContainer = document.querySelector('[wire\\:loading\\.class]');
        if (paginationContainer) {
            paginationContainer.classList.remove('opacity-50');
        }
    });

    // Enhance pagination buttons with better UX
    document.addEventListener('click', function (e) {
        if (e.target.matches('[wire\\:click*="nextPage"], [wire\\:click*="previousPage"], [wire\\:click*="gotoPage"]')) {
            // Add loading state to clicked button
            e.target.disabled = true;
            e.target.classList.add('opacity-50');

            // Re-enable after a short delay
            setTimeout(() => {
                e.target.disabled = false;
                e.target.classList.remove('opacity-50');
            }, 1000);
        }
    });

    // Add smooth transitions for recipe cards
    const recipeCards = document.querySelectorAll('.recipe-card');
    recipeCards.forEach(card => {
        card.style.transition = 'all 0.3s ease-in-out';
    });

    // Add intersection observer for lazy loading (optional)
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, {
            threshold: 0.1
        });

        recipeCards.forEach(card => {
            observer.observe(card);
        });
    }
});

// Add CSS for smooth animations
const style = document.createElement('style');
style.textContent = `
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .recipe-card {
        transition: all 0.3s ease-in-out;
    }

    .recipe-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .pagination-loading {
        opacity: 0.5;
        pointer-events: none;
    }

    .smooth-transition {
        transition: all 0.3s ease-in-out;
    }
`;
document.head.appendChild(style);


