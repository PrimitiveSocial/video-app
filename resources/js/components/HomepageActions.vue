<template>
    <div class="w-full h-screen flex items-center justify-center">
        <button
            type="button"
            v-if="twilioAccessToken"
            @click="emit('twilio-connected')"
            class="inline-flex items-center mx-2 px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            Join meeting using Twilio
        </button>

        <button
            type="button"
            @click="emit('agora-connected')"
            class="inline-flex items-center mx-2 px-6 py-3 border border-transparent text-base font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
        >
            Join meeting using Agora
        </button>
    </div>
</template>

<script setup>
import { defineEmits, inject } from "vue";

const twilioAccessToken = inject('twilioAccessToken')
const emit = defineEmits(['twilio-connected', 'agora-connected'])
</script>
