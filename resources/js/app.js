import "./bootstrap";
import "flowbite";
import Alpine from "alpinejs";
import Swal from "sweetalert2";
import "./recipe-pagination";
import "./vietqr-payment";

window.Alpine = Alpine;
window.Swal = Swal;
Alpine.start();

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
