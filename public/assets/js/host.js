
//var wcs = "wss://ec2-15-206-164-57.ap-south-1.compute.amazonaws.com:8443";
var wcs = "wss://dev.edofox.com:9443";
var turn = "turn:172.105.126.172:5349?transport=udp";

var SESSION_STATUS = Flashphoner.constants.SESSION_STATUS;
var STREAM_STATUS = Flashphoner.constants.STREAM_STATUS;
var STREAM_STATUS_INFO = Flashphoner.constants.STREAM_STATUS_INFO;
var PRELOADER_URL = "../../dependencies/media/preloader.mp4";


//Angular controller
var app = angular.module("app");
app.controller('videoHost', function ($scope, userService, $location, $http) {

    // console.log("Video host controller ...");

    $scope.joined = false;

    var screenStream = null;

    var studentsInterval = null;

    $scope.recordStatus = {
        alignRecord: true,
        rtmp: false
    };

    $scope.cameraVideoProfile = "360p_1"; //Default profile

    var token = null;

    localVideo = document.getElementById("full-screen-video");

    var sessionId = localStorage.sessionId;
    $scope.sessionName = localStorage.sessionName;

    function getSession() {
        if ($scope.hostDetails == null || $scope.hostDetails.sessionId == null) {

            $scope.dataObj = {
                student: {
                    currentPackage: { id: channelId }
                },
                requestType: "package"
            };
        } else {
            $scope.dataObj = {
                student: {
                    currentPackage: { id: $scope.hostDetails.sessionId }
                }
            };

        }

        // console.log("session request ==>", $scope.dataObj);

        userService.callService($scope, "getSession").then(function (response) {
            // console.log(' ============= Got response ========== ', response);
            //$scope.errorMessage = "";

            if (response == null || response.packages == null || response.packages.length == 0) {
                $scope.errorMessage = "No session found ..";
            } else {
                $scope.session = response.packages[0];
                $scope.lectureUrl = $scope.session.videoUrl;
                if ($scope.lectureUrl) {
                    $scope.errorMessage = "Please join the live lecture ..";
                }
                if ($scope.hostDetails == null || $scope.hostDetails.sessionId == null) {
                    $scope.hostDetails = {
                        sessionId: $scope.session.id,
                        sessionName: $scope.session.name
                    };
                }
                //$scope.$apply();
                //$('<iframe />'); // Create an iframe element
                //$($scope.session.videoUrl).appendTo('#watchVideo');
            }


        }).catch(function (error) {
            console.log("Error!" + error);
        });


    }


    getSession();

    function saveRecording() {
        // console.log("Saving the recording ..");

        $scope.dataObj = {
            student: {
                currentPackage: {
                    id: sessionId
                }
            }
        }
        $("#recordingProgress").attr("style", "");
        $("#recordingProgress").html("Saving recording ..");
        userService.callService($scope, "finishRecording").then(function (response) {
            // console.log(' ============= Saved recording ========== ', response);

            if (response.status.statusCode == 200) {
                //$scope.recordingProgress = "Ended successfully!";
                if (sessionType === 'Admin') {
                    $("#recordingProgress").html("<a href='../" + response.packages[0].videoUrl + "'>Watch Recorded session</a>");
                } else {
                    $("#recordingProgress").html("<a href='" + response.packages[0].videoUrl + "'>Watch Recorded session</a>");
                }

                //window.location.href = window.location.href + "&video_url=" +  + "&success=1&q_message=Live Session Ended successfully!";
            } else {
                $scope.recordingProgress = response.status.responseText;
                $("#recordingProgress").html(response.status.responseText);
            }


        }).catch(function (error) {
            console.log("Error!" + error);
            $scope.errorMessage = "Could not start live stream ..";
        });
    }

    $("#recordingProgress").attr("style", "display:none");

    if (uploadVideo != null && uploadVideo == 'Y') {
        saveRecording();
    }

    function getConstraints() {

        var constraints = {
            video: {

            },
            audio: true
        }

        constraints.video.frameRate = 15;

        if ($scope.cameraVideoProfile == '240p_1') {
            constraints.video.height = 240;
            constraints.video.width = 426;
            constraints.video.minBitrate = 100;
            constraints.video.maxBitrate = 400;
        } else if ($scope.cameraVideoProfile == '360p_1') {
            constraints.video.height = 360;
            constraints.video.width = 640;
            constraints.video.minBitrate = 100;
            constraints.video.maxBitrate = 400;
        } else if ($scope.cameraVideoProfile == '480p_1') {
            constraints.video.height = 480;
            constraints.video.width = 854;
            constraints.video.minBitrate = 200;
            constraints.video.maxBitrate = 1000;
        } else if ($scope.cameraVideoProfile == '720p_1') {
            constraints.video.height = 720;
            constraints.video.width = 1280;
            constraints.video.minBitrate = 400;
            constraints.video.maxBitrate = 1500;
            constraints.video.frameRate = 30;
        } else if ($scope.cameraVideoProfile == '1080p_1') {
            constraints.video.minBitrate = 3500;
            constraints.video.maxBitrate = 5000
            constraints.video.height = 1080;
            constraints.video.width = 1920;
            constraints.video.frameRate = 30;
        }

        return constraints;
    }

    try {
        Flashphoner.init({ flashMediaProviderSwfLocation: '../../../../media-provider.swf' });
    } catch (e) {
        $("#notifyFlash").text("Your browser doesn't support Flash or WebRTC technology needed for this example");
        return;
    }

    var currentStream;

    function getVideoDevices() {
        $scope.cameras = [];
        Flashphoner.getMediaDevices(null, true).then(function (list) {

            // console.log("Fetching media devices ..");
            list.video.forEach(function (device) {
                // console.log(device);
                $scope.cameras.push(device);
            });
        });
    }

    function networkBandwidth() {
        // console.log("bandiwdth:", currentStream.getNetworkBandwidth());
    }

    function updateRecordInfo() {
        // console.log("Record info" + currentStream.getRecordInfo());
    }

    $scope.androidAppMessage = false;

    $scope.rtmpUrl = "rtmp://dev.edofox.com:1935/live/" + channelId + "-" + sessionId;

    function playStream() {



        $scope.errorMessage = "Receiving stream ..";
        var session = Flashphoner.getSessions()[0];
        //var streamName = channelName;
        var streamName = channelId + "-" + sessionId;

        // console.log("Receiving RTMP stream for .. " + streamName, $scope.sessionName);
        session.createStream({
            name: streamName,
            display: localVideo
        }).on(STREAM_STATUS.PENDING, function (stream) {
            var video = document.getElementById(stream.id());
            if (!video.hasListeners) {
                video.hasListeners = true;
                video.addEventListener('resize', function (event) {
                    resizeVideo(event.target);
                });
            }
        }).on(STREAM_STATUS.PLAYING, function (stream) {
            //setStatus("#playStatus", stream.status());
            //onPlaying(stream);
            $("#full-screen-video").attr('style', "");
            // console.log("Playing!");
            $scope.joined = true;
            $scope.errorMessage = "";
            $scope.$apply();
            currentStream = stream;
            $scope.studentsChanged();
            if ($scope.showComments) {
                loadMessages();
            }
            setInterval(calculateNetworkBandwidth, 15000);
            setStatus("streaming");

        }).on(STREAM_STATUS.STOPPED, function () {
            //setStatus("#playStatus", STREAM_STATUS.STOPPED);
            //onStopped();
            // console.log("Stopped!");
        }).on(STREAM_STATUS.FAILED, function (stream) {
            //setStatus("#playStatus", STREAM_STATUS.FAILED, stream);
            //onStopped();

            if (stream) {
                // console.log("Failed! Trying to reconnect ..", stream, stream.getInfo());

                switch (stream.getInfo()) {
                    case STREAM_STATUS_INFO.SESSION_DOES_NOT_EXIST:
                        //$("#playInfo").text("Actual session does not exist").attr("class", "text-muted");
                        $scope.errorMessage = "Please start the RTMP stream";
                        break;
                    case STREAM_STATUS_INFO.STOPPED_BY_PUBLISHER_STOP:
                        //$("#playInfo").text("Related publisher stopped its stream or lost connection").attr("class", "text-muted");
                        $scope.errorMessage = "Stream stopped";
                        break;
                    case STREAM_STATUS_INFO.SESSION_NOT_READY:
                        //$("#playInfo").text("Session is not initialized or terminated on play ordinary stream").attr("class", "text-muted");
                        $scope.errorMessage = "Please start the RTMP stream";
                        break;
                    default:
                        $scope.errorMessage = "There is some problem with the live classroom. Please try again.";
                        break;
                }

                prevError = stream.getInfo();


            } else {
                // console.log("Failed! General ..");

            }

            setTimeout(playStream, 2000);
            $scope.$apply();


        }).play();
    }

    function publishStream(sharing) {
        var session = Flashphoner.getSessions()[0];
        var constraints = getConstraints();
        if (sharing) {
            //constraints.video.type = "screen";
            //constraints.video.withoutExtension = true;
            constraints = {
                video: {
                    // width: 640,
                    // height: 480,
                    // frameRate: 30,
                    height: 480,
                    width: 854,
                    minBitrate: 500000,
                    maxBitrate: 2000000,
                    frameRate: 30,
                    type: "screen",
                    withoutExtension: true
                },
                audio: true
            }
        }
        var streamName = channelId + "-" + sessionId;
        //var streamName = "1a5w-pf3j-fqx5-53me";
        // console.log("Publishing stream  " + streamName + " with screen share as " + sharing + " with record as " + $scope.recordStatus.alignRecord, constraints + " and video " + localVideo);

        session.createStream({
            name: streamName,
            display: localVideo,
            record: $scope.recordStatus.alignRecord,
            constraints: constraints,
            cacheLocalResources: true,
            receiveVideo: false,
            receiveAudio: false
        }).on(STREAM_STATUS.PUBLISHING, function (stream) {
            //setStatus("#publishStatus", STREAM_STATUS.PUBLISHING);
            //onPublishing(stream);
            $("#full-screen-video").attr('style', "");
            currentStream = stream;
            $scope.joined = true;
            $scope.$apply();
            // console.log("Done publishing!", stream);
            $scope.errorMessage = "";
            $scope.studentsChanged();
            if ($scope.showComments) {
                loadMessages();
            }
            setInterval(calculateNetworkBandwidth, 15000);
            // console.log("Recorded lecture", currentStream.getRecordInfo());
            setTimeout(updateRecordInfo, 5000);
            setStatus("streaming");
        }).on(STREAM_STATUS.UNPUBLISHED, function () {
            //setStatus("#publishStatus", STREAM_STATUS.UNPUBLISHED);
            //onUnpublished();
            // console.log("Done unpublishing!");
            $scope.joined = false;
            $scope.errorMessage = "Live classroom stopped";
            $scope.$apply();
            // console.log("Recorded lecture", currentStream.getRecordInfo());
        }).on(STREAM_STATUS.FAILED, function (stream) {
            //setStatus("#publishStatus", STREAM_STATUS.FAILED);
            //onUnpublished();
            // console.log("Done failed!", stream.getInfo());
            $scope.errorMessage = "Live classroom failed .. Please check your internet connection .. ";
            $scope.$apply();
            // console.log("Recorded lecture");

            if (stream) {
                if (stream.published()) {
                    switch (stream.getInfo()) {
                        case STREAM_STATUS_INFO.STREAM_NAME_ALREADY_IN_USE:
                            //$("#publishInfo").text("Server already has a publish stream with the same name, try using different one").attr("class", "text-muted");
                            $scope.errorMessage = "Someone already started a lecture in this classroom. Please use a different lecture or different classroom."
                            break;
                        case STREAM_STATUS.FAILED_BY_DTLS_ERROR:
                            $scope.errorMessage = "Could not join classroom. Please download the Android app for better experience.";
                            $scope.androidAppMessage = true;
                            break;
                    }
                }
            }

        }).on(STREAM_STATUS.STOPPED, function () {
            //setStatus("#publishStatus", STREAM_STATUS.FAILED);
            //onUnpublished();
            // console.log("Done stopped!");
            $scope.errorMessage = "Live classroom stopped ..";
            $scope.$apply();
            // console.log("Recorded lecture", currentStream.getRecordInfo());
        }).on(STREAM_STATUS.PLAYBACK_PROBLEM, function () {
            //setStatus("#publishStatus", STREAM_STATUS.FAILED);
            //onUnpublished();
            // console.log("Done playback issue!");
            $scope.errorMessage = "There is some problem with your stream .. Please check your internet connection .. ";
            $scope.$apply();
            // console.log("Recorded lecture", currentStream.getRecordInfo());
        })
            .publish();
    }


    function uploadSpeed(value, duration) {
        // console.log("Upload speed is ", value);
        if (value == -1) {
            //Internet is disconnected
            // console.log("Internet is disconnected ...");
            $scope.errorMessage = "Please check your internet connection .. ";
            $.snackbar({
                content: "Please check your internet connection .. "
            });
        } else {
            $scope.uploadSpeed = parseFloat(value);
            $scope.errorMessage = "";
            setConnectivity();

        }
        $scope.$apply();
    }

    function calculateNetworkBandwidth() {
        Demo1 = new JQSpeedTest({
            testStateCallback: null,
            testFinishCallback: null,
            testDlCallback: null,
            testUlCallback: uploadSpeed,
            testReCallback: null,
            downloadTest: false,
            uploadTest: true
        });
    }


    function connect(screenShared) {
        //var url = $('#urlServer').val();

        if ($scope.lectureUrl != null && $scope.lectureUrl.length > 0) {
            // console.log("Setting url", $scope.lectureUrl);
            $('#watchVideo').attr('src', $scope.lectureUrl);
            $scope.errorMessage = "";
            $scope.joined = true;
            $scope.studentsChanged();
            if ($scope.showComments) {
                loadMessages();
            }
            //setInterval(calculateNetworkBandwidth, 15000);
            setStatus("streaming");
            return;
        }

        $scope.errorMessage = "Connecting..";
        //create session
        
        // console.log("Create new session with url " + wcs + " and STUN URL turn:dev.edofox.com:3478 ");
        Flashphoner.createSession({ urlServer: wcs, mediaOptions: {"iceServers": [ { 'url': 'turn:dev.edofox.com:3478?transport=tcp', 'credential': 'coM77EMrV7Cwhyan', 'username': 'flashphoner' } ]} }).on(SESSION_STATUS.ESTABLISHED, function (session) {
            $scope.errorMessage = "Going live ..";
            //setStatus("#connectStatus", session.status());
            if (Browser.isSafariWebRTC()) {
                Flashphoner.playFirstVideo(localVideo, true, null).then(function () {
                    if ($scope.recordStatus.rtmp) {
                        playStream();
                    } else {
                        publishStream(screenShared);
                    }

                });
                return;
            }
            if ($scope.recordStatus.rtmp) {
                playStream();
            } else {
                publishStream(screenShared);
                getVideoDevices();
            }


            //onConnected(session);
        }).on(SESSION_STATUS.DISCONNECTED, function () {
            //setStatus("#connectStatus", SESSION_STATUS.DISCONNECTED);
            //onDisconnected();
            $scope.errorMessage = "Conenction failed (Disconnected) ..";
        }).on(SESSION_STATUS.FAILED, function () {
            //setStatus("#connectStatus", SESSION_STATUS.FAILED);
            //onDisconnected();
            $scope.errorMessage = "Conenction failed ..";
        });
    }


    function disconnect() {
        currentStream.stop();
        var session = Flashphoner.getSessions()[0];
        session.disconnect();
    }



    $scope.joinChannel = function () {
        // console.log("Trying to connect ...");
        connect();
    }

    $scope.fullScreen = true;

    $scope.toggleFullScreen = function () {
        if ($scope.fullScreen) {
            $("#full-screen-video").attr("style", "height:30vw");
            $scope.fullScreen = false;
        } else {
            $("#full-screen-video").attr("style", "");
            $scope.fullScreen = true;
        }
    }

    // attach file 
    $scope.attachFile = function () {
        $("#fileUploadModal").modal('show');
    }


    // upload file
    $scope.uploadFile = function () {

        var fileList = document.getElementById("attachment").files;
        if (fileList == null || fileList.length == 0) {
            $scope.fileProgress = "Please select a file!";
            return;
        }

        $scope.fileProgress = "Uploading ..";
        // console.log("Uploading file ", fileList);
        // Points to 'forumFiles'
        var filesRef = storageRef.child('forumFiles/' + fileList[0].name);
        filesRef.put(fileList[0]).then(function (snapshot) {
            // console.log('Uploaded a file!', snapshot);
            snapshot.ref.getDownloadURL().then(function (downloadURL) {
                // console.log('File available at', downloadURL);
                $scope.message = $scope.fileMessage;
                $scope.fileName = fileList[0].name;
                if ($scope.message == null || $scope.message.trim().length == 0) {
                    $scope.message = "File attachment " + $scope.fileName;
                }
                $scope.fileUrl = downloadURL;
                $scope.sendMessage();
                $scope.fileProgress = "";
                $("#fileUploadModal").modal('hide');

            });
        });

    }

    $scope.errorMessage = "";


    $scope.uploadSpeed = 0;

    // client.on('network-quality', function (stats) {
    //     if ($scope.joined) {
    //         console.log('downlinkNetworkQuality', stats.downlinkNetworkQuality);
    //         console.log('uplinkNetworkQuality', stats.uplinkNetworkQuality);
    //         if (stats.uplinkNetworkQuality != $scope.uploadSpeed) {
    //             $scope.uploadSpeed = stats.uplinkNetworkQuality;
    //             $scope.$apply();
    //             setConnectivity();
    //             console.log("Speed changed .." + $scope.uploadSpeed);
    //         }

    //     }

    // });

    $scope.networkQuality = function () {
        // bad five-bars
        if ($scope.uploadSpeed > 0) {
            if ($scope.uploadSpeed >= 5) {
                return "good five-bars";
            }
            if ($scope.uploadSpeed >= 3) {
                return "good four-bars";
            }
            if ($scope.uploadSpeed >= 2) {
                return "good three-bars";
            }
            if ($scope.uploadSpeed >= 1) {
                return "good two-bars";
            }
            if ($scope.uploadSpeed < 1) {
                return "good one-bar";
            }
            if ($scope.uploadSpeed < 0.5) {
                return "bad five-bars";
            }
        }
        if ($scope.uploadSpeed == -1) {
            return "bad five-bars";
        }
        return "";
    }


    $scope.qualityChanged = function () {
        //alert("changed!" + $scope.cameraVideoProfile);
        //console.log("Camera=>" , localStreams.camera.stream);
        // if (!$scope.joined) {
        //     return;
        // }
        // if ($scope.recording) {
        //     console.log("Stop recording before quality change ..");
        //     stopRecording();
        // }
        // var uid = localStreams.uid;
        // localStreams.camera.stream.stop(); // stop the camera stream playback
        // localStreams.camera.stream.close(); // clean up and close the camera stream
        // client.unpublish(localStreams.camera.stream); // unpublish the camera stream
        // createCameraStream(uid, {});
        // //localStreams.camera.stream.setVideoProfile($scope.cameraVideoProfile);
        // console.log("Video quality changed to .." + $scope.cameraVideoProfile);
    }

    $scope.leaveChannel = function () {
        // console.log("Leaving channel now ..");
        //disconnect();
        $("#saveConfirmModal").modal('show');
    }

    $scope.saveRecording = function () {

        var location = window.location.href.replace("#/messages", "");
        location = window.location.href.replace("#/polls", "");

        if ($scope.recordStatus.alignRecord) {
            $scope.saveProgress = "Ending lecture ..";
            window.location.href = location + "&recording=Y&success=1&q_message=Live Session Ended successfully!";
        } else {
            $scope.saveProgress = "Ending lecture ..";
            window.location.href = location + "&success=1&q_message=Live Session Ended successfully!";
        }


    }




    $scope.cameras = [];
    $scope.showCameras = false;

    $scope.selectedCam = {
        id: -1
    };

    $scope.switchCamera = function () {
        if ($scope.selectedCam.id < 0) {
            return;
        }
        //changeStreamSource($scope.selectedCam.id, "video");
        $("#cameras").modal('hide');
    }


    $scope.getDevices = function () {
        //console.log(devices.cameras);
        //$("#cameras").modal('show');
        // $scope.cameras = devices.cameras;
        // console.log("Camera=>" + $scope.cameras[0].label);
        //$scope.$apply();
        //return devices.cameras;
        currentStream.switchCam().then(function (id) {
            // console.log("Camera switched successfully!", id);
        }).catch(function (e) {
            console.log("Error " + e);
        });
    }

    function toggleMic() {

        $("#mic-icon").toggleClass('fa-microphone').toggleClass('fa-microphone-slash'); // toggle the mic icon
    }

    function toggleVideo() {

        $("#video-icon").toggleClass('fa-video').toggleClass('fa-video-slash'); // toggle the video icon
    }

    $scope.muteAudio = function () {
        // console.log("Mute audio ..");
        if (currentStream.isAudioMuted()) {
            currentStream.unmuteAudio();
        } else {
            currentStream.muteAudio();
        }
        toggleMic();
    }

    $scope.muteVideo = function () {
        // console.log("Mute video ..");
        if (currentStream.isVideoMuted()) {
            currentStream.unmuteVideo();
        } else {
            currentStream.muteVideo();
        }
        toggleVideo();

    }

    $scope.rtmp = function () {
        $scope.recordStatus.rtmp = !$scope.recordStatus.rtmp;
    }


    $scope.shareLink = function () {
        $scope.shareUrl = window.location.href;
        $scope.shareUrl = $scope.shareUrl.replace("video-host.php", "video-client.php");
        $scope.shareUrl = $scope.shareUrl.replace("test-adminPanel/", "");
        $scope.shareUrl = $scope.shareUrl + "&access=public";
        $scope.shareUrl = $scope.shareUrl.replace("#/messages", "");
        $scope.shareUrl = $scope.shareUrl.replace("#/polls", "");
        $("#shareModal").modal('show');
    }

    $scope.copyRtmpUrl = function () {
        var link = document.getElementById("rtmp_url");
        link.select();
        link.setSelectionRange(0, 99999); /*For mobile devices*/

        /* Copy the text inside the text field */
        document.execCommand("copy");

        $.snackbar({
            content: "Copied RTMP URL to clipboard"
        });
        /* Alert the copied text */
        //alert("Copied to clipboard ");
        //$("#shareStatus").text("Copied to clipboard!");
    }

    $scope.copyToClipboard = function () {
        /* Select the text field */
        var link = document.getElementById("publicLink");
        link.select();
        link.setSelectionRange(0, 99999); /*For mobile devices*/

        /* Copy the text inside the text field */
        document.execCommand("copy");

        /* Alert the copied text */
        //alert("Copied to clipboard ");
        $("#shareStatus").text("Copied to clipboard!");
    }

    $scope.switch = function () {
        $scope.showCameras = true;
        //console.log("Cam ID - " + localStreams.camera.camId, devices.cameras);
        if (devices.cameras.length > 0 && localStreams.camera.camId != null) {
            //Find current device index
            for (var i = 0; i < devices.cameras.length; i++) {
                var camera = devices.cameras[i];
                if (camera.deviceId == localStreams.camera.camId) {
                    i = (i + 1) % devices.cameras.length;
                    //console.log("Changing device index to -> " + i);
                    //changeStreamSource(i, "video");
                    return;
                }
            }
        }

    }

    var isOnlineForDatabase = {
        name: "Admin",
        state: 'online',
        last_changed: firebase.database.ServerValue.TIMESTAMP,
        connectivity: $scope.uploadSpeed,
        channelId: channelId,
        sessionId: sessionId,
        sessionName: $scope.sessionName,
        maxStudents: maxStudents
    };

    var isOfflineForDatabase = {
        name: "Admin",
        state: 'offline',
        last_changed: firebase.database.ServerValue.TIMESTAMP,
        channelId: channelId,
        connectivity: 0,
        maxStudents: maxStudents
    };


    var userStatusDatabaseRef = firebase.database().ref("hostStatus/" + channelName);
    userStatusDatabaseRef.onDisconnect().set(isOfflineForDatabase).then(function () {
        // The promise returned from .onDisconnect().set() will
        // resolve as soon as the server acknowledges the onDisconnect() 
        // request, NOT once we've actually disconnected:
        // https://firebase.google.com/docs/reference/js/firebase.database.OnDisconnect

        // We can now safely set ourselves as 'online' knowing that the
        // server will mark us as offline once we lose connection.
        userStatusDatabaseRef.set(isOnlineForDatabase);

    });

    function setConnectivity() {
        isOnlineForDatabase.connectivity = $scope.uploadSpeed;
        userStatusDatabaseRef.set(isOnlineForDatabase).then(function () {
            // console.log("connectivity set for host in firebase ..", isOnlineForDatabase);
        });
    }

    function setStatus(status) {
        isOnlineForDatabase.state = status;
        userStatusDatabaseRef.set(isOnlineForDatabase).then(function () {
            // console.log("status changed as " + status + " for host in firebase ..", isOnlineForDatabase);
        });
    }



    //Changes for chat feature
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    $scope.messages = [];
    $scope.message = "";
    $scope.fileUrl = null;
    $scope.fileName = "";


    studentName = "Admin";
    userType = "Admin";
    $scope.studentId = adminId;

    $scope.attendees = [];

    //var docRef = db.collection("students").doc("16");

    var latestDoc = null;

    $scope.sendMessage = function () {
        //console.log("Sending message", $scope.message);
        if ($scope.message == null || $scope.message.length == 0) {
            var text = $("#text_message").val();
            if (text != null && text.length > 0) {
                $scope.message = text;
            } else {
                return;
            }
        }
        var day = new Date().getDate();
        if (day < 10) {
            day = "0" + day;
        }
        var month = new Date().getMonth() + 1;
        if (month < 10) {
            month = "0" + month;
        }
        var year = 1900 + new Date().getYear();
        var hours = new Date().getHours();
        if (hours < 10) {
            hours = "0" + hours;
        }
        var minutes = new Date().getMinutes();
        if (minutes < 10) {
            minutes = "0" + minutes;
        }
        var seconds = new Date().getSeconds();
        if (seconds < 10) {
            seconds = "0" + seconds;
        }
        var date = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
        //console.log("Date " + date);
        var message = {
            text: $scope.message,
            fromId: $scope.studentId.toString(),
            from: studentName,
            at: date,
            userType: userType,
            timestamp: firebase.firestore.FieldValue.serverTimestamp(),
            fileUrl: $scope.fileUrl,
            fileName: $scope.fileName,
            sessionId: parseInt(sessionId),
            sessionName: $scope.sessionName
        }
        //console.log("Adding message:", message);
        $scope.messages.push(message);

        //Scroll to bottom
        var d = $('#msg_history');
        d.scrollTop(d.prop("scrollHeight"));

        $scope.message = "";
        $("#text_message").val("");
        var docRef = db.collection("forum").doc(channelName);
        docRef.collection("messages").add(message).then(function (docRef) {
            //console.log("Document written with ID: ", docRef.id);
            $scope.fileUrl = null;
            $scope.fileName = "";
            document.getElementById('attachment').value = '';
            latestDoc = docRef.id;
            message.id = docRef.id;
        })
            .catch(function (error) {
                console.error("Error adding document: ", error);
            });
    }

    //console.log("Current server time==>", firebase.database.ServerValue.TIMESTAMP);

    $scope.formatDate = function (sec) {
        try {
            var date = new Date(sec);
            //console.log("Formatting - " + months[date.getMonth()] + " " + date.getHours() + ":" + date.getMinutes());
            var hours = date.getHours();
            var minutes = date.getMinutes();
            if (hours < 10) {
                hours = "0" + hours;
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            var result = months[date.getMonth()] + " " + date.getDate() + " | " + hours + ":" + minutes;
            return result;
        } catch (e) {
            //console.log(e);
        }
        return "";

    }

    var lastMessage = null;

    $scope.loadOlderMessages = function () {
        var docRef = db.collection("forum").doc(channelName);
        docRef.collection("messages").orderBy("timestamp", "desc").startAfter(lastMessage).limit(limit)
            .get().then(function (querySnapshot) {
                //console.log("Got messages", querySnapshot);
                //var source = doc.metadata.hasPendingWrites ? "Local" : "Server";
                //var messages = [];
                querySnapshot.forEach(function (doc) {
                    $scope.messages.unshift(doc.data());
                    lastMessage = doc;
                });
                //console.log("Loaded old messages", messages);
                $scope.$apply();
                //console.log("Current messages in scope : ", $scope.messages);
                //Scroll to bottom
                //var d = $('#msg_history');
                //d.scrollTop(d.prop("scrollHeight"));


            });
    }

    function contains(fromId) {

        if (fromId == $scope.studentId) {
            return true;
        }
        return false;
    }


    db.collection("forum").doc(channelName).get().then(function (doc) {
        if (doc.exists) {
            if (doc.data().comments != null) {
                $scope.showComments = doc.data().comments;
            } else {
                $scope.showComments = true;
            }

            // console.log("Comments=>", $scope.showComments);
            $scope.$apply();
        }
    });

    var unssubscribe = null;


    //Load Messages
    function loadMessages() {
        $scope.messages = [];
        var docRef = db.collection("forum").doc(channelName);


        docRef.get().then(function (doc) {
            if (doc.exists) {
                //console.log("forum:", doc.data());
                unssubscribe = docRef.collection("messages")
                    .where("sessionId", "==", parseInt(sessionId))
                    .orderBy("timestamp", "desc").limit(limit)
                    .onSnapshot(function (querySnapshot) {
                        //console.log("Got messages", querySnapshot);
                        //var source = doc.metadata.hasPendingWrites ? "Local" : "Server";
                        var messages = [];
                        querySnapshot.docChanges().forEach(function (change) {
                            if (change.type === "added") {

                                //console.log("New msg: ", change.doc.data(), $scope.messages.length);
                                var msg = change.doc.data();
                                msg.id = change.doc.id;
                                if ($scope.messages.length == 0) {
                                    //Page load .. reverse the array
                                    messages.push(msg);
                                } else if (!contains(msg.fromId)) {
                                    //New message .. just push
                                    $scope.messages.push(msg);
                                }

                                lastMessage = change.doc;


                            }

                        });

                        if (messages.length > 0) {
                            $scope.messages = messages.reverse();
                            //lastMessage = $scope.messages[$scope.messages.length - 1];
                        }

                        // var messages = [];
                        // querySnapshot.forEach(function (doc) {

                        // });
                        // $scope.messages = messages;
                        $scope.$apply();
                        //console.log("Current messages in scope : ", $scope.messages);
                        //Scroll to bottom
                        var d = $('#msg_history');
                        d.scrollTop(d.prop("scrollHeight"));
                        //console.log("Lastmessage", lastMessage.data());

                    });
            } else {
                // doc.data() will be undefined in this case
                //console.log("No such document! Adding now ", channelName);
                db.collection("forum").doc(channelName).set({
                    forumName: channelName,
                    instituteId: instituteId,
                    packageId: channelId,
                    comments: $scope.showComments
                })
                    .then(function () {
                        //console.log("Forum successfully added!");
                    })
                    .catch(function (error) {
                        console.error("Error writing forum: ", error);
                    });
            }
        }).catch(function (error) {
            console.log("Error getting document:", error);
        });

    }

    $scope.commentsChanged = function () {
        if (!$scope.joined) {
            alert("Please start the classroom first!");
            $scope.showComments = false;
            return;
        }
        if ($scope.showComments) {
            loadMessages();
            db.collection("forum").doc(channelName).set({
                comments: true
            })
                .then(function () {
                    // console.log("Forum comments on!");
                })
                .catch(function (error) {
                    console.error("Error writing forum: ", error);
                });
        } else {
            // console.log("Turn off comments listener ..");
            unssubscribe();
            db.collection("forum").doc(channelName).set({
                comments: false
            })
                .then(function () {
                    // console.log("Forum comments OFF!");
                })
                .catch(function (error) {
                    console.error("Error writing forum: ", error);
                });

        }
    }

    //Load Packages
    //getStudentPackages


    $scope.showAddPollModal = function () {
        $("#addPollModal").modal('show');
    }

    function addPollDetails() {
        // console.log("Adding Poll:", $scope.poll);

        //Scroll to bottom
        var d = $('#msg_history');
        d.scrollTop(d.prop("scrollHeight"));

        $scope.poll.correctAnswer = "" + $scope.poll.correctAnswer;
        //Parse time in int
        if ($scope.poll.minutes != null) {
            $scope.poll.minutes = parseInt($scope.poll.minutes);
        }
        if ($scope.poll.seconds != null) {
            $scope.poll.seconds = parseInt($scope.poll.seconds);
        }

        var docRef = db.collection("forum").doc(channelName);
        docRef.collection("polls").add($scope.poll).then(function (docRef) {
            // console.log("Poll written with ID: ", docRef.id);
            $scope.poll = null;
            $("#addPollModal").modal('hide');
            $scope.createPollProgress = "";
            $scope.$apply();
        })
            .catch(function (error) {
                console.error("Error adding document: ", error);
            });
    }

    $scope.addPoll = function () {

        if ($scope.poll == null) {
            return;
        }

        // if ($scope.poll.correctAnswer == null || $scope.poll.correctAnswer == "") {
        //     alert("Please set answer to the question!");
        //     return;
        // }

        $scope.poll.fromId = $scope.studentId;
        $scope.poll.studentName = studentName;
        $scope.poll.timestamp = firebase.firestore.FieldValue.serverTimestamp();
        $scope.poll.archived = false;

        $scope.createPollProgress = "Creating poll ..";

        var fileList = document.getElementById("question_image").files;
        if (fileList == null || fileList.length == 0) {
            addPollDetails();
        } else {
            // console.log("Uploading file ", fileList);
            // Points to 'forumFiles'
            var filesRef = storageRef.child('forumFiles/' + fileList[0].name);
            filesRef.put(fileList[0]).then(function (snapshot) {
                // console.log('Uploaded a file!', snapshot);
                snapshot.ref.getDownloadURL().then(function (downloadURL) {
                    // console.log('File available at', downloadURL);
                    $scope.poll.questionImage = downloadURL;
                    addPollDetails();

                });
            });


        }

    }

    $scope.polls = [];

    $scope.endPoll = function (poll) {

        $scope.pollProgress = "Ending poll..";
        var ended = { ended: true };
        var docRef = db.collection("forum").doc(channelName);
        docRef.collection("polls").doc(poll.id).set(ended, { merge: true }).then(function (docRef) {
            // console.log("Poll ended with ID: ", poll.id);
            $scope.pollProgress = "";
            poll.ended = true;
            $scope.$apply();

        })
            .catch(function (error) {
                console.error("Error adding document: ", error);
            });

    }

    $scope.deletePoll = function (poll) {

        $scope.pollProgress = "Deleting poll..";
        var ended = { archived: true };
        var docRef = db.collection("forum").doc(channelName);
        docRef.collection("polls").doc(poll.id).set(ended, { merge: true }).then(function (docRef) {
            // console.log("Poll deleted with ID: ", poll.id);
            $scope.pollProgress = "";

            if (poll.live) {
                var index = 0;
                for (index = 0; index < $scope.polls.length; index++) {
                    if ($scope.polls[index].id == poll.id) {
                        break;
                    }

                }
                // console.log("Removing element at ", index, $scope.polls, poll);
                $scope.polls.splice(index, 1);
                // console.log("After removing", $scope.polls);
            } else {
                var index = 0;
                for (index = 0; index < $scope.bufferedPolls.length; index++) {
                    if ($scope.bufferedPolls[index].id == poll.id) {
                        break;
                    }

                }
                // console.log("Removing element at ", index, $scope.bufferedPolls, poll);
                $scope.bufferedPolls.splice(index, 1);
                // console.log("After removing", $scope.bufferedPolls);
            }

            //poll.ended = true;
            $scope.$apply();

        })
            .catch(function (error) {
                console.error("Error adding document: ", error);
            });

    }

    $scope.liveChanged = function (poll) {
        var live = { live: true, timestamp: firebase.firestore.FieldValue.serverTimestamp() };
        var docRef = db.collection("forum").doc(channelName);
        docRef.collection("polls").doc(poll.id).set(live, { merge: true }).then(function (docRef) {
            // console.log("Poll made live : ", poll.id);
            var index = 0;
            for (index = 0; index < $scope.bufferedPolls.length; index++) {
                if ($scope.bufferedPolls[index].id == poll.id) {
                    break;
                }

            }
            // console.log("Removing element at ", index, $scope.bufferedPolls, poll);
            $scope.bufferedPolls.splice(index, 1);
            // console.log("After removing", $scope.bufferedPolls);
            //poll.ended = true;
            $scope.$apply();

        })
            .catch(function (error) {
                console.error("Error adding document: ", error);
            });
    }

    var currentTime;

    function timeDifference(poll) {

        // console.log("Calculating time difference for poll ", poll);

        userService.callService($scope, "systemTime").then(function (response) {
            // console.log(' ============= Time response ========== ', response);
            //$scope.errorMessage = "";

            if (response != null) {
                currentTime = response.currentTime;
                // console.log("Current time is ", currentTime);

                var seconds = 0;
                if (poll.minutes != null) {
                    seconds = parseInt(poll.minutes) * 60;
                }
                if (poll.seconds != null) {
                    seconds = seconds + parseInt(poll.seconds);
                }
                var timeElapsed = 0;
                if (poll.timestamp != null && poll.timestamp.seconds != null) {
                    timeElapsed = (currentTime / 1000) - poll.timestamp.seconds;
                }
                if (timeElapsed > seconds) {
                    poll.ended = true;
                    // console.log("Poll ended ", timeElapsed);
                    poll.minutes = 0;
                    poll.seconds = 0;
                } else {
                    var timeLeft = seconds - timeElapsed;
                    poll.minutes = parseInt(timeLeft / 60);
                    poll.seconds = parseInt(timeLeft % 60);
                    setInterval(function () {
                        if (poll.ended) {
                            return;
                        }
                        if (poll.seconds > 0) {
                            poll.seconds--;
                        } else {
                            if (poll.minutes > 0) {
                                poll.minutes--;
                                poll.seconds = 59;
                            } else {
                                poll.ended = true;
                            }

                        }
                        $scope.$apply();
                    }, 1000);
                }
            }
        }).catch(function (error) {
            console.log("Error!" + error);
        });


    }


    $scope.polls = [];
    $scope.bufferedPolls = [];

    function addPollToList(pollList, poll) {
        if (pollList.length == 0) {
            // console.log("added poll ", poll);
            pollList.push(poll);
        } else {
            var found = false;
            pollList.forEach(function (p) {
                if (p.id == poll.id) {
                    p = poll;
                    // console.log("Modified poll ", poll);
                    found = true;
                }
            });
            if (!found) {
                pollList.push(poll);
            }
        }
    }

    function loadPolls() {
        var docRef = db.collection("forum").doc(channelName);


        docRef.get().then(function (doc) {
            if (doc.exists) {
                //console.log("forum:", doc.data());
                pollsUnsubscribe = docRef.collection("polls")
                    .where("archived", "==", false)
                    .orderBy("timestamp", "asc").limit(50)
                    .onSnapshot(function (querySnapshot) {
                        // console.log("Got polls", querySnapshot);
                        //var source = doc.metadata.hasPendingWrites ? "Local" : "Server";

                        querySnapshot.docChanges().forEach(function (change) {
                            //if (change.type === "added") {

                            // console.log("Change", change);

                            if (change.type !== "removed") {

                                var poll = change.doc.data();
                                poll.id = change.doc.id;

                                if (poll.live && (poll.minutes != null || poll.seconds != null)) {
                                    timeDifference(poll);
                                }

                                docRef.collection("polls").doc(poll.id).collection("answers")
                                    .onSnapshot(function (querySnapshot) {

                                        var correctCount = 0;
                                        var solvedCount = 0;
                                        var option1Count = 0, option2Count = 0, option3Count = 0, option4Count = 0;

                                        querySnapshot.forEach(function (doc) {
                                            // doc.data() is never undefined for query doc snapshots
                                            // console.log("Student answer", doc.id, " => ", doc.data());
                                            var studentAnswer = doc.data();
                                            if (studentAnswer.correct) {
                                                correctCount++;
                                            }
                                            solvedCount++;
                                            if (studentAnswer.answer != null && poll.type != 'NUMBER') {
                                                if (studentAnswer.answer.indexOf("option1") >= 0) {
                                                    option1Count++;
                                                }
                                                if (studentAnswer.answer.indexOf("option2") >= 0) {
                                                    option2Count++;
                                                }
                                                if (studentAnswer.answer.indexOf("option3") >= 0) {
                                                    option3Count++;
                                                }
                                                if (studentAnswer.answer.indexOf("option4") >= 0) {
                                                    option4Count++;
                                                }
                                            }

                                        });

                                        poll.correctCount = correctCount;
                                        poll.solvedCount = solvedCount;
                                        poll.option1Count = option1Count;
                                        poll.option2Count = option2Count;
                                        poll.option3Count = option3Count;
                                        poll.option4Count = option4Count;

                                        $scope.$apply();
                                    });


                                if (poll.live) {
                                    addPollToList($scope.polls, poll);
                                } else {
                                    addPollToList($scope.bufferedPolls, poll);
                                }

                            }
                        });

                        $scope.$apply();
                        // console.log("Polls changed ", $scope.polls);

                    });
            }
        }).catch(function (error) {
            console.log("Error getting document:", error);
        });

    }


    $scope.messageType = function (msg) {
        // if (msg.type == 'Poll') {
        //     return "poll";
        // }
        if (msg.fromId == $scope.studentId) {
            return "sent_msg"
        } else {
            return "received_msg"
        }
    }

    $scope.isActive = function (pkg) {
        if (pkg.id == $scope.currentPackage.id) {
            return "active_chat"
        }
        return "";
    }

    //loadMessages();
    loadPolls();

    //Watch for users logging in

    function userIndex(student) {
        if ($scope.attendees.length > 0) {
            $scope.attendees.forEach(function (attendee) {
                if (attendee.id = student.id) {
                    return $scope.attendees.indexOf(attendee);
                }
            });
        }
        return -1;
    }


    var userStatues = firebase.database().ref('statuses/' + channelName);
    //.orderByChild('last_changed').startAt(start.getMilliseconds()).endAt(end.getMilliseconds());

    $scope.attendeeCount = 0;

    $scope.showStudents = true;
    $scope.showStudentList = false;


    $scope.showAllStudents = function () {
        $scope.showStudentList = !$scope.showStudentList;
        if ($scope.showStudentList) {
            loadStudents();
        } else {
            $scope.attendees = [];
        }
    }

    
    function loadStudents() {
        //console.log("Load students ..");
        if (!$scope.showStudents) {
            return;
        }
        userStatues.once('value').then(function (snapshot) {
            //var username = (snapshot.val() && snapshot.val().username) || 'Anonymous';
            // ...
            //console.log("Data", snapshot.val());

            if (snapshot == null || snapshot.val() == null) {
                return;
            }

            //var end = new Date();
            //var end = firebase.firestore.Timestamp.now().seconds;
            //end.setHours(23,59,59,59);
            //var start = new Date();
            //start.setHours(0,0,0,0);
            var start = firebase.firestore.Timestamp.now().seconds - (2 * 60 * 60);
            start = start * 1000; //milliseconds

            // console.log("Fetching students from ", start);

            var students = [];
            var studentId = null;
            var studentData = null;
            $scope.attendees = [];
            //if($scope.attendees.length == 0) {
            //Add all Online entries
            //$scope.attendeeCount = Object.keys(snapshot.val()).length;
            //console.log("Data", );
            $scope.attendeeCount = 0;
            Object.keys(snapshot.val()).forEach(function (key) {
                //console.log("Data", key);
                //TODO Later
                //studentId = key;
                studentData = snapshot.val()[key];
                //console.log("Student time" , studentData.last_changed, start);
                //studentData.id = studentId;
                if (studentData.state != 'offline' && (studentData.last_changed >= start)) {
                    if ($scope.showStudentList) {
                        $scope.attendees.push(studentData);
                    }
                    $scope.attendeeCount++;
                }
            });
            $scope.$apply();
            //console.log("Count=>", $scope.attendeeCount);
            return;
        });
    }

    $scope.studentsChanged = function () {
        if ($scope.showStudents) {
            studentsInterval = setInterval(loadStudents, studentsFrequency);
        } else {
            // console.log("Turn off students listener ..");
            clearInterval(studentsInterval);
            //userStatues.off();
        }
    }


    function publishVideoAgain() {

        // console.log("Publishing the video again ...");

        client.publish(localStreams.camera.stream, function (err) {
            // console.log('[ERROR] : publish local stream error: ' + err);
            setTimeout(publishVideoAgain, 3000);
        });

        if ($scope.recording) {
            stopRecording();
            $scope.startRecording();
        }

    }

    function publishScreenShare() {
        // console.log("Publishing screen share..", screenStream);
        client.publish(screenStream, function (err) {
            // console.log('[ERROR] : publish local stream error: ' + err);
            setTimeout(publishScreenShare, 3000);
        });
    }

    $scope.screenShared = false;

    $scope.sharingStyle = function () {
        //console.log("Sharing status ", $scope.screenShared)
        if ($scope.screenShared) {
            return "color:#FF0000;";
        } else {
            return "";
        }

    }

    //Screen sharing
    $scope.shareScreen = function () {

        if ($scope.screenShared) {
            //disconnect();
            //connect(false);
            //publishStream(true);
            currentStream.switchToCam();
            $scope.screenShared = false;
            return;
        }

        //disconnect();
        //connect(true);
        //publishStream(false);
        currentStream.switchToScreen("", true)
        $scope.screenShared = true;
        return;
    }

    // video streams for channel



    // helper methods
    function getCameraDevices() {

    }

    function getMicDevices() {

    }



});