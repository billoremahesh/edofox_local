import {
    WebRTCAdaptor
} from "https://mystream.edofox.com:5443/WebRTCAppEE/js/webrtc_adaptor.js";
import {
    getUrlParameter
} from "https://mystream.edofox.com:5443/WebRTCAppEE/js/fetch.stream.js";


import {
    connectionSuccess, publishSuccess, joinSuccess, leaveSuccess, showConnectionError, handleCameraButtons, handleMicButtons, createRemoteVideo, removeStreamElement,
    handleNotificationEvent, getUserMedia, isAnnotating, toggleScreenShare
} from "./video_handlers.js";


/**
 * This page accepts 3 arguments through url parameter
 * 1. "streamId": the stream id to publish stream. It's optional. ?streamId=stream1
 * 2. "playOnly": If it's true, user does not publish stream. It only play streams in the room.
 * 3. "token": It's experimental. 
 */

var token = getUrlParameter("token");
//var streamId = getUrlParameter("streamId");

var playOnly = getUrlParameter("playOnly");
if (playOnly == null) {
    playOnly = false;
}

//For user audio and video while joining

var dataChannelStreamID = null;

var mediaConstraints = {
    video: true,
    audio: true
};

if (type == 'Student') {
    mediaConstraints.audio = false;
    mediaConstraints.video = false;
}

var volume_change_input = document.getElementById("volume_change_input");
volume_change_input.addEventListener("change", changeVolume);

function changeVolume() {
    /**
     * Change the gain levels on the input selector.
     */
    if (document.getElementById('volume_change_input') != null) {
        webRTCAdaptor.currentVolume = this.value;
        if (webRTCAdaptor.soundOriginGainNode != null) {
            webRTCAdaptor.soundOriginGainNode.gain.value = this.value; // Any number between 0 and 1.
        }

        if (webRTCAdaptor.secondStreamGainNode != null) {
            webRTCAdaptor.secondStreamGainNode.gain.value = this.value; // Any number between 0 and 1.
        }
    }
}


var init = false;
var roomNameBox = document.getElementById("roomName");

var roomOfStream = new Array();
var streamsList = new Array();
var checkSpeakingStreamIds = new Array();
var dominantSpeakerFinderId = null;
var publishStreamId;
var isDataChannelOpen = false;
export var isMicMuted = false;
export var isCameraOff = false;
var roomTimerId = -1;

export function switchVideoMode(value) {
    if (value == "screen") {
        if (!isCameraOff) {
            //If camera is on switch to screen + camera
            webRTCAdaptor.switchDesktopCaptureWithCamera(publishStreamId);
        } else {
            webRTCAdaptor.switchDesktopCapture(publishStreamId);
        }
    } else if (value == "annotation") {
        var canvas = document.getElementById('canvas');
        webRTCAdaptor.updateVideoTrack(canvas.captureStream(25), streamId, webRTCAdaptor.mediaConstraints, null, true);

    } else {
        webRTCAdaptor.switchVideoCameraCapture(publishStreamId);
        // if(isCameraOff) {
        //     webRTCAdaptor.turnOffLocalCamera();
        // }

    }
}

export function switchVideoSource(deviceId) {
    console.log("Switching video source to .." + deviceId);
    webRTCAdaptor.switchVideoCameraCapture(streamId, deviceId);
}

var screen = false;

export function toggleCamera() {
    if (!isCameraOff) {
        turnOffLocalCamera();
    } else {
        console.log("Turning camera ON");
        turnOnLocalCamera();
    }
    handleCameraButtons();
}

export function toggleMic() {
    if (!isMicMuted) {
        muteLocalMic();
    } else {
        unmuteLocalMic();
    }
}

console.log("toggle functions defined");

function turnOffLocalCamera() {
    isCameraOff = true;
    if (isAnnotating) {
        return;
    }
    if (webRTCAdaptor != null) {
        webRTCAdaptor.turnOffLocalCamera();
        sendNotificationEvent("CAM_TURNED_OFF");
    }
    //isCameraOff = true;

    if (publishStreamId == null) {
        mediaConstraints.video = false;
    }

}



export function turnOnLocalCamera() {
    isCameraOff = false;
    if (isAnnotating) {
        getUserMedia();
        //isCameraOff = false;
        return;
    }
    if (webRTCAdaptor != null) {
        webRTCAdaptor.turnOnLocalCamera();
        sendNotificationEvent("CAM_TURNED_ON");
    }
    //isCameraOff = false;
    mediaConstraints.video = true;
    console.log("Camera is ON");
}

function muteLocalMic() {
    if (webRTCAdaptor != null) {
        webRTCAdaptor.muteLocalMic();
        sendNotificationEvent("MIC_MUTED");
        // webRTCAdaptor.enableAudioLevelWhenMuted();
    }
    isMicMuted = true;
    handleMicButtons();
    mediaConstraints.audio = false;
}

export function muteStudentLocalMic() {
    if (playOnly) {
        return false;
    }
    webRTCAdaptor.muteLocalMic();
    // webRTCAdaptor.enableAudioLevelWhenMuted();
    isMicMuted = true;
    console.log("Muting the student local mic ..");
    handleMicButtons();
}

function unmuteLocalMic() {
    if (webRTCAdaptor != null) {
        webRTCAdaptor.unmuteLocalMic();
        sendNotificationEvent("MIC_UNMUTED");
        // webRTCAdaptor.disableAudioLevelWhenMuted();
    }
    isMicMuted = false;
    handleMicButtons();
    mediaConstraints.audio = true;
}

export function sendNotificationEvent(eventType, streamId, notificationObj) {
    var notificationStreamId = publishStreamId;

    //console.log('In Send Notification::'+dataChannelStreamID);
    if (dataChannelStreamID) {
        notificationStreamId = dataChannelStreamID;
    }

    if (streamId) {
        notificationStreamId = streamId;
    }
    if (isDataChannelOpen) {
        var notEvent = {
            streamId: notificationStreamId,
            eventType: eventType
        };

        if (notificationObj != null) {
            notEvent = notificationObj;
            if (!notEvent.streamId) {
                notEvent.streamId = publishStreamId;
            }
        }

        webRTCAdaptor.sendData(notificationStreamId, JSON.stringify(notEvent));
    } else {
        console.log("Could not send the notification because data channel is not open.");
    }
}

function sendMessage(msg) {
    var message_stream_id = dataChannelStreamID ? dataChannelStreamID : publishStreamId;
    webRTCAdaptor.sendData(message_stream_id, JSON.stringify(msg));
}


//var roomName;

function getRoomName() {
    if (roomName == null) {
        roomName = "meet_" + new Date().getMilliseconds();
        console.log("Room " + roomName);
    }
    return roomName;
}

export function joinRoom() {
    //var mode = mcuChbx != null && mcuChbx.checked ? "mcu" : "legacy";

    if (!init) {
        //If webRTC is not initialized, first initialize it and then call join room
        connectAntmedia();
        return;
    }

    var mode = "legacy";
    if (streamId == null) {
        streamId = $("#username").val();
    }
    webRTCAdaptor.joinRoom(getRoomName(), streamId, mode);
}

export function leaveRoom() {
    webRTCAdaptor.leaveFromRoom(getRoomName());
    // for (var node in document.getElementById("players").childNodes) {
    //     if (node.tagName == 'DIV' && node.id != "localVideo") {
    //         document.getElementById("players").removeChild(node);
    //     }
    // }
    //init = false;
    muteLocalMic();
    turnOffLocalCamera();
}

function publish(streamName, token) {
    publishStreamId = streamName;
    webRTCAdaptor.publish(streamName, token);
}

function streamInformation(obj) {
    webRTCAdaptor.play(obj.streamId, token, getRoomName());
}

function playVideo(obj) {
    var room = roomOfStream[obj.streamId];
    // console.log("new stream available with id: " +
    //     obj.streamId + "on the room:" + room + " and track " + obj.track.kind + " is " + obj.track.muted);

    createRemoteVideo(obj);
    webRTCAdaptor.enableAudioLevel(obj.stream, obj.streamId);
}


function removeRemoteVideo(streamId) {
    webRTCAdaptor.stop(streamId);
    streamsList = streamsList.filter(item => item !== streamId);

    removeStreamElement(streamId);
}

function checkVideoTrackStatus(streamsList) {
    streamsList.forEach(function (item) {
        var video = document.getElementById("remoteVideo" + item);
        if (video != null && !video.srcObject.active) {
            removeRemoteVideo(item);
            playVideo(item);
        }
    });
}

function sendInitialNotifications() {
    //Send notification if video/audio is off
    setTimeout(function () {
        if (isCameraOff) {
            sendNotificationEvent("CAM_TURNED_OFF");
            console.log("Sending cam off notification");
        }
        if (isMicMuted) {
            sendNotificationEvent("MIC_MUTED");
        }
        sendMessage({ "msg": "Joined!!" });
        if (playOnly) {
            //for adding student to students list
            var msgObject = {
                streamId: streamId,
                stream: null,
                eventType: 'PLAY_ONLY_JOIN',
                student: {
                    name: username
                }
            }
            sendNotificationEvent('', '', msgObject);

        }
    }, 5000);

}

export function getDeviceOptions(filter) {

    return new Promise(function (resolve, reject) {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
            console.log("enumerateDevices() not supported.");
            reject('Not supported!');
        }

        // List cameras and microphones.
        var myDevices = [];

        navigator.mediaDevices.enumerateDevices()
            .then(function (devices) {
                devices.forEach(function (device) {
                    //console.log(device.kind + ": " + device.label + " id = " + device.deviceId);
                    if (device.kind == filter) {
                        myDevices.push(device);
                    }
                });

                resolve(myDevices);
            })
            .catch(function (err) {
                console.log(err.name + ": " + err.message);
            });


    });


}

function findDominantSpeaker(streamSoundList) {
    var tmpMax = 0;
    var tmpStreamId = null;
    var threshold = 0.01;

    for (let i = 0; i < streamsList.length; i++) {
        let nextStreamId = streamsList[i];
        let tmpValue = streamSoundList[nextStreamId]
        if (tmpValue > threshold) {
            if (tmpValue > tmpMax) {
                tmpMax = tmpValue;
                tmpStreamId = nextStreamId;
            }
        }
    }
    if (tmpStreamId != null) {
        if (checkSpeakingStreamIds[tmpStreamId] == null || typeof checkSpeakingStreamIds[tmpStreamId] == "undefined") {
            var element = document.getElementById("remoteStudent" + tmpStreamId);
            element.style.border = "thick solid #0000FF";
            checkSpeakingStreamIds[tmpStreamId] = true;
            setTimeout(() => {
                var icon = document.getElementById("remoteStudent" + tmpStreamId);
                element.style.border = "";
                checkSpeakingStreamIds[tmpStreamId] = null;
            }, 1000)
        }
    }
}

function startAnimation() {

    $("#broadcastingInfo")
        .fadeIn(
            800,
            function () {
                $("#broadcastingInfo")
                    .fadeOut(
                        800,
                        function () {
                            var state = webRTCAdaptor
                                .signallingState(publishStreamId);
                            if (state != null &&
                                state != "closed") {
                                var iceState = webRTCAdaptor
                                    .iceConnectionState(publishStreamId);
                                if (iceState != null &&
                                    iceState != "failed" &&
                                    iceState != "disconnected") {
                                    startAnimation();
                                }
                            }
                        });
            });

}
var pc_config = {
    'iceServers': [{
        'urls': 'stun:stun1.l.google.com:19302'
    }]
};

var sdpConstraints = {
    OfferToReceiveAudio: false,
    OfferToReceiveVideo: false

};

var appName = location.pathname.substring(0, location.pathname
    .lastIndexOf("/") + 1);
var path = location.hostname + ":" + location.port + appName + "websocket";
var websocketURL = "ws://" + path;

if (location.protocol.startsWith("https")) {
    websocketURL = "wss://" + path;
}

websocketURL = "wss://mystream.edofox.com:5443/WebRTCAppEE/websocket";

var webRTCAdaptor;


function connectAntmedia() {

    if (!mediaConstraints.audio && !mediaConstraints.video) {
        playOnly = true;
        //console.log("Play only is set ON", playOnly);
    }
    // This is for joining student in play only mode
    // if(type=='Student'){
    //     playOnly = true;
    //     console.log("Play only is set ON", playOnly);
    // }

    webRTCAdaptor = new WebRTCAdaptor({
        websocket_url: websocketURL,
        mediaConstraints: mediaConstraints,
        peerconnection_config: pc_config,
        sdp_constraints: sdpConstraints,
        localVideoId: "localVideo",
        isPlayMode: playOnly,
        debug: true,
        dataChannelEnabled: true,
        callback: (info, obj) => {
           // console.log("New message", info, obj);
            if (info == "initialized") {
                init = true;
                console.log("initialized");

                connectionSuccess();

                if (playOnly) {
                    isCameraOff = true;
                    handleCameraButtons();
                }
            } else if (info == "joinedTheRoom") {
                var room = obj.ATTR_ROOM_NAME;
                roomOfStream[obj.streamId] = room;
                console.log("joined the room: " + roomOfStream[obj.streamId]);
                console.log(obj)

                publishStreamId = obj.streamId

                if (playOnly) {
                    joinSuccess();
                    isCameraOff = true;
                    handleCameraButtons();
                } else {
                    publish(obj.streamId, token);
                }

                if (obj.streams != null) {
                    obj.streams.forEach(function (item) {
                        //console.log("Stream joined with ID: " + item);
                        webRTCAdaptor.play(item, token, getRoomName());
                    });
                    streamsList = obj.streams;
                }
                roomTimerId = setInterval(() => {
                    webRTCAdaptor.getRoomInfo(getRoomName(), publishStreamId);
                }, 5000);

                if (streamsList.length > 0 && type == 'Admin') {
                    dominantSpeakerFinderId = setInterval(() => {
                        webRTCAdaptor.getSoundLevelList(streamsList);
                    }, 200);
                }

            } else if (info == "newStreamAvailable") {
                //console.log("New User joined", info, obj);
                playVideo(obj);
                if (dominantSpeakerFinderId == null && type == 'Admin') {
                    dominantSpeakerFinderId = setInterval(() => {
                        webRTCAdaptor.getSoundLevelList(streamsList);
                    }, 200);
                }
            } else if (info == "publish_started") {
                //stream is being published
                console.debug("publish started to room: " +
                    roomOfStream[obj.streamId]);
                //join_publish_button.disabled = true;
                //stop_publish_button.disabled = false;
                //startAnimation();

                //Successfully joined the room and publishing started
                publishSuccess();


            } else if (info == "publish_finished") {
                //stream is being finished
                console.debug("publish finished");
            } else if (info == "screen_share_stopped") {
                //here
                toggleScreenShare();
                console.log("screen share stopped");
            } else if (info == "browser_screen_share_supported") {
                //screen_share_checkbox.disabled = false;
                //camera_checkbox.disabled = false;
                //screen_share_with_camera_checkbox.disabled = false;
                console.log("browser screen share supported");
                //browser_screen_share_doesnt_support.style.display = "none";
            } else if (info == "leavedFromRoom") {
                var room = obj.ATTR_ROOM_NAME;
                console.debug("leaved from the room:" + room);
                if (roomTimerId != null) {
                    clearInterval(roomTimerId);
                    clearInterval(dominantSpeakerFinderId);
                }
                dominantSpeakerFinderId = null;

                //join_publish_button.disabled = false;
                //join_publish_button.style = "";
                //stop_publish_button.disabled = true;    
                //$("#post_publish_controls").attr("style", "display:none");

                leaveSuccess();


                if (streamsList != null) {
                    streamsList.forEach(function (item) {
                        removeRemoteVideo(item);
                    });
                }
                // we need to reset streams list
                streamsList = new Array();
            } else if (info == "closed") {
                //console.log("Connection closed");
                if (typeof obj != "undefined") {
                    console.log("Connecton closed: " +
                        JSON.stringify(obj));
                }
            } else if (info == "play_finished") {
                console.log("play_finished");
                removeRemoteVideo(obj.streamId);
            } else if (info == "streamInformation") {
                streamInformation(obj);
            } else if (info == "gotSoundList") {
                findDominantSpeaker(obj);
            } else if (info == "roomInformation") {
                //Checks if any new stream has added, if yes, plays.
                for (let str of obj.streams) {
                    if (!streamsList.includes(str)) {
                        webRTCAdaptor.play(str, token, getRoomName());
                    }
                }
                // Checks if any stream has been removed, if yes, removes the view and stops webrtc connection.
                for (let str of streamsList) {
                    if (!obj.streams.includes(str)) {
                        removeRemoteVideo(str);
                    }
                }
                //Lastly updates the current streamlist with the fetched one.
                streamsList = obj.streams;

                //Check video tracks active/inactive status
                checkVideoTrackStatus(streamsList);
            } else if (info == "data_channel_opened") {
                console.log("Data Channel open for stream id", obj);

                if (playOnly) {
                    dataChannelStreamID = obj;
                }

                isDataChannelOpen = true;
                sendInitialNotifications();
            } else if (info == "data_channel_closed") {
                console.log("Data Channel closed for stream id", obj);
                isDataChannelOpen = false;
            } else if (info == "data_received") {
                handleNotificationEvent(obj);
            }
            //$("#errorText").attr("style", "display:none");
        },
        callbackError: function (error, message) {
            //some of the possible errors, NotFoundError, SecurityError,PermissionDeniedError

            if (error.indexOf("publishTimeoutError") != -1 && roomTimerId != null) {
                clearInterval(roomTimerId);
            }

            console.log("error callback: " + JSON.stringify(error));
            var errorMessage = JSON.stringify(error);
            if (typeof message != "undefined") {
                errorMessage = message;
            }
            var errorMessage = JSON.stringify(error);
            if (error.indexOf("NotFoundError") != -1) {
                errorMessage = "Camera or Mic are not found or not allowed in your device.";
            } else if (error.indexOf("NotReadableError") != -1 ||
                error.indexOf("TrackStartError") != -1) {
                errorMessage = "Camera or Mic is being used by some other process that does not not allow these devices to be read.";
            } else if (error.indexOf("OverconstrainedError") != -1 ||
                error.indexOf("ConstraintNotSatisfiedError") != -1) {
                errorMessage = "There is no device found that fits your video and audio constraints. You may change video and audio constraints."
            } else if (error.indexOf("NotAllowedError") != -1 ||
                error.indexOf("PermissionDeniedError") != -1) {
                errorMessage = "You are not allowed to access camera and mic.";
                //screen_share_checkbox.checked = false;
                //camera_checkbox.checked = false;
            } else if (error.indexOf("TypeError") != -1) {
                errorMessage = "Video/Audio is required.";
            } else if (error.indexOf("UnsecureContext") != -1) {
                errorMessage = "Fatal Error: Browser cannot access camera and mic because of unsecure context. Please install SSL and access via https";
            } else if (error.indexOf("WebSocketNotSupported") != -1) {
                errorMessage = "Fatal Error: WebSocket not supported in this browser";
            } else if (error.indexOf("no_stream_exist") != -1) {
                //TODO: removeRemoteVideo(error.streamId);
            } else if (error.indexOf("data_channel_error") != -1) {
                errorMessage = "There was a error during data channel communication";
            } else if (error.indexOf("ScreenSharePermissionDenied") != -1) {
                errorMessage = "You are not allowed to access screen share";
                //screen_share_checkbox.checked = false;
                //camera_checkbox.checked = true;
            }

            //alert(errorMessage);
            //$("#errorText").attr("style", "");
            //$("#errorText").text(errorMessage);
            showConnectionError(errorMessage);
        }
    });
}