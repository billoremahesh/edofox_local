var lecture;
$(document).ready(function () {
  Snackbar.show({
    pos: "top-center",
    text: "Let's start a Live lecture!",
  });

  if (lectureSaved) {
    //Fetch lecture recording for preview
    var request = {
      student: {
        currentPackage: {
          sessionId: roomName,
        },
      },
    };

    console.log("Calling get session", request);

    callStudentServiceJSONPost("getSession", request)
      .then(function (result) {
        console.log("Got the response from service", result);
        if (
          result != null &&
          result.packages != null &&
          result.packages.length > 0
        ) {
          lecture = result.packages[0];
          if (lecture != null && lecture.videoUrl != null) {
            $("#view_recording_btn").attr("style", "");
          }
        }
      })
      .catch(function (error) {
        // An error occurred
        //alert("Exception: " + error);
        console.log("Error in service call " + error);
      });
  }

  var videojsPlayer;
  $("#recordedPreviewModal").on("shown.bs.modal", function () {
    videojsPlayer = videojs("my-video");
    console.log(videojsPlayer);
    videojsPlayer.responsive(true);

    videojsPlayer.hlsQualitySelector({
      displayCurrentQuality: true,
    });
  });

  // To pause the playing video, when the modal is closed
  $("#recordedPreviewModal").on("hidden.bs.modal", function () {
    $(this).find("iframe").attr("src", "");
    if (videojsPlayer != null) {
      videojsPlayer.dispose();
    }
  });

  // To pause the playing video, when the modal is closed
  $("#recordedPreviewModal").on("hidden.bs.modal", function () {
    $(this).find("iframe").attr("src", "");
  });

  if (comments == 1) {
    $(".toggle_comments").prop("checked", "true");
    console.log("Comments enabled ..");
  }
});

//When display only student leaves, call getSessionUser to fetch the user stream ID and remove from the list
function onLeaveRoom(evt) {
  console.log("Display only User left", evt);
  //TODO Fetch the details of the user which left and remove the stream from user list
  var request = {
    student: {
      referrer: evt.sender, //sessionId
    },
  };

  // callStudentServiceJSONPost("getSessionUser", request).then ..
  callStudentServiceJSONPost("getSessionUser", request).then(function (result) {
    console.log("Got the getSessionUser", result);
    var session_ = result.student.currentPackage.sessionId;
    console.log(session_);
    $("#remoteStudent" + session_).remove();
  })
    .catch(function (error) {
      // An error occurred
      console.log("Error in service call " + error);
    });

}

//Call this when teacher joins to load the display only users
function getSessionUsers() {
  console.log("Display only User left", evt);
  //TODO Fetch the details of the user which left and remove the stream from user list

  var request = {
    lecture: {
      id: $("#live_session_id").val(), //As defined above in hidden value
    },
    requestType: "DISPLAY_ONLY",
  };
  //

}
