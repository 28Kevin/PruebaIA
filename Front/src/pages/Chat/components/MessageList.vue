<template>
  <div 
    ref="messagesContainer"
    class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar"
  >
    <div v-if="messages.length === 0" class="flex items-center justify-center h-full">
      <div class="text-center max-w-md mx-auto animate-fade-in-up">
        <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl transition-all duration-300 hover:scale-105" 
             :class="isDark 
               ? 'bg-gradient-to-br from-blue-600 to-indigo-700' 
               : 'bg-gradient-to-br from-blue-500 to-indigo-600'">
          <span class="text-4xl animate-bounce-subtle">🌤️</span>
        </div>
        <h2 class="text-3xl font-bold mb-3 transition-colors duration-300" 
            :class="isDark ? 'text-white' : 'text-gray-800'">
          ¡Hola! Soy MeteoBot
        </h2>
        <p class="mb-6 transition-colors duration-300" 
           :class="isDark ? 'text-gray-300' : 'text-gray-600'">
          Tu asistente inteligente especializado en clima y meteorología
        </p>
        <div class="rounded-2xl p-6 border backdrop-blur-sm transition-all duration-300" 
             :class="isDark 
               ? 'bg-gray-800/50 border-gray-700' 
               : 'bg-blue-50/80 border-blue-200'">
          <p class="text-sm font-semibold mb-4 transition-colors duration-300" 
             :class="isDark ? 'text-blue-400' : 'text-blue-700'">
            Puedes preguntarme sobre:
          </p>
          <div class="text-sm space-y-2 transition-colors duration-300" 
               :class="isDark ? 'text-gray-300' : 'text-blue-600'">
            <div class="flex items-center space-x-2">
              <span>☀️</span>
              <span>Temperatura actual de cualquier ciudad</span>
            </div>
            <div class="flex items-center space-x-2">
              <span>🌧️</span>
              <span>Pronóstico del tiempo</span>
            </div>
            <div class="flex items-center space-x-2">
              <span>💨</span>
              <span>Condiciones de viento y humedad</span>
            </div>
            <div class="flex items-center space-x-2">
              <span>🌡️</span>
              <span>Recomendaciones según el clima</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <MessageBubble 
      v-for="message in messages" 
      :key="message.id" 
      :message="message" 
    />
    
    <!-- Sending indicator while message is being sent -->
    <div v-if="isSending" class="flex justify-start mb-2 animate-fade-in-up">
      <div class="rounded-3xl rounded-bl-lg px-5 py-4 max-w-xs shadow-lg backdrop-blur-sm transition-all duration-300" 
           :class="isDark 
             ? 'bg-gray-700/80 text-gray-200' 
             : 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white'">
        <div class="flex items-center space-x-3">
          <div class="flex space-x-1">
            <div class="w-2 h-2 rounded-full animate-bounce" 
                 :class="isDark ? 'bg-gray-300' : 'bg-white'"></div>
            <div class="w-2 h-2 rounded-full animate-bounce" 
                 :class="isDark ? 'bg-gray-300' : 'bg-white'" 
                 style="animation-delay: 150ms"></div>
            <div class="w-2 h-2 rounded-full animate-bounce" 
                 :class="isDark ? 'bg-gray-300' : 'bg-white'" 
                 style="animation-delay: 300ms"></div>
          </div>
          <span class="text-sm font-medium">Enviando mensaje...</span>
        </div>
      </div>
    </div>
    
    <!-- Typing indicator when AI is responding -->
    <div v-if="isTyping" class="flex justify-start mb-2 animate-fade-in-up">
      <div class="rounded-3xl rounded-bl-lg px-5 py-4 max-w-xs shadow-lg backdrop-blur-sm border transition-all duration-300" 
           :class="isDark 
             ? 'bg-gray-800/80 border-gray-700 text-gray-200' 
             : 'bg-white/95 border-gray-200 text-gray-800'">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 transition-all duration-300" 
               :class="isDark 
                 ? 'bg-gradient-to-br from-blue-600 to-indigo-700' 
                 : 'bg-gradient-to-br from-blue-500 to-indigo-600'">
            <span class="text-lg animate-bounce-subtle">🤖</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="flex space-x-1">
              <div class="w-2 h-2 rounded-full animate-bounce" 
                   :class="isDark ? 'bg-gray-400' : 'bg-gray-400'" 
                   style="animation-delay: 0ms"></div>
              <div class="w-2 h-2 rounded-full animate-bounce" 
                   :class="isDark ? 'bg-gray-400' : 'bg-gray-400'" 
                   style="animation-delay: 150ms"></div>
              <div class="w-2 h-2 rounded-full animate-bounce" 
                   :class="isDark ? 'bg-gray-400' : 'bg-gray-400'" 
                   style="animation-delay: 300ms"></div>
            </div>
            <span class="text-sm font-medium">MeteoBot está escribiendo...</span>
          </div>
        </div>
      </div>
    </div>
    
    <LoadingIndicator v-if="isLoading" />
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick, watch } from 'vue'
import { storeToRefs } from 'pinia'
import type { Message } from '../interfaces/chat.interface'
import { useThemeStore } from '../../../stores/theme.store'
import MessageBubble from './MessageBubble.vue'
import LoadingIndicator from './LoadingIndicator.vue'

const themeStore = useThemeStore()
const { isDark } = storeToRefs(themeStore)

interface Props {
  messages: Message[]
  isLoading: boolean
  isSending: boolean
  isTyping: boolean
}

const props = defineProps<Props>()

const messagesContainer = ref<HTMLElement>()

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

// Watch for new messages and scroll to bottom
watch(() => props.messages.length, scrollToBottom)
watch(() => props.isLoading, scrollToBottom)
watch(() => props.isSending, scrollToBottom)
watch(() => props.isTyping, scrollToBottom)
</script>

<!-- # cGFuZ29saW4= -->
