require('./bootstrap');
import { createApp } from 'vue'
import Twilio from './components/Twilio'
import Homepage from './components/Homepage'

const app = createApp({})
app.component('twilio', Twilio)
app.component('homepage', Homepage)
app.mount('#app')
