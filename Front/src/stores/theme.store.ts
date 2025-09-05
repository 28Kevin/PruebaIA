import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export type Theme = 'light' | 'dark' | 'auto'

export const useThemeStore = defineStore('theme', () => {
  // State
  const theme = ref<Theme>('light')
  const isDark = ref(false)

  // Initialize theme from localStorage or system preference
  const initializeTheme = () => {
    const savedTheme = localStorage.getItem('meteobot-theme') as Theme
    if (savedTheme && ['light', 'dark', 'auto'].includes(savedTheme)) {
      theme.value = savedTheme
    } else {
      theme.value = 'auto'
    }
    updateTheme()
  }

  // Update theme based on current setting
  const updateTheme = () => {
    let shouldBeDark = false

    if (theme.value === 'dark') {
      shouldBeDark = true
    } else if (theme.value === 'auto') {
      shouldBeDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    }

    isDark.value = shouldBeDark
    
    // Update document class
    if (shouldBeDark) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }

    // Save to localStorage
    localStorage.setItem('meteobot-theme', theme.value)
  }

  // Actions
  const setTheme = (newTheme: Theme) => {
    theme.value = newTheme
    updateTheme()
  }

  const toggleTheme = () => {
    if (theme.value === 'light') {
      setTheme('dark')
    } else if (theme.value === 'dark') {
      setTheme('light')
    } else {
      // If auto, toggle to opposite of current system preference
      const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
      setTheme(systemPrefersDark ? 'light' : 'dark')
    }
  }

  // Watch for system theme changes when in auto mode
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  mediaQuery.addEventListener('change', () => {
    if (theme.value === 'auto') {
      updateTheme()
    }
  })

  // Watch theme changes
  watch(theme, updateTheme)

  return {
    // State
    theme,
    isDark,
    // Actions
    initializeTheme,
    setTheme,
    toggleTheme
  }
})
