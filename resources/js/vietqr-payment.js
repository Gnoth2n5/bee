import { VietQR } from "vietqr";

/**
 * VietQR Payment Service cho BeeFood
 * Sử dụng thư viện VietQR npm chính thức
 */
window.VietQRPayment = {
    // Khởi tạo VietQR instance
    vietQR: new VietQR({
        clientID: "d2fa39ed-fd5e-441e-9fe5-007ce8f5333c",
        apiKey: "70fc9247-574e-4ddd-9bbf-b264255e7f23",
    }),

    // Cấu hình tài khoản mặc định
    config: {
        bank: "970422", // MB Bank
        accountNumber: "0348392482",
        accountName: "Nguyen Ngoc Tung",
        template: "compact",
    },

    /**
     * Lấy user ID từ API
     */
    async getUserId() {
        try {
            const response = await fetch("/api/vietqr/user-id", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content"),
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin", // Sử dụng session authentication
            });

            if (!response.ok) {
                if (response.status === 401) {
                    console.log("User not authenticated, using guest mode");
                    return {
                        success: false,
                        user_id: "GUEST",
                        memo: "VIPPAYGUEST",
                    };
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log("User ID API response:", data);
            return data;
        } catch (error) {
            console.error("Error getting user ID:", error);
            // Fallback nếu không lấy được user ID
            return {
                success: false,
                user_id: "GUEST",
                memo: "VIPPAYGUEST",
            };
        }
    },

    /**
     * Tạo QR code thanh toán
     * @param {Object} paymentData
     * @returns {Promise<Object>}
     */
    async generatePaymentQR(paymentData) {
        try {
            const { amount, message = "Thanh toán BeeFood" } = paymentData;

            console.log("Generating payment QR with data:", paymentData);

            // Lấy user ID để tạo memo
            const userResponse = await this.getUserId();
            const memo = userResponse.success
                ? userResponse.memo
                : "VIPPAYGUEST";

            console.log("Using memo:", memo);

            // Tạo QR bằng VietQR API
            const result = await this.vietQR.genQRCodeBase64({
                bank: this.config.bank,
                accountNumber: this.config.accountNumber,
                accountName: this.config.accountName,
                amount: amount,
                memo: memo,
                template: this.config.template,
            });

            console.log("VietQR API result:", result);

            if (result && result.data && result.data.code === "00") {
                return {
                    success: true,
                    qr_code: result.data.data.qrDataURL,
                    amount: amount,
                    memo: memo,
                    message: "QR code tạo thành công",
                    vietqr_data: {
                        bank: this.config.accountName,
                        account_name: this.config.accountName,
                        account_number: this.config.accountNumber,
                        amount: amount,
                        content: memo,
                    },
                };
            } else {
                throw new Error(result?.data?.desc || "Lỗi từ VietQR API");
            }
        } catch (error) {
            console.error("VietQR Generation Error:", error);
            return {
                success: false,
                message: "Lỗi tạo QR code: " + error.message,
                error: error.message,
            };
        }
    },

    /**
     * Lấy danh sách ngân hàng
     */
    async getBanks() {
        try {
            const result = await this.vietQR.getBanks();

            if (result && result.data && result.data.code === "00") {
                return {
                    success: true,
                    data: result.data.data,
                };
            } else {
                throw new Error(
                    result?.data?.desc || "Lỗi lấy danh sách ngân hàng"
                );
            }
        } catch (error) {
            console.error("Get Banks Error:", error);
            return {
                success: false,
                message: "Lỗi lấy danh sách ngân hàng: " + error.message,
            };
        }
    },

    /**
     * Lấy danh sách template
     */
    async getTemplates() {
        try {
            const result = await this.vietQR.getTemplate();

            if (result && result.data && result.data.code === "00") {
                return {
                    success: true,
                    data: result.data.data,
                };
            } else {
                throw new Error(
                    result?.data?.desc || "Lỗi lấy danh sách template"
                );
            }
        } catch (error) {
            console.error("Get Templates Error:", error);
            return {
                success: false,
                message: "Lỗi lấy danh sách template: " + error.message,
            };
        }
    },

    /**
     * Xử lý thanh toán gói VIP
     * @param {string} packageId
     * @param {number} amount
     */
    async processVipPayment(packageId, amount) {
        try {
            console.log(
                `Processing VIP payment for package ${packageId}, amount: ${amount}`
            );

            // Tạo QR code
            const qrResult = await this.generatePaymentQR({
                amount: amount,
                message: `Thanh toán gói VIP ${packageId}`,
            });

            if (qrResult.success) {
                // Hiển thị QR code trong modal hoặc popup
                this.showPaymentModal(qrResult, packageId);
                return qrResult;
            } else {
                throw new Error(qrResult.message);
            }
        } catch (error) {
            console.error("VIP Payment Error:", error);
            alert("Lỗi xử lý thanh toán: " + error.message);
            return { success: false, error: error.message };
        }
    },

    /**
     * Hiển thị modal thanh toán
     */
    showPaymentModal(qrData, packageId) {
        // Tạo modal HTML
        const modalHTML = `
            <div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
                    <div class="text-center">
                        <h3 class="text-lg font-semibold mb-4">Thanh toán gói VIP ${packageId}</h3>
                        <div class="mb-4">
                            <img src="${
                                qrData.qr_code
                            }" alt="QR Code" class="mx-auto max-w-full h-auto" style="max-width: 300px;">
                        </div>
                        <div class="text-sm text-gray-600 mb-4">
                            <p><strong>Ngân hàng:</strong> ${
                                qrData.vietqr_data.bank
                            }</p>
                            <p><strong>Số tài khoản:</strong> ${
                                qrData.vietqr_data.account_number
                            }</p>
                            <p><strong>Số tiền:</strong> ${qrData.amount.toLocaleString(
                                "vi-VN"
                            )} VNĐ</p>
                            <p><strong>Nội dung:</strong> ${qrData.memo}</p>
                        </div>
                        <div class="text-xs text-gray-500 mb-4">
                            Quét mã QR bằng ứng dụng ngân hàng để thanh toán
                        </div>
                        <button onclick="VietQRPayment.closePaymentModal()" 
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Thêm modal vào body
        document.body.insertAdjacentHTML("beforeend", modalHTML);
    },

    /**
     * Đóng modal thanh toán
     */
    closePaymentModal() {
        const modal = document.getElementById("payment-modal");
        if (modal) {
            modal.remove();
        }
    },

    /**
     * Test function - giống như trong dự án Vue
     */
    async testQRGeneration() {
        try {
            console.log("Testing QR generation...");

            const result = await this.generatePaymentQR({
                amount: 10000,
                message: "Test thanh toán BeeFood",
            });

            console.log("Test result:", result);

            if (result.success) {
                this.showPaymentModal(result, "TEST");
            } else {
                alert("Test thất bại: " + result.message);
            }
        } catch (error) {
            console.error("Test error:", error);
            alert("Test lỗi: " + error.message);
        }
    },
};

// Khởi tạo khi DOM ready
document.addEventListener("DOMContentLoaded", function () {
    console.log("VietQR Payment Service initialized");

    // Test button (có thể xóa sau khi hoàn thành)
    if (window.location.search.includes("vietqr_test=1")) {
        console.log("VietQR test mode enabled");
        // Tạo nút test
        const testButton = document.createElement("button");
        testButton.textContent = "Test VietQR";
        testButton.className =
            "fixed top-4 right-4 bg-blue-500 text-white p-2 rounded z-50";
        testButton.onclick = () => VietQRPayment.testQRGeneration();
        document.body.appendChild(testButton);
    }
});
