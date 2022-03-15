require('./bootstrap');
import { createApp } from 'vue'
import Homepage from './components/Homepage'

const app = createApp({})
app.component('homepage', Homepage)
app.mount('#app')
