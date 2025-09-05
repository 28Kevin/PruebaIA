# Weather Chatbot API - Laravel Backend

Una API RESTful robusta construida con Laravel 11+ que sirve como backend para un chatbot de clima inteligente. Integra OpenAI para procesamiento de lenguaje natural y Open-Meteo para datos meteorológicos precisos.

## 🏗️ Arquitectura

El proyecto sigue una **arquitectura en capas limpia** con separación estricta de responsabilidades:

```
┌─────────────────┐
│   Controllers   │ ← Manejo de HTTP requests/responses
├─────────────────┤
│    Services     │ ← Lógica de negocio y orquestación
├─────────────────┤
│  Repositories   │ ← Acceso a datos y abstracción de BD
├─────────────────┤
│     Models      │ ← Modelos Eloquent
└─────────────────┘
```

### Flujo de Petición
**Ruta → Controlador → Servicio → Repositorio → Base de Datos**

## 🚀 Características Principales

- **Arquitectura Repository Pattern**: Desacoplamiento completo entre lógica de negocio y acceso a datos
- **OpenAI Function Calling**: Integración inteligente con herramientas meteorológicas
- **Validación Robusta**: Form Requests con validación personalizada
- **API Resources**: Formateo consistente de respuestas JSON
- **Inyección de Dependencias**: Uso completo del contenedor de servicios de Laravel
- **Manejo de Errores**: Sistema robusto de manejo de excepciones
- **CORS Configurado**: Listo para integración frontend

## 📋 Requisitos

- PHP 8.2+
- Laravel 11+
- MySQL 8.0+
- Composer
- OpenAI API Key

## 🛠️ Instalación

1. **Instalar dependencias**
```bash
composer install
```

2. **Configurar variables de entorno**
```bash
cp .env.example .env
```

Editar `.env` con tus configuraciones:
```env
# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=weather_chatbot
DB_USERNAME=root
DB_PASSWORD=

# OpenAI
OPENAI_API_KEY=tu_clave_openai_aqui
OPENAI_ORGANIZATION=tu_organizacion_openai

# Open-Meteo (ya configurado)
OPEN_METEO_BASE_URL=https://api.open-meteo.com/v1
```

3. **Generar clave de aplicación**
```bash
php artisan key:generate
```

4. **Ejecutar migraciones**
```bash
php artisan migrate
```

5. **Iniciar servidor**
```bash
php artisan serve
```

## 📡 Endpoints de la API

### 🆕 Crear Conversación
```http
POST /api/conversations
Content-Type: application/json

{
    "title": "Consulta sobre el clima" // opcional
}
```

**Respuesta:**
```json
{
    "success": true,
    "message": "Conversación creada exitosamente",
    "data": {
        "id": 1,
        "title": "Consulta sobre el clima",
        "created_at": "2024-01-01T12:00:00.000000Z",
        "updated_at": "2024-01-01T12:00:00.000000Z"
    }
}
```

### 📖 Obtener Conversación
```http
GET /api/conversations/{id}
```

**Respuesta:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Consulta sobre el clima",
        "messages": [
            {
                "id": 1,
                "content": "¿Cómo está el clima en Madrid?",
                "sender_type": "user",
                "created_at": "2024-01-01T12:00:00.000000Z"
            },
            {
                "id": 2,
                "content": "En Madrid actualmente hay 22°C con cielo despejado...",
                "sender_type": "assistant",
                "created_at": "2024-01-01T12:01:00.000000Z"
            }
        ]
    }
}
```

### 💬 Enviar Mensaje
```http
POST /api/conversations/{id}/messages
Content-Type: application/json

{
    "content": "¿Cómo está el clima en Barcelona?"
}
```

**Respuesta:**
```json
{
    "success": true,
    "message": "Mensaje enviado exitosamente",
    "data": {
        "id": 3,
        "conversation_id": 1,
        "content": "En Barcelona actualmente hay 25°C con algunas nubes...",
        "sender_type": "assistant",
        "created_at": "2024-01-01T12:02:00.000000Z"
    }
}
```

### 🏥 Health Check
```http
GET /api/health
```

## 🧠 Funcionamiento del AI Assistant

El chatbot utiliza **OpenAI Function Calling** para determinar cuándo necesita datos meteorológicos:

1. **Análisis de Consulta**: OpenAI analiza el mensaje del usuario
2. **Detección de Herramientas**: Si necesita datos del clima, invoca `get_weather_for_location`
3. **Obtención de Datos**: El sistema consulta Open-Meteo API
4. **Respuesta Natural**: OpenAI genera una respuesta en lenguaje natural con los datos obtenidos

### Ejemplo de Flujo:
```
Usuario: "¿Cómo está el clima en Madrid?"
    ↓
OpenAI: Detecta necesidad de datos meteorológicos
    ↓
Sistema: Obtiene coordenadas de Madrid (40.4168, -3.7038)
    ↓
Open-Meteo: Retorna datos del clima actual
    ↓
OpenAI: "En Madrid actualmente hay 22°C con cielo despejado..."
```

## 🏛️ Estructura del Proyecto

```
app/
├── DTOs/                     # Data Transfer Objects
│   ├── CreateConversationDTO.php
│   ├── CreateMessageDTO.php
│   ├── WeatherRequestDTO.php
│   └── OpenAIResponseDTO.php
├── Http/
│   ├── Controllers/
│   │   └── ConversationController.php
│   ├── Requests/
│   │   ├── CreateConversationRequest.php
│   │   └── SendMessageRequest.php
│   └── Resources/
│       ├── ConversationResource.php
│       └── MessageResource.php
├── Models/
│   ├── Conversation.php
│   └── Message.php
├── Repositories/
│   ├── Contracts/
│   │   ├── ConversationRepositoryInterface.php
│   │   └── MessageRepositoryInterface.php
│   ├── ConversationRepository.php
│   └── MessageRepository.php
└── Services/
    ├── Contracts/
    │   ├── ChatServiceInterface.php
    │   ├── OpenAIServiceInterface.php
    │   └── WeatherServiceInterface.php
    ├── ChatService.php
    ├── OpenAIService.php
    └── WeatherService.php
```

## 🔧 Configuración Avanzada

### Personalizar Prompt del AI
El prompt principal se encuentra en `OpenAIService::getSystemPrompt()`. Puedes modificarlo para cambiar el comportamiento del asistente.

### Agregar Nuevas Herramientas
Para agregar nuevas funcionalidades al AI:

1. Definir la herramienta en `OpenAIService::getWeatherTools()`
2. Implementar la lógica en `ChatService::handleToolCall()`
3. Crear el servicio correspondiente si es necesario

### Configurar Base de Datos
Por defecto usa MySQL. Para cambiar a PostgreSQL o SQLite, modifica `config/database.php` y la variable `DB_CONNECTION` en `.env`.

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Con coverage
php artisan test --coverage
```

## 📝 Logs

Los logs se almacenan en `storage/logs/laravel.log`. Para monitorear errores:

```bash
tail -f storage/logs/laravel.log
```

## 🚀 Despliegue

### Producción
1. Configurar variables de entorno de producción
2. Ejecutar migraciones: `php artisan migrate --force`
3. Optimizar aplicación: `php artisan optimize`
4. Configurar servidor web (Nginx/Apache)

### Docker (Opcional)
```dockerfile
FROM php:8.2-fpm
# Configuración Docker aquí
```

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT.

## 🆘 Soporte

Para reportar bugs o solicitar features, crear un issue en el repositorio.

---

**Desarrollado con ❤️ usando Laravel 11+ y las mejores prácticas de arquitectura de software.**
