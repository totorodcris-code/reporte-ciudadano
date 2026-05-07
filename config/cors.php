<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        // Entorno de desarrollo
        'http://localhost:3000',
        'http://localhost:8080',
        'http://localhost:3001',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:8080',
        
        // Aplicaciones móviles (para desarrollo)
        'http://localhost',
        'exp://localhost:19000',
        'exp://192.168.1.*:19000',
        
        // Producción (reemplazar con dominios reales)
        'https://reporteciudadanos.com',
        'https://app.reporteciudadanos.com',
        'https://admin.reporteciudadanos.com',
    ],

    'allowed_origins_patterns' => [
        // Permitir subdominios de desarrollo
        '#^http://localhost:[0-9]+$#',
        '#^http://127\.0\.0\.1:[0-9]+$#',
        
        // Permitir IPs locales para desarrollo móvil
        '#^http://192\.168\.[0-9]{1,3}:[0-9]+$#',
        '#^http://10\.0\.[0-9]{1,3}:[0-9]+$#',
        '#^http://172\.16\.[0-9]{1,3}:[0-9]+$#',
        
        // Permitir Expo development
        '#^exp://localhost:[0-9]+$#',
        '#^exp://192\.168\.[0-9]{1,3}:[0-9]+$#',
    ],

    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'Accept',
        'Origin',
        'X-CSRF-TOKEN',
        'X-Device-Token',
        'X-App-Version',
        'X-Platform',
    ],

    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset',
        'X-Total-Count',
        'X-Current-Page',
        'X-Total-Pages',
    ],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => true,

];
