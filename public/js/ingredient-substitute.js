// JavaScript to manage ingredient substitute modal
let searchHistory = [];
const maxHistory = 5;

// Open modal
function openIngredientModal() {
    const modal = document.getElementById("ingredient-substitute-modal");
    if (modal) {
        modal.classList.remove("hidden");
        resetForm();

        // Focus input
        setTimeout(() => {
            const input = document.getElementById("ingredient-input");
            if (input) {
                input.focus();
            }
        }, 100);
    }
}

// Close modal
function closeIngredientModal() {
    const modal = document.getElementById("ingredient-substitute-modal");
    if (modal) {
        modal.classList.add("hidden");
        resetForm();
    }
}

// Set ingredient value into input
function setIngredientValue(ingredient) {
    const input = document.getElementById("ingredient-input");
    if (input) {
        input.value = ingredient;
        clearValidationError();
        input.focus();
        input.select();
    }
}

// Show validation error
function showValidationError(message) {
    const input = document.getElementById("ingredient-input");
    const errorDiv = document.getElementById("validation-error");

    if (input) {
        input.classList.add("border-red-500");
    }

    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.remove("hidden");
    }
}

// Clear validation error
function clearValidationError() {
    const input = document.getElementById("ingredient-input");
    const errorDiv = document.getElementById("validation-error");

    if (input) {
        input.classList.remove("border-red-500");
    }

    if (errorDiv) {
        errorDiv.classList.add("hidden");
    }
}

// Show loading state
function showLoading() {
    const button = document.getElementById("search-button");
    const spinner = document.getElementById("loading-spinner");
    const buttonText = document.getElementById("button-text");

    if (button) button.disabled = true;
    if (spinner) spinner.classList.remove("hidden");
    if (buttonText) buttonText.textContent = "Đang tìm...";
}

// Hide loading state
function hideLoading() {
    const button = document.getElementById("search-button");
    const spinner = document.getElementById("loading-spinner");
    const buttonText = document.getElementById("button-text");

    if (button) button.disabled = false;
    if (spinner) spinner.classList.add("hidden");
    if (buttonText) buttonText.textContent = "Tìm kiếm";
}

// Show success message
function showSuccessMessage(message) {
    const successDiv = document.getElementById("success-message");
    const successText = document.getElementById("success-text");

    if (successDiv && successText) {
        successText.textContent = message;
        successDiv.classList.remove("hidden");
    }
}

// Show error message
function showErrorMessage(message) {
    const errorDiv = document.getElementById("error-message");
    const errorText = document.getElementById("error-text");

    if (errorDiv && errorText) {
        errorText.textContent = message;
        errorDiv.classList.remove("hidden");
    }
}

// Hide messages
function hideMessages() {
    const successDiv = document.getElementById("success-message");
    const errorDiv = document.getElementById("error-message");

    if (successDiv) successDiv.classList.add("hidden");
    if (errorDiv) errorDiv.classList.add("hidden");
}

// Show results
function showResults(ingredient, substitutes) {
    const resultsSection = document.getElementById("results-section");
    const resultsList = document.getElementById("results-list");
    const resultsTitle = document.getElementById("results-title");
    const resetButton = document.getElementById("reset-button");

    if (resultsSection && resultsList && resultsTitle) {
        resultsTitle.textContent = `Nguyên liệu có thể thay thế cho "${ingredient}"`;

        resultsList.innerHTML = "";
        substitutes.forEach((substitute, index) => {
            const div = document.createElement("div");
            div.className =
                "bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4 hover:shadow-sm transition-shadow";
            div.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-semibold">
                            ${index + 1}
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <h5 class="text-sm font-semibold text-gray-900 mb-1">
                            ${substitute.name}
                        </h5>
                        ${
                            substitute.description
                                ? `
                            <p class="text-xs text-gray-600">
                                ${substitute.description}
                            </p>
                        `
                                : ""
                        }
                    </div>
                </div>
            `;
            resultsList.appendChild(div);
        });

        resultsSection.classList.remove("hidden");
        if (resetButton) resetButton.classList.remove("hidden");
    }
}

// Show no results
function showNoResults() {
    const noResultsSection = document.getElementById("no-results-section");
    if (noResultsSection) {
        noResultsSection.classList.remove("hidden");
    }
}

// Hide results
function hideResults() {
    const resultsSection = document.getElementById("results-section");
    const noResultsSection = document.getElementById("no-results-section");
    const resetButton = document.getElementById("reset-button");

    if (resultsSection) resultsSection.classList.add("hidden");
    if (noResultsSection) noResultsSection.classList.add("hidden");
    if (resetButton) resetButton.classList.add("hidden");
}

// Reset form
function resetForm() {
    const input = document.getElementById("ingredient-input");
    if (input) input.value = "";

    clearValidationError();
    hideMessages();
    hideResults();
    hideLoading();
}

// Submit search via API
async function submitSearch() {
    const input = document.getElementById("ingredient-input");
    const ingredient = input?.value?.trim();

    if (!ingredient) {
        showValidationError("Vui lòng nhập tên nguyên liệu.");
        return;
    }

    clearValidationError();
    hideMessages();
    hideResults();
    showLoading();

    try {
        const response = await fetch("/api/ingredient-substitute", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content") || "",
            },
            body: JSON.stringify({ ingredient: ingredient }),
        });

        const data = await response.json();

        if (data.success && data.substitutes && data.substitutes.length > 0) {
            showSuccessMessage(data.message);
            showResults(ingredient, data.substitutes);
            addToSearchHistory(ingredient);
        } else {
            showErrorMessage(
                data.error || "Không tìm thấy nguyên liệu thay thế."
            );
            showNoResults();
        }
    } catch (error) {
        console.error("Error:", error);
        showErrorMessage("Có lỗi xảy ra. Vui lòng thử lại sau.");
        showNoResults();
    } finally {
        hideLoading();
    }
}

// Load search history from localStorage
function loadSearchHistory() {
    const history = localStorage.getItem("ingredient_search_history");
    searchHistory = history ? JSON.parse(history) : [];
    renderSearchHistory();
}

// Render search history UI
function renderSearchHistory() {
    const section = document.getElementById("search-history-section");
    const list = document.getElementById("search-history-list");

    if (!section || !list) return;

    if (searchHistory.length === 0) {
        section.style.display = "none";
        return;
    }

    section.style.display = "block";
    list.innerHTML = "";

    searchHistory.forEach((item) => {
        const button = document.createElement("button");
        button.type = "button";
        button.onclick = () => setIngredientValue(item);
        button.className =
            "inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm rounded-full transition-colors";
        button.innerHTML = `
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
            </svg>
            ${item}
        `;
        list.appendChild(button);
    });
}

// Save search history to localStorage
function saveSearchHistory() {
    localStorage.setItem(
        "ingredient_search_history",
        JSON.stringify(searchHistory)
    );
}

// Add to search history
function addToSearchHistory(ingredient) {
    ingredient = ingredient.trim();
    if (!ingredient) return;

    // Remove if already exists
    searchHistory = searchHistory.filter((item) => item !== ingredient);

    // Add to beginning
    searchHistory.unshift(ingredient);

    // Limit size
    searchHistory = searchHistory.slice(0, maxHistory);

    saveSearchHistory();
    renderSearchHistory();
}

// Clear search history
function clearSearchHistory() {
    searchHistory = [];
    localStorage.removeItem("ingredient_search_history");
    renderSearchHistory();
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
    loadSearchHistory();

    // Form submit event
    const form = document.getElementById("ingredient-search-form");
    if (form) {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            submitSearch();
        });
    }

    // Input enter key
    const input = document.getElementById("ingredient-input");
    if (input) {
        input.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                submitSearch();
            }
        });
    }
});

// Handle keyboard shortcuts
document.addEventListener("keydown", (e) => {
    const modal = document.getElementById("ingredient-substitute-modal");

    // If modal is open
    if (modal && !modal.classList.contains("hidden")) {
        if (e.key === "Escape") {
            closeIngredientModal();
        }
    }
});

// Global function to open modal (called from navigation)
window.openIngredientSubstituteModal = openIngredientModal;
