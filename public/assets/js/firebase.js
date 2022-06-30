// var firebaseConfig = {
//     apiKey: "AIzaSyAvR8BNsScY9TsmJgBNUJOCM1FPQojJWKQ",
//     authDomain: "third-strategy-115917.firebaseapp.com",
//     databaseURL: "https://third-strategy-115917.firebaseio.com",
//     projectId: "third-strategy-115917",
//     storageBucket: "third-strategy-115917.appspot.com",
//     messagingSenderId: "608768536010",
//     appId: "1:608768536010:web:0d23cde9302cb3eb"
// };

// Your web app's Firebase configuration
var firebaseConfig = {
    apiKey: "AIzaSyATdn22DE3zzJsy4TjMCn_le6xgE0Us5cw",
    authDomain: "edofox-management-module.firebaseapp.com",
    databaseURL: "https://edofox-management-module.firebaseio.com",
    projectId: "edofox-management-module",
    storageBucket: "edofox-management-module.appspot.com",
    messagingSenderId: "937238524256",
    appId: "1:937238524256:web:1989d75d9c036dd4de6e05"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

var db = firebase.firestore();

var storage = firebase.storage();

// Create a storage reference from our storage service
var storageRef = storage.ref();
// console.log("storageRef ", storageRef);

//Limit for firebase messages fetch
const limit = 30;
const studentsFrequency = 10000;