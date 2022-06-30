// Add New Institute
function add_new_institute(
  username,
  password,
  institute_name,
  contact,
  email,
  purchase,
  total_students,
  storage_quota,
  expiry_date,
  account_manager
) {
  // add new institute using a async promise
  return new Promise(function (resolve, reject) {
    var jsonObjects = {
      institute: {
        username: username,
        password: password,
        name: institute_name,
        contact: contact,
        email: email,
        purchase: purchase,
        maxStudents: total_students,
        storageQuota: storage_quota,
        expiryDateString: expiry_date,
        accountManager : account_manager
      },
    };
    var dataString = JSON.stringify(jsonObjects);
    // console.log(dataString);
    var xhr = $.ajax({
      type: "POST",
      data: dataString,
      url: superAdmin + "createAdmin",
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

// Update Institute
function update_institute_features(
  id,
  purchase,
  maxStudents,
  storage_quota,
  expiry_date
) {
  // add new institute using a async promise
  return new Promise(function (resolve, reject) {
    var jsonObjects = {
      institute: {
        id: id,
        purchase: purchase,
        maxStudents: maxStudents,
        storageQuota: storage_quota,
        expiryDateString: expiry_date
      },
    };
    var dataString = JSON.stringify(jsonObjects);
    // console.log(dataString);
    var xhr = $.ajax({
      type: "POST",
      data: dataString,
      url: superAdmin + "upgradeClient",
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
