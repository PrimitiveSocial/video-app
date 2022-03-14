require('./bootstrap');
import { createApp } from 'vue'
import Twilio from './components/Twilio'

const app = createApp({})
app.component('twilio', Twilio)
app.mount('#app')
