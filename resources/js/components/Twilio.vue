<template>
    <div class="h-full bg-gradient-to-b from-slate-800 to-slate-600">
        <div class="flex justify-between items-center p-4">
            <!-- local participant media -->
            <div>
                <div id="video-chat-window" class="w-1/2 w-1/3 border"></div>
                <span>You</span>
            </div>

            <!-- local participant media -->
            <div>
                <div id="remote-media-participants" class=""></div>
            </div>

            <!-- events log -->
            <div id="meeting-events">
                <ul>
                    <li v-for="event in events"> {{ event }}</li>
                </ul>
            </div>

            <!-- ui action -->
            <button id="btn-camera-off">hide camera</button>
            <button id="btn-camera-on">enable camera</button>
        </div>
    </div>
</template>


<script setup>
// https://www.twilio.com/docs/video/tutorials/basic-concepts
// https://www.twilio.com/docs/video/tutorials/understanding-video-rooms
// https://www.twilio.com/docs/video/tutorials/developing-high-quality-video-applications

// https://www.twilio.com/docs/video/tutorials/understanding-video-rooms-apis
// https://www.twilio.com/docs/video/javascript-getting-started

// storage to s3
// recordings
// share screen

import { inject, onMounted, ref } from "vue"

const TwilioVideo = require('twilio-video')
const { isSupported, connect, createLocalVideoTrack, createLocalTracks } = TwilioVideo

const accessToken = inject('twilioAccessToken')
const events = ref([])

const hideCamera = (room) => {
    room.localParticipant.tracks.forEach(publication => {
        publication.track.disable()
    });
}

const showCamera = (room) => {
    room.localParticipant.videoTracks.forEach(publication => {
        publication.track.enable();
    });
}

const joinRoom = (roomName) => {
    createLocalTracks({
        audio: true,
        video: { width: 640 },
    }).then(localTracks => {

        const videoChatWindow = document.getElementById('video-chat-window')

        localTracks.forEach(function(track) {
            videoChatWindow.appendChild(track.attach());
        })

        return connect(accessToken.value, { name: roomName, tracks: localTracks, preferredVideoCodecs: ['H264'] })
            .then(room => {
                document.getElementById('btn-camera-off').onclick = () => hideCamera(room)
                document.getElementById('btn-camera-on').onclick = () => showCamera(room)

                // Log your Client's LocalParticipant in the Room
                events.value.push(`Connected to the Room as LocalParticipant "${room.localParticipant.identity}"`);

                // Log any Participants already connected to the Room
                room.participants.forEach(participant => {
                    events.value.push(`Participant "${participant.identity}" is already connected to the Room`);
                });

                // Log new Participants as they connect to the Room
                room.on('participantConnected', participant => {
                    events.value.push(`Participant "${participant.identity}" has now connected to the Room`);

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
                    events.value.push(`Participant "${participant.identity}" has disconnected from the Room`);
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
    })
}

onMounted( () => {
    joinRoom('personal-meeting')
})

</script>
