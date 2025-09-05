import type { ApiResponse, Conversation, Message } from '../interfaces/chat.interface'

const API_BASE_URL = 'http://127.0.0.1:8000/api'

class ApiService {
  private async request<T>(
    endpoint: string,
    options: RequestInit = {}
  ): Promise<ApiResponse<T>> {
    try {
      const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          ...options.headers,
        },
        ...options,
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.message || `HTTP error! status: ${response.status}`)
      }

      return data
    } catch (error) {
      console.error('API request failed:', error)
      throw error
    }
  }

  async createConversation(title?: string): Promise<ApiResponse<Conversation>> {
    return this.request<Conversation>('/conversations', {
      method: 'POST',
      body: JSON.stringify({ title }),
    })
  }

  async getConversation(conversationId: number): Promise<ApiResponse<Conversation>> {
    return this.request<Conversation>(`/conversations/${conversationId}`)
  }

  async sendMessage(
    conversationId: number,
    content: string
  ): Promise<ApiResponse<Message>> {
    return this.request<Message>(`/conversations/${conversationId}/messages`, {
      method: 'POST',
      body: JSON.stringify({ content }),
    })
  }

  async healthCheck(): Promise<ApiResponse<any>> {
    return this.request<any>('/health')
  }
}

export const apiService = new ApiService()
