# ReporteCiudadanos API v1.0

## Overview

API RESTful para la aplicación móvil ReporteCiudadanos que permite gestionar reportes ciudadanos, categorías, autenticación y más.

**Base URL:** `https://api.reporteciudadanos.com/api/v1`

**Authentication:** Bearer Token (JWT)

## Authentication

### Login
```http
POST /auth/login
```

**Request Body:**
```json
{
  "email": "usuario@ejemplo.com",
  "password": "password123",
  "device_token": "optional_device_token"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "nombre": "Juan Pérez",
      "email": "usuario@ejemplo.com",
      "rol": "usuario",
      "perfil": {
        "iniciales": "JP",
        "nombre_corto": "Juan",
        "es_admin": false
      }
    }
  }
}
```

### Register
```http
POST /auth/register
```

**Request Body:**
```json
{
  "name": "Juan Pérez",
  "email": "usuario@ejemplo.com",
  "password": "password123",
  "password_confirmation": "password123",
  "device_token": "optional_device_token"
}
```

### Get Profile
```http
GET /auth/me
Authorization: Bearer {token}
```

### Update Profile
```http
PUT /auth/profile
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "Juan Pérez Actualizado",
  "email": "nuevo@ejemplo.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

### Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

### Refresh Token
```http
POST /auth/refresh
Authorization: Bearer {token}
```

## Incidencias (Reportes)

### Listar Incidencias
```http
GET /incidencias
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (int): Página actual (default: 1)
- `per_page` (int): Elementos por página (default: 15)
- `user_id` (int): Filtrar por usuario
- `estado` (string): Filtrar por estado (pendiente, en-progreso, resuelto)
- `categoria_id` (int): Filtrar por categoría
- `search` (string): Buscar en título, descripción o dirección

**Response:**
```json
{
  "success": true,
  "message": "Incidencias obtenidas exitosamente",
  "data": {
    "data": [
      {
        "id": 1,
        "titulo": "Bache en calle principal",
        "descripcion": "Gran bache que representa peligro",
        "imagen": "https://api.reporteciudadanos.com/storage/incidencias/imagen.jpg",
        "imagen_url": "https://api.reporteciudadanos.com/storage/incidencias/imagen.jpg",
        "latitud": -19.036,
        "longitud": -65.259,
        "direccion": "Calle Sucre, esquina Junín",
        "estado": "pendiente",
        "prioridad": 1,
        "fecha_reporte": "2024-01-15T10:30:00Z",
        "created_at": "2024-01-15T10:30:00Z",
        "updated_at": "2024-01-15T10:30:00Z",
        "usuario": {
          "id": 1,
          "nombre": "Juan Pérez",
          "email": "usuario@ejemplo.com",
          "rol": "usuario"
        },
        "categoria": {
          "id": 1,
          "nombre": "Infraestructura",
          "descripcion": "Baches, banquetas, guarniciones"
        },
        "resumen": {
          "titulo_corto": "Bache en calle principal",
          "descripcion_corta": "Gran bache que representa peligro",
          "tiene_imagen": true,
          "estado_formateado": "Pendiente",
          "dias_desde_reporte": 5
        }
      }
    ],
    "meta": {
      "total": 100,
      "count": 15,
      "per_page": 15,
      "current_page": 1,
      "total_pages": 7,
      "has_more_pages": true
    }
  }
}
```

### Crear Incidencia
```http
POST /incidencias
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```
titulo: "Bache en calle principal"
descripcion: "Descripción detallada del problema"
imagen: [file]
latitud: -19.036
longitud: -65.259
direccion: "Calle Sucre, esquina Junín"
categoria_id: 1
user_id: 1
prioridad: 1
```

### Ver Incidencia
```http
GET /incidencias/{id}
Authorization: Bearer {token}
```

### Actualizar Incidencia
```http
PUT /incidencias/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

### Eliminar Incidencia
```http
DELETE /incidencias/{id}
Authorization: Bearer {token}
```

### Mis Reportes
```http
GET /incidencias/mis-reportes
Authorization: Bearer {token}
```

### Estadísticas de Incidencias
```http
GET /incidencias/estadisticas
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Estadísticas obtenidas exitosamente",
  "data": {
    "totales": {
      "total": 100,
      "pendientes": 60,
      "en_progreso": 25,
      "resueltos": 15
    },
    "por_categoria": [
      {
        "categoria": "Infraestructura",
        "total": 40
      }
    ],
    "porcentajes": {
      "resueltos": 15.0,
      "pendientes": 60.0,
      "en_progreso": 25.0
    }
  }
}
```

## Categorías

### Listar Categorías
```http
GET /categorias
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Categorías obtenidas exitosamente",
  "data": [
    {
      "id": 1,
      "nombre": "Infraestructura",
      "descripcion": "Banches, banquetas, guarniciones",
      "created_at": "2024-01-01T00:00:00Z",
      "ui_config": {
        "color": "#3B82F6",
        "icono": "fa-hammer",
        "activo": true
      }
    }
  ]
}
```

### Estadísticas de Categorías
```http
GET /categorias/estadisticas
Authorization: Bearer {token}
```

## Headers Especiales

Para desarrollo móvil, puedes incluir estos headers opcionales:

- `X-Device-Token`: Token para notificaciones push
- `X-App-Version`: Versión de la aplicación
- `X-Platform`: Plataforma (ios, android)

## Rate Limiting

La API tiene límites de uso para prevenir abuso:

- **Login/Register**: 5 intentos por minuto
- **Escritura (POST/PUT/DELETE)**: 60 requests por minuto
- **Lectura (GET)**: 120 requests por minuto

Los headers de rate limiting son incluidos en cada respuesta:

- `X-RateLimit-Limit`: Límite total
- `X-RateLimit-Remaining`: Requests restantes
- `X-RateLimit-Reset`: Tiempo hasta reset

## Errores

### Formato de Error
```json
{
  "success": false,
  "message": "Error descriptivo",
  "error_code": "ERROR_CODE",
  "errors": {
    "campo": ["Error específico del campo"]
  }
}
```

### Códigos de Error Comunes

- `401`: No autenticado
- `403`: Permisos insuficientes
- `404`: Recurso no encontrado
- `422`: Error de validación
- `429`: Rate limit exceeded
- `500`: Error del servidor

## Ejemplo de Uso con JavaScript

```javascript
// Login
const login = async (email, password) => {
  const response = await fetch('https://api.reporteciudadanos.com/api/v1/auth/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ email, password }),
  });
  
  const data = await response.json();
  if (data.success) {
    localStorage.setItem('token', data.data.access_token);
    return data.data.user;
  } else {
    throw new Error(data.message);
  }
};

// Obtener incidencias
const getIncidencias = async (page = 1) => {
  const token = localStorage.getItem('token');
  const response = await fetch(`https://api.reporteciudadanos.com/api/v1/incidencias?page=${page}`, {
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  });
  
  return await response.json();
};

// Crear incidencia
const createIncidencia = async (formData) => {
  const token = localStorage.getItem('token');
  const response = await fetch('https://api.reporteciudadanos.com/api/v1/incidencias', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
    },
    body: formData,
  });
  
  return await response.json();
};
```

## Testing

Puedes probar la API con Postman o usando curl:

```bash
# Login
curl -X POST https://api.reporteciudadanos.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@ejemplo.com","password":"password123"}'

# Obtener incidencias
curl -X GET https://api.reporteciudadanos.com/api/v1/incidencias \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```

## Soporte

Para soporte técnico o reportar problemas, contacta a:
- Email: api-support@reporteciudadanos.com
- GitHub: https://github.com/reporte-ciudadanos/issues
