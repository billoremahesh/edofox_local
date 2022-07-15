<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/classrooms/overview.css?v=20210917'); ?>" rel="stylesheet">


<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('classrooms'); ?>"> Classrooms </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="card shadow p-4">

            <div id="main_div">

            <h4 class="text-center"><?= $classroom_details['package_name']; ?></h4>
                <div style="margin:16px 8px;text-align:right;">
                <?php if (in_array("manage_classrooms", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <button type="button" class="btn btn-danger" onclick="block_accounts_bulk_students();">
                        <i class="fa fa-ban" aria-hidden="true"></i> Block Accounts
                    </button>
                    
                    <button type="button" class="btn btn-primary" onclick="migrate_bulk_students();">
                        <i class="fa fa-clone" aria-hidden="true"></i> Migrate
                    </button>

                    <button type="button" class="btn btn-danger" onclick="delete_bulk_students();">
                        <i class="far fa-trash-alt" aria-hidden="true"></i> Delete
                    </button>
                    <?php endif; ?>
                </div>

                <div class="table_custom">
                    <table class="table table-bordered edo-table" id="active_student_data_table">
                        <thead>
                            <tr>
                                <th> #</th>
                                <th> Roll No </th>
                                <th> Student Name </th>
                                <th style="width:5%;"><input type="checkbox" id="selectAll" /> Select All Visible</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if ($classroom_students) :
                                foreach ($classroom_students as $row) :
                                    echo "<tr>";
                                    echo "<td class='row_student_serial'>" . $i . "</td>";
                                    echo "<td class='row_student_roll'>" . $row['roll_no'] . "</td>";
                                    echo "<td class='row_student_name'>" . $row['name'] . "</td>";
                                    echo "<td> <input type='checkbox' name='student_ids[]' class='bulk_students_select' value=" . $row['student_id'] . " > </td>";
                                    echo "</tr>";
                                    $i++;
                                endforeach;
                            endif
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <hr>
                <div style="display: flex; justify-content: space-between;margin:16px auto;">
                    <label>Blocked Students in this classroom:</label>

                    <button type="button" class="btn btn-warning" onclick="unblock_accounts_bulk_students();">
                        <i class="fa fa-check-square-o" aria-hidden="true"></i> Un-Block Accounts
                    </button>
                </div>
                
                <div class="table_custom">
                    <table class="table table-bordered edo-table" id="disable_student_data_table">
                        <thead>
                            <tr>
                                <th> #</th>
                                <th> Roll No </th>
                                <th> Student Name </th>
                                <th style="width:5%;"><input type="checkbox" id="selectAllBlocked" /> Select All Visible</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if ($blocked_classroom_students) :
                                foreach ($blocked_classroom_students as $row) :
                                    echo "<tr>";
                                    echo "<td class='row_student_serial'>" . $i . "</td>";
                                    echo "<td class='row_student_roll'>" . $row['roll_no'] . "</td>";
                                    echo "<td class='row_student_name'>" . $row['name'] . "</td>";
                                    echo "<td> <input type='checkbox' name='student_ids[]' class='blocked_bulk_students_select' value=" . $row['student_id'] . " > </td>";
                                    echo "</tr>";
                                    $i++;
                                endforeach;
                            endif
                            ?>
                        </tbody>
                    </table>
                </div>


            </div>

        </div>
    </div>
</div>

<!-- Block Accounts of Students in Bulk in this classroom Modal -->
<div id="block_accounts_bulk_students_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Block Accounts of Students </h4>
                </div>
                <?php echo form_open('classrooms/block_students_submit'); ?>
                    <div class="modal-body">
                    <input type="hidden" name="block_accounts_package_id" id="block_accounts_students_package_id" value="<?php echo $package_id; ?>" required>

                    <input type="hidden" name="block_accounts_student_ids[]" id="block_accounts_student_ids" required>

                    <p class="text-danger">
                        Are you sure, you want to block accounts of selected students? They will NOT be able to login in their accounts.
                    </p>
                    </div>
                    <div class="modal-footer">
                        
                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"> Yes </button>
                    </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>



    <!-- UNBlock Accounts of Students in Bulk in this classroom Modal -->
    <div id="unblock_accounts_bulk_students_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Un-Block Accounts of Students </h4>
                </div>
                <?php echo form_open('classrooms/unblock_students_submit'); ?>
                    <div class="modal-body">

                        <input type="hidden" name="unblock_accounts_package_id" id="unblock_accounts_students_package_id" value="<?php echo $package_id; ?>" required>

                        <input type="hidden" name="unblock_accounts_student_ids[]" id="unblock_accounts_student_ids" required>

                        <p class="text-danger">
                            Are you sure, you want to un-block accounts of selected students? They will be able to login in their accounts.
                        </p>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="unblock_accounts_bulk_students_submit"> Un-Block Selected Accounts </button>
                    </div>
                <?php echo form_close(); ?>
            </div>

        </div>
        
    </div>

    <!-- Counts of selected checkboxes -->
    <div id="hovering-checkboxes-count-block" style="position: fixed; bottom: 16px; left: 50%; transform: translateX(-50%); display: flex; justify-content: center;">
        <div class="d-none" id="active-students-checkboxes-count-block" style="background-color: #2196f3; padding: 8px; border-radius: 4px; color: white; margin: 0px 8px">
            <span id="active_students_checkboxes_count">0</span> students selected
        </div>


        <div class="d-none" id="blocked-students-checkboxes-count-block" style="background-color: #ff0000; padding: 8px; border-radius: 4px; color: white; margin: 0px 8px">
            <span id="blocked_students_checkboxes_count">0</span> blocked students selected
        </div>
    </div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<!-- Select All CheckBox -->
<script>
    $(document).ready(function() {
        $('#selectAll').click(function(e) {
            $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
        });
    });

            // Select All CheckBox
            $('#selectAll').click(function(e) {
                $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
                selected_checkboxes_count();
            });

            // For selecting all blocked students
            $('#selectAllBlocked').click(function(e) {
                $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
                selected_checkboxes_count();
            });

            $('#active_student_data_table').on('click', 'td.row_student_serial, td.row_student_roll, td.row_student_name', function() {
                // console.log("Row clicked");

                // Toggling row checkbox when row is clicked
                if ($(this).parent().find('input.bulk_students_select').is(':checked')) {
                    $(this).parent().find('input.bulk_students_select').prop('checked', false);
                } else {
                    $(this).parent().find('input.bulk_students_select').prop('checked', true);
                }
                selected_checkboxes_count();

            });


            // Row onclick method
            $('#disable_student_data_table').on('click', 'td.row_student_serial, td.row_student_roll, td.row_student_name', function() {
                // console.log("Row clicked");

                // Toggling row checkbox when row is clicked
                if ($(this).parent().find('input.blocked_bulk_students_select').is(':checked')) {
                    $(this).parent().find('input.blocked_bulk_students_select').prop('checked', false);
                } else {
                    $(this).parent().find('input.blocked_bulk_students_select').prop('checked', true);
                }
                selected_checkboxes_count();

            });
</script>

<script>
        // To unblock accounts of the students showing up in this classroom
        function unblock_accounts_bulk_students() {

            var checkboxes = document.getElementsByClassName('blocked_bulk_students_select');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            if (!checkedOne) {
                alert("Select aleast one student.");
            } else {

                // Check selected checkboxes
                var checkedValue = [];
                var inputElements = checkboxes;

                for (var i = 0; inputElements[i]; ++i) {
                    if (inputElements[i].checked) {
                        checkedValue.push(inputElements[i].value);
                    }
                }
                $('#unblock_accounts_student_ids').val(checkedValue.toString());

                // Open Modal
                $("#unblock_accounts_bulk_students_modal").modal('show');
            }
        }


    function migrate_bulk_students() {

        var checkboxes = document.getElementsByClassName('bulk_students_select');
        var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
        if (!checkedOne) {
            alert("Select aleast one student.");
        } else {

            // Check selected checkboxes
            var checkedValue = [];
            var inputElements = document.getElementsByClassName('bulk_students_select');

            for (var i = 0; inputElements[i]; ++i) {
                if (inputElements[i].checked) {
                    checkedValue.push(inputElements[i].value);
                }
            }
            // Open Modal
            editModalAsync('modal_div', 'migrate_bulk_classroom_students_modal', 'classrooms/migrate_bulk_classroom_students_modal/<?php echo $package_id; ?>').then(function(response) {
                    if (response == 'success') {
                        $('#migrate_student_ids').val(checkedValue.toString());
                        console.log(checkedValue.toString());
                    }
                })
                .catch(function(error) {
                    // An error occurred
                    alert("Exception: " + error);
                });

        }

    }




    // To block accounts of the students showing up in this classroom
    function block_accounts_bulk_students() {

        var checkboxes = document.getElementsByClassName('bulk_students_select');
        var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
        if (!checkedOne) {
            alert("Select aleast one student.");
        } else {

            // Check selected checkboxes
            var checkedValue = [];
            var inputElements = document.getElementsByClassName('bulk_students_select');

            for (var i = 0; inputElements[i]; ++i) {
                if (inputElements[i].checked) {
                    checkedValue.push(inputElements[i].value);
                }
            }
            $('#block_accounts_student_ids').val(checkedValue.toString());

            // Open Modal
            $("#block_accounts_bulk_students_modal").modal('show');
        }
    }
</script>

<script>
    function delete_bulk_students() {
        var checkboxes = document.getElementsByClassName('bulk_students_select');
        var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
        if (!checkedOne) {
            alert("Select aleast one student.");
        } else {
            // Check selected checkboxes
            var checkedValue = [];
            var inputElements = document.getElementsByClassName('bulk_students_select');

            for (var i = 0; inputElements[i]; ++i) {
                if (inputElements[i].checked) {
                    checkedValue.push(inputElements[i].value);
                }
            }

            // Open Modal
            editModalAsync('modal_div', 'delete_bulk_classroom_students_modal', 'classrooms/delete_bulk_classroom_students_modal/<?php echo $package_id; ?>').then(function(response) {
                    if (response == 'success') {
                        $('#delete_student_ids').val(checkedValue.toString());
                        console.log(checkedValue.toString());
                    }
                })
                .catch(function(error) {
                    // An error occurred
                    alert("Exception: " + error);
                });
        }
    }
    
    $('.bulk_students_select, .blocked_bulk_students_select').change(function() {
            selected_checkboxes_count();
    });

    // To show selected checkboxes count
    function selected_checkboxes_count() {
            var active_students_selected = 0;

            $(".bulk_students_select").each(function() {
                // ...
                if (this.checked) {
                    active_students_selected++;
                }

                $("#active-students-checkboxes-count-block #active_students_checkboxes_count").html(active_students_selected);
            });
            
            if (active_students_selected === 0) {
                $("#active-students-checkboxes-count-block").addClass("d-none");
            } else {
                $("#active-students-checkboxes-count-block").removeClass("d-none");
            }


            // Calculating count of blocked students checkboxes
            var blocked_students_selected = 0;
            $(".blocked_bulk_students_select").each(function() {
                // ...
                if (this.checked) {
                    blocked_students_selected++;
                }

                $("#blocked-students-checkboxes-count-block #blocked_students_checkboxes_count").html(blocked_students_selected);
            });

            if (blocked_students_selected === 0) {
                $("#blocked-students-checkboxes-count-block").addClass("d-none");
            } else {
                $("#blocked-students-checkboxes-count-block").removeClass("d-none");
            }
        }
</script>




<script>
    
    // Call the dataTables jQuery plugin
        $(document).ready(function () {
            var active_student_data_table = document.getElementById("active_student_data_table");
            if (active_student_data_table != null) {
                $("#active_student_data_table").DataTable({
                order: [0, "asc"],
                dom: "Bflrtip",
                buttons: ["excel"],                
                pageLength: 50,
                stateSave: true,
                language: {
                    search: ""
                },
            });

            // Moved Datatable Search box and Page length option
            $("#active_student_data_table_filter input").attr('placeholder', 'Search');

            // $("#dataTables_search_box_div").html($("#active_student_data_table_filter"));
            // $("#dataTables_length_div").html($("#active_student_data_table_length"));
            // waitForElementToDisplay("#active_student_data_table_filter", 1000, 1);
            }
        });

    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#active_student_data_table_filter").prepend($("#active_student_data_tableExportGroup"));
            $("#active_student_data_tableExportGroup").show();
            return;
        } else {
            setTimeout(function () {
            waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    function dtExport(sContainerName, sType) {
        var sButtonName = "";
        switch (sType) {
            case "excel":
            sButtonName = "buttons-excel";
            break;
            case "pdf":
            sButtonName = "buttons-pdf";
            break;
    }

    $("#" + sContainerName + " ." + sButtonName).click();
    }
    
    
    
    
    // Disable Class room data table
    // Call the dataTables jQuery plugin
        $(document).ready(function () {
            var disable_student_data_table = document.getElementById("disable_student_data_table");
            if (disable_student_data_table != null) {
                $("#disable_student_data_table").DataTable({
                order: [0, "asc"],
                dom: "Bflrtip",
                buttons: ["excel"],
                pageLength: 50,
                stateSave: true,
                language: {
                    search: ""
                },
            });

            // Moved Datatable Search box and Page length option
            $("#disable_student_data_table_filter input").attr('placeholder', 'Search');

            $("#disable_dataTables_search_box_div").html($("#disable_student_data_table_filter"));
            $("#disable_dataTables_length_div").html($("#disable_student_data_table_length"));
            waitForElementToDisplay("#disable_student_data_table_filter", 1000, 1);
            }
        });

    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#disable_student_data_table_filter").prepend($("#disable_student_data_tableExportGroup"));
            $("#disable_student_data_tableExportGroup").show();
            return;
        } else {
            setTimeout(function () {
            waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }



</script>