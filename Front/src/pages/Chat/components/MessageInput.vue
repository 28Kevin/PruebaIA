<template>
  <div class="border-t backdrop-blur-sm p-6 transition-colors duration-300" 
       :class="isDark 
         ? 'bg-gradient-to-r from-gray-800 via-gray-900 to-gray-800 border-gray-700' 
         : 'bg-gradient-to-r from-white via-blue-50 to-white border-gray-200'">
    <!-- Error Alert -->
    <div v-if="error" class="mb-4 p-4 rounded-2xl animate-fade-in-up transition-colors duration-300" 
         :class="isDark 
           ? 'bg-red-900/30 border border-red-700/50 text-red-200' 
           : 'bg-red-50 border border-red-200 text-red-700'">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
          <div class="w-5 h-5 bg-red-100 rounded-full flex items-center justify-center">
            <span class="text-red-600 text-xs">⚠️</span>
          </div>
          <span class="text-sm font-medium" :class="isDark ? 'text-red-200' : 'text-red-700'">{{ error }}</span>
        </div>
        <button 
          @click="$emit('clearError')"
          class="text-red-400 hover:text-red-600 transition-colors duration-200 p-1 rounded-full hover:bg-red-100"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Quick Suggestions -->
    <div v-if="showSuggestions && suggestions.length > 0" class="mb-4">
      <div class="flex items-center mb-2">
        <span class="text-xs font-semibold uppercase tracking-wide" :class="isDark ? 'text-gray-300' : 'text-gray-600'">Sugerencias rápidas</span>
      </div>
      <div class="flex flex-wrap gap-2">
        <button
          v-for="suggestion in suggestions"
          :key="suggestion"
          @click="selectSuggestion(suggestion)"
          class="px-3 py-1.5 text-sm rounded-full transition-all duration-200 hover:scale-105 border" 
          :class="isDark 
            ? 'bg-blue-900/30 hover:bg-blue-800/50 text-blue-300 border-blue-700/50 hover:border-blue-600' 
            : 'bg-blue-100 hover:bg-blue-200 text-blue-700 border-blue-200 hover:border-blue-300'"
        >
          {{ suggestion }}
        </button>
      </div>
    </div>
    
    <!-- Main Input Form -->
    <form @submit.prevent="handleSubmit" class="relative">
      <div class="flex items-end space-x-3">
        <!-- Text Input -->
        <div class="flex-1 relative">
          <textarea
            ref="textareaRef"
            v-model="inputText"
            :placeholder="placeholder"
            :disabled="isLoading"
            @keydown="handleKeydown"
            @input="handleInput"
            @focus="showSuggestions = true"
            class="w-full px-5 py-4 pr-12 border-2 rounded-3xl focus:outline-none focus:ring-2 disabled:cursor-not-allowed transition-all duration-300 resize-none overflow-hidden glass hover:shadow-lg" 
            :class="[
              isDark 
                ? 'bg-gray-800/90 border-gray-600 text-gray-100 placeholder-gray-400 focus:ring-blue-400 focus:border-blue-400 disabled:bg-gray-900/50' 
                : 'bg-white border-gray-200 text-gray-800 placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50',
              {
                'animate-pulse-glow': isLoading,
                'border-red-300 focus:border-red-500 focus:ring-red-500': error
              }
            ]"
            maxlength="500"
            rows="1"
          ></textarea>
          
          <!-- Character count -->
          <div class="absolute bottom-2 right-3 text-xs" 
               :class="inputText.length > 450 ? 'text-red-500' : (isDark ? 'text-gray-400' : 'text-gray-500')">
            {{ inputText.length }}/500
          </div>
        </div>

        <!-- Voice Input Button -->
        <button
          v-if="supportsVoiceInput"
          type="button"
          @click="toggleVoiceInput"
          :disabled="isLoading"
          class="p-3 rounded-full transition-all duration-300 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
          :class="isListening 
            ? 'bg-red-500 text-white shadow-lg animate-pulse' 
            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
          title="Entrada por voz"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
          </svg>
        </button>

        <!-- Send Button -->
        <button
          type="submit"
          :disabled="!inputText.trim() || isLoading"
          class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-full hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105 hover:shadow-lg"
        >
          <svg v-if="!isLoading" class="w-5 h-5 transform rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
          </svg>
          <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </button>
      </div>
    </form>

    <!-- Voice Input Status -->
    <div v-if="isListening" class="mt-3 flex items-center justify-center space-x-2 text-red-600 animate-fade-in-up">
      <div class="w-2 h-2 bg-red-500 rounded-full animate-bounce"></div>
      <span class="text-sm font-medium">Escuchando... Habla ahora</span>
      <div class="w-2 h-2 bg-red-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick, onMounted, onUnmounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useThemeStore } from '../../../stores/theme.store'

interface Props {
  isLoading: boolean
  error: string | null
}

interface Emits {
  (e: 'sendMessage', text: string): void
  (e: 'clearError'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// Theme store
const themeStore = useThemeStore()
const { isDark } = storeToRefs(themeStore)

const inputText = ref('')
const textareaRef = ref<HTMLTextAreaElement>()
const showSuggestions = ref(false)
const isListening = ref(false)
const recognition = ref<SpeechRecognition | null>(null)

// Quick suggestions for weather queries
const suggestions = ref([
  '¿Cómo está el clima hoy?',
  'Temperatura en Madrid',
  'Pronóstico para mañana',
  '¿Va a llover?',
  'Clima en Barcelona',
  'Humedad actual'
])

const placeholder = computed(() => {
  const placeholders = [
    'Pregúntame sobre el clima...',
    '¿Qué tiempo hace en tu ciudad?',
    'Escribe tu consulta meteorológica...',
    '¿Necesitas el pronóstico del tiempo?'
  ]
  return placeholders[Math.floor(Math.random() * placeholders.length)]
})

const supportsVoiceInput = computed(() => {
  return 'webkitSpeechRecognition' in window || 'SpeechRecognition' in window
})

const handleSubmit = () => {
  const text = inputText.value.trim()
  if (text) {
    emit('sendMessage', text)
    inputText.value = ''
    showSuggestions.value = false
    autoResize()
  }
}

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault()
    handleSubmit()
  }
}

const handleInput = () => {
  autoResize()
  // Hide suggestions when user starts typing
  if (inputText.value.length > 0) {
    showSuggestions.value = false
  }
}

const autoResize = async () => {
  await nextTick()
  if (textareaRef.value) {
    textareaRef.value.style.height = 'auto'
    const maxHeight = 120 // Max 3 lines approximately
    const newHeight = Math.min(textareaRef.value.scrollHeight, maxHeight)
    textareaRef.value.style.height = newHeight + 'px'
  }
}

const selectSuggestion = (suggestion: string) => {
  inputText.value = suggestion
  showSuggestions.value = false
  textareaRef.value?.focus()
  autoResize()
}

const initVoiceRecognition = () => {
  if (!supportsVoiceInput.value) return

  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
  recognition.value = new SpeechRecognition()
  
  recognition.value.lang = 'es-ES'
  recognition.value.continuous = false
  recognition.value.interimResults = false

  recognition.value.onstart = () => {
    isListening.value = true
  }

  recognition.value.onresult = (event) => {
    const transcript = event.results[0][0].transcript
    inputText.value = transcript
    autoResize()
  }

  recognition.value.onend = () => {
    isListening.value = false
  }

  recognition.value.onerror = (event) => {
    console.error('Speech recognition error:', event.error)
    isListening.value = false
  }
}

const toggleVoiceInput = () => {
  if (!recognition.value) return

  if (isListening.value) {
    recognition.value.stop()
  } else {
    recognition.value.start()
  }
}

// Hide suggestions when clicking outside
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement
  if (!target.closest('.message-input-container')) {
    showSuggestions.value = false
  }
}

onMounted(() => {
  initVoiceRecognition()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  if (recognition.value) {
    recognition.value.abort()
  }
})
</script>

<!-- # cGFuZ29saW4= -->
