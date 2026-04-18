/**
 * AJAX Foods Module
 * Dựa trên everything-claude-code skills:
 * - api-design: consistent request/response handling
 * - laravel-patterns: clean frontend architecture
 * 
 * Chức năng: Xử lý AJAX cho food catalog (filter, search, pagination)
 */

import axios from 'axios';

/**
 * Lấy danh sách foods với filter AJAX
 * @param {Object} params - { category, q, page }
 * @returns {Promise<Object>} Response data
 */
export async function fetchFoods(params = {}) {
    try {
        const response = await axios.get('/api/foods', { params });
        return response.data;
    } catch (error) {
        console.error('AJAX fetchFoods error:', error);
        throw error;
    }
}

/**
 * Lấy chi tiết một food
 * @param {string} slug - Food slug
 * @returns {Promise<Object>} Response data
 */
export async function fetchFood(slug) {
    try {
        const response = await axios.get(`/api/foods/${slug}`);
        return response.data;
    } catch (error) {
        console.error('AJAX fetchFood error:', error);
        throw error;
    }
}

/**
 * Render food cards từ data
 * @param {Array} foods - Array of food objects
 * @returns {string} HTML string
 */
export function renderFoodCards(foods) {
    if (!foods || foods.length === 0) {
        return `
            <div class="col-12">
                <div class="alert alert-secondary mb-0">Không tìm thấy món phù hợp.</div>
            </div>
        `;
    }

    return foods.map(food => `
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="small text-secondary">${food.category?.name || ''}</div>
                    <h2 class="h4 mt-1">${food.name}</h2>
                    <p class="text-secondary">${food.short_description || food.description || 'Đang cập nhật mô tả.'}</p>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <div class="fw-bold fs-5">${Number(food.price).toLocaleString('vi-VN')} đ</div>
                            <div class="small ${food.stock > 0 ? 'text-success' : 'text-danger'}">Tồn kho: ${food.stock}</div>
                        </div>
                        <a href="/thuc-pham/${food.slug}" class="btn btn-outline-dark">Xem món</a>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Render pagination từ meta data
 * @param {Object} meta - Pagination metadata
 * @param {string} baseUrl - Base URL for pagination links
 * @returns {string} HTML string
 */
export function renderPagination(meta, baseUrl = '/thuc-pham') {
    if (meta.last_page <= 1) {
        return '';
    }

    const { current_page, last_page } = meta;
    let html = '<nav aria-label="Food pagination"><ul class="pagination mb-0">';

    // Previous button
    html += `<li class="page-item ${current_page === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${current_page - 1}">‹</a>
    </li>`;

    // Page numbers
    for (let i = 1; i <= last_page; i++) {
        if (i === 1 || i === last_page || (i >= current_page - 1 && i <= current_page + 1)) {
            html += `<li class="page-item ${i === current_page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        } else if (i === current_page - 2 || i === current_page + 2) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Next button
    html += `<li class="page-item ${current_page === last_page ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${current_page + 1}">›</a>
    </li>`;

    html += '</ul></nav>';
    return html;
}
