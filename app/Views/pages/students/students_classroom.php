<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<!-- Students Classroom View -->

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/students/students_classroom.css'); ?>" rel="stylesheet" />

<div id="content">
    <div class="container-fluid mt-4">
        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"><?= $title; ?></label>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('students'); ?>"> Students </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>



        <div class="card shadow p-4">
            <div class="card_box">
                <h6 class='text-center fw-bolder'><?= strtoupper($student_details['name']); ?></h6>
                <div class="d-flex flex-row-reverse m-2">

                    <div>
                        <!-- Moved Datatable Search box -->
                        <div id="dataTables_search_box_div"></div>
                    </div>

                    <div>
                        <!-- Moved Datatable Page Length Menu -->
                        <div style="margin-left: 16px;" id="dataTables_length_div"></div>
                    </div>

                    <?php if (in_array("manage_classrooms", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                        <div style="text-align: right;">
                            <button class='btn btn-sm border-0' onclick="show_add_modal('modal_div','add_classroom','students/add_classroom_modal/<?= $student_id; ?>/<?= $instituteID; ?>');" data-bs-toggle="tooltip" title="Add package to student">
                                <i class='action_button_plus_icon material-icons' style="background-color: #ed4c05;">add</i>
                            </button>
                        </div>
                    <?php endif; ?>

                </div>

                <?php
                if (!empty($student_classrooms)) :
                ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-condensed" id='custom_data_table'>
                            <thead>
                                <tr>
                                    <th> # </th>
                                    <th> Classroom Name </th>
                                    <th> Classroom Status </th>
                                    <th> Actions </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cnt = 1;
                                foreach ($student_classrooms as $row) :
                                    $update_stu_package_id = $row['id'];
                                    $update_package_name = $row['package_name'];
                                    $classroom_status = $row['status'];

                                    echo "<tr>";
                                    echo "<td>" . $cnt . "</td>";
                                    echo "<td>" . $update_package_name . "</td>";
                                    echo "<td>" . $classroom_status . "</td>";
                                ?>
                                    <td>
                                        <button class='btn btn-sm' onclick="show_add_modal('modal_div','update_student_classroom','students/update_student_classroom/<?php echo encrypt_string($update_stu_package_id); ?>');"> <i class='material-icons material-icon-small'>edit</i>
                                        </button>

                                        <button class='btn btn-sm' onclick="show_add_modal('modal_div','delete_student_classroom','students/delete_classroom_modal/<?= $student_id; ?>/<?php echo encrypt_string($update_stu_package_id); ?>/<?= $instituteID; ?>');">
                                            <i class='material-icons material-icon-small text-danger'>delete</i>
                                        </button>
                                    </td>
                                <?php
                                    echo "</tr>";
                                    $cnt++;
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                else :
                    echo "<div class='text-danger text-center'>No classroom mapped, please add classroom for this student</div>";
                endif;
                ?>
                <div id="custom_data_tbleExportGroup" class="export-icon-group" style="display: none">
                    <img class="export-icon" onclick='dtExport("custom_data_tble_wrapper","excel");' src="<?php echo base_url('assets/img/icons/download-excel-512x512.png'); ?>" alt='Excel' height='16' width='16'>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>