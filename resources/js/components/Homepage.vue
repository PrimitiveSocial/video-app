<template>
    <div class="w-full h-screen">
       <homepage-actions
            @twilio-connected="showTwilioVideoChat = true"
            v-if="!showTwilioVideoChat"
       ></homepage-actions>

        <twilio v-if="showTwilioVideoChat"></twilio>
    </div>
</template>

<script setup>
    import { ref, onMounted, provide } from "vue"

    import HomepageActions from "./HomepageActions"
    import Twilio from "./Twilio"

    const twilioAccessToken = ref('')
    const showTwilioVideoChat = ref(false)

    const getTwilioAccessToken = () => {
        axios.post('access-token/twilio').then( response => {
            twilioAccessToken.value = response.data
        })
    }

    provide('twilioAccessToken', twilioAccessToken)

    onMounted( () => {
        getTwilioAccessToken()
    })
</script>
