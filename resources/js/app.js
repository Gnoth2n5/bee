import "./bootstrap";

let createIcons; // Khai báo biến toàn cục cho hàm
let lucideIcons; // Khai báo biến toàn cục cho đối tượng icons

async function initializeLucideIcons() {
    try {
        // Dynamic import thư viện Lucide và lấy cả createIcons và icons
        const { createIcons: lucideCreateIcons, icons } = await import(
            "lucide"
        );

        // Kiểm tra và gán hàm createIcons và icons vào biến toàn cục
        if (typeof lucideCreateIcons !== "function") {
            throw new Error("createIcons is not a function");
        }
        createIcons = lucideCreateIcons; // Gán hàm vào biến toàn cục
        lucideIcons = icons; // Lưu đối tượng icons vào biến toàn cục

        // Đợi DOM sẵn sàng trước khi khởi tạo icon
        document.addEventListener("DOMContentLoaded", () => {
            createIcons({ icons: lucideIcons }); // Truyền đối tượng icons vào createIcons
            console.log("✅ Lucide icons loaded successfully");
        });
    } catch (error) {
        console.error("❌ Lucide icons failed to load:", error);
    }
}

// Gọi hàm khởi tạo Lucide
initializeLucideIcons();
import "flowbite";
import Alpine from "alpinejs";
import Swal from "sweetalert2";
import "./recipe-pagination";
import "./vietqr-payment";
import "./weather-slideshow";

window.Alpine = Alpine;
window.Swal = Swal;
Alpine.start();

// Hero Slider Functions
function initializeHeroSlider() {
    const heroSlides = [
        {
            id: 0,
            title: "Khám phá thế giới",
            subtitle: "Ẩm thực tuyệt vời",
            description:
                "Hàng nghìn công thức nấu ăn ngon từ khắp thế giới, được chia sẻ bởi những người yêu thích ẩm thực như bạn.",
            buttonText: "Bắt đầu nấu ăn",
            buttonLink: "/recipes",
            accent: "from-red-500 to-pink-600",
            stats: { recipes: "1K+", community: "500" },
        },
        {
            id: 1,
            title: "Chia sẻ những",
            subtitle: "Kiệt tác ẩm thực",
            description:
                "Tham gia cộng đồng sôi động và chia sẻ những công thức yêu thích với các tín đồ ẩm thực trên toàn thế giới.",
            buttonText: "Khám phá",
            buttonLink: "/register",
            accent: "from-orange-500 to-red-600",
            stats: { recipes: "1K+", community: "500" },
        },
        {
            id: 2,
            title: "Nấu ăn như một",
            subtitle: "Chuyên gia",
            description:
                "Nắm vững nghệ thuật nấu ăn với hướng dẫn từng bước, mẹo hay từ chuyên gia và các buổi nấu ăn tương tác.",
            buttonText: "Tìm hiểu thêm",
            buttonLink: "/about",
            accent: "from-yellow-500 to-orange-600",
            stats: { recipes: "1K+", community: "500" },
        },
    ];

    let currentSlide = 0;
    let isAnimating = false;
    let slideInterval;

    function updateContent(slideData) {
        const titleLine1 = document.getElementById("hero-title-line1");
        const titleLine2 = document.getElementById("hero-title-line2");
        const description = document.getElementById("hero-description");
        const btnText = document.getElementById("hero-btn-text");
        const primaryBtn = document.getElementById("hero-primary-btn");
        const statDot = document.getElementById("hero-stat-dot");
        const statRecipes = document.getElementById("hero-stat-recipes");
        const statCommunity = document.getElementById("hero-stat-community");
        const badge = document.getElementById("hero-badge");
        const featuredIcon = document.getElementById("featured-icon");
        const featuredIconMobile = document.getElementById(
            "featured-icon-mobile"
        );

        if (titleLine1) titleLine1.textContent = slideData.title;
        if (titleLine2) {
            titleLine2.textContent = slideData.subtitle;
            titleLine2.className = `block bg-gradient-to-r ${slideData.accent} bg-clip-text text-transparent animate-pulse`;
        }
        if (description) description.textContent = slideData.description;
        if (btnText) btnText.textContent = slideData.buttonText;
        if (primaryBtn) {
            primaryBtn.href = slideData.buttonLink;
            primaryBtn.className = `group inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r ${slideData.accent} text-white font-bold text-lg rounded-full hover:shadow-2xl transform hover:scale-105 transition-all duration-300 hover:-translate-y-1`;
        }
        if (statDot) {
            statDot.className = `w-3 h-3 rounded-full bg-gradient-to-r ${slideData.accent} animate-pulse`;
        }
        if (statRecipes)
            statRecipes.textContent = `${slideData.stats.recipes} Công thức`;
        if (statCommunity)
            statCommunity.textContent = `${slideData.stats.community} Cộng đồng`;
        if (badge) {
            badge.className = `inline-flex items-center px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 mb-6 hero-animate`;
        }
        if (featuredIcon) {
            featuredIcon.className = `w-12 h-12 rounded-full bg-gradient-to-r ${slideData.accent} flex items-center justify-center group-hover:scale-110 transition-transform duration-300`;
        }
        if (featuredIconMobile) {
            featuredIconMobile.className = `w-12 h-12 rounded-full bg-gradient-to-r ${slideData.accent} flex items-center justify-center group-hover:scale-110 transition-transform duration-300`;
        }
        // Re-initialize Lucide icons if available
        if (createIcons) createIcons({ icons: lucideIcons });
    }

    function showSlide(slideIndex, withAnimation = true) {
        if (isAnimating) return;
        if (slideIndex === currentSlide) return;

        isAnimating = true;

        // Update background
        const backgrounds = document.querySelectorAll(".hero-background");
        backgrounds.forEach((bg) => bg.classList.remove("active"));

        const currentBackground = document.querySelector(
            `[data-slide="${slideIndex}"]`
        );
        if (currentBackground) {
            currentBackground.classList.add("active");
        }

        // Update dots
        const dots = document.querySelectorAll(".hero-dot");
        dots.forEach((dot) => dot.classList.remove("active"));
        const currentDot = document.querySelector(
            `[data-slide="${slideIndex}"].hero-dot`
        );
        if (currentDot) {
            currentDot.classList.add("active");
        }

        // Animate content if needed (excluding search)
        if (withAnimation) {
            const animatedElements = document.querySelectorAll(
                ".hero-animate:not(.hero-search-wrapper):not(.hero-search-wrapper *)"
            );
            animatedElements.forEach((el) => {
                el.classList.remove("slide-animation-in");
                el.classList.add("slide-animation-out");
            });

            setTimeout(() => {
                updateContent(heroSlides[slideIndex]);
                animatedElements.forEach((el) => {
                    el.classList.remove("slide-animation-out");
                    el.classList.add("slide-animation-in");
                });
                if (createIcons) createIcons({ icons: lucideIcons });
            }, 350);

            setTimeout(() => {
                animatedElements.forEach((el) => {
                    el.classList.remove("slide-animation-in");
                });
                isAnimating = false;
            }, 700);
        } else {
            updateContent(heroSlides[slideIndex]);
            isAnimating = false;
        }

        currentSlide = slideIndex;
    }

    function nextSlide() {
        const next = (currentSlide + 1) % heroSlides.length;
        showSlide(next);
        resetInterval();
    }

    function prevSlide() {
        const prev = (currentSlide - 1 + heroSlides.length) % heroSlides.length;
        showSlide(prev);
        resetInterval();
    }

    function resetInterval() {
        clearInterval(slideInterval);
        startInterval();
    }

    function startInterval() {
        slideInterval = setInterval(nextSlide, 5000);
    }

    // Initialize
    showSlide(0, false);
    startInterval();

    // Event listeners
    const prevBtn = document.getElementById("hero-prev-btn");
    const nextBtn = document.getElementById("hero-next-btn");
    const mobilePrevBtn = document.getElementById("hero-mobile-prev");
    const mobileNextBtn = document.getElementById("hero-mobile-next");

    if (prevBtn) prevBtn.addEventListener("click", prevSlide);
    if (nextBtn) nextBtn.addEventListener("click", nextSlide);
    if (mobilePrevBtn) mobilePrevBtn.addEventListener("click", prevSlide);
    if (mobileNextBtn) mobileNextBtn.addEventListener("click", nextSlide);

    const dots = document.querySelectorAll(".hero-dot");
    dots.forEach((dot) => {
        dot.addEventListener("click", () => {
            const slideIndex = parseInt(dot.getAttribute("data-slide"));
            showSlide(slideIndex);
            resetInterval();
        });
    });

    // Pause on hover
    const heroSection = document.getElementById("hero-section");
    if (heroSection) {
        heroSection.addEventListener("mouseenter", () =>
            clearInterval(slideInterval)
        );
        heroSection.addEventListener("mouseleave", startInterval);
    }
}

// Export Hero Slider function
window.initializeHeroSlider = initializeHeroSlider;

// Initialize hero slider when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    const heroSection = document.getElementById("hero-section");
    if (heroSection) {
        setTimeout(() => {
            try {
                initializeHeroSlider();
            } catch (error) {
                console.error("❌ Error in initializeHeroSlider:", error);
            }
        }, 100);
    }
});

// Location Management Utilities
window.LocationManager = {
    // Lưu vị trí vào localStorage
    saveLocation: function (latitude, longitude) {
        const locationData = {
            latitude: latitude,
            longitude: longitude,
            timestamp: new Date().getTime(),
        };
        localStorage.setItem("user_location", JSON.stringify(locationData));
        console.log("Location saved to localStorage:", locationData);
        return locationData;
    },

    // Lấy vị trí từ localStorage
    getLocation: function () {
        const savedLocation = localStorage.getItem("user_location");
        if (savedLocation) {
            try {
                const locationData = JSON.parse(savedLocation);
                const now = new Date().getTime();
                const oneHour = 60 * 60 * 1000; // 1 giờ

                // Kiểm tra xem vị trí có còn mới không (trong vòng 1 giờ)
                if (now - locationData.timestamp < oneHour) {
                    console.log(
                        "Found valid location in localStorage:",
                        locationData
                    );
                    return locationData;
                } else {
                    console.log(
                        "Saved location is too old, removing from localStorage"
                    );
                    this.removeLocation();
                    return null;
                }
            } catch (error) {
                console.error("Error parsing saved location:", error);
                this.removeLocation();
                return null;
            }
        }
        return null;
    },

    // Xóa vị trí khỏi localStorage
    removeLocation: function () {
        localStorage.removeItem("user_location");
        console.log("Location removed from localStorage");
    },

    // Kiểm tra xem có vị trí hợp lệ không
    hasValidLocation: function () {
        return this.getLocation() !== null;
    },

    // Lấy vị trí từ trình duyệt và lưu vào localStorage
    getCurrentLocation: function () {
        return new Promise((resolve, reject) => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        // Lưu vào localStorage
                        const locationData = this.saveLocation(
                            latitude,
                            longitude
                        );

                        resolve({
                            latitude: latitude,
                            longitude: longitude,
                            locationData: locationData,
                        });
                    },
                    (error) => {
                        console.error("Geolocation error:", error);
                        reject(error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000,
                    }
                );
            } else {
                reject(new Error("Trình duyệt không hỗ trợ lấy vị trí"));
            }
        });
    },
};

// Dark Mode Management
window.ThemeManager = {
    // Initialize theme on page load
    init: function() {
        // Check localStorage first, then system preference
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
        this.setTheme(theme);
        
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                this.setTheme(e.matches ? 'dark' : 'light');
            }
        });
        
        console.log('Theme Manager initialized:', theme);
    },

    // Set theme and save to localStorage
    setTheme: function(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', theme);
        console.log('Theme set to:', theme);
    },

    // Get current theme
    getTheme: function() {
        return localStorage.getItem('theme') || 
               (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    },

    // Toggle between light and dark
    toggle: function() {
        const currentTheme = this.getTheme();
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
        
        // Show notification
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                background: newTheme === 'dark' ? '#1f2937' : '#ffffff',
                color: newTheme === 'dark' ? '#ffffff' : '#1f2937',
            });

            Toast.fire({
                icon: 'success',
                title: newTheme === 'dark' ? '🌙 Chế độ tối' : '☀️ Chế độ sáng'
            });
        }
        
        return newTheme;
    }
};

// Global function for navigation button
window.toggleTheme = function() {
    return window.ThemeManager.toggle();
};

// Initialize theme when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.ThemeManager.init());
} else {
    window.ThemeManager.init();
};
