/* ============================================
   GLOBAL APP JAVASCRIPT
   ============================================ */

// API Base URL (configure in .env)
const API_BASE = document.querySelector('meta[name="api-url"]')?.content || '/api';

/* ============================================
   UTILITY FUNCTIONS
   ============================================ */

/**
 * Show toast notification
 * @param {string} message - Message text
 * @param {string} type - 'success', 'error', 'warning', 'info'
 * @param {number} duration - Auto-close in ms (0 = manual)
 */
function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        animation: slideInRight 0.3s ease;
        max-width: 400px;
    `;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    if (duration > 0) {
        setTimeout(() => toast.remove(), duration);
    }
    
    return toast;
}

/**
 * Format date to locale string
 * @param {string|Date} date 
 * @returns {string}
 */
function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Format bytes to readable size
 * @param {number} bytes 
 * @returns {string}
 */
function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

/**
 * Get CSRF token from meta tag
 * @returns {string}
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

/**
 * Validate email
 * @param {string} email 
 * @returns {boolean}
 */
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/**
 * Validate file type
 * @param {File} file 
 * @param {string[]} allowedTypes - MIME types
 * @returns {boolean}
 */
function isValidFileType(file, allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']) {
    return allowedTypes.includes(file.type);
}

/**
 * Validate file size
 * @param {File} file 
 * @param {number} maxSizeMB 
 * @returns {boolean}
 */
function isValidFileSize(file, maxSizeMB = 10) {
    return file.size <= (maxSizeMB * 1024 * 1024);
}

/* ============================================
   API FUNCTIONS
   ============================================ */

/**
 * Generic fetch wrapper
 * @param {string} endpoint 
 * @param {object} options 
 * @returns {Promise}
 */
async function apiCall(endpoint, options = {}) {
    const url = `${API_BASE}${endpoint}`;
    const config = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            ...options.headers
        },
        ...options
    };
    
    try {
        const response = await fetch(url, config);
        
        if (!response.ok) {
            const error = await response.json().catch(() => ({}));
            const first = error.errors ? Object.values(error.errors).flat()[0] : null;
            throw new Error(first || error.message || `HTTP ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

/**
 * GET request
 */
function apiGet(endpoint) {
    return apiCall(endpoint, { method: 'GET' });
}

/**
 * POST request
 */
function apiPost(endpoint, data) {
    return apiCall(endpoint, {
        method: 'POST',
        body: JSON.stringify(data)
    });
}

/**
 * PUT request
 */
function apiPut(endpoint, data) {
    return apiCall(endpoint, {
        method: 'PUT',
        body: JSON.stringify(data)
    });
}

/**
 * DELETE request
 */
function apiDelete(endpoint) {
    return apiCall(endpoint, { method: 'DELETE' });
}

/**
 * Upload file
 */
async function apiUploadFile(endpoint, file, additionalData = {}) {
    const formData = new FormData();
    formData.append('file', file);
    
    Object.keys(additionalData).forEach(key => {
        formData.append(key, additionalData[key]);
    });
    
    try {
        const response = await fetch(`${API_BASE}${endpoint}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: formData
        });
        
        if (!response.ok) {
            const err = await response.json().catch(() => ({}));
            const first = err.errors ? Object.values(err.errors).flat()[0] : null;
            throw new Error(first || err.message || `Upload gagal (${response.status})`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Upload Error:', error);
        throw error;
    }
}

/* ============================================
   DOM MANIPULATION
   ============================================ */

/**
 * Toggle element visibility
 */
function toggleElement(selector) {
    const el = document.querySelector(selector);
    if (el) {
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
}

/**
 * Remove element
 */
function removeElement(selector) {
    const el = document.querySelector(selector);
    if (el) el.remove();
}

/**
 * Add loading state to button
 */
function setButtonLoading(selector, loading = true) {
    const btn = document.querySelector(selector);
    if (!btn) return;
    
    if (loading) {
        btn.disabled = true;
        btn.dataset.originalText = btn.textContent;
        // Safe: HTML is static, no user input involved
        btn.innerHTML = '<span class="spinner me-2"></span>Loading...';
    } else {
        btn.disabled = false;
        btn.textContent = btn.dataset.originalText;
    }
}

/* ============================================
   LOCAL STORAGE
   ============================================ */

const Storage = {
    set: (key, value) => localStorage.setItem(key, JSON.stringify(value)),
    get: (key) => {
        try {
            return JSON.parse(localStorage.getItem(key));
        } catch {
            return null;
        }
    },
    remove: (key) => localStorage.removeItem(key),
    clear: () => localStorage.clear()
};

/* ============================================
   INITIALIZATION
   ============================================ */

// Auto-close alerts
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert').forEach(alert => {
        if (alert.dataset.autoClose !== 'false') {
            setTimeout(() => {
                alert.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }
    });
});

// Add animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes fadeOut {
        to { opacity: 0; transform: translateY(-20px); }
    }
`;
document.head.appendChild(style);
