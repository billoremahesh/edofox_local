// Validate Login
function check_valid_login_user(username, password) {
  // validate user using a async promise
  return new Promise(function (resolve, reject) {
    var jsonObjects = {
      institute: {
        username: username,
        password: password,
      },
    };
    var dataString = JSON.stringify(jsonObjects);
    // console.log(dataString);
    var xhr = $.ajax({
      type: "POST",
      data: dataString,
      url: rootAdmin + "login",
      contentType: "application/json",
    })
      .done(function (response) {
        // success logic here
        resolve(JSON.stringify(response));
        // console.log(response);
      })
      .fail(function (jqXHR) {
        // Our error logic here
        reject(jqXHR.responseText);
      });
  });
}

// Set New Password
function set_new_password(token, password) {
  return new Promise(function (resolve, reject) {
    var jsonObjects = {
      institute: {
        token: token,
        password: password,
      },
    };
    var dataString = JSON.stringify(jsonObjects);
    // console.log(dataString);
    var xhr = $.ajax({
      type: "POST",
      data: dataString,
      url: rootAdmin + "changePassword",
      contentType: "application/json",
    })
      .done(function (response) {
        // success logic here
        resolve(JSON.stringify(response));
        // console.log(response);
      })
      .fail(function (jqXHR) {
        // Our error logic here
        reject(jqXHR.responseText);
      });
  });
}
