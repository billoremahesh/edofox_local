<?php
if (!empty($realtime_student_details)) :
?>
    <div class="mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Name :</label></div>
                    <div class="col-xs-6 col-md-6">
                        <a href="<?= base_url('tests/student_test_activity/' . $encrypted_test_id . '/' . $encrypted_student_id); ?>" target="_blank"><?= $realtime_student_details['name'] ?> <i class='fas fa-external-link-alt' aria-hidden='true'></i></a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Roll Number :</label></div>
                    <div class="col-xs-6 col-md-6"><?= $realtime_student_details['roll_no'] ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Mobile :</label></div>
                    <div class="col-xs-6 col-md-6"><?= $realtime_student_details['mobile_no'] ?></div>
                </div>

                <?php if (!empty($realtime_student_details['school_district'])) : ?>
                    <div class="row">
                        <div class="col-xs-6 col-md-2"><label>District :</label></div>
                        <div class="col-xs-6 col-md-6"><?= $realtime_student_details['school_district'] ?></div>
                    </div>
                <?php endif; ?>

                <hr />

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Test Status :</label></div>
                    <div class="col-xs-6 col-md-6"><?= $realtime_student_details['status'] ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Time Left :</label></div>
                    <div class="col-xs-6 col-md-6"><?= $realtime_student_details['time_left'] ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Test Started Count :</label></div>
                    <div class="col-xs-6 col-md-6"><?= $realtime_student_details['exam_started_count'] ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Started At Time :</label></div>
                    <div class="col-xs-6 col-md-6"><?= changeDateTimezone($realtime_student_details['created_date']) ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Attempted :</label></div>
                    <div class="col-xs-6 col-md-6"><?= $realtime_student_details['attemptedCount'] ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Answered :</label></div>
                    <div class="col-xs-6 col-md-6"><?= $realtime_student_details['solvedCount'] ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Marked for review :</label></div>
                    <div class="col-xs-6 col-md-6"><?= $realtime_student_details['flaggedCount'] ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Submission :</label></div>
                    <div class="col-xs-6 col-md-6"><?= strtoupper($realtime_student_details['submission_type']) ?></div>
                </div>

                <?php if (!empty($realtime_student_details['proctoring_remarks'])) : ?>
                    <div class="row">
                        <div class="col-xs-6 col-md-2"><label>Proctoring Score :</label></div>
                        <div class="col-xs-6 col-md-6"><?= $realtime_student_details['proctoring_remarks'] ?></div>
                    </div>
                <?php endif; ?>

                <hr />

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Device :</label></div>
                    <div class="col-xs-6 col-md-6"><?= strtoupper($realtime_student_details['device']) ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Device Info :</label></div>
                    <div class="col-xs-6 col-md-10"><?= $realtime_student_details['device_info'] ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-6 col-md-2"><label>Edit Test Status :</label></div>
                    <div class="col-xs-6 col-md-6">
                        <button class='btn btn-default more_option_button' onclick="show_edit_modal('modal_div','edit_student_test_status','tests/edit_student_test_status/<?php echo $test_id; ?>/<?php echo $student_id; ?>');" data-bs-toggle="tooltip" title="Update student test status">
                            <i class='fas fa-pencil-alt' aria-hidden='true'></i>
                        </button>

                        <button class='btn btn-default more_option_button' onclick="show_edit_modal('modal_div','reset_student_exam_session','tests/reset_student_exam_session/<?php echo $test_id; ?>/<?php echo $student_id; ?>');" data-bs-toggle="tooltip" title="Reset student exam session">
                            <i class="fas fa-redo"></i>
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
else :
    echo "Student details not found";
endif;
?>