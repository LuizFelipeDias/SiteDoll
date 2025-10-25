import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/main.css'
import axios from 'axios'

// Sanctum cookies
axios.defaults.withCredentials = true
axios.defaults.baseURL = '/'

createApp(App)
    .use(router)
    .mount('#app')
