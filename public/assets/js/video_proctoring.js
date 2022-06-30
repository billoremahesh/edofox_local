function joinRoom(meetingNo, password) {
  const api_key = "rxQth6vBQxe-v6DMtW4ZIg";
  $.post(
    apachehost + "API/authenticate/generate_signature",
    {
      action: "sign",
      meetingNumber: meetingNo,
      role: role,
    },
    function (data) {
      var response_string = JSON.parse(data);
      client.join({
        apiKey: api_key,
        signature: response_string.data.signature,
        meetingNumber: meetingNo,
        password: password,
        userName: "Admin",
      });
    }
  );

  setGalleryView();
  JoinVideo();
}

function setGalleryView() {
  setTimeout(function () {
    let $gallery = $("#suspension-view-tab-thumbnail-gallery");
    if ($gallery.length) {
      $gallery.click();
      $("#meetingSDKElement div div div div.drag-video").hide();
      $('button[title|="Security"]').hide();
      $('button[title|="Share Screen"]').hide();
    } else {
      setGalleryView();
    }
  }, 2000);
}

function JoinVideo() {
  setTimeout(function () {
    let joinVideoBtn = document.querySelector('button[title="Start Video"]');
    if (joinVideoBtn) {
      joinVideoBtn.click();
    }
    if (!document.querySelector('button[title="Stop Video"]')) {
      JoinVideo();
    }
  }, 2000);
}

function showInfo() {
  var list = client.getAttendeeslist();
  for (var l of list) {
    console.log(`Name : ${l.displayName} | Video Status : ${l.bVideoOn}`);
  }
}

function findGetParameter(parameterName) {
  var result = null,
    tmp = [];
  location.search
    .substr(1)
    .split("&")
    .forEach(function (item) {
      tmp = item.split("=");
      if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    });
  return result;
}
