# Weather Chatbot - Aplicación Completa

Una aplicación completa de chatbot de clima con backend Laravel 11+ y frontend Vue.js 3, integrada con OpenAI y Open-Meteo API.

## 🏗️ Arquitectura del Sistema

```
┌─────────────────────────────────────────────────────────────┐
│                    FRONTEND (Vue.js 3)                     │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐ │
│  │   Components    │  │   Pinia Store   │  │ API Service │ │
│  │   - MessageList │  │   - Chat State  │  │ - HTTP Calls│ │
│  │   - MessageBubble│  │   - Actions     │  │ - Error     │ │
│  │   - MessageInput │  │                 │  │   Handling  │ │
│  └─────────────────┘  └─────────────────┘  └─────────────┘ │
└─────────────────────────────────────────────────────────────┘
                                │
                                │ HTTP/JSON API
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                   BACKEND (Laravel 11+)                    │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐ │
│  │   Controllers   │  │    Services     │  │ Repositories│ │
│  │   - Lightweight │  │   - ChatService │  │ - Data      │ │
│  │   - HTTP Only   │  │   - OpenAI      │  │   Access    │ │
│  │                 │  │   - Weather     │  │ - Eloquent  │ │
│  └─────────────────┘  └─────────────────┘  └─────────────┘ │
└─────────────────────────────────────────────────────────────┘
                                │
                    ┌───────────┼───────────┐
                    │                       │
                    ▼                       ▼
            ┌─────────────┐         ┌─────────────┐
            │   OpenAI    │         │ Open-Meteo  │
            │     API     │         │     API     │
            │ Function    │         │  Weather    │
            │  Calling    │         │    Data     │
            └─────────────┘         └─────────────┘
```

## 🚀 Características Implementadas

### Backend (Laravel 11+)
- ✅ **Arquitectura en Capas**: Repository Pattern estricto
- ✅ **OpenAI Function Calling**: Integración inteligente con herramientas
- ✅ **MeteoBot AI**: Prompt especializado en clima
- ✅ **Open-Meteo API**: Datos meteorológicos precisos
- ✅ **API RESTful**: Endpoints bien estructurados
- ✅ **Validación Robusta**: Form Requests y manejo de errores
- ✅ **CORS Configurado**: Listo para frontend

### Frontend (Vue.js 3)
- ✅ **Arquitectura Moderna**: Composition API + TypeScript
- ✅ **Estado Centralizado**: Pinia store
- ✅ **Componentes Reutilizables**: MessageList, MessageBubble, MessageInput
- ✅ **Integración API**: Servicio HTTP configurado
- ✅ **UI Responsiva**: Tailwind CSS
- ✅ **Manejo de Errores**: Estados de loading y error

## 📋 Instalación y Configuración

### 1. Backend (Laravel)

```bash
cd Back/
composer install
cp .env.example .env
php artisan key:generate
```

**Configurar variables de entorno en `.env`:**
```env
# Base de datos
DB_CONNECTION=mysql
DB_DATABASE=weather_chatbot
DB_USERNAME=root
DB_PASSWORD=

# OpenAI (REQUERIDO)
OPENAI_API_KEY=tu_clave_openai_aqui

# Open-Meteo (ya configurado)
OPEN_METEO_BASE_URL=https://api.open-meteo.com/v1
```

```bash
# Crear base de datos y ejecutar migraciones
php artisan migrate

# Iniciar servidor
php artisan serve
```

### 2. Frontend (Vue.js)

```bash
cd Front/
npm install
npm run dev
```

## 🌐 Endpoints de la API

### Backend (http://localhost:8000)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/api/conversations` | Crear nueva conversación |
| `GET` | `/api/conversations/{id}` | Obtener conversación con mensajes |
| `POST` | `/api/conversations/{id}/messages` | Enviar mensaje al chatbot |
| `GET` | `/api/health` | Health check |

### Ejemplo de uso:

**1. Crear conversación:**
```bash
curl -X POST http://localhost:8000/api/conversations \
  -H "Content-Type: application/json" \
  -d '{"title": "Consulta del clima"}'
```

**2. Enviar mensaje:**
```bash
curl -X POST http://localhost:8000/api/conversations/1/messages \
  -H "Content-Type: application/json" \
  -d '{"content": "¿Cómo está el clima en Madrid?"}'
```

## 🤖 Funcionamiento del MeteoBot

El chatbot utiliza **OpenAI Function Calling** para determinar cuándo necesita datos meteorológicos:

1. **Usuario pregunta**: "¿Cómo está el clima en Barcelona?"
2. **OpenAI analiza**: Detecta necesidad de datos meteorológicos
3. **Sistema obtiene coordenadas**: Barcelona (41.3851, 2.1734)
4. **Open-Meteo API**: Retorna datos del clima actual
5. **OpenAI responde**: "En **Barcelona** actualmente hay **22°C** con cielo despejado ☀️"

## 🎯 Ejemplos de Consultas

- "¿Qué temperatura hace en Madrid?"
- "¿Necesito paraguas en Londres mañana?"
- "¿Cómo está el clima en Buenos Aires?"
- "¿Hace frío en Nueva York?"
- "¿Llueve en París?"

## 🛠️ Desarrollo

### Estructura del Proyecto

```
PruebaIA/
├── Back/                     # Backend Laravel 11+
│   ├── app/
│   │   ├── DTOs/            # Data Transfer Objects
│   │   ├── Http/
│   │   │   ├── Controllers/ # Controladores ligeros
│   │   │   ├── Requests/    # Validación
│   │   │   └── Resources/   # Formateo JSON
│   │   ├── Models/          # Eloquent Models
│   │   ├── Repositories/    # Capa de acceso a datos
│   │   └── Services/        # Lógica de negocio
│   ├── database/migrations/ # Migraciones BD
│   └── routes/api.php       # Rutas API
├── Front/                   # Frontend Vue.js 3
│   └── src/
│       └── pages/Chat/
│           ├── components/  # Componentes Vue
│           ├── interfaces/  # TypeScript interfaces
│           ├── services/    # API service
│           └── stores/      # Pinia store
└── README.md               # Esta documentación
```

### Tecnologías Utilizadas

**Backend:**
- Laravel 11+
- PHP 8.2+
- MySQL
- OpenAI PHP Client
- Guzzle HTTP

**Frontend:**
- Vue.js 3
- TypeScript
- Pinia (State Management)
- Tailwind CSS
- Vite

## 🚀 Despliegue

### Producción
1. Configurar variables de entorno de producción
2. Optimizar Laravel: `php artisan optimize`
3. Build del frontend: `npm run build`
4. Configurar servidor web (Nginx/Apache)

### Docker (Opcional)
```dockerfile
# Backend
FROM php:8.2-fpm
# ... configuración Docker

# Frontend
FROM node:18-alpine
# ... configuración Docker
```

## 🔧 Personalización

### Modificar el Prompt del AI
Editar `app/Services/OpenAIService.php` método `getSystemPrompt()`

### Agregar Nuevas Funcionalidades
1. Definir nueva herramienta en `getWeatherTools()`
2. Implementar lógica en `ChatService`
3. Crear servicio específico si es necesario

## 🐛 Troubleshooting

### Problemas Comunes

**Error de CORS:**
- Verificar configuración en `config/cors.php`
- Asegurar que el frontend esté en la lista de orígenes permitidos

**Error de Base de Datos:**
- Verificar que MySQL esté corriendo
- Confirmar credenciales en `.env`
- Ejecutar migraciones: `php artisan migrate`

**Error de OpenAI:**
- Verificar API Key en `.env`
- Confirmar que tienes créditos disponibles

## 📝 Logs y Monitoreo

```bash
# Ver logs del backend
tail -f Back/storage/logs/laravel.log

# Ver logs del frontend (consola del navegador)
F12 -> Console
```

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama feature: `git checkout -b feature/nueva-funcionalidad`
3. Commit cambios: `git commit -am 'Agregar nueva funcionalidad'`
4. Push: `git push origin feature/nueva-funcionalidad`
5. Crear Pull Request

## 📄 Licencia

MIT License - Ver archivo LICENSE para más detalles.

---

**Desarrollado con ❤️ usando Laravel 11+ y Vue.js 3**

*Para soporte técnico, crear un issue en el repositorio.*
