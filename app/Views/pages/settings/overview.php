<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/settings/overview.css?v=20210915'); ?>" rel="stylesheet">
<div id="content">
    <div class="container-fluid mt-4">

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


        <div class="row">
            <div class="col-md-3 col-xl-2">

                <div class="card">
                    <div class="list-group list-group-flush" role="tablist">
                        <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#account" role="tab">
                            Institute Details
                        </a>
                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#subjects" role="tab">
                            Subjects
                        </a>
                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#chapters" onclick="load_chapter_datatable()" role="tab">
                            Chapters
                        </a>
                        <a class="list-group-item list-group-item-action d-none" data-bs-toggle="list" href="#hot_keys" role="tab">
                            Shortcut keys
                        </a>
                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#privacy" role="tab">
                            Privacy and safety
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-9 col-xl-10">

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="account" role="tabpanel">

                        <div class="card">
                            <div class="card-header">
                                <h5>Institute Details</h5>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-8">

                                        <form action="institutes/update_institute_details_submit" role="form" enctype="multipart/form-data" method="post" id="update_profile_form">

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="institute_name" class="form-label">Institute Name *</label>
                                                    <input type="text" class="form-control" id="institute_name" name="institute_name" value="<?php echo $institute_details['institute_name']; ?>" minlength="3" maxlength="120" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="institute_email" class="form-label">Email *</label>
                                                    <input type="email" class="form-control" id="institute_email" name="email" value="<?php echo $institute_details['email']; ?>" minlength="3" maxlength="120" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="institute_contact_number" class="form-label">Contact Number *</label>
                                                    <input type="text" class="form-control" id="institute_contact_number" name="contact" value="<?php echo trim($institute_details['contact_number']); ?>" maxlength="10" size="10" pattern="\d{10}" title="Enter 10 digit mobile number" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="video_streaming_condition" class="form-label">Allow Videos Streaming In: *</label>
                                                    <select class="form-control" id="video_streaming_condition" name="video_streaming_condition">
                                                        <option value="">Everywhere</option>
                                                        <option value="APP" <?php if ($institute_details['video_constraint'] == "APP") echo "selected"; ?>>APP Only</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="institute_gst_no" class="form-label">GST No</label> (For Invoices)
                                                    <input type="text" class="form-control" id="institute_gst_no" name="institute_gst_no" value="<?php echo $institute_details['gst_no']; ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="institute_address" class="form-label">Timezone</label>
                                                    <select class="timezone_dropdown" name="timezone" id="timezone" required>
                                                        <option value=""></option>
                                                        <?php
                                                        if (!empty($timezones)) :
                                                            foreach ($timezones as $timezone) :
                                                                $select_check = "";
                                                                if ($timezone['zone_name'] == $institute_details['timezone']) :
                                                                    $select_check = "selected";
                                                                endif;
                                                        ?>
                                                                <option value="<?= $timezone['zone_name']; ?>" <?= $select_check; ?>>
                                                                    <?= $timezone['zone_name']; ?>
                                                                </option>
                                                        <?php
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="institute_address" class="form-label">Address</label>
                                                    <textarea class="form-control" id="institute_address" name="institute_address"><?= $institute_details['address']; ?></textarea>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mt-4">
                                                        <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                                                        <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                                                        <input type="submit" class="btn btn-primary" name="updateProfile" value="Save Changes" />
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <?php if (isset($_SESSION['logo_path'])) {
                                                $logoUrl = $_SESSION['logo_path'];
                                                $logo_prefix = "../";
                                                if (strpos($logoUrl, 'http') >= 0) {
                                                    $logo_prefix = "";
                                                }
                                            ?>
                                                <img alt="logo-img" width="80" height="80" class="rounded-circle img-fluid mt-2" src="<?= $logo_prefix . $logoUrl; ?>">
                                            <?php
                                            } ?>
                                            <div class="fw-bold">
                                                <?php echo $_SESSION['instituteName']; ?>
                                            </div>
                                            <div class="mt-2">
                                                <a class="btn btn-primary" onclick="show_edit_modal('modal_div','update_insitute_logo','institutes/update_logo/<?php echo $instituteID; ?>/settings');"><i class="fas fa-upload"></i> Update Institute Logo</a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- Subject List -->
                    <div class="tab-pane fade show" id="subjects" role="tabpanel">

                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h5>Subjects</h5>
                                    </div>
                                    <div class="text-end">
                                        <button class='btn btn-sm border-0' onclick="show_add_modal('modal_div','add_subject_modal','subjects/add_subject_modal');" data-bs-toggle="tooltip" title="Add New Subject">
                                            <i class='action_button_plus_icon material-icons' style="background-color: #ed4c05;">add</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="table-responsive table_custom">
                                    <table class="table table-bordered" id="subject_table" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th> # </th>
                                                <th> Subject </th>
                                                <th> Created Date </th>
                                                <th> Update </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($subjects)) :
                                                $i = 1;
                                                foreach ($subjects as $subject) :
                                                    $subject_id = encrypt_string($subject['subject_id']);
                                            ?>
                                                    <tr>
                                                        <td><?= $i; ?></td>
                                                        <td><?= $subject['subject']; ?></td>
                                                        <td><?= changeDateTimezone($subject['created_date']); ?></td>
                                                        <?php
                                                        if (!empty($subject['institute_id'])) :
                                                        ?>
                                                            <td>

                                                                <!-- Update Option -->
                                                                <button class='btn btn-sm' onclick="show_edit_modal('modal_div','update_subject_modal','subjects/update_subject_modal/<?php echo $subject_id; ?>');" data-bs-toggle="tooltip" title="Edit subject details"><i class='material-icons material-icon-small'>edit</i></button>



                                                                <!-- Delete Option -->
                                                                <button class='btn btn-sm' onclick="show_edit_modal('modal_div','delete_subject_modal','subjects/delete_subject_modal/<?php echo $subject_id; ?>');" data-bs-toggle="tooltip" title="Delete subject"><i class='material-icons material-icon-small text-danger'>delete</i></button>

                                                            </td>
                                                        <?php else : ?>
                                                            <td> <i class="fas fa-exclamation-circle" data-bs-toggle="tooltip" title="Default Subjects, not editable"></i> </td>
                                                        <?php endif; ?>
                                                    </tr>
                                            <?php
                                                    $i++;
                                                endforeach;
                                            endif;
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Chapter List  -->
                    <div class="tab-pane fade show" id="chapters" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h5>Chapters</h5>
                                    </div>
                                    <div>
                                        <button class='btn btn-sm border-0' onclick="show_add_modal('modal_div','add_chapter_modal','chapters/add_chapter_modal');" data-bs-toggle="tooltip" title="Add New Chapter">
                                            <i class='action_button_plus_icon material-icons' style="background-color: #ed4c05;">add</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive table_custom">
                                    <table class="table w-100" id="chapterListTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Subject</th>
                                                <th>Chapter Name</th>
                                                <th>Created Date</th>
                                                <th>Update</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>


                            </div>
                        </div>
                    </div>


                    <div class="tab-pane fade show" id="hot_keys" role="tabpanel">

                        <div class="card">
                            <div class="card-header">
                                <h5>Shortcut Keys</h5>
                            </div>
                            <div class="card-body">
                                <p><b>A shortcut key is a key or a combination of keys on a computer keyboard that, when pressed at one time, performs a task (such as starting an application) more quickly than by using a mouse or other input device.</b></p>
                                <ol>
                                    <li>Add New Test - ctrl+alt+t</li>
                                    <li>Add New Student - ctrl+alt+s</li>
                                    <li>Add New Classroom - ctrl+alt+c</li>
                                </ol>
                            </div>
                        </div>

                    </div>


                    <div class="tab-pane fade show" id="privacy" role="tabpanel">

                        <div class="card">
                            <div class="card-header">
                                <h5>Privacy and safety</h5>
                            </div>
                            <div class="card-body">
                                <p><b>As part of our new security policy, users need to periodically change the password to prevent from malpractices on the account. Please proceed to next step and change the password at your earliest convenience. We regret any inconvenience caused.</b></p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script>
    var chapterListTable;
    $(document).ready(function() {
        initializeTooltip();
        var subject_table = $('#subject_table').DataTable({
            "pageLength": 10,
            language: {
                search: ""
            }
        });
        $("#subject_table_filter input").attr('placeholder', 'Search');
    });
</script>


<script>
    // Initializing select2
    $('.timezone_dropdown').select2({
        width: "100%",
    });
</script>

<script>
    function load_chapter_datatable() {
        // his method provides the ability to check if a table node is already a DataTable or not
        if (!$.fn.DataTable.isDataTable('#chapterListTable')) {
            chapterListTable = $('#chapterListTable').DataTable({
                "processing": true,
                "stateSave": true,
                "serverSide": true,
                "pageLength": 10,
                dom: "Bflrtip",
                buttons: [""],
                "columnDefs": [{
                    "targets": [0, 4],
                    "orderable": false,
                }],
                "order": [
                    [1, "asc"]
                ],
                "ajax": {
                    'url': base_url + "/chapters/chapter_list",
                    "type": "POST",
                    "data": function(d) {}
                },
                language: {
                    search: ""
                },
                'columns': [{
                        data: 'sr_no'
                    },
                    {
                        data: 'subject'
                    },
                    {
                        data: 'chapter_name'
                    },
                    {
                        data: 'created_date'
                    },
                    {
                        data: 'action',
                        class: 'btn_col'
                    }
                ]
            });
            // Moved Datatable Search box and Page length option
            $("#chapterListTable_filter input").attr('placeholder', 'Search');
        }
    }
</script>