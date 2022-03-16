<template>
    <div class="h-full bg-gradient-to-b from-slate-800 to-slate-600">

        <div class="flex justify-between p-8 space-x-8">
            <div id="local-participant-player" class="local-player"></div>
            <div id="remote-participants-list"></div>
            <!-- events log -->
            <div id="meeting-events">
                <ul class="space-y-4">
                    <li v-for="event in events" class="text-white"> {{ event }}</li>
                </ul>
            </div>
        </div>

        <div class="fixed bottom-0 w-full flex items-center justify-center space-x-4 p-4">
            <!-- ui action -->
            <button id="btn-camera-off" @click="muteVideo">hide camera</button>
            <button id="btn-camera-on" @click="unMuteVideo">enable camera</button>
            <button id="btn-leave" @click="leave">leave call</button>
        </div>

    </div>
</template>

<script setup>

// https://www.agora.io/en/products/agora-app-builder/
// https://docs.agora.io/en/Video/landing-page?platform=Web
// https://docs.agora.io/en/Agora%20Platform/agora_platform?platform=All%20Platforms
// https://docs.agora.io/en/Video/start_call_web_ng?platform=Web
// https://docs.agora.io/en/Video/faq/token_error

// https://github.com/AgoraIO/API-Examples-Web/tree/main/Demo/basicVideoCall
// https://docs.agora.io/en/Video/API%20Reference/web_ng/interfaces/iagorartcclient.html

import { onMounted, ref } from "vue"
import AgoraRTC from "agora-rtc-sdk-ng"

const rtc = {
    client: AgoraRTC.createClient({ mode: "rtc", codec: "vp8" }),
    localTracks : {
        videoTrack: null,
        audioTrack: null
    },
    remoteUsers: {}
}

const options = {
    appId: "b07e18fc47864a07bf5918c2d1fc38ab",
    channel: "personal-meeting",
    token: "006b07e18fc47864a07bf5918c2d1fc38abIAAzjGnQT0bqHkYbMzhV16AwVvyizwX/qzt62ZDy2MtN2P9+R5gAAAAAEADjTvSOMk8zYgEAAQAyTzNi",
    uid: Math.floor(Math.random() * 100)
};

const events = ref([])

const appendVideoTag = ({id, parentId}) => {
    const div = document.createElement("div");

    div.id = id
    div.style.width = '480px'
    div.style.height = '320px'
    document.getElementById(parentId).append(div)
}

const logToUi = (msg) => {
    events.value.push(msg)
}

// Add the user who has subscribed to the channel to the local interface.
const handleUserPublished = async (user, mediaType) => {
    await rtc.client.subscribe(user, mediaType)

    if (mediaType === 'video') {
        appendVideoTag({ id: `player-${user.uid}`, parentId:'remote-participants-list' })
        user.videoTrack.play(`player-${user.uid}`)
        logToUi(`user ${user.uid} turned on video` )
    }

    if (mediaType === 'audio') {
        user.audioTrack.play();
        logToUi(`user ${user.uid} turned on audio` )
    }

}

// Remove the user specified from the channel in the local interface
const handleUserUnpublished = (user, mediaType) => {
    if (mediaType === 'video') {
        document.getElementById(`player-${user.uid}`).remove()
        logToUi(`user ${user.uid} turned off video` )
    }

    if (mediaType === 'audio') {
        logToUi(`user ${user.uid} turned off audio` )
    }
}

const muteVideo = async () => {
    if (!rtc.localTracks.videoTrack) return;
    await rtc.localTracks.videoTrack.setMuted(true);
}

const unMuteVideo = async () => {
    if (!rtc.localTracks.videoTrack) return;
    await rtc.localTracks.videoTrack.setMuted(false);
}

const join = async () => {
    // Event listeners
    rtc.client.on("user-published", handleUserPublished)
    rtc.client.on("user-unpublished", handleUserUnpublished)
    rtc.client.on("user-joined", (user) => { logToUi(`user ${user.uid} has joined` ) })
    rtc.client.on("user-left", (user) => { logToUi(`user ${user.uid} has left` ) })

    // Join a channel and create local tracks
    await rtc.client.join(options.appId, options.channel, options.token, options.uid)
    rtc.localTracks.audioTrack = await AgoraRTC.createMicrophoneAudioTrack()
    rtc.localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack()

    // Play the local video track to the local browser
    rtc.localTracks.videoTrack.play("local-participant-player")

    // Publish the local video and audio tracks to the channel.
    await rtc.client.publish(Object.values(rtc.localTracks));

    // push event logs to the ui
    logToUi(`user ${options.uid} (you) has joined`)
}

const leave = async () => {
    rtc.localTracks.audioTrack.stop()
    rtc.localTracks.audioTrack.close()

    rtc.localTracks.videoTrack.stop()
    rtc.localTracks.videoTrack.close()

    await rtc.client.leave()
}

onMounted( async () => {
    await join()
})

</script>

<style scoped>
.local-player {
    width: 480px;
    height: 320px;
}
</style>
