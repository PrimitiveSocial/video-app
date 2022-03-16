<template>
    <div class="h-full bg-gradient-to-b from-slate-800 to-slate-600">
        <div class="flex justify-between p-8 space-x-8">
            <div id="local-participant" style="width:640px;height:480px">

            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from "vue"
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
    uid: 1
};

const startCall = () => {
    rtc.client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
}

onMounted( async () => {
    await startCall()
        // Join an RTC channel.
        await rtc.client.join(options.appId, options.channel, options.token, options.uid);
        // Create a local audio track from the audio sampled by a microphone.
        rtc.localAudioTrack = await AgoraRTC.createMicrophoneAudioTrack();
        // Create a local video track from the video captured by a camera.
        rtc.localVideoTrack = await AgoraRTC.createCameraVideoTrack();
        // Publish the local audio and video tracks to the RTC channel.
        await rtc.client.publish([rtc.localAudioTrack, rtc.localVideoTrack]);

        // Dynamically create a container in the form of a DIV element for playing the local video track.
        const localParticipantPlayer = document.createElement("div");
        // Specify the ID of the DIV container. You can use the uid of the local user.
        localParticipantPlayer.id = options.uid
        localParticipantPlayer.classList.value = 'w-64 h-64' //must set width and height to display video
        document.getElementById('local-participant').append(localParticipantPlayer);

        // Play the local video track.
        // Pass the DIV container and the SDK dynamically creates a player in the container for playing the local video track.
        rtc.localVideoTrack.play(localParticipantPlayer);
})

</script>
