var SESSION_STATUS = Flashphoner.constants.SESSION_STATUS;
var STREAM_STATUS = Flashphoner.constants.STREAM_STATUS;
var STREAM_STATUS_INFO = Flashphoner.constants.STREAM_STATUS_INFO;
var wcs = "wss://dev.edofox.com:9443";
var hls = "https://dev.edofox.com:8445";

//var channelName = 'Testing1'; // set channel name

// create client 

var app = angular.module("app");
app.controller('videoClient', function ($scope, userService, $location, $http) {

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
        //console.log("Uploading file ", fileList);
        //console.log("storageRef ", storageRef);
        // Points to 'forumFiles'
        var filesRef = storageRef.child('forumFiles/' + fileList[0].name);
        filesRef.put(fileList[0]).then(function (snapshot) {
            //console.log('Uploaded a file!', snapshot);
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

    //console.log("Client Controller called ...");

    // Due to broswer restrictions on auto-playing video, 
    // user must click to init and join channel
    $("#watch-live-btn").click(function () {
        //alert("Joining 1!");
        //$scope.joinChannel();
    });

    var initMessage = "Please wait for your teacher. Refresh the page if the problem persists."

    $scope.errorMessage = initMessage;

    $scope.joined = false;

    var currentStream;

    remoteVideo = document.getElementById("full-screen-video");


    /**,
            receiverLocation: '../../dependencies/websocket-player/WSReceiver2.js',
            decoderLocation: '../../dependencies/websocket-player/video-worker2.js' */

    try {
        Flashphoner.init({
            flashMediaProviderSwfLocation: '../../../../media-provider.swf'
        });
    } catch (e) {
        console.log("Error!", e);
        $("#errorText").text("Your browser doesn't support video lecture. Please consider using latest version of Chrome or Firefox.");
        $scope.errorMessage = "Your browser doesn't support video lecture. Please consider using latest version of Chrome or Firefox.";
        return;
    }

    $("#volumeControl").slider({
        range: "min",
        min: 0,
        max: 100,
        value: 80,
        step: 10,
        animate: true,
        slide: function (event, ui) {
            //WCS-2375. fixed autoplay in ios safari
            currentStream.unmuteRemoteAudio();
            currentVolumeValue = ui.value;
            currentStream.setVolume(currentVolumeValue);
        }
    }).slider("disable");

    // console.log("slider init!");

    var currentTime;

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
        //var userID = $("#userId").val();
        //var channelName = $("#channelId").val();

        //$scope.errorMessage = "Fetching Live session info ..";

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
                loadMessages();
                //$scope.$apply();
                //$('<iframe />'); // Create an iframe element
                //$($scope.session.videoUrl).appendTo('#watchVideo');
            }

        }).catch(function (error) {
            console.log("Error!" + error);
            $scope.errorMessage = "Could not fetch classrooms ..";
        });


    }

    function saveVideoActivity() {

        try {
            var studentId;

            var isnum = /^\d+$/.test($scope.studentId);
            if (isnum) {
                studentId = $scope.studentId;
            }

            $scope.dataObj = {
                student: {
                    id: studentId,
                    name: studentName,
                    rollNo: $scope.studentId,
                    currentPackage: {
                        id: channelId
                    }
                },
                requestType: 'LIVE_JOINED',
                feedback: {
                    id: $scope.hostDetails.sessionId
                }

            }

            userService.callService($scope, "saveVideoActivity").then(function (response) {
                // console.log(response);
                if (response.status.statusCode != 200) {
                    //swal("Error", response.status.responseText, "error");
                    // console.log("Could not save activity ..", response);
                    alert("Could not mark your attendance " + response.status.statusCode);
                    return;
                }
                // console.log("Activity saved!");
                $.snackbar({
                    content: "Your attedance is noted!"
                });

            }).catch(function (error) {

                // console.log("Could not save activity ..", error);
                alert("Could not mark your attendance " + error);

            });
        } catch (e) {
            alert("Could not mark your attendance " + e);
        }


    }

    function downloadSpeed(value, duration) {
        // console.log("Download speed is ", value);
        if (value == -1) {
            //Internet is disconnected
            // console.log("Internet is disconnected ...");
            $scope.errorMessage = "Please check your internet connection .. ";
            $.snackbar({
                content: "Please check your internet connection .. "
            });
        } else {
            $scope.downloadSpeed = parseFloat(value);
            $scope.errorMessage = "";
            setConnectivity();

        }
        $scope.$apply();
    }

    function calculateNetworkBandwidth() {
        Demo1 = new JQSpeedTest({
            testStateCallback: null,
            testFinishCallback: null,
            testDlCallback: downloadSpeed,
            testUlCallback: null,
            testReCallback: null,
            downloadTest: true,
            uploadTest: false
        });
    }

    function handleGeneralError() {
        if ($scope.joined) {
            $scope.errorMessage = "Disconnected. Please check your network connection.";
            if ($scope.hostDetails != null && $scope.hostDetails.state != 'streaming') {
                $scope.errorMessage = "Teacher has been disconnected from the classroom";
            }

        } else {

            if ($scope.hostDetails != null && $scope.hostDetails.state == 'offline') {
                $scope.errorMessage = "No teacher has joined the classroom yet";
            } else if ($scope.hostDetails != null && $scope.hostDetails.state == 'online') {
                $scope.errorMessage = "Please wait for teacher to start the classroom";
            } else {
                $scope.errorMessage = "Could not join classroom. Please download the Android app for better experience.";
                $scope.androidAppMessage = true;
            }
        }
    }

    var prevError;

    $scope.androidAppMessage = false;

    function playStream() {

        $scope.errorMessage = "Starting ..";
        var session = Flashphoner.getSessions()[0];
        //var streamName = channelName;
        var streamName = channelId + "-" + $scope.hostDetails.sessionId;

        // console.log("Creating stream for .. " + streamName, $scope.sessionName);
        session.createStream({
            name: streamName,
            display: remoteVideo
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
            saveVideoActivity();
            setInterval(calculateNetworkBandwidth, 15000);
            $("#buttons-container").attr("style", "");
            $("#volumeControl").slider("enable");
            //stream.setVolume(currentVolumeValue);
            setTimeout(function () {
                $("#full-screen-video").attr('style', "");
            }, 2000);
        }).on(STREAM_STATUS.STOPPED, function () {
            //setStatus("#playStatus", STREAM_STATUS.STOPPED);
            //onStopped();
            // console.log("Stopped!");
        }).on(STREAM_STATUS.FAILED, function (stream) {
            //setStatus("#playStatus", STREAM_STATUS.FAILED, stream);
            //onStopped();

            if (stream) {
                // console.log("Failed! Trying to reconnect ..", stream, stream.getInfo());

                if (prevError == STREAM_STATUS_INFO.STOPPED_BY_PUBLISHER_STOP) {
                    $scope.errorMessage = "Teacher has been disconnected from the classroom";
                    return;
                }

                switch (stream.getInfo()) {
                    case STREAM_STATUS_INFO.SESSION_DOES_NOT_EXIST:
                        //$("#playInfo").text("Actual session does not exist").attr("class", "text-muted");
                        $scope.errorMessage = "Please wait for teacher to start the classroom";
                        break;
                    case STREAM_STATUS_INFO.STOPPED_BY_PUBLISHER_STOP:
                        //$("#playInfo").text("Related publisher stopped its stream or lost connection").attr("class", "text-muted");
                        $scope.errorMessage = "Teacher has been disconnected from the classroom";
                        break;
                    case STREAM_STATUS_INFO.SESSION_NOT_READY:
                        //$("#playInfo").text("Session is not initialized or terminated on play ordinary stream").attr("class", "text-muted");
                        $scope.errorMessage = "Please wait for teacher to start the classroom";
                        break;
                    case STREAM_STATUS_INFO.RTSP_STREAM_NOT_FOUND:
                        //$("#playInfo").text("Rtsp stream not found where agent received '404-Not Found'").attr("class", "text-muted");
                        $scope.errorMessage = "There is some problem with the live classroom. Please try again.";
                        break;
                    case STREAM_STATUS_INFO.FAILED_TO_CONNECT_TO_RTSP_STREAM:
                        //$("#playInfo").text("Failed to connect to rtsp stream").attr("class", "text-muted");
                        $scope.errorMessage = "There is some problem with the live classroom. Please try again.";
                        break;
                    case STREAM_STATUS.FAILED_BY_DTLS_ERROR:
                        $scope.errorMessage = "Could not join classroom. Please download the Android app for better experience.";
                        $scope.androidAppMessage = true;
                        break;
                    default:
                        //$("#playInfo").text("Other: " + stream.getInfo()).attr("class", "text-muted");
                        handleGeneralError();
                        break;
                }

                prevError = stream.getInfo();


            } else {
                // console.log("Failed! General ..");
                handleGeneralError();
            }

            setTimeout(playStream, 2000);
            $scope.$apply();


        }).play();
    }

    function connect() {

        if ($scope.lectureUrl) {
            $('#watchVideo').attr('src', $scope.lectureUrl);
            $scope.errorMessage = "";
            $scope.joined = true;
            saveVideoActivity();
            setInterval(calculateNetworkBandwidth, 15000);
            return;
        }

        //var url = $('#urlServer').val();

        $scope.errorMessage = "Joining ..";
        //create session
        // console.log("Create new session with url " + wcs + " with TURN setup");
        Flashphoner.createSession({ urlServer: wcs, mediaOptions: {"iceServers": [ { 'url': 'turn:dev.edofox.com:3478?transport=tcp', 'credential': 'coM77EMrV7Cwhyan', 'username': 'flashphoner' } ]} }).on(SESSION_STATUS.ESTABLISHED, function (session) {
            //setStatus("#connectStatus", session.status());
            if (Browser.isSafariWebRTC()) {
                Flashphoner.playFirstVideo(localVideo, true, PRELOADER_URL).then(function () {
                    playStream();
                });
                return;
            }
            playStream();
            //onConnected(session);
        }).on(SESSION_STATUS.DISCONNECTED, function () {
            //setStatus("#connectStatus", SESSION_STATUS.DISCONNECTED);
            //onDisconnected();
            // console.log("Session disconnected!");
        }).on(SESSION_STATUS.FAILED, function () {
            //setStatus("#connectStatus", SESSION_STATUS.FAILED);
            //onDisconnected();
            // console.log("Session failed!");
        });
    }

    var userStatues = firebase.database().ref('statuses/' + channelName).orderByChild('state').equalTo('online');

    $scope.hlsPlayer = "true";

    function playUsingHls() {
        //Load via HLS

        $("#remoteVideo").attr("style", "");

        var streamName = channelId + "-" + $scope.hostDetails.sessionId;
        var player = videojs('remoteVideo');

        var src = hls + "/" + streamName + "/" + streamName + ".m3u8";
        // var key = $('#key').val();
        // var token = $("#token").val();
        // if (key.length > 0 && token.length > 0) {
        //     src += "?" + key + "=" + token;
        // }
        player.src({
            src: src,
            type: "application/vnd.apple.mpegurl"
        });
        player.play();
        $scope.joined = true;

        $scope.errorMessage = "";
        $scope.$apply();

        setInterval(calculateNetworkBandwidth, 15000);

        saveVideoActivity();

    }

    // join a channel
    $scope.joinChannel = function (playHls) {

        // console.log("Joining channel");
        var streamName = channelId + "-" + $scope.hostDetails.sessionId;

        $scope.errorMessage = "Establishing connection ..";

        //alert("Joining!");

        // console.log("Validating for max students ..");

        userStatues.once('value').then(function (snapshot) {

            if (snapshot != null && snapshot.val() != null) {
                var noOfStudents = Object.keys(snapshot.val()).length;
                // console.log("Checking against max students .. " + $scope.hostDetails.maxStudents + " with " + noOfStudents);
                if ($scope.hostDetails.maxStudents != null && noOfStudents > $scope.hostDetails.maxStudents) {
                    $scope.errorMessage = "Cannot join the classroom as maximum students limit reached!";
                    return;
                }
            }

            $scope.errorMessage = "Connecting ..";

            if (!playHls || playHls == 'false') {
                connect();
            } else {
                playUsingHls();
            }


        });
    }

    $scope.fullScreen = function () {
        currentStream.fullScreen();
    }


    $scope.networkQuality = function () {
        // bad five-bars
        if ($scope.downloadSpeed > 0) {
            if ($scope.downloadSpeed >= 5) {
                return "good five-bars";
            }
            if ($scope.downloadSpeed >= 3) {
                return "good four-bars";
            }
            if ($scope.downloadSpeed >= 2) {
                return "good three-bars";
            }
            if ($scope.downloadSpeed >= 1) {
                return "good two-bars";
            }
            if ($scope.downloadSpeed < 1) {
                return "good one-bar";
            }
            if ($scope.downloadSpeed < 0.5) {
                return "bad five-bars";
            }
        }
        if ($scope.downloadSpeed == -1) {
            return "bad five-bars";
        }
        return "";
    }

    //Chat features
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    $scope.messages = [];
    $scope.message = "";

    var userType = null;
    var instituteId = null;

    $scope.studentId = getCookie("studentId");
    var studentName = getCookie("studentName");

    //studentName = studentName.replace("+", " ");
    studentName = studentName.split('+').join(' ');

    // console.log("Chat loaded ...." + $scope.studentId + " Name " + studentName);


    var docRef = db.collection("forum").doc(channelName);

    //Watch for comments turned off
    docRef.onSnapshot(function (doc) {
        // ...
        // console.log("Changed!", doc.data());
        if (doc.data().comments != null) {
            $scope.hostComments = doc.data().comments;
        } else {
            $scope.hostComments = true;
        }
        $scope.$apply();
    });


    //Watch user when leaves the page

    var userStatusDatabaseRef

    function watchUser() {
        if ($scope.studentId == null || $scope.studentId == "") {
            return;
        }
        $scope.downloadSpeed = 0;
        var isOfflineForDatabase = {
            name: studentName,
            state: 'offline',
            last_changed: firebase.database.ServerValue.TIMESTAMP,
            connectivity: 0
        };

        var isOnlineForDatabase = {
            name: studentName,
            state: 'online',
            last_changed: firebase.database.ServerValue.TIMESTAMP,
            connectivity: $scope.downloadSpeed
        };

        userStatusDatabaseRef = firebase.database().ref("statuses/" + channelName + '/' + $scope.studentId);
        userStatusDatabaseRef.onDisconnect().set(isOfflineForDatabase).then(function () {
            // The promise returned from .onDisconnect().set() will
            // resolve as soon as the server acknowledges the onDisconnect() 
            // request, NOT once we've actually disconnected:
            // https://firebase.google.com/docs/reference/js/firebase.database.OnDisconnect

            // We can now safely set ourselves as 'online' knowing that the
            // server will mark us as offline once we lose connection.
            userStatusDatabaseRef.set(isOnlineForDatabase);
        });

    }

    $scope.saveStudentInfo = function () {

        studentName = $("#student_name").val();
        if (studentName == null || studentName.length == 0) {
            $scope.studentInfoError = "Please enter your name ..";
            return;
        }
        //setcookie("studentId", studentName + "-" + new Date().getTime(), time() + 36000);
        //setcookie("studentName", studentName, time() + 36000);
        // console.log("Saving student info .. " + studentName);
        $scope.studentId = studentName + "-" + new Date().getTime();
        document.cookie = "studentId=" + $scope.studentId + "; expires=Sun, 31 Dec 2023 12:00:00 UTC";
        document.cookie = "studentName=" + studentName + "; expires=Sun, 31 Dec 2023 12:00:00 UTC";
        $("#studentInfo").modal('hide');
        watchUser();
    }


    watchUser();

    function setConnectivity() {
        var isOnlineForDatabase = {
            name: studentName,
            state: 'online',
            last_changed: firebase.database.ServerValue.TIMESTAMP,
            connectivity: $scope.downloadSpeed
        };
        userStatusDatabaseRef.set(isOnlineForDatabase).then(function () {
            // console.log("connectivity set for user in firebase ..", isOnlineForDatabase);
        });
    }

    //$scope.hostDetails = {};

    //Listener for host connectivity ..
    var hostStatusRef = firebase.database().ref("hostStatus/" + channelName);

    hostStatusRef.on('value', function (snapshot) {
        // console.log("Host changed!", snapshot.val());
        if ($scope.hostDetails == null) {
            $scope.hostDetails = snapshot.val();
            if ($scope.hostDetails != null) {
                if ($scope.hostDetails.state == 'offline') {
                    $scope.errorMessage = "Please wait for your teacher to enter the classroom";
                } else if ($scope.hostDetails.state == 'online') {
                    $scope.errorMessage = "Teacher is starting the classroom";
                } else {
                    $scope.errorMessage = "Lecture has already started. Please join";
                }
            }

            getSession();
        } else {
            if (snapshot.val().name != null) {
                $scope.hostDetails.name = snapshot.val().name;
            }
            if (snapshot.val().state != null) {
                $scope.hostDetails.state = snapshot.val().state;
                if ($scope.hostDetails.state == 'offline') {
                    $scope.errorMessage = "Teacher has been disconnected from the classroom";
                }
            }
            if (snapshot.val().connectivity != null) {
                $scope.hostDetails.connectivity = snapshot.val().connectivity;
            }
            if (snapshot.val().sessionId != null && $scope.hostDetails.sessionId != snapshot.val().sessionId) {
                $scope.hostDetails.sessionId = snapshot.val().sessionId;
                getSession();
            }
            if (snapshot.val().sessionName != null) {
                $scope.hostDetails.sessionName = snapshot.val().sessionName;
            }
            if (snapshot.val().maxStudents != null) {
                $scope.hostDetails.maxStudents = snapshot.val().maxStudents;
            }

        }
        $scope.$apply();

        //console.log("Host changed as", $scope.hostDetails);
    });

    $scope.hostColor = function () {
        if ($scope.hostDetails == null) {
            return "";
        }
        if ($scope.hostDetails.connectivity > 0) {
            if ($scope.hostDetails.connectivity < 3) {
                return "background-color: green";
            }
            if ($scope.hostDetails.connectivity < 5) {
                return "background-color: yellow";
            }
            if ($scope.hostDetails.connectivity > 4) {
                return "background-color: red";
            }
        }
        return "";
    }


    $scope.fileUrl = null;
    $scope.fileName = "";

    $scope.sendMessage = function () {
        if ($scope.message == null || $scope.message.length == 0) {
            return;
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
            fromId: $scope.studentId,
            from: studentName,
            at: date,
            userType: userType,
            timestamp: firebase.firestore.FieldValue.serverTimestamp(),
            fileUrl: $scope.fileUrl,
            fileName: $scope.fileName,
            sessionId: parseInt($scope.hostDetails.sessionId),
            sessionName: $scope.hostDetails.sessionName
        }
        //console.log("Adding message:", message);
        $scope.messages.push(message);

        //Scroll to bottom
        var d = $('#msg_history');
        d.scrollTop(d.prop("scrollHeight"));

        $scope.message = "";
        //var docRef = db.collection("forum").doc(channelName);
        docRef.collection("messages").add(message).then(function (docRef) {
            // console.log("Document written with ID: ", docRef.id);
            $scope.fileUrl = null;
            $scope.fileName = "";
            document.getElementById('attachment').value = '';
            message.id = docRef.id;
        })
            .catch(function (error) {
                console.error("Error adding document: ", error);
            });
    }

    $scope.formatDate = function (sec) {
        try {
            var date = new Date(sec);
            //console.log("Formatting - " + months[date.getMonth()] + " " + date.getHours() + ":" + date.getMinutes());
            var result = months[date.getMonth()] + " " + date.getDate() + " | " + date.getHours() + ":" + date.getMinutes();
            return result;
        } catch (e) {
            console.log(e);
        }
        return "";

    }

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
        // if ($scope.messages != null && $scope.messages.length > 0) {
        //     $scope.messages.forEach(function (msg) {
        //         if (msg.id == messageId) {
        //             return true;
        //         }
        //     });
        //     return false;
        // }
        if (fromId == $scope.studentId) {
            return true;
        }
        return false;
    }


    var start = new Date();
    start.setHours(0, 0, 0, 0);

    var end = new Date();
    end.setHours(23, 59, 59, 999);

    //Load Messages
    function loadMessages() {
        //console.log("Loading messages ..");
        $scope.messages = [];
        docRef.get().then(function (doc) {
            if (doc.exists) {

                docRef.collection("messages")
                    .where("sessionId", "==", parseInt($scope.hostDetails.sessionId))
                    .orderBy("at", "desc")
                    .limit(limit)
                    .onSnapshot(function (querySnapshot) {
                        //var source = doc.metadata.hasPendingWrites ? "Local" : "Server";
                        // var messages = [];
                        // querySnapshot.forEach(function (doc) {
                        //     messages.push(doc.data());
                        // });
                        // $scope.messages = messages;

                        var messages = [];
                        querySnapshot.docChanges().forEach(function (change) {

                            if (change.type === "added") {
                                var msg = change.doc.data();
                                msg.id = change.doc.id;
                                //console.log("New msg: ", change.doc.data(), $scope.messages.length);
                                if ($scope.messages.length == 0) {
                                    //Page load .. reverse the array
                                    messages.push(msg);
                                } else if (!contains(msg.fromId)) {
                                    //New message .. just push
                                    $scope.messages.push(msg);
                                    //console.log("Added new message ", msg);
                                }
                                lastMessage = change.doc;
                            }
                            // if (change.type === "modified") {
                            //     console.log("Modified city: ", change.doc.data());
                            // }
                            // if (change.type === "removed") {
                            //     console.log("Removed city: ", change.doc.data());
                            // }
                        });

                        if (messages.length > 0) {
                            $scope.messages = messages.reverse();
                            //lastMessage = $scope.messages[$scope.messages.length - 1];
                        }


                        $scope.$apply();
                        //console.log("Current messages in scope : ", $scope.messages);
                        //Scroll to bottom
                        var d = $('#msg_history');
                        d.scrollTop(d.prop("scrollHeight"));

                    });
            } else {
                // doc.data() will be undefined in this case
                //console.log("No such document! Adding now ", channelName);
                db.collection("forum").doc(channelName).set({
                    forumName: channelName,
                    instituteId: instituteId,
                    packageId: channelId
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

    $scope.polls = [];

    $scope.unsolvedPolls = 0;

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
                    if ($scope.unsolvedPolls > 0) {
                        $scope.unsolvedPolls--;
                    }

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
                                if ($scope.unsolvedPolls > 0) {
                                    $scope.unsolvedPolls--;
                                }
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


    function loadPolls() {
        var docRef = db.collection("forum").doc(channelName);


        docRef.get().then(function (doc) {
            if (doc.exists) {
                //console.log("forum:", doc.data());
                pollsUnsubscribe = docRef.collection("polls")
                    .where("archived", "==", false)
                    .where("live", "==", true)
                    .orderBy("timestamp", "asc").limit(50)
                    .onSnapshot(function (querySnapshot) {
                        // console.log("Got polls", querySnapshot);
                        //var source = doc.metadata.hasPendingWrites ? "Local" : "Server";
                        $scope.polls = [];
                        $scope.unsolvedPolls = 0;
                        querySnapshot.docChanges().forEach(function (change) {
                            // console.log("Change", change);
                            if (change.type !== "removed") {

                                //console.log("New msg: ", change.doc.data(), $scope.messages.length);
                                var poll = change.doc.data();
                                poll.id = change.doc.id;
                                // if ($scope.messages.length == 0) {
                                //     //Page load .. reverse the array
                                //     messages.push(msg);
                                // } else if (!contains(msg.fromId)) {
                                //     //New message .. just push
                                //     $scope.messages.push(msg);
                                // }

                                if (!poll.ended) {
                                    $scope.unsolvedPolls++;
                                }


                                if (poll.live && (poll.minutes != null || poll.seconds != null)) {
                                    timeDifference(poll);
                                }



                                //Check if answer is already given for this poll
                                docRef.collection("polls").doc(poll.id).collection("answers")
                                    .where("from", "==", $scope.studentId)
                                    .get()
                                    .then(function (querySnapshot) {

                                        querySnapshot.forEach(function (doc) {
                                            // doc.data() is never undefined for query doc snapshots
                                            // console.log("Student answer", doc.id, " => ", doc.data());
                                            poll.correct = doc.data().correct;
                                            poll.answer = doc.data().answer;
                                            poll.answerSaved = true;
                                            if ($scope.unsolvedPolls > 0) {
                                                $scope.unsolvedPolls--;
                                            }
                                            $scope.$apply();
                                        });
                                    })
                                    .catch(function (error) {
                                        console.log("Error getting documents: ", error);
                                    });

                                if ($scope.polls.length == 0) {
                                    $scope.polls.push(poll);
                                    // console.log("added poll ", poll);
                                } else {
                                    var found = false;
                                    $scope.polls.forEach(function (p) {
                                        if (p.id == poll.id) {
                                            p = poll;
                                            // console.log("Modified poll ", p);
                                            found = true;
                                        }
                                    });
                                    if (!found) {
                                        $scope.polls.push(poll);
                                    }
                                }
                            } else {
                                // var found = false;
                                // $scope.polls.forEach(function (p) {
                                //     if (p.id == poll.id) {

                                //     }
                                // });

                            }

                        });

                        $scope.$apply();


                    });
            }
        }).catch(function (error) {
            console.log("Error getting document:", error);
        });

    }

    $scope.submitAnswer = function (poll) {

        // if ((poll.answer == null || poll.answer == "") && ) {
        //     alert("Please provide an answer!");
        //     return;
        // }

        // console.log("Selected answer", poll);

        if (poll.correctAnswer != null && poll.correctAnswer != "") {
            if (poll.type == 'SINGLE' || poll.type == 'NUMBER') {
                if (poll.answer == poll.correctAnswer) {
                    poll.correct = true;
                } else {
                    poll.correct = false;
                }
            } else if (poll.type == 'MULTIPLE') {
                var correct = false;
                if (poll.option1Selected) {
                    poll.answer = poll.option1;
                    if (poll.correctAnswer.indexOf("option1") >= 0) {
                        correct = true;
                    } else {
                        correct = false;
                    }
                }
                if (poll.option2Selected) {
                    poll.answer = poll.answer + "," + poll.option2;
                    if (poll.correctAnswer.indexOf("option2") >= 0) {
                        correct = true;
                    } else {
                        correct = false;
                    }
                }
                if (poll.option3Selected) {
                    poll.answer = poll.answer + "," + poll.option3;
                    if (poll.correctAnswer.indexOf("option3") >= 0) {
                        correct = true;
                    } else {
                        correct = false;
                    }
                }
                if (poll.option4Selected) {
                    poll.answer = poll.answer + "," + poll.option4;
                    if (poll.correctAnswer.indexOf("option4") >= 0) {
                        correct = true;
                    } else {
                        correct = false;
                    }
                }
                poll.correct = correct;
            }

        } else {
            poll.correct = true;
        }



        var studentAnswer = {
            from: $scope.studentId,
            answer: "" + poll.answer,
            timestamp: firebase.firestore.FieldValue.serverTimestamp(),
            correct: poll.correct,
            studentName: studentName
        }
        $scope.submitProgress = "Submitting answer ..";
        docRef.collection("polls").doc(poll.id).collection("answers").doc($scope.studentId).set(studentAnswer).then(function (ref) {
            // console.log("Document written with ID: ", ref);
            poll.answerSaved = true;

            $scope.submitProgress = "";

            if ($scope.unsolvedPolls > 0) {
                $scope.unsolvedPolls--;
            }

            $scope.$apply();

        })
            .catch(function (error) {
                console.error("Error adding document: ", error);
                poll.answerSaved = false;
            });
    }

    loadPolls();

    $scope.messageType = function (msg) {
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

    $scope.msgtoreply = "";
    $scope.message_resp = "";
    $scope.addReply = function (msg) {
        $("#addReplyModal").modal('show');
        $scope.msgtoreply = msg;
    }

    $scope.addComment = function () {
        $("#addReplyModal").modal('hide');
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
            text: $scope.message_resp,
            fromId: $scope.studentId.toString(),
            from: studentName,
            at: date,
            userType: userType

        }
        // console.log("Adding message:", message);
        $scope.messages.push(message);

        //Scroll to bottom
        var d = $('#msg_history');
        d.scrollTop(d.prop("scrollHeight"));

        $scope.message = "";
        //var docRef = db.collection("forum").doc(channelName);
        docRef.collection("messages").add(message).then(function (docRef) {
            //console.log("Document written with ID: ", docRef.id);
        })
            .catch(function (error) {
                console.error("Error adding document: ", error);
            });
    }

    //loadMessages();

});
