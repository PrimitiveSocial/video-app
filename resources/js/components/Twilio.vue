<template>
    <div class="bg-gray-100">
        <ul>
            <li>get access token with video grant permission</li>
            <li>connect to room with access token and room settings</li>
            <li>room types: https://www.twilio.com/docs/video/tutorials/understanding-video-rooms</li>
            <li>Adhoc room are what we will use as we can create it on the fly without any request. read more about adhoc room and room types in general</li>
            <li>the api provides the features but no the UI (kind of like the composition api)</li>
            <li>create local video track to capture media devices</li>
        </ul>
        <div id="video-chat-window"></div>
        <div id="remote-media-participants"></div>
    </div>
</template>


<script setup>
// https://www.twilio.com/docs/video
// https://www.twilio.com/docs/video/javascript-getting-started
// https://www.twilio.com/docs/video/tutorials/understanding-video-rooms

import { onMounted, ref } from "vue"

const TwilioVideo = require('twilio-video')
const { isSupported, connect, createLocalVideoTrack } = TwilioVideo

const accessToken = ref('')

const getAccessToken = () => {
    axios.post('access-token/twilio').then( response => {
        accessToken.value = response.data
       joinRoom('meeting room')
    })
}

const joinRoom = (roomName) => {
    connect(accessToken.value, { name: roomName})
        .then( room => {
            // capture local media from your device's microphone, camera or screen-share
            const videoChatWindow = document.getElementById('video-chat-window')
            createLocalVideoTrack().then(track => {
                videoChatWindow.appendChild(track.attach());
            })

            // Log your Client's LocalParticipant in the Room
            const localParticipant = room.localParticipant;
            console.log(`Connected to the Room as LocalParticipant "${localParticipant.identity}"`);

            // Log any Participants already connected to the Room
            room.participants.forEach(participant => {
                console.log(`Participant "${participant.identity}" is connected to the Room`);
            });

            // Log new Participants as they connect to the Room
            room.on('participantConnected', participant => {
                console.log(`Participant "${participant.identity}" has connected to the Room`);

                participant.tracks.forEach(publication => {
                    if (publication.isSubscribed) {
                        const track = publication.track;
                        document.getElementById('remote-media-participants').appendChild(track.attach());
                    }
                });

                participant.on('trackSubscribed', track => {
                    document.getElementById('remote-media-participants').appendChild(track.attach());
                });
            });

            // Log Participants as they disconnect from the Room
            room.on('participantDisconnected', participant => {
                console.log(`Participant "${participant.identity}" has disconnected from the Room`);
            });

            // For RemoteParticipants that are already in the Room, we can attach their RemoteTracks
            room.participants.forEach(participant => {
                participant.tracks.forEach(publication => {
                    if (publication.track) {
                        document.getElementById('remote-media-participants').appendChild(publication.track.attach());
                    }
                });

                participant.on('trackSubscribed', track => {
                    document.getElementById('remote-media-participants').appendChild(track.attach());
                });
            });

        })
        .catch(err => {
            //https://www.twilio.com/docs/api/errors/20151
            console.log(err.message)
            console.log(err.code)
        })
}

    onMounted( () => {
        getAccessToken()
    })


</script>
