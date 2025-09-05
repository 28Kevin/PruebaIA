<template>
  <div 
    class="flex mb-4 animate-fade-in-up" 
    :class="message.sender_type === 'user' ? 'justify-end' : 'justify-start'"
  >
    <!-- Bot Avatar with enhanced animation -->
    <div v-if="message.sender_type === 'assistant'" class="flex-shrink-0 mr-3">
      <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
        <span class="text-lg animate-bounce-subtle">🤖</span>
      </div>
    </div>
    
    <div 
      class="max-w-xs sm:max-w-sm lg:max-w-2xl mobile-message-bubble px-4 sm:px-5 py-3 sm:py-4 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
      :class="message.sender_type === 'user' 
        ? 'bg-gradient-to-r from-blue-500 via-blue-600 to-indigo-600 text-white rounded-3xl rounded-br-lg backdrop-blur-sm' 
        : (isDark ? 'bg-gray-800/95 backdrop-blur-sm border border-gray-600/50 text-gray-100 rounded-3xl rounded-bl-lg' : 'bg-white/95 backdrop-blur-sm border border-gray-200/50 text-gray-800 rounded-3xl rounded-bl-lg')"
    >
      <!-- Assistant header with status -->
      <div v-if="message.sender_type === 'assistant'" class="flex items-center justify-between mb-3">
        <div class="flex items-center">
          <span class="text-xs font-bold tracking-wide" :class="isDark ? 'text-blue-400' : 'text-blue-600'">METEOBOT</span>
          <div class="w-2 h-2 bg-green-400 rounded-full ml-2 animate-pulse shadow-sm"></div>
        </div>
        <div v-if="getMessageCategory(message.content)" class="text-lg">
          {{ getCategoryEmoji(message.content) }}
        </div>
      </div>
      
      <!-- Message content with markdown support -->
      <div class="prose prose-sm max-w-none">
        <div 
          class="text-sm leading-relaxed" 
          :class="message.sender_type === 'user' ? 'text-white' : (isDark ? 'text-gray-100' : 'text-gray-800')"
          v-html="formatMessageContent(message.content)"
        ></div>
      </div>
      
      <!-- Weather data visualization -->
      <div v-if="isWeatherMessage(message.content) && message.sender_type === 'assistant'" class="mt-4">
        <WeatherCard :weather-data="extractWeatherData(message.content)" />
      </div>
      
      <!-- Message footer -->
      <div class="flex items-center justify-between mt-3 pt-2 border-t" 
           :class="message.sender_type === 'user' ? 'border-white/20' : (isDark ? 'border-gray-600/50' : 'border-gray-200')">
        <div class="flex items-center space-x-2">
          <p class="text-xs font-medium" 
             :class="message.sender_type === 'user' ? 'text-blue-100' : (isDark ? 'text-gray-400' : 'text-gray-500')">
            {{ formatTime(message.created_at) }}
          </p>
          <div v-if="message.sender_type === 'assistant'" class="flex items-center space-x-1">
            <div class="w-1 h-1 bg-current rounded-full opacity-50"></div>
            <span class="text-xs opacity-70">AI</span>
          </div>
        </div>
        
        <!-- Action buttons -->
        <div class="flex items-center space-x-1 sm:space-x-2">
          <button 
            v-if="message.sender_type === 'assistant'"
            @click="copyMessage"
            class="p-2 rounded-full transition-colors duration-200 group touch-button"
            :class="isDark ? 'hover:bg-gray-700' : 'hover:bg-gray-100'"
            title="Copiar mensaje"
          >
            <svg class="w-3 h-3 transition-colors duration-200" 
                 :class="isDark ? 'text-gray-400 group-hover:text-gray-200' : 'text-gray-400 group-hover:text-gray-600'" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
          </button>
          <button 
            v-if="isWeatherMessage(message.content)"
            @click="shareWeather"
            class="p-2 rounded-full transition-colors duration-200 group touch-button"
            :class="isDark ? 'hover:bg-gray-700' : 'hover:bg-gray-100'"
            title="Compartir clima"
          >
            <svg class="w-3 h-3 transition-colors duration-200" 
                 :class="isDark ? 'text-gray-400 group-hover:text-gray-200' : 'text-gray-400 group-hover:text-gray-600'" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Enhanced User Avatar -->
    <div v-if="message.sender_type === 'user'" class="flex-shrink-0 ml-3">
      <div class="w-10 h-10 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
        <span class="text-lg">👤</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// import { ref } from 'vue' // Removed unused import
import { storeToRefs } from 'pinia'
import type { Message } from '../interfaces/chat.interface'
import { useThemeStore } from '../../../stores/theme.store'
import WeatherCard from './WeatherCard.vue'

const themeStore = useThemeStore()
const { isDark } = storeToRefs(themeStore)

interface Props {
  message: Message
}

const props = defineProps<Props>()

const formatTime = (timestamp: string): string => {
  if (!timestamp) return ''
  return new Date(timestamp).toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatMessageContent = (content: string): string => {
  // Basic markdown support
  let formatted = content
    // Bold text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    // Italic text
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    // Code blocks
    .replace(/`(.*?)`/g, '<code class="bg-gray-100 px-1 py-0.5 rounded text-xs font-mono">$1</code>')
    // Line breaks
    .replace(/\n/g, '<br>')
    // Temperature highlighting
    .replace(/(\d+°[CF])/g, '<span class="font-bold text-blue-600">$1</span>')
    // Weather conditions highlighting
    .replace(/(soleado|nublado|lluvia|tormenta|nieve|viento)/gi, '<span class="font-semibold text-green-600">$1</span>')
  
  return formatted
}

const isWeatherMessage = (content: string): boolean => {
  const weatherKeywords = [
    'temperatura', 'clima', 'tiempo', 'lluvia', 'sol', 'nublado', 
    'viento', 'humedad', 'pronóstico', '°C', '°F', 'km/h', '%'
  ]
  return weatherKeywords.some(keyword => 
    content.toLowerCase().includes(keyword.toLowerCase())
  )
}

const getMessageCategory = (content: string): string | null => {
  if (content.toLowerCase().includes('temperatura')) return 'temperature'
  if (content.toLowerCase().includes('lluvia')) return 'rain'
  if (content.toLowerCase().includes('viento')) return 'wind'
  if (content.toLowerCase().includes('humedad')) return 'humidity'
  return null
}

const getCategoryEmoji = (content: string): string => {
  const category = getMessageCategory(content)
  const emojiMap: Record<string, string> = {
    temperature: '🌡️',
    rain: '🌧️',
    wind: '💨',
    humidity: '💧'
  }
  return emojiMap[category || ''] || '🌤️'
}

const extractWeatherData = (content: string) => {
  // Extract weather data from message content
  const tempMatch = content.match(/(\d+)°[CF]/)
  const humidityMatch = content.match(/(\d+)%/)
  const windMatch = content.match(/(\d+)\s*km\/h/)
  
  return {
    temperature: tempMatch ? tempMatch[1] : null,
    humidity: humidityMatch ? humidityMatch[1] : null,
    windSpeed: windMatch ? windMatch[1] : null
  }
}

const copyMessage = async () => {
  try {
    await navigator.clipboard.writeText(props.message.content)
    // You could add a toast notification here
  } catch (err) {
    console.error('Error copying message:', err)
  }
}

const shareWeather = async () => {
  if (navigator.share) {
    try {
      await navigator.share({
        title: 'Información del Clima - MeteoBot',
        text: props.message.content
      })
    } catch (err) {
      console.error('Error sharing:', err)
    }
  }
}
</script>

<!-- # cGFuZ29saW4= -->
