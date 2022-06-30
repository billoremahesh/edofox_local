console.log("In handler!");

import {
  toggleCamera,
  toggleMic,
  joinRoom,
  isCameraOff,
  isMicMuted,
  switchVideoMode,
  sendNotificationEvent,
  muteStudentLocalMic,
  getDeviceOptions,
  switchVideoSource,
  leaveRoom,
  turnOnLocalCamera,
} from "./antmedia.js";

import { CanvasDesigner } from "../js/canvas-designer-widget.js";

//canvas related
var canvas,
  annotation_canvas,
  designer = null;
var vid = document.getElementById("localVideo");

var vidScaled_h, vidScaled_w;
//Camera size scaling in Annotation window
const video_scale = 0.25;

var localStream;

//UI controls

var join_publish_button = document.getElementById("join_publish_button");
join_publish_button.addEventListener("click", startLecture, false);

var stop_publish_button = document.getElementsByClassName(
  "stop_publish_button"
);
if (stop_publish_button != null) {
  for (var i = 0; i < stop_publish_button.length; i++) {
    stop_publish_button[i].addEventListener("click", confirmEnd, false);
  }
}

var stop_publish_confirm_button = document.getElementById(
  "end_lecture_confirm_btn"
);
if (stop_publish_confirm_button != null) {
  stop_publish_confirm_button.addEventListener("click", exitLecture, false);
}

// Camera controls
var camera_controls = document.getElementsByClassName("video-btn");
if (camera_controls != null) {
  for (var i = 0; i < camera_controls.length; i++) {
    camera_controls[i].addEventListener("click", toggleCamera, false);
    camera_controls[i].addEventListener("click", toggle_camera_buttons, false);
  }
}

// Mic controls
var mic_controls = document.getElementsByClassName("mic-btn");
if (mic_controls != null) {
  for (var i = 0; i < mic_controls.length; i++) {
    mic_controls[i].addEventListener("click", toggleMic, false);
  }
}

//Annotation control
var annotation_control = document.getElementById("draw-btn");
if (annotation_control != null) {
  annotation_control.addEventListener("click", toggleAnnotation, false);
}

//Screen share controls
var screen_share_controls = document.getElementById("btnShare");
if (screen_share_controls != null) {
  screen_share_controls.addEventListener("click", toggleScreenShare, false);
}

// var device_list_control = document.getElementById("device_list");
// if (device_list_control != null) {
//   device_list_control.addEventListener("click", showDeviceList, false);
// }

$("button.device_list").click(function () {
  showDeviceList();
});

//Change Video source option

var change_video_source_btn = document.getElementById(
  "change_video_source_btn"
);
if (change_video_source_btn != null) {
  change_video_source_btn.addEventListener("click", changeVideoSource, false);
}

var video_preview = document.getElementById("video_preview");
if (video_preview != null) {
  video_preview.addEventListener("click", openFullscreen, false);
}

var mute_all_btn = document.getElementsByClassName("mute_all_students");
if (mute_all_btn != null) {
  for (var i = 0; i < mute_all_btn.length; i++) {
    mute_all_btn[i].addEventListener("click", muteAll, false);
  }
}

$("button.msg_send_btn").click(function () {
  sendMessage();
});


// $("a.remote_mic_action").click(function () {
//   console.log("Clicked", e);
//   toggleStudentMic(e);
// });

// $('.student_list').unbind('click', 'a.remote_mic_action', function(e) {
//   e.preventDefault();
//   console.log("Clicked toggle student mic");
//   var id = $(this).attr('id');
//   toggleStudentMic(id);
// });

$("input.text_message:text").keyup(function (event) {
  // Number 13 is the "Enter" key on the keyboard
  if (event.keyCode === 13) {
    //Cancel the default action, if needed
    event.preventDefault();
    sendMessage();
  }
});

var unreadMessages = 0;

var showingMessages = false;
var myOffcanvas = document.getElementById('messages_chat_window')
myOffcanvas.addEventListener('show.bs.offcanvas', function () {
  console.log('show');
  unreadMessages = 0;
  toggleControls("msg_count_badge", false);
  showingMessages = true;
});

myOffcanvas.addEventListener('hidden.bs.offcanvas', function () {
  console.log('hidden');
  showingMessages = false;
});

var view_recording_btn = document.getElementById("view_recording_btn");
if (view_recording_btn != null) {
  view_recording_btn.addEventListener("click", viewRecordingModal, false);
  view_recording_btn.style = "display:none";
}

var toggle_comments_btn = document.getElementsByClassName("toggle_comments");
if (toggle_comments_btn != null) {
  for (var i = 0; i < toggle_comments_btn.length; i++) {
    toggle_comments_btn[i].addEventListener("change", toggleComments, false);
  }
}

var initialState = true;
var connected = false;

function confirmEnd() {
  $("#leaveConfirmModal").modal("show");
}

function exitLecture() {
  leaveRoom();
  $("#leaveConfirmModal").modal("hide");
  //Refresh the page to reset
  window.location.href = window.location.href + "/true";
}

function startLecture() {
  console.log("STarting lecture now ..");
  toggleControls("message-alert", true);
  if (!connected) {
    $("#message-alert").text("Establishing connection ..");
  } else {
    $("#message-alert").text("Joining classroom ..");
  }

  joinRoom();
}

console.log("Added handler!");

function isAdmin() {
  if (type == "Admin") {
    return true;
  }
  return false;
}

//Upon successful connection with Antmedia

export function connectionSuccess() {
  console.log("Connected!!!");
  initialState = true;
  connected = true;
  //$("#message-alert").text("Connected!");
  toggleControls("message-alert", false);

  if (isAdmin()) {
    $("#join_publish_button").html("Start Lecture");
    toggleControls("video_preview", true);
    initDesigner();

    Snackbar.show({
      pos: "top-center",
      text: "Connected! Click on Start Lecture to go live",
    });
  } else {
    $("#join_publish_button").html("Join Lecture");

    Snackbar.show({
      pos: "top-center",
      text: "Connected! Click on Join Lecture to join",
    });
  }

  console.log("initialized");

  if (view_recording_btn) {
    view_recording_btn.style = "display:none";
  }

  //Connect to Edofox websocket as well

  console.log("Connecting to edofox WS");

  if (typeof connect === "function") {
    //Connect to web
    connect({
      studentId: userId,
      module: "Live",
      sessionId: streamId,
    });
  }
}

//Upon successfully joined the room

export function joinSuccess() {
  console.log("Join success UI changes here ..");

  // if (isAdmin()) {
  toggleControlsByClass("buttons-container", true);
  // }

  toggleControls("message-alert", false);
  toggleControls("messages_main_div", true);

  join_publish_button.style = "display:none";

  if (!isAdmin()) {
    muteStudentLocalMic();
    console.log("Muting the local mic for student ..");
    toggleControls("video_preview", true);
    toggleControls("messages_panel", true);
  }
}

//For changing recording status

function recordStream(streamId, status) {
  // const url = `https://mystream.edofox.com:5443/WebRTCAppEE/rest/v2/broadcasts/${streamId}/recording/${status}?recordType=mp4`;
  // //console.log(url);
  // fetch(url, {
  //   method: "PUT",
  // })
  //   .then((res) => res.text())
  //   .then((res) => console.log(res));

  var request = {
    student: {
      currentPackage: {
        streamId: streamId
      }
    },
    requestType: status
  };

  callAdminServiceJSONPost("updateLiveRecordingStatus", request)
  .then(function (result) {
    console.log("Got the response from recording service", result);
  })
  .catch(function (error) {
    // An error occurred
    //alert("Exception: " + error);
    console.log("Error in service call " + error);
  });

}

//Upon successfully publishing the video

export function publishSuccess() {
  console.log("Publishing!!!");

  toggleControlsByClass("buttons-container", true);
  toggleControls("messages_main_div", true);

  toggleControls("message-alert", false);

  toggleControls("video_preview", true);

  toggleControls("messages_panel", true);

  join_publish_button.style = "display:none";

  //Set recording ON
  //"https://mystream.edofox.com:5443/WebRTCAppEE/rest/broadcast/enableMp4Muxing?id=" + publishStreamId + "&enableMp4=1" //throwing 403

  if (!isAdmin()) {
    toggleRemoteUserElement("mic-btn", false);
    muteStudentLocalMic();
    console.log("Muting the local mic for student ..");
    //If comments are disabled
    if (comments == 0) {
      toggleCommentsSection(false);
    }
  } else if (recording == 1) {
    console.log("Starting the recording ..");
    recordStream(streamId, "START");
    //  var audio = document.getElementById("localVideo");
    // addToCheck(audio.srcObject,'Teacher');
  }
}

//Upon successfully existing the room

export function leaveSuccess() {
  console.log("Left the room succesfully!");

  if (isAdmin()) {
    recordStream(streamId, "STOP");
  }

  toggleControlsByClass("buttons-container", false);
  toggleControls("messages_main_div", false);
  toggleControls("message-alert", true);
  $("#message-alert").text("Disconnected from the lecture");

  toggleControls("video_preview", false);

  toggleControls("messages_panel", false);

  join_publish_button.style = "";
}

export function showConnectionError(msg) {
  //Show connection error
  $("#message-alert").text(msg);
}

//Handle camera UI based on cameraOff condition

export function handleCameraButtons() {
  if (isCameraOff) {
    $("#video-btn").attr("style", "color:gray;");
    //turn_off_camera_button.disabled = true;
    //turn_on_camera_button.disabled = false;
  } else {
    //turn_off_camera_button.disabled = false;
    //turn_on_camera_button.disabled = true;
    $("#video-btn").attr("style", "");
  }
}

//Handle mic UI based on micMuted condition

export function handleMicButtons() {
  if (isMicMuted) {
    $(".mic-btn")
      .find($(".fas"))
      .removeClass("fa-microphone")
      .addClass("fa-microphone-slash");
  } else {
    $(".mic-btn")
      .find($(".fas"))
      .removeClass("fa-microphone-slash")
      .addClass("fa-microphone");
  }
}

function toggleMicStudent() {
  if (initialState) {
    //As student has just joined..force unmute as its initial state
    toggleMic(true);
    initialState = false;
  } else {
    toggleMic();
  }
}

var screen = false;

export function toggleScreenShare() {
  if (isAnnotating) {
    disableAnnotation();
    turnOnLocalCamera();
    handleCameraButtons();
  }
  if (screen) {
    switchVideoMode("camera");
    $("#btnShare").attr("style", "color: gray;");
    screen = false;
  } else {
    if (isMicMuted) {
      alert("Please turn the mic on before starting screen share");
      return;
    }
    switchVideoMode("screen");
    $("#btnShare").attr("style", "");
    screen = true;
  }
}

export var isAnnotating = false;
var draw_interval = null;

function toggleAnnotation() {
  if (isAnnotating) {
    disableAnnotation();
    turnOnLocalCamera();
    handleCameraButtons();
  } else {
    enableAnnotation();
    //Keep existing state of camera
    if (!isCameraOff) {
      turnOnLocalCamera();
      handleCameraButtons();
    }
  }
}

function disableAnnotation() {
  $("#draw-btn").attr("style", "color: gray;");
  toggleControls("canvas-designer", false);
  canvas.style.display = "none";
  vid.style.display = "block";
  clearInterval(draw_interval);
  designer.clearCanvas();
  isAnnotating = false;
}

function enableAnnotation() {
  toggleControls("canvas-designer", true);
  canvas.style.display = "block";
  canvas.style.width = "100%";
  vid.style.display = "none";
  switchVideoMode("annotation");
  $("#draw-btn").attr("style", "");
  draw_interval = setInterval(function () {
    draw();
  }, 40);
  isAnnotating = true;
}

function toggleControls(id, show) {
  if (show) {
    $("#" + id).attr("style", "");
    console.log("Showing " + id);
  } else {
    $("#" + id).attr("style", "display:none");
  }
}

function toggleControlsByClass(class_name, show) {
  if (show) {
    $("." + class_name).attr("style", "");
    console.log("Showing " + class_name);
  } else {
    $("." + class_name).attr("style", "display:none");
  }
}

function toggle_camera_buttons() {
  console.log("inside camera toggle");
  if (isCameraOff) {
    $(".video-btn")
      .find($(".fas"))
      .removeClass("fa-video")
      .addClass("fa-video-slash");
  } else {
    $(".video-btn")
      .find($(".fas"))
      .removeClass("fa-video-slash")
      .addClass("fa-video");
  }
}

function fetchStudentDetails(obj, isPlayOnly) {
  var request = {
    student: {
      currentPackage: {
        sessionId: obj.streamId,
      },
    },
  };

  callStudentServiceJSONPost("getStreamInfo", request)
    .then(function (result) {
      console.log("Got the response for " + obj.streamId + " from service", result);
      if (result != null && result.student != null) {
        //Add remote user and create it from template
        //Check if existing element present
        var existing = document.getElementById("remoteStudent" + obj.streamId);
        var cln;
        if (existing != null) {
          cln = existing;
          console.log("Existing found for " + obj.streamId);
        } else {
          cln = document.getElementById("student_template").cloneNode(true);
          console.log("Cloned new for " + obj.streamId);
        }
        //console.log("Before", cln);
        cln.id = "remoteStudent" + obj.streamId;
        cln.getElementsByTagName("video")[0].id = "remoteVideo" + obj.streamId;
        cln.getElementsByTagName("span")[0].id = "remoteMicStatus" + obj.streamId;
        cln.getElementsByTagName("span")[1].id = "student_name" + obj.streamId;

        cln.getElementsByTagName("span")[1].innerHTML = result.student.name;

        //cln.getElementsByTagName("a")[0].addEventListener("click", toggleStudentMic, false);

        cln.getElementsByTagName("a")[0].id = "remoteStudentMicToggle" + obj.streamId;

        if (result.student.profilePic) {
          cln.getElementsByTagName("img")[0].id = "remoteProfilePic" + obj.streamId;
          cln.getElementsByTagName("img")[0].src = result.student.profilePic;
        }

        // cln.querySelector('#remoteVideoStatus').id = "remoteVideoStatus" + streamId;
        //cln.querySelector('#remoteMicStatus').id = "remoteMicStatus" + streamId;
        cln.style = "";

        //console.log(cln);

        $(".student_list").append(cln);

        $("a.remote_mic_action").unbind("click").click(function () {
          var id = $(this).attr("id");
          console.log("Called click", id);
          toggleStudentMic(id);
        });

        console.log("adding student to the list " + obj.streamId);

        //Update UI values
        //$(".student_name").text(result.student.name);



        toggleRemoteUserElement("remoteMicStatus" + obj.streamId, false);

        var video = document.getElementById("remoteVideo" + obj.streamId);
        video.srcObject = obj.stream;
        console.log("Added stream to video ", obj.stream, video);

        //addToCheck(obj.stream,result.student.name);

        if (isPlayOnly) {
          //Hide mic icon
          $("#remoteMicStatus" + obj.streamId).attr("style", "display:none");
          cln.getElementsByTagName("span")[0].style = 'display:none';
        } else {
          //Hide mic icon
          $("#remoteMicStatus" + obj.streamId).attr("style", "");
          cln.getElementsByTagName("span")[0].style = '';
        }
      }
    })
    .catch(function (error) {
      // An error occurred
      //alert("Exception: " + error);
      console.log("Error in service call " + error);
    });
}

var teacherStream;

export function createRemoteVideo(obj) {
  if (type == "Student" && streamId != obj.streamId) {
    //CHeck if the stream is from student/admin
    var request = {
      student: {
        currentPackage: {
          sessionId: obj.streamId,
        },
      },
    };

    if (teacherStream == null) {
      callStudentServiceJSONPost("getStreamInfo", request)
        .then(function (result) {
          console.log("Got the response from service", result);
          if (result != null && result.student == null) {
            //Student IS NULL thus stream is from admin
            var video = document.getElementById("remoteVideo");

            if (video != null) {
              video = document.getElementById("remoteVideo");
              video.srcObject = obj.stream;
            }

            console.log("playing admin " + obj.streamId);

            teacherStream = obj.streamId;

            toggleControls("video_preview", true);
            toggleControls("message-alert", false);
          }
        })
        .catch(function (error) {
          // An error occurred
          //alert("Exception: " + error);
          console.log("Error in service call " + error);
        });
    }
  } else {
    //Add user to the list as student

    fetchStudentDetails(obj);
  }
}

export function removeStreamElement(streamId) {
  $("#remoteStudent" + streamId).remove();

  if (teacherStream == streamId) {
    toggleControls("video_preview", false);
    toggleControls("message-alert", true);
    $("#message-alert").text("Teacher disconnected from the lecture");
    teacherStream = null;
  }
}

export function handleNotificationEvent(obj) {
  //console.log("Received data : ", obj.data);
  var notificationEvent = JSON.parse(obj.data);
  if (notificationEvent != null && typeof notificationEvent == "object") {
    var eventStreamId = notificationEvent.streamId;
    var eventTyp = notificationEvent.eventType;

    if (eventTyp == "CAM_TURNED_OFF") {
      console.log("Camera turned off for : ", eventStreamId);
      toggleRemoteUserElement("remoteVideoStatus" + eventStreamId, false);
    } else if (eventTyp == "CAM_TURNED_ON") {
      console.log("Camera turned on for : ", eventStreamId);
      toggleRemoteUserElement("remoteVideoStatus" + eventStreamId, true);
    } else if (eventTyp == "MIC_MUTED") {
      console.log("Microphone muted for : ", eventStreamId);
      toggleRemoteUserElement("remoteMicStatus" + eventStreamId, false, true);
    } else if (eventTyp == "MIC_UNMUTED") {
      console.log("Microphone unmuted for : ", eventStreamId);
      toggleRemoteUserElement("remoteMicStatus" + eventStreamId, true, false);
      //Unmute the audio if muted
      $("#remoteVideo" + eventStreamId).removeAttr("muted");
      console.log("Removed muted attribute from student .." + eventStreamId);
    } else if (eventTyp == "TOGGLE_STUDENT_MIC") {
      if (eventStreamId == streamId) {
        toggleMic();
        console.log("Toggling mic for student ..");

        Snackbar.show({
          pos: "top-center",
          text: "Teacher has changed your microphone status",
        });
      }
    } else if (eventTyp == "MUTE_ALL") {
      if (!isAdmin()) {
        muteStudentLocalMic();

        Snackbar.show({
          pos: "top-center",
          text: "Teacher has muted your mic",
        });
      }
    } else if (eventTyp == "NEW_MESSAGE") {
      if (eventStreamId != streamId) {
        addMessageToPanel(notificationEvent);
      }
      if (!showingMessages) {
        unreadMessages = unreadMessages + 1;
        toggleControls("msg_count_badge", true);
        $("#msg_count_badge").text(unreadMessages);
      }
    } else if (eventTyp == "COMMENTS_OFF") {
      toggleCommentsSection(false);

      Snackbar.show({
        pos: "top-center",
        text: "Comments are disabled by the teacher",
      });

      console.log("disabled comments ..");
    } else if (eventTyp == "COMMENTS_ON") {
      toggleCommentsSection(true);

      Snackbar.show({
        pos: "top-center",
        text: "Comments are enabled by the teacher",
      });

      console.log("enabled comments ..");
    } else if (eventTyp == "PLAY_ONLY_JOIN") {
      //for adding student to students list
      fetchStudentDetails(notificationEvent, true);
    }
  }
}

//Toggle UI element for remote user

function toggleRemoteUserElement(elementId, enabled, micMuted) {
  if (enabled) {
    $("#" + elementId).attr("style", "");
    if (micMuted != undefined && micMuted == false) {
      $("#" + elementId)
        .find($(".fas"))
        .removeClass("fa-microphone-slash")
        .addClass("fa-microphone");
    }
  } else {
    $("#" + elementId).attr("style", "color:gray");
    if (micMuted) {
      $("#" + elementId)
        .find($(".fas"))
        .removeClass("fa-microphone")
        .addClass("fa-microphone-slash");
    }
  }
}

function showDeviceList() {
  getDeviceOptions("videoinput")
    .then(function (result) {
      console.log("Devices", result);
      $("#show_device_list").html("");
      result.forEach(function (item) {
        $("#show_device_list").append(
          "<div class='form-check'><input class='form-check-input' value='" +
          item.deviceId +
          "' type='radio' name='video_source' id='video_source'><label class='form-check-label' for='video_source'>" +
          item.label +
          " </label></div>"
        );
      });

      $("#devicesModal").modal("show");
    })
    .catch(function (error) {
      // An error occurred
      //alert("Exception: " + error);
    });
}

function changeVideoSource() {
  var sourceId = $('input[name="video_source"]:checked').val();
  if (!sourceId) {
    alert("Please select a video source to change to!");
    return;
  }
  console.log("Selected video source ", sourceId);
  switchVideoSource(sourceId);
}

jQuery("a.remote_mic_action").click(function () {
  console.log("Clicked", this);
});

$(".remote_mic_action").click(function () {
  console.log("Clicked", this);
  if (isAdmin()) {
    var id = this.id;
    //Toggle mic of the participant
    var streamId = id.replace("remoteMicStatus", "");
    console.log("Toggle mic for .. " + streamId);
    sendNotificationEvent("TOGGLE_STUDENT_MIC", streamId);
  }
});

function toggleStudentMic(id) {
  //console.log("Clicked", e.srcElement.parentElement.id);
  if (isAdmin()) {
    var streamId = id.replace("remoteStudentMicToggle", "");
    console.log("Toggle mic for .. " + streamId);
    sendNotificationEvent("TOGGLE_STUDENT_MIC", streamId);
  }
}

function muteAll() {
  sendNotificationEvent("MUTE_ALL");
  //Change status of ALL to muted
  var ancestor = document.getElementsByClassName("student_list");
  if (ancestor == undefined || ancestor == "") {
    return;
  }
  for (var j = 0; j < ancestor.length; j++) {
    var descendents = ancestor[j].getElementsByTagName("span");
    if (descendents != null && descendents.length > 0) {
      for (var i = 0; i < descendents.length; i++) {
        var id = descendents[i].id;
        console.log("ID is " + id);
        $("#" + id)
          .find($(".fas"))
          .removeClass("fa-microphone")
          .addClass("fa-microphone-slash");
        $("#" + id).attr("style", "color:gray");
      }
    }
  }
}

function openFullscreen() {
  var myVideo = $("#remoteVideo");
  console.log("going full screen");
  var elem = myVideo[0];
  console.log(elem);
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.mozRequestFullScreen) {
    /* Firefox */
    elem.mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) {
    /* Chrome, Safari & Opera */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) {
    /* IE/Edge */
    elem.msRequestFullscreen();
  }
}

function sendMessage() {
  var text = document.getElementsByClassName("text_message")[0].value;
  if (text == undefined || text == "") {
    text = document.getElementsByClassName("text_message")[1].value;
    if (text == undefined || text == "") {
      // alert("Please type some message");
      return;
    }
  }

  var msgObject = {
    streamId: streamId,
    eventType: "NEW_MESSAGE",
    text: text,
    from: username,
  };
  sendNotificationEvent("", "", msgObject);
  addMessageToPanel(msgObject);
  $(".text_message").val("");

  var objDiv = document.getElementById("msg_history");
  objDiv.scrollTop = objDiv.scrollHeight;
}

function addMessageToPanel(msgObject) {
  var type = "incoming";
  if (msgObject.streamId == streamId) {
    type = "outgoing";
  }

  var cln = document.getElementById("chat_template_" + type).cloneNode(true);
  cln.id = "msg_" + msgObject.streamId + "_" + new Date().getMilliseconds();
  cln.getElementsByTagName("p")[0].innerText = msgObject.text;

  var options = { hour: "numeric", minute: "numeric", second: "numeric" };
  var today = new Date();
  var formattedDate = today.toLocaleDateString("en-US", options);
  cln.getElementsByTagName("span")[0].innerText = formattedDate;
  if (type != "outgoing") {
    cln.getElementsByTagName("span")[0].innerText =
      cln.getElementsByTagName("span")[0].innerText + " by " + msgObject.from;
    //Replace profile pic if found
    var imgUrl = document.getElementById("remoteProfilePic" + msgObject.streamId) != null ? document.getElementById("remoteProfilePic" + msgObject.streamId).src : null;
    if (imgUrl != null) {
      cln.getElementsByTagName("img")[0].src = imgUrl;
    }
  }

  cln.style = "";

  $(".msg_history").append(cln);

  //console.log("adding msg to the list " + cln.id);
}

function viewRecordingModal() {
  document.getElementById("recordedPreviewModalBody").innerHTML = "";
  var dataString =
    "subtopic=" +
    lecture.name +
    "&videoUrl=" +
    lecture.videoUrl +
    "&testId=&status=&progress=";
  $.ajax({
    type: "POST",
    data: dataString,
    url: base_url + "/dlp/display_course_videos",
    success: function (data) {
      document.getElementById("recordedPreviewModalBody").innerHTML = data;
      $("#recordedPreviewModal").modal("show");
    },
  });
}

//initiate designer widget which does annotation

function initDesigner() {
  if (designer != null) {
    return;
  }
  designer = new CanvasDesigner();

  // you can place widget.html anywhere
  designer.widgetHtmlURL = base_url + "/assets/js/canvas-designer.html";
  designer.widgetJsURL = base_url + "/assets/js/canvas-designer.js";

  designer.setSelected("pencil");

  designer.setTools({
    pencil: true,
    text: true,
    image: true,
    pdf: false,
    eraser: true,
    line: true,
    arrow: true,
    dragSingle: true,
    dragMultiple: true,
    arc: false,
    rectangle: true,
    quadratic: false,
    bezier: false,
    marker: true,
    zoom: false,
    lineWidth: false,
    colorsPicker: true,
    extraOptions: false,
    code: false,
    undo: false,
  });

  designer.appendTo(document.getElementById("canvas-designer"));
  var iframe = document.getElementById("canvas-designer").firstElementChild;
  iframe.addEventListener("load", function () {
    var doc = iframe.contentDocument || iframe.contentWindow.document;
    canvas = document.getElementById("canvas");
    annotation_canvas = doc.getElementById("main-canvas");
    if (annotation_canvas != null) {
      annotation_canvas.style.display = "none";
    }
    var tmp_canvas = doc.getElementById("temp-canvas");
    localStream = canvas.captureStream(25);

    //get video for canvas with getUserMedia
    getUserMedia();

    var canvas_designer = document.getElementById("canvas-designer");
    canvas_designer.style.display = "none";
    canvas.style.display = "none";
  });
}

export function getUserMedia() {
  navigator.mediaDevices
    .getUserMedia({ video: true, audio: true })
    .then(function (stream) {
      var video = document.querySelector("video#localVideo");
      localStream.addTrack(stream.getAudioTracks()[0]);

      video.srcObject = stream;

      video.onloadedmetadata = function (e) {
        vidScaled_h = vid.videoHeight * video_scale;
        vidScaled_w = vid.videoWidth * video_scale;
        //annotation_canvas.width = vid.Height;
        //annotation_canvas.height = vid.Width;
        if (annotation_canvas != null) {
          canvas.width = annotation_canvas.width;
          canvas.height = annotation_canvas.height;
        }
        video.play();
      };
    });
}

function draw() {
  if (canvas.getContext) {
    var ctx = canvas.getContext("2d");
    // console.log(canvas.width);

    ctx.fillStyle = "white";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    ctx.drawImage(annotation_canvas, 0, 0);

    var vid_loc_x = canvas.width - vidScaled_w;
    if (!isCameraOff) {
      ctx.drawImage(vid, vid_loc_x, 0, vidScaled_w, vidScaled_h);
    }

    /* 
        ctx.font = "30px Arial";
          ctx.fillStyle = 'rgba(0, 200, 70, 1)';*/
  }
}

function updateSessionDetails(status) {
  var request = {
    student: {
      currentPackage: {
        sessionId: roomName,
        status: status,
      },
    },
  };

  callAdminServiceJSONPost("updateLiveLecture", request)
    .then(function (result) {
      console.log("Got the response from service", result);
    })
    .catch(function (error) {
      // An error occurred
      //alert("Exception: " + error);
      console.log("Error in service call " + error);
    });
}

function toggleComments() {
  if (this.checked) {
    console.log("Checkbox is checked..");
    if (comments == 0) {
      comments = 1;
      sendNotificationEvent("COMMENTS_ON");
      updateSessionDetails("COMMENTS_ON");
      console.log("Enabled comments..");
    }
  } else {
    console.log("Checkbox is not checked..");
    if (comments == 1) {
      comments = 0;
      sendNotificationEvent("COMMENTS_OFF");
      updateSessionDetails("COMMENTS_OFF");
      console.log("Disabled comments..");
    }
  }
  return;
  // if (comments == 0) {
  //     comments = 1;
  //     sendNotificationEvent("COMMENTS_ON");
  //     //updateSessionDetails("COMMENTS_ON");
  //     console.log("Enabled comments..", e);
  // } else {
  //     comments = 0;
  //     sendNotificationEvent("COMMENTS_OFF");
  //     //updateSessionDetails("COMMENTS_OFF");
  //     console.log("Disabled comments..", e);
  // }
  // return false;
}

function toggleCommentsSection(val) {
  if (val) {
    $(".msg_send_btn").prop("disabled", false);
    $(".text_message").prop("disabled", false);
    $(".msg_history").attr("style", "");
    $(".msg_history").val("");
  } else {
    $(".msg_history").attr("style", "background-color: #C5C5C5");
    $(".msg_history").val("Comments are disabled by the teacher");
    $(".msg_send_btn").prop("disabled", true);
    $(".text_message").prop("disabled", true);
  }
}
/*
var dataArray = new Uint8Array(128);
var bufferLength = 128;
var analyzers = [];
var TalkingChecker = null;


function addToCheck(stream,name){
    var context = new AudioContext();
    var src = context.createMediaStreamSource(stream);
    var analyser = context.createAnalyser();
    src.connect(analyser);
    analyser.fftSize = 256;

    analyzers.push([analyser,name]);
    if(TalkingChecker == null){
        TalkingChecker = setInterval(()=>{
            checkTalking()},25);       
    }
    console.table(analyzers);
}
    
function checkTalking() {
    
    for(var x=0;x<analyzers.length;x++){
   
        analyzers[x][0].getByteFrequencyData(dataArray);
        var values = 0;
        for (var i = 0; i < bufferLength; i++) {
            values += dataArray[i];
        }
        var average = (values / bufferLength);
        //console.log(average);
        if(average>50){
            console.log(`${analyzers[x][1]} isTalking`);
        }
    }    
}
*/
