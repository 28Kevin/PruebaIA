<template>
  <div class="rounded-2xl p-5 border backdrop-blur-sm transition-all duration-300 hover:shadow-lg"
       :class="isDark 
         ? 'bg-gradient-to-br from-gray-800/80 to-blue-900/80 border-gray-700/50' 
         : 'bg-gradient-to-br from-blue-50/80 to-indigo-100/80 border-blue-200/50'">
    
    <!-- Weather Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-2">
        <div class="w-8 h-8 rounded-full flex items-center justify-center"
             :class="isDark ? 'bg-blue-600/30' : 'bg-blue-500/20'">
          <span class="text-lg">{{ getMainWeatherEmoji() }}</span>
        </div>
        <h3 class="font-semibold text-sm"
            :class="isDark ? 'text-blue-300' : 'text-blue-700'">
          Datos del Clima
        </h3>
      </div>
      <div class="text-xs opacity-70"
           :class="isDark ? 'text-gray-400' : 'text-blue-600'">
        Tiempo real
      </div>
    </div>

    <!-- Weather Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
      <!-- Temperature -->
      <div v-if="weatherData.temperature" class="text-center p-3 rounded-xl transition-all duration-200 hover:scale-105"
           :class="isDark ? 'bg-gray-700/50' : 'bg-white/60'">
        <div class="text-2xl mb-2 animate-bounce-subtle">🌡️</div>
        <div class="text-xl font-bold mb-1"
             :class="getTemperatureColor(weatherData.temperature)">
          {{ weatherData.temperature }}°C
        </div>
        <div class="text-xs font-medium opacity-70"
             :class="isDark ? 'text-gray-400' : 'text-blue-600'">
          Temperatura
        </div>
      </div>
      
      <!-- Humidity -->
      <div v-if="weatherData.humidity" class="text-center p-3 rounded-xl transition-all duration-200 hover:scale-105"
           :class="isDark ? 'bg-gray-700/50' : 'bg-white/60'">
        <div class="text-2xl mb-2 animate-bounce-subtle">💧</div>
        <div class="text-xl font-bold mb-1"
             :class="getHumidityColor(weatherData.humidity)">
          {{ weatherData.humidity }}%
        </div>
        <div class="text-xs font-medium opacity-70"
             :class="isDark ? 'text-gray-400' : 'text-blue-600'">
          Humedad
        </div>
      </div>
      
      <!-- Wind Speed -->
      <div v-if="weatherData.windSpeed" class="text-center p-3 rounded-xl transition-all duration-200 hover:scale-105"
           :class="isDark ? 'bg-gray-700/50' : 'bg-white/60'">
        <div class="text-2xl mb-2 animate-bounce-subtle">💨</div>
        <div class="text-xl font-bold mb-1"
             :class="getWindColor(weatherData.windSpeed)">
          {{ weatherData.windSpeed }}
        </div>
        <div class="text-xs font-medium opacity-70"
             :class="isDark ? 'text-gray-400' : 'text-blue-600'">
          km/h
        </div>
      </div>
    </div>
    
    <!-- Weather Condition Summary -->
    <div v-if="getWeatherCondition()" class="pt-4 border-t transition-colors duration-300"
         :class="isDark ? 'border-gray-700/50' : 'border-blue-200/50'">
      <div class="flex items-center justify-center space-x-3 p-3 rounded-xl"
           :class="isDark ? 'bg-gray-700/30' : 'bg-white/40'">
        <span class="text-2xl animate-pulse">{{ getWeatherEmoji() }}</span>
        <div class="text-center">
          <div class="text-sm font-semibold"
               :class="isDark ? 'text-blue-300' : 'text-blue-700'">
            {{ getWeatherCondition() }}
          </div>
          <div class="text-xs opacity-70"
               :class="isDark ? 'text-gray-400' : 'text-blue-600'">
            Condición actual
          </div>
        </div>
      </div>
    </div>

    <!-- Weather Tips -->
    <div v-if="getWeatherTip()" class="mt-4 p-3 rounded-xl"
         :class="isDark ? 'bg-blue-900/30' : 'bg-blue-50/60'">
      <div class="flex items-start space-x-2">
        <span class="text-sm">💡</span>
        <p class="text-xs leading-relaxed"
           :class="isDark ? 'text-blue-300' : 'text-blue-700'">
          {{ getWeatherTip() }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { useThemeStore } from '../../../stores/theme.store'

const themeStore = useThemeStore()
const { isDark } = storeToRefs(themeStore)

interface WeatherData {
  temperature?: string | null
  humidity?: string | null
  windSpeed?: string | null
}

interface Props {
  weatherData: WeatherData
}

const props = defineProps<Props>()

const getWeatherCondition = (): string => {
  const temp = parseInt(props.weatherData.temperature || '0')
  const humidity = parseInt(props.weatherData.humidity || '0')
  const wind = parseInt(props.weatherData.windSpeed || '0')
  
  if (temp > 35) return 'Muy Caluroso'
  if (temp > 28) return 'Caluroso'
  if (temp < 5) return 'Muy Frío'
  if (temp < 15) return 'Frío'
  if (humidity > 85) return 'Muy Húmedo'
  if (humidity > 70) return 'Húmedo'
  if (humidity < 25) return 'Muy Seco'
  if (humidity < 40) return 'Seco'
  if (wind > 25) return 'Ventoso'
  return 'Agradable'
}

const getWeatherEmoji = (): string => {
  const condition = getWeatherCondition()
  const emojiMap: Record<string, string> = {
    'Muy Caluroso': '🔥',
    'Caluroso': '☀️',
    'Muy Frío': '❄️',
    'Frío': '🥶',
    'Muy Húmedo': '🌧️',
    'Húmedo': '🌫️',
    'Muy Seco': '🌵',
    'Seco': '🌟',
    'Ventoso': '💨',
    'Agradable': '😊'
  }
  return emojiMap[condition] || '🌤️'
}

const getMainWeatherEmoji = (): string => {
  const temp = parseInt(props.weatherData.temperature || '20')
  if (temp > 25) return '☀️'
  if (temp < 10) return '❄️'
  return '🌤️'
}

const getTemperatureColor = (temp: string): string => {
  const temperature = parseInt(temp)
  if (temperature > 30) return isDark.value ? 'text-red-400' : 'text-red-600'
  if (temperature > 20) return isDark.value ? 'text-orange-400' : 'text-orange-600'
  if (temperature > 10) return isDark.value ? 'text-blue-400' : 'text-blue-600'
  return isDark.value ? 'text-cyan-400' : 'text-cyan-600'
}

const getHumidityColor = (humidity: string): string => {
  const humidityValue = parseInt(humidity)
  if (humidityValue > 70) return isDark.value ? 'text-blue-400' : 'text-blue-600'
  if (humidityValue > 40) return isDark.value ? 'text-green-400' : 'text-green-600'
  return isDark.value ? 'text-yellow-400' : 'text-yellow-600'
}

const getWindColor = (wind: string): string => {
  const windValue = parseInt(wind)
  if (windValue > 20) return isDark.value ? 'text-red-400' : 'text-red-600'
  if (windValue > 10) return isDark.value ? 'text-orange-400' : 'text-orange-600'
  return isDark.value ? 'text-green-400' : 'text-green-600'
}

const getWeatherTip = (): string => {
  const temp = parseInt(props.weatherData.temperature || '20')
  const humidity = parseInt(props.weatherData.humidity || '50')
  const wind = parseInt(props.weatherData.windSpeed || '5')
  
  if (temp > 35) return 'Temperatura muy alta. Mantente hidratado y busca sombra.'
  if (temp < 5) return 'Temperatura muy baja. Abrígate bien antes de salir.'
  if (humidity > 85) return 'Humedad muy alta. Puede sentirse bochornoso.'
  if (humidity < 25) return 'Aire muy seco. Considera usar humidificador.'
  if (wind > 25) return 'Viento fuerte. Ten cuidado al caminar o conducir.'
  if (temp >= 20 && temp <= 25 && humidity >= 40 && humidity <= 60) {
    return 'Condiciones perfectas para actividades al aire libre.'
  }
  return ''
}
</script>

<!-- # cGFuZ29saW4= -->
