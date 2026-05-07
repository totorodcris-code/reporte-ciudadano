// axios nos permite hacer peticiones GET, POST, PUT Y DELETE hacia la API de Laravel
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json'
    }
});

// Interceptor para agregar el token automáticamente
api.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    
    console.log('API Request:', {
        url: config.url,
        method: config.method,
        hasToken: !!token,
        token: token ? `${token.substring(0, 20)}...` : null
    });
    
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    } else {
        console.warn('No authentication token found in localStorage');
    }

    return config;
});

// Interceptor para manejar errores de respuesta
api.interceptors.response.use(
    response => response,
    error => {
        console.error('API Error:', {
            url: error.config?.url,
            method: error.config?.method,
            status: error.response?.status,
            message: error.message
        });
        
        if (error.response?.status === 401) {
            console.error('Authentication failed - clearing token');
            localStorage.removeItem('token');
            // Redirect to login or handle auth failure
        }
        
        return Promise.reject(error);
    }
);

export default api;