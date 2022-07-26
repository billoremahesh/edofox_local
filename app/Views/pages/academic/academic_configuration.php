<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<style>
    .crud-btn {
        width: 22px !important;
        height: 22px;
        background-color: rgb(11 69 233);
        color: #fff;
        border: 0px;
        border-radius: 50%;
        margin-right: 7px;
        font-size: 11px !important;
        text-align: center !important;
        line-height: 23px !important;
        display: flex;
    }

    .crud-btn:hover {
        background-color: rgb(107, 126, 197);
        color: #fff;

    }

    .ms-options-wrap button {
        overflow: hidden;
    }
</style>
<link href="<?php echo base_url('assets/css/classrooms/overview.css?v=20220331'); ?>" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="<?php echo base_url('assets/css/schedule/overview.css?v=20220609'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/academic'); ?>"> Academic </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="bg-white rounded shadow p-4" style="width: 1200px; margin: 0 auto;">

            <div class="text-center" style="background: #ddd2c8e0;"><b style="font-size: 20px;"> <?php echo strtoupper($syllabus_details['syllabus_name']); ?> ( <?php echo strtoupper($syllabus_details['subject']); ?> )</b></div>
            <div id="custom_loader"></div>

            <div class="d-flex justify-content-between my-1">

                <div class="col-md-6" style="padding-right:15px;" >
                    <div class="row" style="height: 500px;overflow: scroll;" >
                        <div class="col-md-12">

                            <nav class="nav">
                                <ul class="metisFolder metismenu">
                                    <li id="main-menu">

                                    </li>
                                </ul>
                            </nav>
                        </div>


                    </div>


                </div>
                <div class="col-md-6">
                <div class="row" style="height: 500px;overflow: scroll;" >
                    <div class="col-md-12">
                        <label class="form_label" for="classroom">Classroom Name</label>
                        <select name="classroom" id="classroom"  class="form-control">
                            <option value="">Select Classroom</option>
                            <?php foreach ($syllabus_classes as $value) {   ?>
                                <option value="<?= $value['id'] ?>"><?= $value['package_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form_label" for="staff_name">Staff Name</label>
                        <select name="staff_name" id="staff_name" class="form-control">
                            <option value="">Select Staff</option>
                            <?php foreach ($syllabus_classes as $value) {   ?>
                                <option value="<?= $value['id'] ?>"><?= $value['package_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="row">

                        <div class="col-md-6">
                            <label class="form_label" for="from_date">From Date</label>
                            <input type="text" class="form-control" id="schedule_start_date" placeholder="Start Date"   />
                        </div>
                        <div class="col-md-6">
                            <label class="form_label" for="to_date">To Date</label>
                            <input type="text" class="form-control" id="schedule_end_date" placeholder="End Date"   />

                        </div>
                    </div>

                    <div class="row">
                        <div id="schedule_cards_div">
                            <div class="kanban">
                                <div class="kanban-container" style="padding-left: 0px;" >
                                    <div class="kanban-column">
                                        <div class="kanban-column-header">Tuesday, December 20</div>
                                        <div class="kanban-column">
                                            <ul class="kanban-column-list">
                                                <li class="schedule_card position-relative">
                                                    <div class="badge subject_badge">BIOLOGY</div>
                                                    <div class="card_head">
                                                        <div class="card_title" data-bs-toggle="tooltip" data-bs-placement="top" title="test schedule">TEST SCHEDULE</div>
                                                    </div>
                                                    <div class="card_body">
                                                        <div class="card_supporting_text">Classroom: ABCDXYZ</div>
                                                        <div class="card_supporting_text">9:00 AM-9:30 AM</div>
                                                        <div class="card_supporting_text">Duration: 30 minutes, </div>
                                                        <div class="card_supporting_text">Session Frequency:Date</div>
                                                        <div class="d-flex justify-content-between my-2"><span class="material-icons schedule_btns schedule_edit_btn" onclick="show_edit_modal('modal_div','update_schedule_modal','/schedule/update_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">edit</span><span class="material-icons schedule_btns schedule_delete_btn  mx-2" onclick="show_edit_modal('modal_div','delete_schedule_modal','/schedule/delete_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">delete</span></div>
                                                    </div>
                                                </li>
                                                <li class="schedule_card position-relative">
                                                    <div class="badge subject_badge">BIOLOGY</div>
                                                    <div class="card_head">
                                                        <div class="card_title" data-bs-toggle="tooltip" data-bs-placement="top" title="test schedule">TEST SCHEDULE</div>
                                                    </div>
                                                    <div class="card_body">
                                                        <div class="card_supporting_text">Classroom: ABCDXYZ</div>
                                                        <div class="card_supporting_text">9:00 AM-9:30 AM</div>
                                                        <div class="card_supporting_text">Duration: 30 minutes, </div>
                                                        <div class="card_supporting_text">Session Frequency:Date</div>
                                                        <div class="d-flex justify-content-between my-2"><span class="material-icons schedule_btns schedule_edit_btn" onclick="show_edit_modal('modal_div','update_schedule_modal','/schedule/update_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">edit</span><span class="material-icons schedule_btns schedule_delete_btn  mx-2" onclick="show_edit_modal('modal_div','delete_schedule_modal','/schedule/delete_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">delete</span></div>
                                                    </div>
                                                </li>
                                            
                                             
                                                <li>
                                                    <div><button class="btn btn-outline-primary" onclick="show_edit_modal('modal_div','add_schedule_modal','/schedule/add_schedule_modal/7213/2022-12-20');"> Add Schedule</button></div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="kanban-column">
                                        <div class="kanban-column-header">Tuesday, December 20</div>
                                        <div class="kanban-column">
                                            <ul class="kanban-column-list">
                                                <li class="schedule_card position-relative">
                                                    <div class="badge subject_badge">BIOLOGY</div>
                                                    <div class="card_head">
                                                        <div class="card_title" data-bs-toggle="tooltip" data-bs-placement="top" title="test schedule">TEST SCHEDULE</div>
                                                    </div>
                                                    <div class="card_body">
                                                        <div class="card_supporting_text">Classroom: ABCDXYZ</div>
                                                        <div class="card_supporting_text">9:00 AM-9:30 AM</div>
                                                        <div class="card_supporting_text">Duration: 30 minutes, </div>
                                                        <div class="card_supporting_text">Session Frequency:Date</div>
                                                        <div class="d-flex justify-content-between my-2"><span class="material-icons schedule_btns schedule_edit_btn" onclick="show_edit_modal('modal_div','update_schedule_modal','/schedule/update_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">edit</span><span class="material-icons schedule_btns schedule_delete_btn  mx-2" onclick="show_edit_modal('modal_div','delete_schedule_modal','/schedule/delete_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">delete</span></div>
                                                    </div>
                                                </li>

                                                <li class="schedule_card position-relative">
                                                    <div class="badge subject_badge">BIOLOGY</div>
                                                    <div class="card_head">
                                                        <div class="card_title" data-bs-toggle="tooltip" data-bs-placement="top" title="test schedule">TEST SCHEDULE</div>
                                                    </div>
                                                    <div class="card_body">
                                                        <div class="card_supporting_text">Classroom: ABCDXYZ</div>
                                                        <div class="card_supporting_text">9:00 AM-9:30 AM</div>
                                                        <div class="card_supporting_text">Duration: 30 minutes, </div>
                                                        <div class="card_supporting_text">Session Frequency:Date</div>
                                                        <div class="d-flex justify-content-between my-2"><span class="material-icons schedule_btns schedule_edit_btn" onclick="show_edit_modal('modal_div','update_schedule_modal','/schedule/update_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">edit</span><span class="material-icons schedule_btns schedule_delete_btn  mx-2" onclick="show_edit_modal('modal_div','delete_schedule_modal','/schedule/delete_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">delete</span></div>
                                                    </div>
                                                </li>
                                                <li class="schedule_card position-relative">
                                                    <div class="badge subject_badge">BIOLOGY</div>
                                                    <div class="card_head">
                                                        <div class="card_title" data-bs-toggle="tooltip" data-bs-placement="top" title="test schedule">TEST SCHEDULE</div>
                                                    </div>
                                                    <div class="card_body">
                                                        <div class="card_supporting_text">Classroom: ABCDXYZ</div>
                                                        <div class="card_supporting_text">9:00 AM-9:30 AM</div>
                                                        <div class="card_supporting_text">Duration: 30 minutes, </div>
                                                        <div class="card_supporting_text">Session Frequency:Date</div>
                                                        <div class="d-flex justify-content-between my-2"><span class="material-icons schedule_btns schedule_edit_btn" onclick="show_edit_modal('modal_div','update_schedule_modal','/schedule/update_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">edit</span><span class="material-icons schedule_btns schedule_delete_btn  mx-2" onclick="show_edit_modal('modal_div','delete_schedule_modal','/schedule/delete_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">delete</span></div>
                                                    </div>
                                                </li>
                                             
                                                <li>
                                                    <div><button class="btn btn-outline-primary" onclick="show_edit_modal('modal_div','add_schedule_modal','/schedule/add_schedule_modal/7213/2022-12-20');"> Add Schedule</button></div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="kanban-column">
                                        <div class="kanban-column-header">Tuesday, December 20</div>
                                        <div class="kanban-column">
                                            <ul class="kanban-column-list">
                                                <li class="schedule_card position-relative">
                                                    <div class="badge subject_badge">BIOLOGY</div>
                                                    <div class="card_head">
                                                        <div class="card_title" data-bs-toggle="tooltip" data-bs-placement="top" title="test schedule">TEST SCHEDULE</div>
                                                    </div>
                                                    <div class="card_body">
                                                        <div class="card_supporting_text">Classroom: ABCDXYZ</div>
                                                        <div class="card_supporting_text">9:00 AM-9:30 AM</div>
                                                        <div class="card_supporting_text">Duration: 30 minutes, </div>
                                                        <div class="card_supporting_text">Session Frequency:Date</div>
                                                        <div class="d-flex justify-content-between my-2"><span class="material-icons schedule_btns schedule_edit_btn" onclick="show_edit_modal('modal_div','update_schedule_modal','/schedule/update_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">edit</span><span class="material-icons schedule_btns schedule_delete_btn  mx-2" onclick="show_edit_modal('modal_div','delete_schedule_modal','/schedule/delete_schedule_modal/1561d25459bc5fdd1572ce8c80b214d23ad5db6661fb6434d30bfc0b436250e19d70a10a4af319320f75c031a383376d740ae5512f558ed5a20a80c5aba0bd2e3f4b60c6f7773ccc53aad213ee61d549c6d8b0');">delete</span></div>
                                                    </div>
                                                </li>
                                             
                                                <li>
                                                    <div><button class="btn btn-outline-primary" onclick="show_edit_modal('modal_div','add_schedule_modal','/schedule/add_schedule_modal/7213/2022-12-20');"> Add Schedule</button></div>
                                                </li>
                                            </ul>
                                        </div>
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
                $(document).ready(function() {
                    get_syllabus_details('default');
                });

                function get_syllabus_details(type) {
                    if (type == 'default') {
                        toggle_custom_loader(true, "custom_loader");
                    }
                    jQuery.ajax({
                        url: base_url + '/syllabus/get_syllabus_details',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            subject_id: <?= $syllabus_details['subject_id'] ?>,
                            syllabus_id: <?= $syllabus_details['id'] ?>
                        },
                        success: function(result) {
                            $("#main-menu").html("");
                            var syllabus_topic = get_chepter_list1(result);
                            $("#main-menu").append(syllabus_topic);
                            toggle_custom_loader(false, "custom_loader");
                        }
                    });
                }

                function get_chepter_list1(result) {
                    html = "";
                    result.parent.forEach((element) => {
                        html = html + `    <a href="#">
                                            <span class="fa fa-fw fa-folder"></span>
                                            ` + element.topic_name + ` 
                                        </a>
                                        <ul> ` + add_child_chapter1(result, element.id) + `
                                           
                                         </ul>  `;
                    });

                    return html;
                }

                function add_child_chapter1(result, parent_id) {
                    html = "";
                    if (result.child[parent_id] != undefined) {
                        child_arr = result.child[parent_id];
                        child_arr.forEach((element) => {
                            html = html + `<li>
                                <a href="#" aria-expanded="true">
                                    <span class="fa fa-fw fa-folder"></span>
                                         ` + element.topic_name + `
                                    
                                </a> <ul> ` + add_child_chapter2(result, element.id) + `
                                           
                                           </ul>  
                            </li> `;
                        });
                    }
                    return html;
                }


                function add_child_chapter2(result, parent_id) {
                    html = "";
                    if (result.child[parent_id] != undefined) {
                        child_arr = result.child[parent_id];
                        child_arr.forEach((element) => {
                            html = html + `<li>
                                <a href="#" aria-expanded="true">
                                    <span class="fa fa-fw fa-folder"></span>
                                         ` + element.topic_name + `
                                      </a> <ul> ` + add_child_chapter3(result, element.id) + `
                                           
                                           </ul>  
                            </li> `;
                        });
                    }
                    return html;
                }

                function add_child_chapter3(result, parent_id) {
                    html = "";
                    if (result.child[parent_id] != undefined) {
                        child_arr = result.child[parent_id];
                        child_arr.forEach((element) => {
                            html = html + `<li>
                                <a href="#" aria-expanded="true">
                                    <span class="fa fa-fw fa-folder"></span>
                                         ` + element.topic_name + `</a> <ul> ` + add_child_chapter4(result, element.id) + `
                                           
                                           </ul>  
                            </li> `;
                        });
                    }
                    return html;
                }

                function add_child_chapter4(result, parent_id) {
                    html = "";
                    if (result.child[parent_id] != undefined) {
                        child_arr = result.child[parent_id];
                        child_arr.forEach((element) => {
                            html = html + `<li>
                                <a href="#" aria-expanded="true">
                                    <span class="fa fa-fw fa-folder"></span>
                                         ` + element.topic_name + `
                                      </a> <ul> ` + add_child_chapter3(result, element.id) + `
                                           
                                           </ul>  
                            </li> `;
                        });
                    }
                    return html;
                }

                function get_chepter_list(result) {
                    html = "";
                    result.forEach((element) => {

                        buttun = `<i class="fa fa-fw fa-plus crud-btn" onclick="add_topic_modal(` + element.id + `)" style="margin-left: 5px;" ></i> <i class="fa fa-fw fa-trash crud-btn"></i>`;
                        // html = html + `<li><a href="#"><span class="fa fa-fw fa-file" style="color:#28a5d5;" ></span><input type="checkbox" name="add_syllabus_topic" id="add_syllabus_topic` + element.id + `" class="add_syllabus_topic" onclick="addSyllabusTopic(` + element.id + `)" value=` + element.id + ` /> ` + element.chapter_name + `</a></li>`;
                        html = html + `<li><a href="#" aria-expanded="true" > <span class="fa fa-fw fa-folder"></span>` + element.topic_name + buttun + `</a></li>`;
                    });
                    return html;
                }




                function addSyllabusTopic(chapter_id) {
                    let is_disabled = 0;
                    let syllabus_id = "<?php echo $syllabus_details['id']; ?>";
                    if ($("#add_syllabus_topic" + chapter_id).is(':checked')) {
                        is_disabled = 0;
                    } else {
                        is_disabled = 1;
                    }

                    jQuery.ajax({
                        url: base_url + '/syllabus/update_syllabus_topic',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            subject_id: <?= $syllabus_details['subject_id'] ?>,
                            syllabus_id: syllabus_id,
                            is_disabled: is_disabled,
                            chapter_id: chapter_id,
                        },
                        success: function(result) {
                            var attendance_data = get_chepter_list(result);
                            $("#chapater").html(attendance_data);
                            toggle_custom_loader(false, "custom_loader");
                        }
                    });
                }

                function add_child_topic(parent_id) {
                    show_add_modal('modal_div', 'add_syllabus_modal', 'syllabus/add_child_syllabus_configuration_modal/' + parent_id);

                }

                function delete_child_topic(topic_id, name, type, chapter_id, institute_id) {
                    console.log(institute_id, 'institute_id');
                    if (type == 'single') {
                        msg = `<p> Are you sure, you want to delete this <b >` + name + `</b> Topic?</p>`;
                    } else {
                        msg = `<p> Are you sure, you want to delete this <b >` + name + `</b> Syllabus of All Topic?</p>`;
                    }

                    if (institute_id != null) {
                        $("#delete_checkbox_div").show();
                    } else {
                        $("#delete_checkbox_div").hide();
                    }

                    $('#delete_checkbox').prop('checked', false); // Checks it
                    $("#topic_id").val(topic_id);
                    $("#chapter_id").val(chapter_id);
                    $("#topic_type").val(type);
                    $("#confirmation_msg").html(msg);
                    $('#delete_syllabus_topics_modal').modal('show');
                }

                function delete_topics() {
                    $('#delete_syllabus_topics_modal').modal('hide');
                    toggle_custom_loader(true, "custom_loader");
                    let topic_id = $("#topic_id").val();
                    let topic_type = $("#topic_type").val();
                    let isChecked = $('#delete_checkbox').prop('checked');
                    let chapter_id = $("#chapter_id").val();
                    jQuery.ajax({
                        url: base_url + '/syllabus/delete_topics',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            topic_id: topic_id,
                            topic_type: topic_type,
                            isChecked: isChecked,
                            chapter_id: chapter_id
                        },
                        success: function(result) {
                            if (topic_type == 'single') {
                                msg = `Topic deleted Successfully.`;
                            } else {
                                msg = `All Topic deleted Successfully.`;
                            }
                            Snackbar.show({
                                pos: 'top-center',
                                text: msg
                            });
                            get_syllabus_details('custom');

                        }
                    });

                }

                function add_topic_modal(parent_id) {

                    $("#chapter option:selected").removeAttr("selected");
                    $("#new_topic_name").val("");
                    $('#topic_data').trigger("reset");
                    $(".ms-options-wrap button").html("-Select Chapter-");
                    $(".ms-search").val();
                    // $("#chapter").val(""); 
                    $("#parent_topic_id").val(parent_id);
                    $('#add_topics_modal').modal('show');
                }


                function add_topic_data() {

                    let chapter = $("#chapter").val();
                    let difficulty = $("#difficulty").val();
                    let importance = $("#importance").val();
                    let parent_topic_id = $("#parent_topic_id").val();
                    let syllabus_id = $("#syllabus_id").val();
                    let new_topic_name = $("#new_topic_name").val();
                    let isChecked = $('#checkbox').prop('checked');
                    let subject_id = $("#subject_id").val();
                    if ((difficulty == '' && importance == '') || (chapter.length == 0 && $.trim(new_topic_name) == '')) {
                        return false;
                    }

                    $('#add_topics_modal').modal('hide');
                    toggle_custom_loader(true, "custom_loader");

                    jQuery.ajax({
                        url: base_url + '/syllabus/add_topics',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            importance: importance,
                            chapter: chapter,
                            difficulty: difficulty,
                            parent_topic_id: parent_topic_id,
                            syllabus_id: syllabus_id,
                            new_topic_name: new_topic_name,
                            checkbox: isChecked,
                            subject_id: subject_id
                        },
                        success: function(result) {
                            $("#checkbox_div").hide();
                            msg = `Topic Added Successfully.`;
                            Snackbar.show({
                                pos: 'top-center',
                                text: msg
                            });
                            get_syllabus_details('custom');
                        }
                    });

                }

                $('#chapter').multiselect({
                    columns: 1,
                    placeholder: 'Select Chapter',
                    search: true,
                    selectAll: true
                });


                $(".ms-options").click(function(e) {
                    $("#checkbox_div").hide();
                });

                $(".ms-search").change(function(e) {

                    $("#checkbox_div").show();
                    $("#new_topic_name").val(e.target.value);
                    $(".ms-options-wrap button").html(e.target.value);
                    $(".ms-options").hide();
                    // add_topic_data();

                });




                $(document).ready(function() {

                    var classroom_filter_val = "";
                    var classroom_filter_text = "";

                    $("#schedule_start_date").flatpickr({
                        dateFormat: "Y-m-d",
                        onChange: function(selectedDates) {
                            $("#schedule_end_date").flatpickr({
                                dateFormat: "Y-m-d",
                                minDate: new Date(selectedDates),
                                maxDate: new Date(selectedDates).fp_incr(6), // add 7 days
                            });
                        }
                    });


                });


 

                $("#classroom").change(function(e) {
                    toggle_custom_loader(true, "custom_loader");
                    console.log(e.target.value,'event');
                    jQuery.ajax({
                        url: base_url + '/academic/get_classroom_staff',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            classroom_id:e.target.value,
                        },
                        success: function(result) {  
                           $("#staff_name").html(make_staff_list(result));
                            toggle_custom_loader(false, "custom_loader");
                        }
                    });
                });

                function make_staff_list(result){

                    html = "<option value=`` >Select Staff</option>";
                    result.forEach((element) => {
                        console.log(element,'element');

                         html = html + `<option value=`+element.admin_id+` >`+element.name+`</option>`;
                    }); 
                    return html;

                }

            </script>