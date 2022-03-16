<template>
    <div class="w-full h-screen">
       <homepage-actions
            @twilio-connected="showTwilioVideoChat = true"
            @agora-connected="showAgoraVideoChat = true"
            v-if="!showTwilioVideoChat && !showAgoraVideoChat"
       ></homepage-actions>

        <twilio v-if="showTwilioVideoChat" @disconnected="showTwilioVideoChat = false"></twilio>
        <agora v-if="showAgoraVideoChat" @disconnected="showAgoraVideoChat = false"></agora>
    </div>
</template>

<script setup>
    import { ref, onMounted, provide } from "vue"

    import HomepageActions from "./HomepageActions"
    import Twilio from "./Twilio"
    import Agora from "./Agora"

    const twilioAccessToken = ref('')
    const showTwilioVideoChat = ref(false)
    const showAgoraVideoChat = ref(false)

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
