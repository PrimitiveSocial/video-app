<template>
    <div class="h-full bg-gradient-to-b from-slate-800 to-slate-600">
        <div class="flex justify-between p-8 space-x-8">
            <!-- local participant media -->
            <div>
                <div id="video-chat-window" class=""></div>
                <span class="text-white">You</span>
            </div>

            <!-- local participant media -->
            <div>
                <div id="remote-media-participants" class=""></div>
            </div>

            <!-- events log -->
            <div id="meeting-events">
                <ul class="space-y-4">
                    <li v-for="event in events" class="text-white"> {{ event }}</li>
                </ul>
            </div>
        </div>

        <div class="fixed bottom-0 w-full flex items-center justify-center space-x-4 p-4">
            <!-- ui action -->
            <button id="btn-camera-off">hide camera</button>
            <button id="btn-camera-on">enable camera</button>
            <button id="btn-leave">leave call</button>
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
const { isSupported, connect, createLocalTracks } = TwilioVideo

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

const leave = (room) => {
    room.disconnect();
}

const handleRemoteParticipantDisabledCamera = (track) => {
    const attachedElements = track.detach();
    attachedElements.forEach(element => element.remove());
}

const joinRoom = (roomName) => {
    createLocalTracks({
        audio: true,
        video: true,
        // video: { height: 360, frameRate: 24, width: 640 },
    }).then(localTracks => {
        // load audio and video
        const videoChatWindow = document.getElementById('video-chat-window')
        localTracks.forEach(function(track) {
            videoChatWindow.appendChild(track.attach());
        })

        return connect(accessToken.value, { name: roomName, tracks: localTracks,  preferredVideoCodecs: ['H264'] })
            .then(room => {
                console.log(room)
                // ui events
                document.getElementById('btn-camera-off').onclick = () => hideCamera(room)
                document.getElementById('btn-camera-on').onclick = () => showCamera(room)
                document.getElementById('btn-leave').onclick = () => leave(room)

                // Log your Client's LocalParticipant in the Room
                events.value.push(`Connected to the Room as LocalParticipant "${room.localParticipant.identity}"`);

                // Log any Participants already connected to the Room
                room.participants.forEach(participant => {
                    events.value.push(`Participant "${participant.identity}" is already connected to the Room`);
                });

                // room events
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

                room.on('disconnected', room => {
                    // Detach the local media elements
                    room.localParticipant.tracks.forEach(publication => {
                        const attachedElements = publication.track.detach();
                        attachedElements.forEach(element => element.remove());
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
