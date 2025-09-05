<template>
  <div class="flex h-screen transition-colors duration-300" 
       :class="isDark 
         ? 'bg-gradient-to-br from-gray-900 via-gray-800 to-blue-900' 
         : 'bg-gradient-to-br from-blue-50 via-white to-indigo-50'">
    
    <!-- Conversation Sidebar -->
    <ConversationSidebar
      v-if="showSidebar"
      :conversations="conversations"
      :current-conversation-id="conversation?.id || null"
      @select-conversation="handleSelectConversation"
      @create-conversation="handleCreateConversation"
      @delete-conversation="handleDeleteConversation"
      @export-conversations="handleExportConversations"
      class="flex-shrink-0"
    />
    
    <!-- Main Chat Area -->
    <div class="flex flex-col flex-1 min-w-0">
      <!-- Header -->
      <header class="shadow-lg transition-colors duration-300 safe-area-top" 
              :class="isDark 
                ? 'bg-gradient-to-r from-gray-800 to-gray-900 text-white' 
                : 'bg-gradient-to-r from-blue-600 to-indigo-700 text-white'">
        <div class="flex items-center justify-between max-w-6xl mx-auto mobile-header">
          <div class="flex items-center space-x-2 md:space-x-4">
            <!-- Sidebar Toggle -->
            <button
              @click="handleToggleSidebar"
              class="p-2 rounded-lg transition-all duration-200 hover:scale-105 touch-button"
              :class="isDark 
                ? 'hover:bg-gray-700 text-gray-300' 
                : 'hover:bg-white/20 text-white'"
              title="Toggle sidebar"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
            </button>
            
            <div class="w-10 h-10 md:w-12 md:h-12 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-105" 
                 :class="isDark ? 'bg-blue-600/30' : 'bg-white/20'">
              <span class="text-2xl md:text-3xl animate-bounce-subtle">🤖</span>
            </div>
            <div class="min-w-0">
              <h1 class="text-lg md:text-2xl font-bold tracking-tight truncate">MeteoBot</h1>
              <p class="text-xs md:text-sm opacity-90 hidden sm:block">Tu asistente inteligente del clima</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-2 md:space-x-3">
            <ThemeToggle class="theme-toggle" />
            <button
              @click="handleClearMessages"
              class="px-2 py-2 md:px-4 md:py-2 rounded-xl text-xs md:text-sm font-medium transition-all duration-200 backdrop-blur-sm border disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105 touch-button"
              :class="isDark 
                ? 'bg-gray-700/50 hover:bg-gray-600/50 border-gray-600 text-gray-200' 
                : 'bg-white/10 hover:bg-white/20 border-white/20 text-white'"
              :disabled="messages.length === 0"
            >
              <span class="hidden sm:inline">🗑️ Limpiar Chat</span>
              <span class="sm:hidden">🗑️</span>
            </button>
          </div>
        </div>
      </header>

      <!-- Messages Area -->
      <div class="flex-1 max-w-6xl mx-auto w-full custom-scrollbar mobile-optimized">
        <MessageList 
          :messages="messages" 
          :is-loading="isLoading"
          :is-sending="isSending"
          :is-typing="isTyping"
        />
      </div>

      <!-- Input Area -->
      <div class="max-w-6xl mx-auto w-full safe-area-bottom">
        <MessageInput
          :is-loading="isLoading"
          :error="error"
          @send-message="handleSendMessage"
          @clear-error="handleClearError"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useChatStore } from './stores/chat.store'
import { useThemeStore } from '../../stores/theme.store'
import MessageList from './components/MessageList.vue'
import MessageInput from './components/MessageInput.vue'
import ThemeToggle from './components/ThemeToggle.vue'
import ConversationSidebar from './components/ConversationSidebar.vue'

// Use the stores
const chatStore = useChatStore()
const themeStore = useThemeStore()

// Extract reactive state from stores
const { 
  conversation, 
  conversations, 
  messages, 
  isLoading, 
  isSending, 
  isTyping, 
  error, 
  showSidebar 
} = storeToRefs(chatStore)
const { isDark } = storeToRefs(themeStore)

// Initialize theme on mount
onMounted(() => {
  themeStore.initializeTheme()
})

// Event handlers
const handleSendMessage = async (text: string) => {
  await chatStore.sendMessage(text)
}

const handleClearError = () => {
  chatStore.clearError()
}

const handleClearMessages = () => {
  chatStore.clearMessages()
}

const handleSelectConversation = (conversationId: number) => {
  chatStore.selectConversation(conversationId)
}

const handleCreateConversation = () => {
  chatStore.createNewConversation()
}

const handleDeleteConversation = (conversationId: number) => {
  chatStore.deleteConversation(conversationId)
}

const handleExportConversations = () => {
  chatStore.exportConversations()
}

const handleToggleSidebar = () => {
  chatStore.toggleSidebar()
}
</script>

<!-- # cGFuZ29saW4= -->
