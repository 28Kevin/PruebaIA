export interface Message {
  id: number
  conversation_id: number
  content: string
  sender_type: 'user' | 'assistant'
  created_at: string
  updated_at: string
  is_from_user: boolean
  is_from_assistant: boolean
}

export interface Conversation {
  id: number
  title: string | null
  created_at: string
  updated_at: string
  messages?: Message[]
}

export interface ApiResponse<T> {
  success: boolean
  message?: string
  data?: T
  error?: string
}

export interface ChatState {
  conversation: Conversation | null
  messages: Message[]
  isLoading: boolean
  error: string | null
}
