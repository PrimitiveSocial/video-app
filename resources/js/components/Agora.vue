<template>
    <div class="h-full bg-gradient-to-b from-slate-800 to-slate-600">
        <div class="flex justify-between p-8 space-x-8">
            <div id="local-participant"></div>
            <div id="remote-participant"></div>
            <!-- events log -->
            <div id="meeting-events">
                <ul class="space-y-4">
                    <li v-for="event in events" class="text-white"> {{ event }}</li>
                </ul>
            </div>
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

import { onMounted, ref } from "vue"
import AgoraRTC from "agora-rtc-sdk-ng"

const rtc = {
    localAudioTrack: null,
    localVideoTrack: null,
    client: null
}

const options = {
    appId: "b07e18fc47864a07bf5918c2d1fc38ab",
    channel: "personal-meeting",
    token: "006b07e18fc47864a07bf5918c2d1fc38abIAAzjGnQT0bqHkYbMzhV16AwVvyizwX/qzt62ZDy2MtN2P9+R5gAAAAAEADjTvSOMk8zYgEAAQAyTzNi",
    uid: Math.floor(Math.random() * 100)
};

const events = ref([])

const initAgoraClient = () => {
    rtc.client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
}
const joinCall = async () => {
    await rtc.client.join(options.appId, options.channel, options.token, options.uid)
    events.value.push(`user ${options.uid} (you) has joined` )
}

const startLocalParticipantVideoAndAudio = async () => {
    // Create a local audio track from the audio sampled by a microphone.
    rtc.localAudioTrack = await AgoraRTC.createMicrophoneAudioTrack();
    // Create a local video track from the video captured by a camera.
    rtc.localVideoTrack = await AgoraRTC.createCameraVideoTrack();
    // Publish the local audio and video tracks to the RTC channel.
    await rtc.client.publish([rtc.localAudioTrack, rtc.localVideoTrack]);
    // Dynamically create a container in the form of a DIV element for playing the local video track.
    const localParticipantPlayer = document.createElement("div");
    // Specify the ID of the DIV container. You can use the uid of the local user.
    localParticipantPlayer.id = options.uid.toString()
    localParticipantPlayer.classList.value = 'w-64 h-64' //must set width and height to display video
    document.getElementById('local-participant').append(localParticipantPlayer);
    // Play the local video track.
    // Pass the DIV container and the SDK dynamically creates a player in the container for playing the local video track.
    rtc.localVideoTrack.play(localParticipantPlayer);
}

const listenToRemoteParticipantsEvents = async () => {
    rtc.client.on("user-published", async (user, mediaType) => {
        // Subscribe to the remote user when the SDK triggers the "user-published" event
        await rtc.client.subscribe(user, mediaType);

        events.value.push(`user ${user.uid} has joined` )

        // If the remote user publishes a video track.
        if (mediaType === "video") {
            // Get the RemoteVideoTrack object in the AgoraRTCRemoteUser object.
            const remoteVideoTrack = user.videoTrack;
            // Dynamically create a container in the form of a DIV element for playing the remote video track.
            const remotePlayerContainer = document.createElement("div");
            // Specify the ID of the DIV container. You can use the uid of the remote user.
            remotePlayerContainer.id = user.uid.toString();
            remotePlayerContainer.classList.value = 'w-64 h-64'
            document.getElementById('remote-participant').append(remotePlayerContainer);
            // Play the remote video track.
            // Pass the DIV container and the SDK dynamically creates a player in the container for playing the remote video track.
            remoteVideoTrack.play(remotePlayerContainer);
        }

        // If the remote user publishes an audio track.
        if (mediaType === "audio") {
            // Get the RemoteAudioTrack object in the AgoraRTCRemoteUser object.
            const remoteAudioTrack = user.audioTrack;
            // Play the remote audio track. No need to pass any DOM element.
            remoteAudioTrack.play();
        }

        // Listen for the "user-unpublished" event
        rtc.client.on("user-unpublished", user => {
            // Get the dynamically created DIV container.
            const remotePlayerContainer = document.getElementById(user.uid);
            // Destroy the container.
            remotePlayerContainer.remove();
        });

    });
}

const startExistingRemoteParticipantsVideoAndAudio = async () => {

    rtc.client.remoteUsers.forEach(user => {
        const remoteVideoTrack = user.videoTrack
        // Specify the ID of the DIV container. You can use the uid of the remote user.
        const remotePlayerContainer = document.createElement("div");
        remotePlayerContainer.id = user.uid.toString();
        remotePlayerContainer.classList.value = 'w-64 h-64'
        document.getElementById('remote-participant').append(remotePlayerContainer);
        // Play the remote video track.
        // Pass the DIV container and the SDK dynamically creates a player in the container for playing the remote video track.
        remoteVideoTrack.play(remotePlayerContainer);

        const remoteAudioTrack = user.audioTrack;
        remoteAudioTrack.play()
    });
}

onMounted( async () => {
    await initAgoraClient()
    await joinCall()
    await startLocalParticipantVideoAndAudio()
    //await listenToRemoteParticipantsEvents()
    await startExistingRemoteParticipantsVideoAndAudio()
})

</script>
