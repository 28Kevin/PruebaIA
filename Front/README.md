# Vue.js 3 Chat Application

Una aplicación de chat moderna construida con Vue.js 3, TypeScript, Pinia y Tailwind CSS, siguiendo una arquitectura basada en funcionalidades.

## 🚀 Características

- **Vue.js 3** con Composition API y `<script setup>`
- **TypeScript** para tipado estático
- **Pinia** para gestión de estado
- **Tailwind CSS** para estilos
- **Routing basado en archivos** con `vite-plugin-pages`
- **Arquitectura por funcionalidades** para mejor organización del código
- **Componentes presentacionales** reutilizables
- **UX moderna** con indicadores de carga y manejo de errores

## 📁 Estructura del Proyecto

```
src/
├── pages/
│   └── Chat/
│       ├── components/
│       │   ├── MessageList.vue      # Lista de mensajes
│       │   ├── MessageBubble.vue    # Burbuja individual de mensaje
│       │   ├── MessageInput.vue     # Input para escribir mensajes
│       │   └── LoadingIndicator.vue # Indicador de carga
│       ├── interfaces/
│       │   └── chat.interface.ts    # Interfaces TypeScript
│       ├── stores/
│       │   └── chat.store.ts        # Store de Pinia
│       └── index.vue                # Página principal del chat
├── App.vue                          # Componente raíz
├── main.ts                          # Punto de entrada
└── style.css                       # Estilos de Tailwind
```

## 🛠️ Instalación y Configuración

1. **Instalar dependencias:**
   ```bash
   npm install
   ```

2. **Ejecutar en modo desarrollo:**
   ```bash
   npm run dev
   ```

3. **Construir para producción:**
   ```bash
   npm run build
   ```

4. **Vista previa de la build:**
   ```bash
   npm run preview
   ```

## 🏗️ Arquitectura

### Gestión de Estado (Pinia)

El store de chat (`chat.store.ts`) maneja:
- **Estado**: mensajes, estado de carga, errores
- **Acciones**: `sendMessage()`, `clearMessages()`, `clearError()`

### Componentes

- **Componentes presentacionales**: Reciben datos via props y emiten eventos
- **Página principal**: Conecta con el store y maneja la lógica de negocio
- **Tipado estricto**: Todas las interfaces están definidas en TypeScript

### Flujo de Datos

1. Usuario escribe mensaje en `MessageInput`
2. Evento se emite hacia `index.vue`
3. `index.vue` llama a la acción `sendMessage()` del store
4. Store actualiza el estado reactivo
5. Componentes se re-renderizan automáticamente

## 🎨 Características de UX

- **Scroll automático** a nuevos mensajes
- **Indicadores de carga** mientras el bot responde
- **Manejo de errores** con notificaciones
- **Límite de caracteres** en el input
- **Timestamps** en los mensajes
- **Diseño responsive** para móviles y desktop

## 🔧 Tecnologías Utilizadas

- **Vue.js 3.4+** - Framework reactivo
- **TypeScript 5.3+** - Tipado estático
- **Pinia 2.1+** - Gestión de estado
- **Tailwind CSS 3.4+** - Framework de CSS
- **Vite 5.0+** - Build tool
- **vite-plugin-pages** - Routing basado en archivos

## 📝 Uso

1. La aplicación se inicia en la ruta `/Chat`
2. Escribe un mensaje en el campo de texto
3. El asistente responderá automáticamente después de 2 segundos
4. Usa el botón "Limpiar Chat" para reiniciar la conversación

## 🚀 Próximos Pasos

- Integrar con API real del backend
- Añadir autenticación de usuarios
- Implementar persistencia de mensajes
- Añadir soporte para archivos multimedia
- Implementar notificaciones push
