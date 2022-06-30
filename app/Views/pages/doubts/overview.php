<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<?php
// Include Service URLs Parameters File
include_once(APPPATH . "Views/service_urls.php");
?>
<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/doubts/overview.css?v=202111171921'); ?>" rel="stylesheet">
<div id="content">
    <div class="container-fluid">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="row mb-4">

            <div class="col-md-3">
                <div class="top-counts-block" onclick="changeDoubtTypeDropdown('Exams')">
                    <div>
                        <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/writing.png" style="width:32px" />
                    </div>

                    <div style="margin-left: 16px;">
                        <label class="counts-subtitle">Exam Doubts </label>
                        <h4 class="count-number">
                            <span id="ExamsPendingCount" data-bs-toggle="tooltip" title="Pending Doubts">
                                0
                            </span>
                            <span class="total-doubts-count-number" id="ExamsTotalCount" data-bs-toggle="tooltip" title="Total Doubts">
                                / 0
                            </span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="top-counts-block" onclick="changeDoubtTypeDropdown('Videos')">
                    <div>
                        <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/video_icon.png" style="width: 32px;" />
                    </div>

                    <div style="margin-left: 16px;">
                        <label class="counts-subtitle">Video Doubts</label>
                        <h4 class="count-number">
                            <span id="VideosPendingCount" data-bs-toggle="tooltip" title="Pending Doubts">
                                0
                            </span>
                            <span class="total-doubts-count-number" id="VideosTotalCount" data-bs-toggle="tooltip" title="Total Doubts">
                                / 0
                            </span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="top-counts-block" onclick="changeDoubtTypeDropdown('General')">
                    <div>
                        <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/subjectIcons/subject-exam.png" style="width: 32px;" />
                    </div>

                    <div style="margin-left: 16px;">
                        <label class="counts-subtitle">General Doubts</label>
                        <h4 class="count-number">
                            <span id="GeneralPendingCount" data-bs-toggle="tooltip" title="Pending Doubts">
                                0
                            </span>
                            <span class="total-doubts-count-number" id="GeneralTotalCount" data-bs-toggle="tooltip" title="Total Doubts">
                                / 0
                            </span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>


        <div class="text-center my-2 d-none" id="loading-div">
            <i class='fas fa-atom fa-spin fa-2x fa-fw'></i>
        </div>

        <div class="bg-white shadow rounded mb-5 p-4">

            <div class="row justify-content-center">

                <?php
                $session_subject_filter = (isset($_SESSION['session_subject_filter'])) ? $_SESSION['session_subject_filter'] : "";
                ?>
                <div class="col-md-3">
                    <label for="subject_filter_dropdown" class="form-label mt-2"> Subjects Filter</label>
                    <select class="form-control doubt_search_filter" id="subject_filter_dropdown">
                        <option value="-1">Show All Subjects' Doubts</option>
                        <?php
                        if (!empty($resultForSubjects)) :
                            foreach ($resultForSubjects as $subject_row) :
                        ?>
                                <option value="<?= $subject_row['subject_id']; ?>" <?= ($session_subject_filter == $subject_row['subject_id']) ? "selected" : ""; ?>><?= $subject_row['subject']; ?></option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>

                <?php
                $session_time_filter = (isset($_SESSION['session_time_filter'])) ? $_SESSION['session_time_filter'] : "";
                ?>
                <div class="col-md-3">
                    <label for="time_filter" class="form-label mt-2"> Days Filter</label>
                    <select class="form-control doubt_search_filter" id="time_filter">
                        <option value="">Show Doubts of all time</option>
                        <option value="last7" <?= ($session_time_filter === "last7") ? "selected" : ""; ?>>Last 7 days</option>
                        <option value="last100" <?= ($session_time_filter === "last100") ? "selected" : ""; ?>>Last 100 days</option>
                        <option value="old30" <?= ($session_time_filter === "old30") ? "selected" : ""; ?>>Older than month</option>
                        <option value="old100" <?= ($session_time_filter === "old100") ? "selected" : ""; ?>>Older than 100 days</option>
                    </select>
                </div>

                <?php
                $session_doubts_type_filter = (isset($_SESSION['session_doubts_type_filter'])) ? $_SESSION['session_doubts_type_filter'] : "";
                ?>
                <div class="col-md-3">
                    <label for="doubts_type_filter" class="form-label mt-2"> Doubts Type Filter</label>
                    <select class="form-control doubt_search_filter" id="doubts_type_filter" onchange="doubtsTypeChanged(this.value)">
                        <option value="Exams" <?= ($session_doubts_type_filter === "Exams") ? "selected" : ""; ?>>Show Exams Doubts</option>
                        <option value="Videos" <?= ($session_doubts_type_filter === "Videos") ? "selected" : ""; ?>>Show Videos Doubts</option>
                        <option value="General" <?= ($session_doubts_type_filter === "General") ? "selected" : ""; ?>>Show General Doubts</option>
                    </select>
                </div>
                <?php
                $session_doubts_resolution_type_filter = (isset($_SESSION['session_doubts_resolution_type_filter'])) ? $_SESSION['session_doubts_resolution_type_filter'] : "";
                ?>
                <div class="col-md-3">
                    <label for="doubts_resolution_type_filter" class="form-label mt-2"> Doubts Resolution Filter</label>
                    <select class="form-control doubt_search_filter" id="doubts_resolution_type_filter" onchange="tabChanged(this.value)">
                        <option value="Unresolved" <?= ($session_doubts_resolution_type_filter === "Unresolved") ? "selected" : ""; ?>>Show Pending Doubts</option>
                        <option value="Resolved" <?= ($session_doubts_resolution_type_filter === "Resolved") ? "selected" : ""; ?>>Show Resolved Doubts</option>
                    </select>
                </div>
            </div>

        </div>

        <div class='my-3'>

            <div class="doubts-content-block" id="unresolved_doubts_tbody"></div>

            <div class="doubts-content-block" id="resolved_doubts_tbody"></div>
        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    var teacherId;
    var superadminFlag = <?= $superadminFlag; ?>
</script>

<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" defer src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>


<script>
    MathJax = {
        tex: {
            inlineMath: [
                ['$', '$'],
                ['\\(', '\\)']
            ]
        },
        startup: {
            ready: function() {
                MathJax.startup.defaultReady();
                document.getElementById('render').disabled = false;
            }
        }
    }
</script>

<script>
    function loader(show) {
        if (show) {
            $("#loading-div").removeClass("d-none");
        } else {
            $("#loading-div").addClass("d-none");
        }
    }

    function addMathJax(input, spanId) {
        output = document.getElementById(spanId);
        if (output == null) {
            // console.log("returning .." + spanId);
            return;
        }
        if (input == null || input.trim().length == 0) {
            output.innerHTML = "";
            return;
        }
        output.innerHTML = input;
        //
        //  Reset the tex labels (and automatic equation numbers, though there aren't any here).
        //  Reset the typesetting system (font caches, etc.)
        //  Typeset the page, using a promise to let us know when that is complete
        //
        // console.log("Adding to - " + spanId);
        MathJax.texReset();
        MathJax.typesetClear();
        MathJax.typesetPromise()
            .catch(function(err) {

                console.log("Error -- " + err.message);
            })
            .then(function() {});
    }


    var doubtsType = $('#doubts_type_filter').val();
    var tab = $('#doubts_resolution_type_filter').val();
    var fromDate, toDate;

    function tabChanged(resolutionType) {
        // console.log("Tab changed..");
        loader(true);
        tab = resolutionType;
        loadFeedbacks($('#subject_filter_dropdown').val(), doubtsType, 0, resolutionType);
    }


    function doubtsTypeChanged(type) {
        // console.log("Doubts type changed " + type);
        doubtsType = type;
        loadFeedbacks($('#subject_filter_dropdown').val(), type, 0, tab);
    }


    // To change the dropdown value of doubts filter on click of the top counts cards
    function changeDoubtTypeDropdown(type) {
        // console.log("changeDoubtTypeDropdown ", type);
        $("#doubts_type_filter").val(type).change();
    }
</script>

<script>
    function loadSummary(type, inputValue, filter) {

        var studentObj = null;
        if (teacherId != null) {
            if (superadminFlag) {
                studentObj = {
                    id: teacherId,
                    accessType: 'Teacher',
                    referrer: 'Admin' //Only for super admin which will skip classroom filter for doubts
                }
            } else {
                studentObj = {
                    id: teacherId,
                    accessType: 'Teacher'
                }
            }
        }

        var subjectId = null;
        if (inputValue > 0) {
            subjectId = inputValue;
        }

        var request = {
            institute: {
                id: <?= $instituteID ?>,
                status: 'Admin'
            },
            requestType: type,
            searchFilter: filter,
            student: studentObj,
            subjectId: subjectId,
            startTime: $("#time_filter").val()
        };

        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                    $.ajax({
                        url: '<?= $fetchFeedbackSummary ?>',
                        beforeSend: function(request) {
                            request.setRequestHeader("AuthToken", resp.data.admin_token);
                        },
                        type: 'post',
                        dataType: 'json',
                        contentType: 'application/json',
                        success: function(summary) {
                            // console.log("Summary==> " + type, summary);
                            if (summary != null && summary.question != null && summary.question.feedback != null) {
                                $("#" + filter + "PendingCount").text(summary.question.feedback.frequency);
                                $("#" + filter + "TotalCount").text(" / " + summary.question.result);

                            }

                        },
                        data: JSON.stringify(request)
                    });



                } else {
                    alert("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                // An error occurred
                alert("Exception: " + error);
            });



    }

    var reachedLast = false;
    var loading = true;
    var serviceCallCounter = 0;

    function loadFeedback(type, inputValue, filter, startIndex) {
        var studentObj = null;
        var subjectId = null;
        if (teacherId != null) {
            if (superadminFlag) {
                studentObj = {
                    id: teacherId,
                    accessType: 'Teacher',
                    referrer: 'Admin' //Only for super admin which will skip classroom filter for doubts
                }
            } else {
                studentObj = {
                    id: teacherId,
                    accessType: 'Teacher'
                }
            }

        }

        if (startIndex != null) {
            lastID = startIndex;
        } else {
            lastID = 0;
        }

        if (inputValue > 0) {
            subjectId = inputValue;
        }

        var request = {
            institute: {
                id: <?= $instituteID ?>,
                status: 'Admin'
            },
            requestType: type,
            searchFilter: filter,
            student: studentObj,
            subjectId: subjectId,
            startIndex: lastID,
            startTime: $("#time_filter").val()
        };

        loading = true;
        loader(true);

        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {

                    $.ajax({
                        url: '<?= $fetchFeedbackData ?>',
                        beforeSend: function(request) {
                            request.setRequestHeader("AuthToken", resp.data.admin_token);
                        },
                        type: 'post',
                        dataType: 'json',
                        contentType: 'application/json',
                        success: function(feedbackData) {

                            loader(false);

                            loading = false;

                            if (type == 'Unresolved' && feedbackData != null && feedbackData.test != null && feedbackData.test.test != null) {
                                $("#pendingCount").text(feedbackData.test.test.length);
                            }

                            //var inputValue = $(this).val();
                            if (type == 'Resolved') {
                                fd = {
                                    'dropdownValue': inputValue,
                                    'resolvedObjectDataString': feedbackData,
                                    'serviceCallCounter': serviceCallCounter,
                                    'doubts_type_filter': filter,
                                    'time_filter': $("#time_filter").val()
                                };
                            } else {
                                fd = {
                                    'dropdownValue': inputValue,
                                    'unResolvedObjectDataString': feedbackData,
                                    'serviceCallCounter': serviceCallCounter,
                                    'doubts_type_filter': filter,
                                    'time_filter': $("#time_filter").val()
                                };
                            }

                            lastID = lastID + feedbackData.test.test.length;
                            if (feedbackData.test == null || feedbackData.test.test == null || feedbackData.test.test.length == 0) {
                                reachedLast = true;
                            }

                            // console.log("fd ", fd);
                            // console.log("Last ID is " + lastID);
                            $.post(base_url + '/doubts/fetch_filtered_doubts', fd, function(data) {
                                serviceCallCounter++;


                                //do after submission operation in DOM
                                if (type == 'Resolved') {
                                    $("#resolved_doubts_tbody").append(data);
                                } else {
                                    $("#unresolved_doubts_tbody").append(data);
                                }

                                initializeTooltip();

                                MathJax.texReset();
                                MathJax.typesetClear();
                                MathJax.typesetPromise()
                                    .catch(function(err) {
                                        console.log("Error -- " + err.message);
                                    })
                                    .then(function() {
                                        // console.log("Done adding to == > ");
                                    });
                            });
                        },
                        data: JSON.stringify(request)
                    });


                } else {
                    alert("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                // An error occurred
                alert("Exception: " + error);
            });



    }

    function loadFeedbacks(inputValue, filter, startIndex, resolutionType) {

        // console.log("Load feedbacks for " + inputValue);
        if (startIndex == null || startIndex == 0) {
            $("#unresolved_doubts_tbody").html("");
            $("#resolved_doubts_tbody").html("");
            $("#unresolved_doubts_tbody").empty();
            $("#resolved_doubts_tbody").empty();
            reachedLast = false;
            serviceCallCounter = 0;
        }

        // Toggling doubts content block based on resolutionType
        $(".doubts-content-block").addClass("d-none");
        $("#" + resolutionType.toLowerCase() + "_doubts_tbody").removeClass("d-none");

        if (resolutionType != null) {
            loadFeedback(resolutionType, inputValue, filter, startIndex);
        } else {
            loadFeedback('Unresolved', inputValue, filter, startIndex);
        }
    }

    //To validate solution photo / image in modal
    function validateFile(file) {
        // console.log("Validating file");

        //Validating file type
        var ext = $(file).val().split('.').pop().toLowerCase();
        if ($.inArray(ext, ['png', 'jpg', 'jpeg']) == -1) {
            alert('File type is invalid. Please upload png, jpg, jpeg file only!');
            $(file).val("");
            return;
        }
    }

    var token;

    $(document).ready(function() {

        $('.doubt_search_filter').select2({
            width: "100%",
            dropdownAutoWidth: true
        });

        //Load authentication first
        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                    token = resp.data.admin_token;

                    //Set filter value to show all
                    loadFeedbacks($('#subject_filter_dropdown').val(), doubtsType, 0, tab);

                    //Load summary for all doubts
                    loadSummary('Unresolved', $('#subject_filter_dropdown').val(), 'Exams');
                    loadSummary('Unresolved', $('#subject_filter_dropdown').val(), 'Videos');
                    loadSummary('Unresolved', $('#subject_filter_dropdown').val(), 'General');


                    // Reference link: https://stackoverflow.com/questions/42107672/dropdown-onchange-calling-php-function
                    $('#subject_filter_dropdown').change(function() {
                        //Selected value
                        var inputValue = $(this).val();
                        loadFeedbacks(inputValue, doubtsType, 0, tab);
                        loadSummary('Unresolved', inputValue, 'Exams');
                        loadSummary('Unresolved', inputValue, 'Videos');
                        loadSummary('Unresolved', inputValue, 'General');
                    });

                    //time_filter
                    $('#time_filter').change(function() {
                        //Selected value
                        var inputValue = $("#subject_filter_dropdown").val();
                        // console.log("Time filter changed ..", inputValue);
                        loadFeedbacks(inputValue, doubtsType, 0, tab);
                        loadSummary('Unresolved', inputValue, 'Exams');
                        loadSummary('Unresolved', inputValue, 'Videos');
                        loadSummary('Unresolved', inputValue, 'General');
                    });
                } else {
                    alert("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                // An error occurred
                // console.log("Exception: " + error);
            });
    });

    //For pagination on scroll
    $(window).scroll(function() {
        if (!loading && !reachedLast) {
            //200 is the buffer to work on mobile browsers
            if (((Math.ceil($(window).scrollTop()) + 200) >= $(document).height() - $(window).height()) && (lastID != 0)) {
                // console.log("Loading more data " + lastID);
                var inputValue = $('#subject_filter_dropdown').val();
                loadFeedbacks(inputValue, doubtsType, lastID, tab);
            }
        }

    });
</script>