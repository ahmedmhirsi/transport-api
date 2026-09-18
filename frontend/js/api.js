const getApiBaseRoot = () => {
    const path = window.location.pathname || '';
    const origin = window.location.origin || 'http://localhost';

    if (path.includes('/dxc/') || path.includes('/frontend/') || path.includes('/backend/public')) {
        return `${origin}/dxc/backend/public`;
    }

    return origin;
};

const API_BASE_ROOT = getApiBaseRoot();

const getErrorMessage = (payload) => {
    if (!payload) return 'Network or Server Error';
    if (typeof payload.error === 'string') return payload.error;
    if (payload.error && typeof payload.error.message === 'string') return payload.error.message;
    if (typeof payload.message === 'string') return payload.message;
    if (payload.code) return payload.code;
    return 'Network or Server Error';
};

const API = {
    baseUrl: `${API_BASE_ROOT}/api`,
    token: localStorage.getItem('jwt_token'),
    
    setToken(token) { this.token = token; localStorage.setItem('jwt_token', token); },
    clearToken() { this.token = null; localStorage.removeItem('jwt_token'); },
    getToken() { return this.token || localStorage.getItem('jwt_token'); },
    
    async request(method, endpoint, data = null) {
        const headers = { 'Content-Type': 'application/json' };
        if (this.token) headers['Authorization'] = 'Bearer ' + this.token;
        
        const options = { method, headers };
        if (data) options.body = JSON.stringify(data);
        
        try {
            const response = await fetch(this.baseUrl + endpoint, options);
            const text = await response.text();
            let json = {};

            if (text) {
                try {
                    json = JSON.parse(text);
                } catch (e) {
                    json = { error: text };
                }
            }
            
            if (response.status === 401) {
                this.clearToken();
                window.location.href = 'index.html';
                return { status: 401, error: 'Unauthorized' };
            }

            if (!response.ok) {
                return { status: response.status, error: getErrorMessage(json), ...json };
            }
            
            return { status: response.status, ...json };
        } catch (error) {
            console.error('API Error:', error);
            return { status: 500, error: 'Network or Server Error' };
        }
    },
    
    login: (email, password) => API.request('POST', '/login', { email, password }),
    getTrajets: (params = '') => API.request('GET', '/trajets' + (params ? '?' + params : '')),
    createReservation: (trajetId, seats) => API.request('POST', '/reservations', { trajet_id: trajetId, seats }),
    getReservations: (page = 1) => API.request('GET', '/reservations?page=' + page),
    getReservation: (id) => API.request('GET', '/reservations/' + id),
    cancelReservation: (id) => API.request('POST', '/reservations/' + id + '/cancel'),
    getStats: () => API.request('GET', '/reservations/stats'),
};

const ThirdPartyAPI = {
    baseUrl: `${API_BASE_ROOT}/api`,
    apiKey: localStorage.getItem('api_key'),
    
    setApiKey(key) { this.apiKey = key; localStorage.setItem('api_key', key); },
    clearApiKey() { this.apiKey = null; localStorage.removeItem('api_key'); },
    
    async request(method, endpoint) {
        const headers = { 'Content-Type': 'application/json' };
        if (this.apiKey) headers['X-API-Key'] = this.apiKey;
        
        try {
            const response = await fetch(this.baseUrl + endpoint, { method, headers });
            const text = await response.text();
            const json = text ? JSON.parse(text) : {};

            if (response.status === 401) {
                this.clearApiKey();
                window.location.href = 'third-party.html';
                return { status: 401, error: 'Unauthorized' };
            }

            if (!response.ok) {
                return { status: response.status, error: getErrorMessage(json), ...json };
            }
            
            return { status: response.status, ...json };
        } catch (error) {
            console.error('ThirdPartyAPI Error:', error);
            return { status: 500, error: 'Network or Server Error' };
        }
    },
    
    getReservations: () => ThirdPartyAPI.request('GET', '/third-party/reservations'),
    getStats: () => ThirdPartyAPI.request('GET', '/third-party/stats'),
    getWebhookEvents: () => ThirdPartyAPI.request('GET', '/third-party/webhook-events'),
};
