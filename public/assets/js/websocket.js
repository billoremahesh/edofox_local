'use strict';

var protocol = 'http://';
if(host.indexOf("https") >= 0) {
    protocol = 'https://';
}

var websocketUrl = websocketDomain + ':8081'
var stompClient = null;
var stompClient = null;
var currentSubscription;
var topic = null;
//var username;

var socket;


function get_device_details(){
    window.mobilecheck = function () {
        var check = false;
        (function (a) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true; })(navigator.userAgent || navigator.vendor || window.opera);
        return check;
    };
    
    
    var device = 'web';
    if (window.mobilecheck()) {
        device = 'mobile';
    }
    
    var deviceInfo = window.navigator.userAgent;

    return {
        device:device,
        deviceInfo: deviceInfo
    };
}


function connect(headers) {
    // var name1 = $("#name").val().trim();
    // Cookies.set('name', name1);
    // usernamePage.classList.add('d-none');
    // chatPage.classList.remove('d-none');
    socket = new SockJS(protocol + websocketUrl + '/sock');
    stompClient = Stomp.over(socket);
    stompClient.connect(headers ,onConnected, onError);
    //event.preventDefault();
}

function saveSessionId() {
    var socketUrl = socket._transport.url;
    if(socketUrl) {
        var array = socketUrl.split("/");
        console.log("Saving session ID", array[array.length - 2]);

        var now = new Date();
        var time = now.getTime();
        var expireTime = time + 1000*36000;
        now.setTime(expireTime);
        document.cookie = "ws_session=" + array[array.length - 2] + "; expires=" + +now.toUTCString() + ";path=/";
    }
}

function onConnected() {
    enterRoom(roomId);
    //waiting.classList.add('d-none');
    console.log("Connected!");
    saveSessionId();
}

function onError(error) {
    //waiting.textContent = 'uh oh! service unavailable';
    console.log("Error in websocket connection", error);
}

function enterRoom(newRoomId) {
    Cookies.set('roomId', newRoomId);
    //roomIdDisplay.textContent = newRoomId;
    topic = `/chat-app/chat/${newRoomId}`;

    //currentSubscription = stompClient.subscribe(`/chat-room/${newRoomId}`, onMessageReceived);

    currentSubscription = stompClient.subscribe(`/chat-room/room_left`, onMessageReceived);
    //   var username = $("#name").val().trim();
    //   stompClient.send(`${topic}/addUser`,
    //     {},
    //     JSON.stringify({sender: username, type: 'JOIN'})
    //   );
}


function sendMessage(event) {
    var messageContent = $("#message").val().trim();
    var username = $("#name").val().trim();
    var newRoomId = $('#room').val().trim();
    topic = `/chat-app/chat/${newRoomId}`;
    if (messageContent && stompClient) {
        var chatMessage = {
            sender: username,
            content: messageContent,
            type: 'CHAT'
        };

        stompClient.send(`${topic}/sendMessage`, {}, JSON.stringify(chatMessage));
        document.querySelector('#message').value = '';
    }
    event.preventDefault();
}

function onMessageReceived(payload) {
    var message = JSON.parse(payload.body);
    console.log("Message received is ", message);

    if (message.type === 'JOIN') {
        //TODO On join can be handled if needed

    } else if (message.type === 'LEAVE') {
        //TODO On Leave can be handled if needed
        if (typeof onLeaveRoom === "function") {
            onLeaveRoom(message);
        }
    } else {
        //TODO On message received can be handled if needed
    }

}

// $(document).ready(function() {
//   userJoinForm.addEventListener('submit', connect, true);
//   messagebox.addEventListener('submit', sendMessage, true);
// });