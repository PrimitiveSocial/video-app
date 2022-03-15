<template>
    <div class="w-full h-screen flex items-center justify-center">
        <button
            v-if="twilioAccessToken"
            type="button"
            class="inline-flex items-center mx-2 px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            Join meeting using Twilio
        </button>

        <button
            type="button"
            class="inline-flex items-center mx-2 px-6 py-3 border border-transparent text-base font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
        >
            Join meeting using Agora
        </button>
    </div>
</template>

<script setup>
    import { ref, onMounted } from "vue"

    const twilioAccessToken = ref('')

    const getTwilioAccessToken = () => {
        axios.post('access-token/twilio').then( response => {
            twilioAccessToken.value = response.data
        })
    }

    onMounted( () => {
        getTwilioAccessToken()
    })
</script>
