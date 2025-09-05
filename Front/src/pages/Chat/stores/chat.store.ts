import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import type { Message, Conversation } from '../interfaces/chat.interface'
import { apiService } from '../services/api.service'

export const useChatStore = defineStore('chat', () => {
  // State
  const conversation = ref<Conversation | null>(null)
  const conversations = ref<Conversation[]>([])
  const messages = ref<Message[]>([])
  const isLoading = ref<boolean>(false)
  const isSending = ref<boolean>(false)
  const isTyping = ref<boolean>(false)
  const error = ref<string | null>(null)
  const showSidebar = ref<boolean>(true)

  // Actions
  const initializeConversation = async (): Promise<void> => {
    try {
      error.value = null
      const response = await apiService.createConversation('Nueva conversación de clima')
      
      if (response.success && response.data) {
        conversation.value = response.data
      } else {
        throw new Error(response.error || 'Error al crear la conversación')
      }
    } catch (err) {
      error.value = 'Error al inicializar la conversación. Por favor, recarga la página.'
      console.error('Error initializing conversation:', err)
    }
  }

  const sendMessage = async (content: string): Promise<void> => {
    try {
      // Initialize conversation if not exists
      if (!conversation.value) {
        await initializeConversation()
        if (!conversation.value) return
      }

      // Clear any previous errors
      error.value = null
      
      // 1. Add user message immediately to UI
      const userMessage: Message = {
        id: Date.now(), // Temporary ID
        content,
        sender_type: 'user',
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
        conversation_id: conversation.value.id,
        is_from_user: true,
        is_from_assistant: false
      }
      messages.value.push(userMessage)

      // 2. Show "Enviando..." while making API request
      isSending.value = true

      // 3. Send message to API
      const response = await apiService.sendMessage(conversation.value.id, content)
      
      if (response.success && response.data) {
        // 4. Hide "Enviando..." and show "Escribiendo..." for AI response
        isSending.value = false
        isTyping.value = true
        
        // 5. Refresh conversation to get all messages with correct IDs
        await loadConversation(conversation.value.id)
        
        // 6. Hide "Escribiendo..." after loading
        isTyping.value = false
      } else {
        throw new Error(response.error || 'Error al enviar el mensaje')
      }

    } catch (err) {
      error.value = 'Error al enviar el mensaje. Por favor, inténtalo de nuevo.'
      console.error('Error sending message:', err)
      isSending.value = false
      isTyping.value = false
    } finally {
      isLoading.value = false
    }
  }

  const loadConversation = async (conversationId: number): Promise<void> => {
    try {
      const response = await apiService.getConversation(conversationId)
      
      if (response.success && response.data) {
        conversation.value = response.data
        messages.value = response.data.messages || []
      } else {
        throw new Error(response.error || 'Error al cargar la conversación')
      }
    } catch (err) {
      error.value = 'Error al cargar la conversación.'
      console.error('Error loading conversation:', err)
    }
  }

  const clearMessages = (): void => {
    messages.value = []
    conversation.value = null
    error.value = null
  }

  const clearError = (): void => {
    error.value = null
  }

  const loadConversations = async (): Promise<void> => {
    try {
      // This would typically fetch from an API endpoint
      // For now, we'll use localStorage to persist conversations
      const saved = localStorage.getItem('meteobot-conversations')
      if (saved) {
        conversations.value = JSON.parse(saved)
      }
    } catch (err) {
      console.error('Error loading conversations:', err)
    }
  }

  const saveConversations = (): void => {
    try {
      localStorage.setItem('meteobot-conversations', JSON.stringify(conversations.value))
    } catch (err) {
      console.error('Error saving conversations:', err)
    }
  }

  const selectConversation = async (conversationId: number): Promise<void> => {
    try {
      await loadConversation(conversationId)
    } catch (err) {
      console.error('Error selecting conversation:', err)
    }
  }

  const createNewConversation = async (): Promise<void> => {
    try {
      // Clear current state
      messages.value = []
      conversation.value = null
      error.value = null
      
      // Initialize new conversation
      await initializeConversation()
    } catch (err) {
      console.error('Error creating new conversation:', err)
    }
  }

  const deleteConversation = async (conversationId: number): Promise<void> => {
    try {
      // Remove from local conversations list
      conversations.value = conversations.value.filter(c => c.id !== conversationId)
      saveConversations()
      
      // If deleting current conversation, create a new one
      if (conversation.value?.id === conversationId) {
        await createNewConversation()
      }
    } catch (err) {
      console.error('Error deleting conversation:', err)
    }
  }

  const exportConversations = (): void => {
    try {
      const dataStr = JSON.stringify(conversations.value, null, 2)
      const dataBlob = new Blob([dataStr], { type: 'application/json' })
      const url = URL.createObjectURL(dataBlob)
      const link = document.createElement('a')
      link.href = url
      link.download = `meteobot-conversations-${new Date().toISOString().split('T')[0]}.json`
      link.click()
      URL.revokeObjectURL(url)
    } catch (err) {
      console.error('Error exporting conversations:', err)
    }
  }

  const toggleSidebar = (): void => {
    showSidebar.value = !showSidebar.value
  }

  // Update conversations list when conversation changes
  watch(conversation, (newConv: Conversation | null) => {
    if (newConv && !conversations.value.find(c => c.id === newConv.id)) {
      conversations.value.unshift(newConv)
      saveConversations()
    }
  })

  // Initialize on store creation
  loadConversations()

  return {
    // State
    conversation,
    conversations,
    messages,
    isLoading,
    isSending,
    isTyping,
    error,
    showSidebar,
    // Actions
    sendMessage,
    clearMessages,
    clearError,
    initializeConversation,
    loadConversation,
    loadConversations,
    selectConversation,
    createNewConversation,
    deleteConversation,
    exportConversations,
    toggleSidebar
  }
})
