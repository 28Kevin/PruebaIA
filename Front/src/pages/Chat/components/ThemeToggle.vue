<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="p-2 rounded-full transition-all duration-300 hover:scale-105"
      :class="isDark 
        ? 'bg-gray-700 text-yellow-400 hover:bg-gray-600' 
        : 'bg-white/20 text-white hover:bg-white/30'"
      title="Cambiar tema"
    >
      <svg v-if="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
      </svg>
      <svg v-else-if="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
      </svg>
      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <div
      v-if="showDropdown"
      class="absolute right-0 top-full mt-2 w-48 rounded-2xl shadow-xl border backdrop-blur-sm z-50 animate-fade-in-up"
      :class="isDark 
        ? 'bg-gray-800/95 border-gray-700' 
        : 'bg-white/95 border-gray-200'"
    >
      <div class="p-2">
        <button
          v-for="option in themeOptions"
          :key="option.value"
          @click="selectTheme(option.value)"
          class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl transition-all duration-200 hover:scale-105"
          :class="[
            theme === option.value 
              ? isDark 
                ? 'bg-blue-600 text-white' 
                : 'bg-blue-500 text-white'
              : isDark
                ? 'text-gray-300 hover:bg-gray-700'
                : 'text-gray-700 hover:bg-gray-100'
          ]"
        >
          <component :is="option.icon" class="w-4 h-4" />
          <span class="text-sm font-medium">{{ option.label }}</span>
          <div v-if="theme === option.value" class="ml-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useThemeStore, type Theme } from '../../../stores/theme.store'

const themeStore = useThemeStore()
const { theme, isDark } = storeToRefs(themeStore)

const showDropdown = ref(false)

const themeOptions = [
  {
    value: 'light' as Theme,
    label: 'Claro',
    icon: 'svg'
  },
  {
    value: 'dark' as Theme,
    label: 'Oscuro',
    icon: 'svg'
  },
  {
    value: 'auto' as Theme,
    label: 'Automático',
    icon: 'svg'
  }
]

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

const selectTheme = (newTheme: Theme) => {
  themeStore.setTheme(newTheme)
  showDropdown.value = false
}

const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement
  if (!target.closest('.theme-toggle')) {
    showDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<!-- # cGFuZ29saW4= -->
