//To fetch the list of chapters in the subject and classroom via ajax
function fetchChaptersList(subjectId, classroomId) {

    // console.log("Inside fetchChaptersList." + subjectId + " - " + classroomId);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            $("#chapters-list-content").html(this.responseText);
            $("#chapters_loading_div").hide();
        }
    };
    xmlhttp.open("GET", "test_operations/ajax_fetch_dlp_chapters_list.php?subjectId=" + subjectId + "&classroomId=" + classroomId, true);
    xmlhttp.send();

}


function fetchDoubtsList(chapterId, subjectId) {
    //Load doubt management UI
    // console.log("Calling doubts URL " + 'student_doubts_content.php?studentId=' + studentId + '&instituteId=' + instituteId + '&chapterId=' + chapterId);
    $.ajax({
        url: 'student_doubts_content.php?studentId=' + studentId + '&instituteId=' + instituteId + '&chapterId=' + chapterId + "&subjectId=" + subjectId,
        success: function (data) {
            // console.log("Loading Doubts UI ... ");
            $("#doubts-content-div").html(data);
        }
    });
}



//To change the video and resources content list on click via ajax
function fetchTotalContentList(chapterId, classroomId, subjectId) {
    //Loading
    $("#video-content-div").html("");
    $("#doc-content-div").html("");
    $("#tests-content-div").html("");
    //Hiding the doubts list as it already has some html content
    $("#doubts-content-div").hide();
    $("#video_content_loading_div").show();
    $("#doc_content_loading_div").show();
    $("#content-block").show();
    $("#tests_content_loading_div").show();

    fetchVideoContentList(chapterId, classroomId);
    fetchDocContentList(chapterId, classroomId);
    fetchTestsContentList(chapterId, classroomId);

    fetchDoubtsList(chapterId, subjectId);

    //$("#resolved-doubts-table-list").html("");
    //$("#unresolved-doubts-table-list").html("");

    $("#chapter_id_input").val(chapterId);
}




//To fetch the VIDEO content in the given chapters via ajax
function fetchVideoContentList(chapterId, classroomId) {

    // console.log("Inside fetchVideoContentList." + chapterId + " - " + classroomId);

    const type = "VIDEO";

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            $("#video-content-div").html(this.responseText);
            $("#video_content_loading_div").hide();

            $('html,body').animate({
                scrollTop: $("#video-content-div").offset().top
            },
                'slow');


            //Fetch doubts for this chapter asked by the student
            fetchDlpChapterDoubts(chapterId, studentId);
        }
    };
    xmlhttp.open("GET", "test_operations/ajax_fetch_dlp_content.php?chapterId=" + chapterId + "&classroomId=" + classroomId + "&type=" + type, true);
    xmlhttp.send();

}





//To fetch the DOC content in the given chapters via ajax
function fetchDocContentList(chapterId, classroomId) {

    // console.log("Inside fetchDocContentList." + chapterId + " - " + classroomId);

    const type = "DOC";

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            $("#doc-content-div").html(this.responseText);
            $("#doc_content_loading_div").hide();
        }
    };
    xmlhttp.open("GET", "test_operations/ajax_fetch_dlp_content.php?chapterId=" + chapterId + "&classroomId=" + classroomId + "&type=" + type, true);
    xmlhttp.send();

}



//To fetch the TESTS in the given chapters via ajax
function fetchTestsContentList(chapterId, classroomId) {

    // console.log("Inside fetchTestsContentList." + chapterId + " - " + classroomId);

    const type = "TEST";

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            $("#tests-content-div").html(this.responseText);
            $("#tests_content_loading_div").hide();
        }
    };
    xmlhttp.open("GET", "test_operations/ajax_fetch_dlp_content.php?chapterId=" + chapterId + "&classroomId=" + classroomId + "&type=" + type, true);
    xmlhttp.send();

}





//Open the modal to show PDF using the doc url
function displayResourcePdf(docUrl, docName) {
    if (docUrl.length == 0) {
        document.getElementById("displayResourcePdfModalBody").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("displayResourcePdfModalBody").innerHTML = this.responseText;
                $("#displayResourcePdfModal").modal('show');

                $('.pdf-viewer').css("background", "url('./dist/img/loading.gif') center center no-repeat")
            }
        };
        xmlhttp.open("GET", "test_operations/ajax_resource_pdf_modal.php?docUrl=" + docUrl + "&docName=" + docName, true);
        xmlhttp.send();
    }

}




//Fetch the doubts asked by student for the chapter
function fetchDlpChapterDoubts(chapterId, studentId) {
    // console.log("Inside fetchDlpChapterDoubts." + chapterId + " - " + studentId);

    //Fetching doubt counts to show in the tabs
    fetchDlpChapterDoubtCounts(chapterId, studentId, "unresolved");
    fetchDlpChapterDoubtCounts(chapterId, studentId, "resolved");


    $("#resolved-doubts-list-loading-div").show();
    $("#unresolved-doubts-list-loading-div").show();

    $("#doubts-content-div").show();

    $.post("test_operations/ajax_doubts_fetch_dlp_doubts.php", { chapterId: chapterId, studentId: studentId, doubtType: "resolved" }, function (data, status) {

        // console.log("Doubts Data: " + data + "\nStatus: " + status);
        $("#resolved-doubts-list-loading-div").hide();

        //Setting the data from response
        $("#resolved-doubts-table-list").html(data);

        //Setting up the datatable for features like search
        $('#cleared_doubts_table').DataTable();
    });


    $.post("test_operations/ajax_doubts_fetch_dlp_doubts.php", { chapterId: chapterId, studentId: studentId, doubtType: "unresolved" }, function (data, status) {

        // console.log("Doubts Data: " + data + "\nStatus: " + status);
        $("#unresolved-doubts-list-loading-div").hide();

        //Setting the data from response
        $("#unresolved-doubts-table-list").html(data);

        //Setting up the datatable for features like search
        $('#pending_doubts_table').DataTable();
    });

}




//Fetching doubt counts based on resolved or unresolved for this student and chapter
function fetchDlpChapterDoubtCounts(chapterId, studentId, type) {
    // console.log("fetchDlpChapterDoubtCounts " + chapterId + "-" + studentId + "-" + type);

    $.get("test_operations/ajax_doubts_fetch_dlp_doubts.php", { chapterId: chapterId, studentId: studentId, countType: type }, function (data) {

        if (type === 'resolved') {
            $("#resolved-doubts-count").html(data.trim());
        }

        if (type === 'unresolved') {
            $("#unresolved-doubts-count").html(data.trim());
        }

    });

}

//Fetching the modal data for updating the DLP resource content
function editResourceTeacher(chapterId, mappingId, resourceId, resourceType) {
    // console.log("editResource " + chapterId + " - " + mappingId + " - " + resourceId + " - " + resourceType);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            $("#updateResourceModalBody").html(this.responseText);
            $("#updateResourceModal").modal('show');

            if (resourceType === "VIDEO") {
                $("#updateResourceModalBody #edit_video_url").prop('required', true);
            }


            if (resourceType === "DOC") {
                $("#updateResourceModalBody #edit_video_url").prop('required', false);
            }

            $('.js-example-basic-single2').select2({ width: '100%' });
        }
    };
    xmlhttp.open("GET", "test-adminPanel/sql_operations/dlp/update_resource_modal_append.php?chapterId=" + chapterId + "&mappingId=" + mappingId + "&resourceId=" + resourceId + "&resourceType=" + resourceType, true);
    xmlhttp.send();
}


function deleteCourseResourceTeacher(resourceId, resourceType) {
    // console.log("Inside deleteCourseResource." + resourceId);

    if (resourceType === "TEST") {
        var result = confirm("WARNING! Do you want to delete this test from this chapter? This cannot be undone.");
    } else {
        var result = confirm("WARNING! Do you want to delete this resource? This cannot be undone.");
    }
    if (result) {

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText.trim() == "SUCCESS") {
                    // console.log("Deleted." + this.responseText);
                    location.reload();
                } else {
                    console.log("Failed." + this.responseText);
                    // alert("Failed: " + this.responseText);
                }
            } else {
                // console.log("Deletion failed.");
            }
        };
        xmlhttp.open("POST", "test-adminPanel/sql_operations/dlp/admin_ajax_delete_resource.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("resourceId=" + resourceId + "&resourceType=" + resourceType);
    }

}

function updateContentOrderTeacher(resourceMappingId, value) {
    value = parseInt(value);
    // console.log("updateContentOrder " +resourceMappingId + "-" + value + typeof (value));
    $.snackbar({
        content: "Updating...",
        timeout: 1000
    });
    if (value && typeof (value) === 'number') {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText.trim() == "SUCCESS") {
                    // console.log("Updated." + this.responseText);
                    $.snackbar({
                        content: "Content order updated successfully. Reload to reflect new order."
                    });

                } else {
                    // console.log("Failed." + this.responseText);
                    $.snackbar({
                        content: "Error: " + this.responseText
                    });
                }
            } else {
                // console.log("Deletion failed.");
            }
        };
        xmlhttp.open("POST", "test-adminPanel/sql_operations/dlp/admin_ajax_update_content_order.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("resourceMappingId=" + resourceMappingId + "&contentOrder=" + value);
    } else {
        //alert("Enter a number");
        $.snackbar({
            content: "Please enter a number."
        });
    }
}