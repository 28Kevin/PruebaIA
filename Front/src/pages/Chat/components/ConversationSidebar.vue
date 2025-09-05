<template>
  <div class="flex flex-col h-full transition-all duration-300"
       :class="[
         'w-80 border-r backdrop-blur-sm',
         isDark 
           ? 'bg-gray-900/95 border-gray-700' 
           : 'bg-white/95 border-gray-200',
         isCollapsed ? 'w-16' : 'w-80'
       ]">
    
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between p-4 border-b"
         :class="isDark ? 'border-gray-700' : 'border-gray-200'">
      <div v-if="!isCollapsed" class="flex items-center space-x-2">
        <div class="w-8 h-8 rounded-full flex items-center justify-center"
             :class="isDark ? 'bg-blue-600' : 'bg-blue-500'">
          <span class="text-white text-sm">💬</span>
        </div>
        <h2 class="font-semibold text-sm"
            :class="isDark ? 'text-gray-100' : 'text-gray-800'">
          Conversaciones
        </h2>
      </div>
      
      <button
        @click="toggleSidebar"
        class="p-2 rounded-lg transition-all duration-200 hover:scale-105"
        :class="isDark 
          ? 'hover:bg-gray-700 text-gray-200' 
          : 'hover:bg-gray-100 text-gray-600'"
        :title="isCollapsed ? 'Expandir' : 'Contraer'"
      >
        <svg class="w-4 h-4 transition-transform duration-300" 
             :class="{ 'rotate-180': isCollapsed }"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
        </svg>
      </button>
    </div>

    <!-- New Conversation Button -->
    <div v-if="!isCollapsed" class="p-4">
      <button
        @click="createNewConversation"
        class="w-full flex items-center justify-center space-x-2 p-3 rounded-xl transition-all duration-200 hover:scale-105"
        :class="isDark 
          ? 'bg-blue-600 hover:bg-blue-700 text-white' 
          : 'bg-blue-500 hover:bg-blue-600 text-white'"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span class="font-medium text-sm">Nueva Conversación</span>
      </button>
    </div>

    <!-- Conversation List -->
    <div class="flex-1 overflow-y-auto custom-scrollbar">
      <div v-if="isCollapsed" class="p-2">
        <button
          @click="createNewConversation"
          class="w-full p-3 rounded-lg transition-all duration-200 hover:scale-105"
          :class="isDark 
            ? 'bg-blue-600 hover:bg-blue-700 text-white' 
            : 'bg-blue-500 hover:bg-blue-600 text-white'"
          title="Nueva Conversación"
        >
          <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
        </button>
      </div>

      <div v-if="conversations.length === 0 && !isCollapsed" 
           class="p-4 text-center">
        <div class="text-4xl mb-2">🌤️</div>
        <p class="text-sm opacity-70"
           :class="isDark ? 'text-gray-400' : 'text-gray-500'">
          No hay conversaciones aún
        </p>
      </div>

      <div v-else class="space-y-1 p-2">
        <div
          v-for="conv in conversations"
          :key="conv.id"
          @click="selectConversation(conv.id)"
          class="group relative cursor-pointer rounded-xl p-3 transition-all duration-200 hover:scale-105"
          :class="[
            currentConversationId === conv.id
              ? isDark 
                ? 'bg-blue-600/20 border border-blue-500/30' 
                : 'bg-blue-50 border border-blue-200'
              : isDark
                ? 'hover:bg-gray-800 border border-transparent'
                : 'hover:bg-gray-50 border border-transparent'
          ]"
        >
          <div v-if="!isCollapsed">
            <div class="flex items-start justify-between">
              <div class="flex-1 min-w-0">
                <h3 class="font-medium text-sm truncate"
                    :class="[
                      currentConversationId === conv.id
                        ? isDark ? 'text-blue-300' : 'text-blue-700'
                        : isDark ? 'text-white' : 'text-gray-800'
                    ]">
                  {{ conv.title || 'Nueva conversación' }}
                </h3>
                <p class="text-xs mt-1 opacity-70"
                   :class="isDark ? 'text-gray-400' : 'text-gray-500'">
                  {{ formatConversationDate(conv.created_at) }}
                </p>
                <div v-if="conv.messages && conv.messages.length > 0" 
                     class="text-xs mt-1 opacity-60 truncate"
                     :class="isDark ? 'text-gray-400' : 'text-gray-500'">
                  {{ getLastMessage(conv) }}
                </div>
              </div>
              
              <button
                @click.stop="deleteConversation(conv.id)"
                class="opacity-0 group-hover:opacity-100 p-1 rounded-full transition-all duration-200 hover:scale-110"
                :class="isDark 
                  ? 'hover:bg-red-600/20 text-red-400' 
                  : 'hover:bg-red-50 text-red-500'"
                title="Eliminar conversación"
              >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
          
          <div v-else class="flex justify-center">
            <div class="w-2 h-2 rounded-full"
                 :class="[
                   currentConversationId === conv.id
                     ? 'bg-blue-500'
                     : isDark ? 'bg-gray-600' : 'bg-gray-400'
                 ]">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar Footer -->
    <div v-if="!isCollapsed" class="p-4 border-t"
         :class="isDark ? 'border-gray-700' : 'border-gray-200'">
      <div class="flex items-center justify-between text-xs"
           :class="isDark ? 'text-gray-400' : 'text-gray-500'">
        <span>{{ conversations.length }} conversaciones</span>
        <button
          @click="exportConversations"
          class="flex items-center space-x-1 px-2 py-1 rounded-lg transition-colors duration-200"
          :class="isDark 
            ? 'hover:bg-gray-700 text-gray-400' 
            : 'hover:bg-gray-100 text-gray-500'"
          title="Exportar conversaciones"
        >
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <span>Exportar</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useThemeStore } from '../../../stores/theme.store'
import type { Conversation } from '../interfaces/chat.interface'

const themeStore = useThemeStore()
const { isDark } = storeToRefs(themeStore)

interface Props {
  conversations: Conversation[]
  currentConversationId: number | null
}

interface Emits {
  (e: 'selectConversation', id: number): void
  (e: 'createConversation'): void
  (e: 'deleteConversation', id: number): void
  (e: 'exportConversations'): void
}

defineProps<Props>()
const emit = defineEmits<Emits>()

const isCollapsed = ref(false)

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value
}

const selectConversation = (id: number) => {
  emit('selectConversation', id)
}

const createNewConversation = () => {
  emit('createConversation')
}

const deleteConversation = (id: number) => {
  if (confirm('¿Estás seguro de que quieres eliminar esta conversación?')) {
    emit('deleteConversation', id)
  }
}

const exportConversations = () => {
  emit('exportConversations')
}

const formatConversationDate = (dateString: string): string => {
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now.getTime() - date.getTime())
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 1) return 'Hoy'
  if (diffDays === 2) return 'Ayer'
  if (diffDays <= 7) return `Hace ${diffDays} días`
  
  return date.toLocaleDateString('es-ES', {
    day: 'numeric',
    month: 'short'
  })
}

const getLastMessage = (conversation: Conversation): string => {
  if (!conversation.messages || conversation.messages.length === 0) {
    return 'Sin mensajes'
  }
  
  const lastMessage = conversation.messages[conversation.messages.length - 1]
  return lastMessage.content.substring(0, 50) + (lastMessage.content.length > 50 ? '...' : '')
}
</script>

<!-- # cGFuZ29saW4= -->
