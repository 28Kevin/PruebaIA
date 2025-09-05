import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import routes from '~pages'
import App from './App.vue'
import './style.css'

const app = createApp(App)

// Setup Pinia
const pinia = createPinia()
app.use(pinia)

// Setup Router with file-based routing
const router = createRouter({
  history: createWebHistory(),
  routes
})

app.use(router)
app.mount('#app')
