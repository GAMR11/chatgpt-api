Laravel 12 Gemini API Integration

Este proyecto es una implementación de backend utilizando Laravel 12 que actúa como un wrapper/proxy seguro para interactuar con la Inteligencia Artificial de Google, específicamente el modelo Gemini 2.5 Flash Lite.

El sistema está diseñado para ser Stateless y compatible con entornos Serverless como Vercel.

🌐 Demo en Vivo

La API se encuentra desplegada y operativa en Vercel:

Base URL: https://chatgpt-api-ruby.vercel.app

Endpoint

Método

Descripción

Estado

/api/gemini/chat

POST

Chat con la IA

✅ Activo

/api/gemini/health

GET

Verificación de servicio

✅ Activo

🚀 Características

Integración con Gemini 2.5 Flash Lite: Utiliza la última versión ligera y rápida del modelo.

Serverless Ready: Configurado para funcionar sin persistencia de archivos locales (Vercel/AWS Lambda).

Validación de Datos: Reglas estrictas para message, temperature y maxTokens.

Manejo de Errores Robusto: Control de excepciones, tiempos de espera (timeouts) y reintentos automáticos.

Seguridad: API Key protegida en el servidor; el cliente nunca la ve.

🛠️ Requisitos Previos (Local)

PHP 8.2 o superior.

Composer.

Una API Key de Google AI Studio.

⚙️ Instalación Local

Clonar el repositorio

git clone <https://github.com/GAMR11/chatgpt-api.git>
cd <chatgpt-api>


Instalar dependencias

composer install


Configurar el entorno

cp .env.example .env
php artisan key:generate


Configurar la API Key
En tu archivo .env:

GEMINI_API_KEY="tu_api_key_aqui"
SESSION_DRIVER=cookie  # Importante para simular entorno serverless


🔌 Documentación de la API

1. Chat con Gemini

Envía un mensaje al modelo y recibe una respuesta generada.

URL Producción: https://chatgpt-api-ruby.vercel.app/api/gemini/chat

URL Local: http://localhost:8000/api/gemini/chat

Método: POST

Cuerpo de la Solicitud (JSON):

Parámetro

Tipo

Requerido

Descripción

Restricciones

message

string

Sí

El prompt para la IA.

Máx 5000 caracteres.

temperature

float

No

Creatividad.

0.0 a 2.0 (Default: 0.7).

maxTokens

integer

No

Longitud máx.

1 a 8192 (Default: 2048).

Ejemplo de uso (cURL):

curl -X POST [https://chatgpt-api-ruby.vercel.app/api/gemini/chat](https://chatgpt-api-ruby.vercel.app/api/gemini/chat) \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{
    "message": "Escribe un poema corto sobre programación",
    "temperature": 1.0,
    "maxTokens": 500
}'


Respuesta Exitosa (200 OK):

{
    "success": true,
    "data": {
        "message": "Código en pantalla,\nluz en la oscuridad,\nun bug se escapa,\n¡café y libertad!",
        "model": "gemini-2.5-flash-lite"
    }
}


2. Health Check

URL: /api/gemini/health

Método: GET

{
    "status": "ok",
    "service": "gemini",
    "model": "gemini-2.5-flash-lite",
    "api_key_configured": true
}


☁️ Detalles del Despliegue en Vercel

Este proyecto tiene configuraciones específicas para correr en una arquitectura Serverless:

Almacenamiento Efímero: No se usa SQLite ni almacenamiento local de archivos.

Sesiones & Caché:

SESSION_DRIVER: Configurado como cookie (las sesiones viajan encriptadas al navegador).

CACHE_DRIVER: Configurado como array (la caché vive solo lo que dura la petición).

Configuración Vercel:

Se utiliza un archivo vercel.json para redirigir el tráfico al index.php de Laravel.

Los logs se redirigen a stderr para ser visibles en el dashboard de Vercel.

Estructura de Archivos Clave

api/index.php: Punto de entrada para el runtime de Vercel.

vercel.json: Configuración de rutas y entorno.

🛡️ Seguridad

Bloqueo de Contenido: Filtros de seguridad activos (HARM_CATEGORY_HARASSMENT, etc.).

Logs: Errores registrados sin exponer datos sensibles.

📄 Licencia

Este proyecto es de código abierto bajo la licencia MIT.