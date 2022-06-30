function displayCourseData(subtopic, videoUrl, testId, foundationId) {
  // console.log("Foundation ID", foundationId);
  videoId = foundationId;
  subtopic = encodeURI(subtopic);
  videoUrl = encodeURI(videoUrl);
  testId = encodeURI(testId);
  if (videoUrl.length == 0) {
    document.getElementById("displayCourseModalBody").innerHTML = "";
    return;
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("displayCourseModalBody").innerHTML =
          this.responseText;
        $("#displayCourseModal").modal("show");
        detectMobileBrowser();
      }
    };
    xmlhttp.open(
      "GET",
      "load_doubt_videos" +
        subtopic +
        "&videoUrl=" +
        videoUrl +
        "&testId=" +
        testId,
      true
    );
    xmlhttp.send();
  }
}
