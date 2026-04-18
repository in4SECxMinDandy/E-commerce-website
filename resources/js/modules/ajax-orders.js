/**
 * AJAX Orders Module
 * Dua tren everything-claude-code skills:
 * - api-design: consistent request/response handling
 * - laravel-security: auth validation, error handling
 *
 * Chuc nang: Xu ly AJAX cho order placement
 */

import axios from 'axios';

// Configure axios for Laravel CSRF protection
function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
    return match ? decodeURIComponent(match[2]) : null;
}

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

/**
 * Gui order qua AJAX
 * @param {Object} data - { food_id, quantity, note }
 * @returns {Promise<Object>} Response data
 */
export async function submitOrder(data) {
    // Refresh CSRF token from cookie before each request
    const csrfToken = getCookie('XSRF-TOKEN');
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    };
    if (csrfToken) {
        headers['X-XSRF-TOKEN'] = csrfToken;
    }

    try {
        const response = await axios.post('/orders', data, { headers });
        return response.data;
    } catch (error) {
        if (error.response) {
            const { status, data: errorData } = error.response;

            if (status === 422) {
                throw {
                    type: 'validation',
                    message: errorData.message || 'Dữ liệu không hợp lệ.',
                    errors: errorData.errors || {},
                };
            }

            if (status === 401) {
                window.location.href = '/login';
                return;
            }

            if (status === 429) {
                throw {
                    type: 'rate_limit',
                    message: 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau.',
                };
            }
        }

        throw {
            type: 'network',
            message: 'Không thể kết nối server. Vui lòng kiểm tra kết nối mạng.',
        };
    }
}

/**
 * Hien thi success toast/alert
 * @param {string} message - Success message
 * @param {Object} data - Order data
 */
import Swal from 'sweetalert2';

export function showOrderSuccess(message, data) {
    let detailsHtml = '';
    if (data) {
        detailsHtml = `
            <div class="mt-3 text-start bg-light p-3 rounded">
                <p class="mb-1"><strong>Món:</strong> ${data.food_name}</p>
                <p class="mb-1"><strong>Số lượng:</strong> ${data.quantity}</p>
                <p class="mb-0 text-success fw-bold fs-5"><strong>Tổng:</strong> ${Number(data.total_price).toLocaleString('vi-VN')} đ</p>
            </div>
        `;
    }

    Swal.fire({
        title: 'Thành công!',
        html: `<p class="mb-0">${message}</p>${detailsHtml}`,
        icon: 'success',
        confirmButtonText: 'Xem đơn hàng',
        confirmButtonColor: '#212529',
        showCancelButton: true,
        cancelButtonText: 'Tiếp tục mua sắm',
        cancelButtonColor: '#6c757d',
        customClass: {
            title: 'fs-4 font-monospace fw-bold',
            popup: 'rounded-4 shadow-lg border-0'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/history';
        }
    });
}

/**
 * Hien thi error alert
 * @param {string|Object} error - Error message or error object
 */
export function showOrderError(error) {
    const message = typeof error === 'string' ? error : error.message || 'Đã xảy ra lỗi.';

    Swal.fire({
        title: 'Lỗi!',
        text: message,
        icon: 'error',
        confirmButtonText: 'Đóng',
        confirmButtonColor: '#212529',
        customClass: {
            title: 'fs-4 font-monospace fw-bold',
            popup: 'rounded-4 shadow-lg border-0'
        }
    });
}

/**
 * Clear order form va errors
 */
export function clearOrderForm() {
    const form = document.querySelector('[data-order-form]');
    if (form) {
        form.reset();
    }

    const errorContainer = document.querySelector('[data-order-error]');
    if (errorContainer) {
        errorContainer.remove();
    }
}
