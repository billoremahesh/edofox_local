<?php include_once(APPPATH . "Views/header.php"); ?>
<!-- Students View -->

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/upload_exam_pdf_paper.css?v=20220524'); ?>" rel="stylesheet" />


<div id="content">
    <div class="container-fluid mt-4">
        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"><?= $test_details['test_name']; ?></label>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="bg-white w-100 shadow rounded p-4 mb-5">

            <h4 class="text-center"><?= $test_details['test_name']; ?></h4>
            <form action="#" method="post" id="add_test_pdf_paper">
                <div class="my-4">
                    <label class="form-label" for="pdf_file">Choose PDF File *</label>
                    <input type="file" class="form-control" id="pdf_file" name="pdf_file" required />
                </div>
                <hr />

                <div class="row justify-content-between">
                    <div class="col-10">
                        <h6> Exam Section Wise Configurations </h6>
                    </div>
                    <div class="col-2 text-right">
                        <button type="button" class="add_section_btn" onclick="add_exam_section_structure()">
                            Add Section
                        </button>
                    </div>
                </div>

                <table class="table table-bordered table-condensed my-4" id="exam_section_structure_tbl">
                    <thead>
                        <tr>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Weightage</th>
                            <th>Negative Mark</th>
                            <th>Question Type</th>
                            <th>From Question</th>
                            <th>To Question</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if (!empty($test_template_data)) {
                            $template_id = $test_details['template_id'];
                            $index_no = 1;
                            foreach ($test_template_data as $template_data) {
                                $i = $template_data['from_question'];
                                $weightage = 1;
                                $weightage_template_rule = get_template_rule($template_id, 'SECTION_WEIGHTAGE', $i);
                                if (!empty($weightage_template_rule)) {
                                    $weightage = $weightage_template_rule['value'];
                                }

                                $negative_mark = 0;
                                $negative_mark_template_rule = get_template_rule($template_id, 'SECTION_NEGATIVE_MARKS', $i);
                                if (!empty($negative_mark_template_rule)) {
                                    $negative_mark = $negative_mark_template_rule['value'];
                                }
                                $quetionType = "SINGLE";
                                $quetionType_template_rule = get_template_rule($template_id, 'SECTION_QUESTION_TYPE', $i);
                                if (!empty($quetionType_template_rule)) {
                                    $quetionType = $quetionType_template_rule['value'];
                                }

                                $subjectid = "";
                                $subjectid_template_rule = get_template_rule($template_id, 'SECTION_SUBJECT', $i);
                                if (!empty($subjectid_template_rule)) {
                                    $subjectid = $subjectid_template_rule['value'];
                                }

                        ?>
                                <tr class="exam_section_structure_tr" id="exam_section_structure_tr_1">
                                    <td>
                                        <input type='text' class='form-control exam_sections' id="exam_sections_<?= $index_no; ?>" value="<?= $template_data['value']; ?>" name='exam_sections[]'>
                                    </td>
                                    <td>
                                        <select class="form-select" name='subject[]' id="exam_subjects_<?= $index_no; ?>">
                                            <option></option>
                                            <?php foreach ($subject_details as $row) {
                                                $sub_selected = "";
                                                if ($subjectid == $row['subject_id']) {
                                                    $sub_selected = " selected";
                                                }
                                                echo "<option value='" . $row['subject_id'] . "' $sub_selected >" . $row['subject'] . "</option>";
                                            } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name='exam_sections_weightage[]' id="exam_weightage_<?= $index_no; ?>" min="0"  value="<?= $weightage; ?>" />
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name='exam_sections_negative_mark[]' id="exam_negative_mark_<?= $index_no; ?>" value="<?= $negative_mark; ?>" />
                                    </td>
                                    <td>
                                        <select class="form-select" name="exam_sections_question_type[]" id="exam_question_type_<?= $index_no; ?>">
                                            <option value="SINGLE" <?php if ($quetionType == "SINGLE") {
                                                                        echo "selected";
                                                                    } ?>>SINGLE</option>
                                            <option value="MULTIPLE" <?php if ($quetionType == "MULTIPLE") {
                                                                            echo "selected";
                                                                        } ?>>MULTIPLE</option>
                                            <option value="MATCH" <?php if ($quetionType == "MATCH") {
                                                                        echo "selected";
                                                                    } ?>>MATCH</option>
                                            <option value="NUMBER" <?php if ($quetionType == "NUMBER") {
                                                                        echo "selected";
                                                                    } ?>>NUMBER</option>
                                            <option value="DESCRIPTIVE" <?php if ($quetionType == "DESCRIPTIVE") {
                                                                            echo "selected";
                                                                        } ?>>Subjective Answer</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name='exam_sections_from_question[]' value="<?= $template_data['from_question']; ?>" id="from_question_<?= $index_no; ?>" />
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name='exam_sections_to_question[]' value="<?= $template_data['to_question']; ?>" id="to_question_<?= $index_no; ?>" />
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            <?php
                                $index_no++;
                            }
                        } else {
                            ?>
                            <tr class="exam_section_structure_tr" id="exam_section_structure_tr_1">
                                <td>
                                    <input type='text' class='form-control exam_sections' placeholder='Section' name='exam_sections[]' id="exam_sections_1">
                                </td>
                                <td>
                                    <select class="form-select" name='subject[]' id="exam_subjects_1">
                                        <option></option>
                                        <?php foreach ($subject_details as $row) {
                                            echo "<option value=" . $row['subject_id'] . ">" . $row['subject'] . "</option>";
                                        } ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name='exam_sections_weightage[]' id="exam_weightage_1" min="0" value="1" />
                                </td>
                                <td>
                                    <input type="number" class="form-control" name='exam_sections_negative_mark[]' id="exam_negative_mark_1" />
                                </td>
                                <td>
                                    <select class="form-select" name="exam_sections_question_type[]" id="exam_question_type_1">
                                        <option value="SINGLE">SINGLE</option>
                                        <option value="MULTIPLE">MULTIPLE</option>
                                        <option value="MATCH">MATCH</option>
                                        <option value="NUMBER">NUMBER</option>
                                        <option value="DESCRIPTIVE">Subjective Answer</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name='exam_sections_from_question[]' id="from_question_1" />
                                </td>
                                <td>
                                    <input type="number" class="form-control" name='exam_sections_to_question[]' id="to_question_1" />
                                </td>
                                <td>
                                </td>
                            </tr>

                        <?php
                        }
                        ?>

                    </tbody>

                </table>


                <p id="uploadError" class="text-danger"></p>
                <p id="uploadStatus" class="text-success"></p>

                <div class="row justify-content-end">
                    <button type="submit" style="width:150px;" class="btn btn-success submitBtn" name="upload_exam_paper" id="upload_exam_paper">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    var institute_id = "<?= $decrypted_institute_id; ?>";
    var test_id = "<?= $decrypt_test_id; ?>";
    var subjects_arr_str = "<?php foreach ($subject_details as $row) {
                                echo "<option value=" . $row['subject_id'] . ">" . $row['subject'] . "</option>";
                            } ?>";

    function add_exam_section_structure() {
        var lastid = $(".exam_section_structure_tr:last").attr("id");
        var split_id = lastid.split("_");
        var nextindex = Number(split_id[4]) + 1;
        var html = "<tr class='exam_section_structure_tr' id='exam_section_structure_tr_" + nextindex + "'>";
        html = html + "<td><input type='text' class='form-control exam_sections' placeholder='Section' name='exam_sections[]' id='exam_sections_" + nextindex + "'></td>";
        html = html + "<td> <select class='form-select' name='subject[]' id='exam_subjects_" + nextindex + "'><option></option>" + subjects_arr_str + "</select></td>";
        html = html + "<td><input type='number' class='form-control' name='exam_sections_weightage[]' id='exam_weightage_" + nextindex + "' min='0' value='1' /></td>";
        html = html + "<td><input type='number' class='form-control' name='exam_sections_negative_mark[]' id='exam_negative_mark_" + nextindex + "' /></td>";
        html = html + "<td><select class='form-select' name='exam_sections_question_type[]' id='exam_question_type_" + nextindex + "'><option value='SINGLE'>SINGLE</option><option value='MULTIPLE'>MULTIPLE</option><option value='MATCH'>MATCH</option><option value='NUMBER'>NUMBER</option><option value='DESCRIPTIVE'>Subjective Answer</option></select></td>";
        html = html + "<td><input type='number' class='form-control' name='exam_sections_from_question[]' id='from_question_" + nextindex + "' /></td>";
        html = html + "<td><input type='number' class='form-control' name='exam_sections_to_question[]' id='to_question_" + nextindex + "' /></td>";
        html = html + "<td><span onclick='remove_extra_structure_div(" + nextindex + ")' class='remove_ed_icon'><i class='fas fa-trash'></i></span></td>";
        html = html + "</tr>";
        $("#exam_section_structure_tbl").append(html);
    }

    function remove_extra_structure_div(remove_id) {
        $("#exam_section_structure_tr_" + remove_id).remove();
    }
</script>




<script>
    $("#add_test_pdf_paper").submit(function(evt) {

        var files = document.getElementById('pdf_file').files[0];
        if (files == null || files == undefined) {
            alert("Please select a file to upload!");
            return;
        }
        var fd = new FormData();
        fd.append("file", files);

        $(".submitBtn").attr("disabled", true);
        evt.preventDefault();

        var test_sections_configure = [];
        var outArray = $('.exam_sections').toArray();
        outArray.forEach(function(i, key) {
            key = key + 1;
            obj = {};
            var from_question_no = $("#from_question_" + key).val();
            obj['qn_id'] = from_question_no;
            var to_question_no = $("#to_question_" + key).val();
            obj['questionNumber'] = to_question_no;
            var section = $("#exam_sections_" + key).val();
            obj['section'] = section;
            var weightage = $("#exam_weightage_" + key).val();
            obj['weightage'] = weightage;
            var negativeMarks = $("#exam_negative_mark_" + key).val();
            obj['negativeMarks'] = negativeMarks;
            var type = $("#exam_question_type_" + key).val();
            obj['type'] = type;
            var subjectId = $("#exam_subjects_" + key).val();
            obj['subjectId'] = subjectId;
            test_sections_configure.push(obj);
        })

        console.log(test_sections_configure);

        var request = {
            requestType: 'OFFLINE_PDF_UPLOAD',
            test: {
                id: test_id,
                test: test_sections_configure
            }
        }

        fd.append("request", JSON.stringify(request));
        get_admin_token().then(function(result) {
            var resp = JSON.parse(result);
            if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {

                token = resp.data.admin_token;
                $.ajax({
                    url: rootAdmin + "uploadTestPdf",
                    beforeSend: function(request) {
                        request.setRequestHeader("AuthToken", token);
                    },
                    type: "POST",
                    data: fd,
                    success: function(msg) {
                        // console.log("Response", msg);
                        if (msg != null && msg.status != null && msg.status.statusCode == 200) {
                            Snackbar.show({
                                pos: 'top-center',
                                text: 'Test paper uploaded successfully'
                            });
                            window.location = base_url + "/tests";
                        } else {
                            $(".submitBtn").attr("disabled", false);
                            $("#uploadError").html("Some error occured in uploaded test paper");
                        }

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $(".submitBtn").attr("disabled", false);
                        ("#uploadError").html("Error in service call");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
        });
    });
</script>