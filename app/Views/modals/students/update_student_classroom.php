<!-- Update Student Classroom -->
<div id="update_student_classroom" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('Students/update_student_classroom_submit'); ?>" method="post" id="update_student_classroom_form">
                <div class="modal-body">
                    <div class='fw-bolder mb-2'> Student Name: <?= strtoupper($student_details['name']); ?></div>
                    <div class="row">
                        <div class="mb-2">
                            <label class="form-label">Classroom:</label>
                            <select class="form-control" name="to_update_package_id" id="to_update_package_id" required>
                                <option value="" disabled></option>
                                <?php
                                $selectedPackageId = $student_package_data['package_id'];
                                if (!empty($packages_data)) :
                                    foreach ($packages_data as $row) :
                                        $package_id = $row['id'];
                                        $package_name = $row['package_name'];
                                ?>
                                        <option value="<?php echo $package_id ?>" <?php if ($package_id == $selectedPackageId) echo "selected"; ?>><?= $package_name; ?></option>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Classroom Status:</label>
                            <?php $update_payment_status = $student_package_data['status']; ?>
                            <select class="form-control" name="payment_status" id="payment_status" required>
                                <option value="" disabled></option>
                                <option value="Completed" <?php if ($update_payment_status == "Completed") { ?>selected="selected" <?php } ?>>Approved</option>
                                <option value="Created" <?php if ($update_payment_status == "Created") { ?>selected="selected" <?php } ?>>Payment Pending</option>
                                <option value="Rejected" <?php if ($update_payment_status == "Rejected") { ?>selected="selected" <?php } ?>>Rejected</option>
                            </select>
                        </div>
                        <input type="hidden" class="form-control" name="update_stu_package_id" id="update_stu_package_id" value="<?php echo $stu_pkg_id; ?>" required>
                        <input type="hidden" name="student_id" value="<?= encrypt_string($student_package_data['student_id']); ?>" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>