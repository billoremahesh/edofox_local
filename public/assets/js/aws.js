var albumBucketName = "edofox-s3";
var bucketRegion = "ap-south-1";
//var IdentityPoolId = "IDENTITY_POOL_ID";

// Initialize the Amazon Cognito credentials provider
//AWS.config.region = 'ap-south-1'; // Region
// AWS.config.credentials = new AWS.CognitoIdentityCredentials({
//     IdentityPoolId: 'ap-south-1:68070441-76a4-4994-986f-d20401dc294f',
// });

AWS.config.update({
  region: bucketRegion,
  credentials: new AWS.CognitoIdentityCredentials({
    //IdentityPoolId: 'ap-south-1:68070441-76a4-4994-986f-d20401dc294f',
    IdentityPoolId: "ap-south-1:c80daaa2-dbcc-4549-bd4b-ddea2382341b", //For new bucket
  }),
});

var s3 = new AWS.S3({
  apiVersion: "2006-03-01",
  params: { Bucket: albumBucketName },
});

async function addPhoto(filName, folderName, file) {
  // var files = document.getElementById("photoupload").files;
  // if (!files.length) {
  //     return alert("Please choose a file to upload first.");
  // }
  //var file = files[0];
  //var fileName = "_file.png";
  //var albumPhotosKey = encodeURIComponent(albumName) + "/";

  //var photoKey = albumPhotosKey + fileName;

  var bucketName = albumBucketName + "/" + folderName;

  if (file.size > FILE_SIZE_LIMIT) {
    console.log("Compressing file");
    compressed = await compressImage(file);
    console.log("Compression done!", compressed);
    console.log("Skipped compression. File is small ..");
    // Use S3 ManagedUpload class as it supports multipart uploads
    var upload = new AWS.S3.ManagedUpload({
      params: {
        Bucket: bucketName,
        Key: filName,
        Body: compressed,
      },
    });
    return upload.promise();
  } else {
    console.log("Skipped compression. File is small ..");
    // Use S3 ManagedUpload class as it supports multipart uploads
    var upload = new AWS.S3.ManagedUpload({
      params: {
        Bucket: bucketName,
        Key: filName,
        Body: file,
      },
    });
    return upload.promise();
  }
}

const MAX_WIDTH = 1024;
const MAX_HEIGHT = 768;
const MIME_TYPE = "image/jpeg";
const QUALITY = 1;
const FILE_SIZE_LIMIT = 256 * 1024; //500 KB

// const input = document.getElementById("img-input");
// input.onchange = function (ev) {
//   const file = ev.target.files[0]; // get the file
//   const blobURL = URL.createObjectURL(file);
//   const img = new Image();
//   img.src = blobURL;
//   img.onerror = function () {
//     URL.revokeObjectURL(this.src);
//     // Handle the failure properly
//     console.log("Cannot load image");
//   };
//   img.onload = function () {
//     URL.revokeObjectURL(this.src);
//     const [newWidth, newHeight] = calculateSize(img, MAX_WIDTH, MAX_HEIGHT);
//     const canvas = document.createElement("canvas");
//     canvas.width = newWidth;
//     canvas.height = newHeight;
//     const ctx = canvas.getContext("2d");
//     ctx.drawImage(img, 0, 0, newWidth, newHeight);
//     canvas.toBlob(
//       (blob) => {
//         // Handle the compressed image. es. upload or save in local state
//         displayInfo('Original file', file);
//         displayInfo('Compressed file', blob);
//       },
//       MIME_TYPE,
//       QUALITY
//     );
//     document.getElementById("root").append(canvas);
//   };
// };

function compressImage(file) {
  return new Promise(function (resolve, reject) {
    const blobURL = URL.createObjectURL(file);
    const img = new Image();
    img.src = blobURL;

    img.onerror = function () {
      URL.revokeObjectURL(this.src);
      // Handle the failure properly
      console.log("Cannot load image");
      resolve(file);
    };
    img.onload = function () {
      URL.revokeObjectURL(this.src);
      const [newWidth, newHeight] = calculateSize(img, MAX_WIDTH, MAX_HEIGHT);
      const canvas = document.createElement("canvas");
      canvas.width = newWidth;
      canvas.height = newHeight;
      const ctx = canvas.getContext("2d");
      ctx.drawImage(img, 0, 0, newWidth, newHeight);
      canvas.toBlob(
        (blob) => {
          // Handle the compressed image. es. upload or save in local state
          displayInfo("Original file", file);
          displayInfo("Compressed file", blob);
          resolve(blob);
        },
        MIME_TYPE,
        QUALITY
      );
    };
  });
}

function calculateSize(img, maxWidth, maxHeight) {
  let width = img.width;
  let height = img.height;

  // calculate the width and height, constraining the proportions
  if (width > height) {
    if (width > maxWidth) {
      height = Math.round((height * maxWidth) / width);
      width = maxWidth;
    }
  } else {
    if (height > maxHeight) {
      width = Math.round((width * maxHeight) / height);
      height = maxHeight;
    }
  }
  return [width, height];
}

// Utility functions for demo purpose

function displayInfo(label, file) {
  //const p = document.createElement('p');
  //p.innerText = `${label} - ${readableBytes(file.size)}`;
  //document.getElementById('root').append(p);
  console.log(label + " == " + readableBytes(file.size));
}

function readableBytes(bytes) {
  const i = Math.floor(Math.log(bytes) / Math.log(1024)),
    sizes = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];

  return (bytes / Math.pow(1024, i)).toFixed(2) + " " + sizes[i];
}
