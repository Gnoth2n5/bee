/**
 * Weather Recipe Slideshow
 * Auto-sliding functionality for weather recipe section
 */

class WeatherSlideshow {
    constructor() {
        this.currentSlide = 0;
        this.slides = [];
        this.dots = [];
        this.slideInterval = null;
        this.autoplayDuration = 5000; // 5 seconds
        this.isPlaying = true;
        this.isHovered = false;

        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", () => this.setup());
        } else {
            this.setup();
        }

        // Listen for Livewire updates
        if (typeof Livewire !== "undefined") {
            Livewire.on("slide-changed", (event) => {
                this.currentSlide = event.currentSlide || 0;
                this.updateSlides();
            });
        }
    }

    setup() {
        this.findElements();
        if (this.slides.length > 1) {
            this.setupEventListeners();
            this.startAutoplay();
        }
    }

    findElements() {
        // Find all slides
        this.slides = Array.from(document.querySelectorAll('[id^="slide-"]'));

        // Find all dots
        this.dots = Array.from(
            document.querySelectorAll(
                '.dot-indicator, [wire\\:click*="goToSlide"]'
            )
        );

        // Get current slide from active state
        const activeSlide = document.querySelector(
            '[id^="slide-"].opacity-100'
        );
        if (activeSlide) {
            const slideId = activeSlide.id;
            const slideNumber = parseInt(slideId.replace("slide-", ""));
            if (!isNaN(slideNumber)) {
                this.currentSlide = slideNumber;
            }
        }

        console.log(
            `Weather Slideshow: Found ${this.slides.length} slides, current: ${this.currentSlide}`
        );
    }

    setupEventListeners() {
        const container = document.querySelector("#weather-section");
        if (!container) return;

        // Pause on hover
        container.addEventListener("mouseenter", () => {
            this.isHovered = true;
            this.pauseAutoplay();
        });

        // Resume on mouse leave
        container.addEventListener("mouseleave", () => {
            this.isHovered = false;
            if (this.isPlaying) {
                this.startAutoplay();
            }
        });

        // Pause on focus (for accessibility)
        container.addEventListener("focusin", () => {
            this.pauseAutoplay();
        });

        // Resume when focus leaves
        container.addEventListener("focusout", () => {
            if (this.isPlaying && !this.isHovered) {
                this.startAutoplay();
            }
        });

        // Listen for visibility changes
        document.addEventListener("visibilitychange", () => {
            if (document.hidden) {
                this.pauseAutoplay();
            } else if (this.isPlaying && !this.isHovered) {
                this.startAutoplay();
            }
        });
    }

    updateSlides() {
        this.slides.forEach((slide, index) => {
            if (index === this.currentSlide) {
                // Show current slide
                slide.classList.remove("opacity-0", "scale-95");
                slide.classList.add("opacity-100", "scale-100");
            } else {
                // Hide other slides
                slide.classList.remove("opacity-100", "scale-100");
                slide.classList.add("opacity-0", "scale-95");
            }
        });

        // Update dots
        this.updateDots();
    }

    updateDots() {
        this.dots.forEach((dot, index) => {
            if (index === this.currentSlide) {
                // Active dot
                dot.classList.remove("bg-gray-400", "dark:bg-gray-500");
                dot.classList.add(
                    "bg-gradient-to-r",
                    "from-orange-500",
                    "to-red-600",
                    "scale-125"
                );
            } else {
                // Inactive dot
                dot.classList.remove(
                    "bg-gradient-to-r",
                    "from-orange-500",
                    "to-red-600",
                    "scale-125"
                );
                dot.classList.add("bg-gray-400", "dark:bg-gray-500");
            }
        });
    }

    nextSlide() {
        if (this.slides.length === 0) return;

        this.currentSlide = (this.currentSlide + 1) % this.slides.length;
        this.updateSlides();

        // Also trigger Livewire update if available
        if (typeof Livewire !== "undefined") {
            try {
                // Find the Livewire component and call nextSlide
                const component = Livewire.find(
                    document.querySelector("[wire\\:id]")
                );
                if (component && typeof component.call === "function") {
                    component.call("goToSlide", this.currentSlide);
                }
            } catch (error) {
                console.log(
                    "Weather Slideshow: Could not sync with Livewire:",
                    error.message
                );
            }
        }
    }

    goToSlide(index) {
        if (index >= 0 && index < this.slides.length) {
            this.currentSlide = index;
            this.updateSlides();
        }
    }

    startAutoplay() {
        if (this.slides.length <= 1) return;

        this.pauseAutoplay(); // Clear any existing interval

        this.slideInterval = setInterval(() => {
            if (!this.isHovered && !document.hidden) {
                this.nextSlide();
            }
        }, this.autoplayDuration);

        console.log("Weather Slideshow: Autoplay started");
    }

    pauseAutoplay() {
        if (this.slideInterval) {
            clearInterval(this.slideInterval);
            this.slideInterval = null;
        }
    }

    destroy() {
        this.pauseAutoplay();
        this.isPlaying = false;
    }

    // Public methods for manual control
    play() {
        this.isPlaying = true;
        if (!this.isHovered) {
            this.startAutoplay();
        }
    }

    pause() {
        this.isPlaying = false;
        this.pauseAutoplay();
    }

    // Method to reinitialize after Livewire updates
    refresh() {
        this.pauseAutoplay();
        setTimeout(() => {
            this.findElements();
            if (this.slides.length > 1 && this.isPlaying) {
                this.startAutoplay();
            }
        }, 100);
    }
}

// Initialize slideshow
let weatherSlideshow = null;

// Initialize when DOM is ready
function initWeatherSlideshow() {
    if (weatherSlideshow) {
        weatherSlideshow.destroy();
    }
    weatherSlideshow = new WeatherSlideshow();
}

// Initialize immediately if DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initWeatherSlideshow);
} else {
    initWeatherSlideshow();
}

// Re-initialize after Livewire navigation
if (typeof Livewire !== "undefined") {
    // Livewire v3 events
    Livewire.on("weather-slideshow-refresh", () => {
        if (weatherSlideshow) {
            weatherSlideshow.refresh();
        }
    });

    // Re-initialize after component updates
    document.addEventListener("livewire:navigated", () => {
        setTimeout(initWeatherSlideshow, 100);
    });

    // Re-initialize after page load
    document.addEventListener("livewire:load", () => {
        setTimeout(initWeatherSlideshow, 100);
    });
}

// Export for global access
window.WeatherSlideshow = WeatherSlideshow;
window.weatherSlideshow = weatherSlideshow;

export default WeatherSlideshow;
