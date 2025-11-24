Laravel 12 Gemini API Integration

Este proyecto es una implementación de backend utilizando Laravel 12 que actúa como un wrapper/proxy seguro para interactuar con la Inteligencia Artificial de Google, específicamente el modelo Gemini 2.5 Flash Lite.

El sistema expone endpoints RESTful para enviar mensajes de chat y verificar el estado del servicio, manejando la autenticación y el manejo de errores de forma centralizada.

🚀 Características

Integración con Gemini 2.5 Flash Lite: Utiliza la última versión ligera y rápida del modelo.

Validación de Datos: Reglas estrictas para message, temperature y maxTokens.

Manejo de Errores Robusto: Control de excepciones, tiempos de espera (timeouts) y reintentos automáticos (retries) en caso de fallos de red.

Configuración Segura: Las claves de API se manejan a través de variables de entorno y archivos de configuración de servicios.

Health Check: Endpoint dedicado para verificar la conectividad y configuración de la API Key.

🛠️ Requisitos Previos

PHP 8.2 o superior.

Composer.

Una API Key de Google AI Studio.

⚙️ Instalación y Configuración

Clonar el repositorio

git clone <https://github.com/GAMR11/chatgpt-api.git>
cd <chatgpt-api>


Instalar dependencias

composer install


Configurar el entorno
Copia el archivo de ejemplo y genera la clave de la aplicación:

cp .env.example .env
php artisan key:generate


Configurar la API Key de Gemini
Abre el archivo .env y agrega tu clave de API:

GEMINI_API_KEY="tu_api_key_aqui"


Nota: La configuración se carga en config/services.php bajo la clave gemini.api_key.

🔌 Documentación de la API

1. Chat con Gemini

Envía un mensaje al modelo y recibe una respuesta generada.

URL: /api/gemini/chat

Método: POST

Headers:

Content-Type: application/json

Accept: application/json

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

Creatividad de la respuesta.

0.0 a 2.0 (Default: 0.7).

maxTokens

integer

No

Longitud máx de la respuesta.

1 a 8192 (Default: 2048).

Ejemplo de Solicitud (cURL):

curl -X POST http://localhost/api/gemini/chat \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{
    "message": "Explica qué es Laravel en una frase",
    "temperature": 0.5,
    "maxTokens": 100
}'


Respuesta Exitosa (200 OK):

{
    "success": true,
    "data": {
        "message": "Laravel es un framework de PHP elegante y expresivo diseñado para facilitar y acelerar el desarrollo de aplicaciones web robustas.",
        "model": "gemini-2.5-flash-lite"
    }
}


Respuesta de Error (Ej. 422 Unprocessable Entity):

{
    "message": "The message field is required.",
    "errors": {
        "message": [
            "The message field is required."
        ]
    }
}


2. Health Check

Verifica si el servicio está operativo y la API Key está configurada correctamente.

URL: /api/gemini/health

Método: GET

Respuesta Exitosa (200 OK):

{
    "status": "ok",
    "service": "gemini",
    "model": "gemini-2.5-flash-lite",
    "api_key_configured": true
}


📂 Estructura del Código

Controlador: App\Http\Controllers\GeminiController.php

Contiene la lógica de negocio, validación y conexión HTTP con Google.

Rutas: routes/api.php

Define el grupo de rutas con prefijo gemini.

Configuración: config/services.php

Mapea la variable de entorno a la configuración de Laravel.

🛡️ Seguridad

Bloqueo de Contenido: El controlador implementa safetySettings para bloquear contenido de acoso (HARASSMENT) y discurso de odio (HATE_SPEECH) con un umbral BLOCK_MEDIUM_AND_ABOVE.

Logs: Los errores de conexión y respuestas vacías se registran en storage/logs/laravel.log para facilitar la depuración sin exponer detalles sensibles al cliente.

📄 Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.