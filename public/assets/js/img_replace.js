var uploadedImage = null;

// Method to validate image type
// Ref: https://www.geeksforgeeks.org/file-type-validation-while-uploading-it-using-javascript/
function fileValidation(name) {
  // Allowing file type
  var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;

  if (name != null) {
    console.log("Checking for string " + name);
    if (!allowedExtensions.exec(name)) {
      alert("Invalid file type. Only jpg/png allowed");
      return false;
    }
    return true;
  }

  if (!allowedExtensions.exec($("#uploaded_file").val())) {
    alert("Invalid file type. Only jpg/png allowed");
    $("#uploaded_file").val("");
    return false;
  } else {
    return true;
  }
}

function getDataUrl(img) {
  // Create canvas
  const canvas = document.createElement("canvas");
  const ctx = canvas.getContext("2d");
  // Set width and height
  canvas.width = img.width;
  canvas.height = img.height;
  // Draw the image
  ctx.drawImage(img, 0, 0);
  return canvas.toDataURL("image/jpeg");
}

$(function () {
  // preventing page from redirecting
  $("html").on("dragover", function (e) {
    e.preventDefault();
    e.stopPropagation();
    $("#drag_title").text("Drag here");
  });

  $("html").on("drop", function (e) {
    e.preventDefault();
    e.stopPropagation();
  });

  // Drag enter
  $(".upload-area").on("dragenter", function (e) {
    e.stopPropagation();
    e.preventDefault();
    $("#drag_title").text("Drop");
  });

  // Drag over
  $(".upload-area").on("dragover", function (e) {
    e.stopPropagation();
    e.preventDefault();
    $("#drag_title").text("Drop");
  });

  // Drop
  $(".upload-area").on("drop", function (e) {
    e.stopPropagation();
    e.preventDefault();

    var file = e.originalEvent.dataTransfer.files;

    console.log("Dropped file", file);
    if (fileValidation(file[0].name)) {
      $("#drag_title").text("Upload");

      //Preview Image

      // const files = document.getElementById("#uploaded_file").files[0];
      // if (files) {
      //     const fileReader = new FileReader();
      //     fileReader.readAsDataURL(files);
      //     fileReader.addEventListener("load", function() {
      //         $("#preview_upload").style.display = "block";
      //         $("#preview_upload").src = this.result;
      //     });
      // }

      const fileReader = new FileReader();
      fileReader.readAsDataURL(file[0]);
      fileReader.addEventListener("load", function () {
        $("#preview_upload").attr("style", "");
        $("#preview_upload").attr("src", this.result);

        // console.log("Using image dropped ", uploadedImage);
        clearCanvas(false, true);

        uploadedImage = this.result;
      });

      // var fd = new FormData();

      // fd.append('file', file[0]);

      //uploadedImage = file[0];
    }
  });

  // Open file selector on div click
  $("#uploadfile").click(function () {
    $("#uploaded_file").click();
  });

  // file selected
  $("#uploaded_file").change(function () {
    //var fd = new FormData();

    //uploadedImage = $('#uploaded_file')[0].files[0];

    console.log("File selected! ", $("#uploaded_file").val());

    if (fileValidation()) {
      //On file selected
      var reader = new FileReader();

      reader.onload = function (e) {
        $("#preview_upload").attr("style", "");
        $("#preview_upload").attr("src", e.target.result);
        clearCanvas(false, true);

        uploadedImage = e.target.result;
        // console.log("Using image selected ", uploadedImage);
      };

      reader.readAsDataURL(document.getElementById("uploaded_file").files[0]);

      //uploadedImage = files;

      // fd.append('file', files);

      // uploadData(fd);
    }
  });
});

function retrieveImageFromClipboardAsBlob(pasteEvent, callback) {
  if (pasteEvent.clipboardData == false) {
    if (typeof callback == "function") {
      callback(undefined);
    }
  }

  var items = pasteEvent.clipboardData.items;

  if (items == undefined) {
    if (typeof callback == "function") {
      callback(undefined);
    }
  }

  for (var i = 0; i < items.length; i++) {
    // Skip content if not image
    if (items[i].type.indexOf("image") == -1) continue;
    // Retrieve image on clipboard as blob
    var blob = items[i].getAsFile();

    if (typeof callback == "function") {
      callback(blob);
    }
  }
}

var imageAdded = false;

var oldImage;

window.addEventListener(
  "paste",
  function (e) {
    // Handle the event
    retrieveImageFromClipboardAsBlob(e, function (imageBlob) {
      // If there's an image, display it in the canvas
      if (imageBlob) {
        var canvas = document.getElementById("mycanvas");
        var ctx = canvas.getContext("2d");

        //If image is already added take reference
        if (imageAdded) {
          oldImage = new Image();
          oldImage.src = canvas.toDataURL();
          oldImage.onload = function () {
            console.log("Old image loaded ");
          };
        }

        // Create an image to render the blob on the canvas
        var img = new Image();

        var y = 0;
        // Once the image loads, render the img on the canvas
        img.onload = function () {
          //If there's already an image present..append new image to it
          if (imageAdded) {
            y = oldImage.height;
            // Update dimensions of the canvas calculating the dimensions of the combined image
            if (oldImage.width < this.width) {
              canvas.width = this.width;
            } else {
              canvas.width = oldImage.width;
            }
            canvas.height = this.height + oldImage.height;
            // console.log("Adding old image first to canvas");
            // Draw the image
            ctx.drawImage(oldImage, 0, 0);
          } else {
            // Update dimensions of the canvas with the dimensions of the image
            canvas.width = this.width;
            canvas.height = this.height;
          }

          // console.log("Adding image at " + y);
          // Draw the image
          ctx.drawImage(img, 0, y);

          imageAdded = true;
        };

        // Crossbrowser support for URL
        var URLObj = window.URL || window.webkitURL;

        // Creates a DOMString containing a URL representing the object given in the parameter
        // namely the original Blob
        img.src = URLObj.createObjectURL(imageBlob);

        //uploadedImage = imageBlob;
        setTimeout(function () {
          uploadedImage = canvas.toDataURL();
          // console.log("Image pasted!", uploadedImage);
        }, 1000);

        // canvas.toBlob(function(blob) {
        //     uploadedImage = blob;
        //     console.log("Converted canvas to blob", uploadedImage, canvas.toDataURL());
        // });

        $("#drag_title").text("Upload");
      }
    });
  },
  false
);

function clearCanvas(closeModal, hideCanvas) {
  imageAdded = false;
  var canvas = document.getElementById("mycanvas");
  var ctx = canvas.getContext("2d");
  //canvas.width = 0;
  //canvas.height = 0;
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  uploadedImage = null;

  if (closeModal) {
    $("#imageReplaceModal").modal("hide");
  }

  if (hideCanvas) {
    $("#mycanvas").attr("style", "display:none");
  } else {
    $("#mycanvas").attr("style", "border:1px solid grey;");
    $("#preview_upload").attr("src", null);
    $("#drag_title").html(
      "Drag and Drop file here<br />Or<br />Click to select file"
    );
  }
}
