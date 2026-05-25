/**
 * APIClient - JavaScript library for API communication
 * 
 * Features:
 * - Consistent API calls (GET, POST, PUT, DELETE)
 * - Automatic error handling
 * - Session expiration detection
 * - Loading state management
 */
class APIClient {
    constructor(baseURL = '/api') {
        this.baseURL = baseURL;
    }
    
    /**
     * Make API request
     * 
     * @param {string} endpoint API endpoint
     * @param {object} options Fetch options
     * @returns {Promise} Response data
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            ...options
        };
        
        try {
            const response = await fetch(url, config);
            const data = await response.json();
            
            // Handle HTTP errors
            if (!response.ok) {
                // Session expired - redirect to login
                if (response.status === 401) {
                    window.location.href = '/app/views/login.php?session_expired=1';
                    return;
                }
                
                // Throw error with message from response
                throw new Error(data.error?.message || 'Request failed');
            }
            
            return data;
            
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }
    
    /**
     * GET request
     * 
     * @param {string} endpoint API endpoint
     * @param {object} params Query parameters
     * @returns {Promise} Response data
     */
    async get(endpoint, params = {}) {
        const query = new URLSearchParams(params).toString();
        const url = query ? `${endpoint}?${query}` : endpoint;
        return this.request(url, { method: 'GET' });
    }
    
    /**
     * POST request
     * 
     * @param {string} endpoint API endpoint
     * @param {object} data Request body
     * @returns {Promise} Response data
     */
    async post(endpoint, data) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }
    
    /**
     * PUT request
     * 
     * @param {string} endpoint API endpoint
     * @param {object} data Request body
     * @returns {Promise} Response data
     */
    async put(endpoint, data) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }
    
    /**
     * DELETE request
     * 
     * @param {string} endpoint API endpoint
     * @returns {Promise} Response data
     */
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
    
    /**
     * Upload file
     * 
     * @param {string} endpoint API endpoint
     * @param {FormData} formData Form data with file
     * @returns {Promise} Response data
     */
    async upload(endpoint, formData) {
        const url = `${this.baseURL}${endpoint}`;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = '/app/views/login.php?session_expired=1';
                    return;
                }
                throw new Error(data.error?.message || 'Upload failed');
            }
            
            return data;
            
        } catch (error) {
            console.error('Upload Error:', error);
            throw error;
        }
    }
}

// Create global instance
const api = new APIClient();
