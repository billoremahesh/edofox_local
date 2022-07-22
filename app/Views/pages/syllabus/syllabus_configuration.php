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

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/syllabus'); ?>"> Syllabus </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="bg-white rounded shadow p-4" style="width: 900px; margin: 0 auto;">

            <div class="text-center" style="background: #ddd2c8e0;"><b style="font-size: 20px;"> <?php echo strtoupper($syllabus_details['syllabus_name']); ?> ( <?php echo strtoupper($syllabus_details['subject']); ?> )</b></div>
            <div id="custom_loader"></div>
            <div class="col-md-12">
                <div class="row">
                    <div style="margin-top:10px;">

                        <span class="inln">
                            <?php $syllabus_id = $syllabus_details['id']; ?>
                            <i class="fa fa-fw fa-plus crud-btn" onclick="add_topic_modal(null)"></i>
                        </span>
                        <!-- <span class="inln">
                                <?php $syllabus_id = $syllabus_details['id']; ?>
                                <a href="#" onclick="show_add_modal('modal_div','add_syllabus_modal','syllabus/update_syllabus_configuration_modal/<?= $syllabus_id ?>');" data-toggle='tooltip' title='Update topic in Syllabus' style="display: inherit;">
                                    <i class="fa fa-pencil crud-btn"></i>
                                </a>
                            </span> -->
                        <span class="inln">
                            <?php $syllabus_id = $syllabus_details['id']; ?>
                            <a href="#" data-toggle='tooltip' title='Delete Topic in Syllabus' style="display: inherit;">
                                <i class="fa fa-fw fa-trash crud-btn" onclick="delete_child_topic(<?= $syllabus_id ?>,'<?= $syllabus_details['syllabus_name'] ?>','all',<?= $syllabus_id ?>,null)"></i>
                            </a>
                        </span>

                        <!-- <span><i class="fa fa-fw fa-eye crud-btn"></i></span> -->
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between my-1">

                <div class="col-md-12">
                    <div class="row">
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
            </div>


        </div>
    </div>
</div>


<!-- delete poup -->
<div class="modal fade" id="delete_syllabus_topics_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> Delete Syllabus of topics </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="confirmation_msg"></span>

                <div class="col-md-12" id="delete_checkbox_div" style="display:none;">
                    <input type="checkbox" name="delete_checkbox" id="delete_checkbox" /> <label class="form_label" for="chapter" style="color:red;">Delete from <b>chapters database</b></label>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="topic_id" id="topic_id" />
                <input type="hidden" name="topic_type" id="topic_type" />
                <input type="hidden" name="chapter_id" id="chapter_id" />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger" onclick="delete_topics()"> Yes </button>
            </div>
        </div>
    </div>
</div>


<!-- Add Classroom Modal -->
<div id="add_topics_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" id="topic_data" name="topic_data" method="POST">
                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <div id="custom_loader"></div>
                    </div>
                    <div class="col-md-12">
                        <label class="form_label" for="subject_name">Syllabus Name</label>
                        <p><b><?= $syllabusDetails['syllabus_name'] ?></b></p>
                    </div>

                    <div class="col-md-12">
                        <label class="form_label" for="difficulty">Difficulty<span style="color:red;">*</span></label>
                        <select name="difficulty" id="difficulty" class="form-control" required>
                            <option value="">-Select Difficulty-</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form_label" for="importance">important<span style="color:red;">*</span></label>
                        <select name="importance" id="importance" class="form-control" required>
                            <option value="">-Select Importance-</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                            <option value="Very High">Very High</option>
                        </select>
                    </div>


                    <div class="col-md-12">
                        <label class="form_label" for="chapter">Chapters Name<span style="color:red;">*</span></label>
                        <select name="chapter[]" class="form-control" multiple id="chapter" style="broder:1px solid #ced4da;" required>
                            <?php
                            if (!empty($chapter_list)) {
                                foreach ($chapter_list as $row) {
                                    $chapter_id = $row['id'];
                                    $chapter_name = $row['topic_name'];
                                    if (in_array($chapter_id, $selected_chapter) != true) {
                            ?>
                                        <option value="<?= $chapter_id; ?>"> <?= $chapter_name; ?> </option>
                            <?php
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-12" id="checkbox_div" style="display:none;">
                        <input type="checkbox" name="checkbox" id="checkbox" value="yes" checked="checked" /> <label class="form_label" for="chapter" style="color:red;"> Add <b>New Chapter</b> to Chapters database</label>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="syllabus_id" id="syllabus_id" value="<?= $syllabus_details['id'] ?>" />
                    <input type="hidden" name="parent_topic_id" id="parent_topic_id" value="" />
                    <input type="hidden" name="new_topic_name" id="new_topic_name" value="" />
                    <input type="hidden" name="subject_id" id="subject_id" value="<?= $syllabus_details['subject_id'] ?>" />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="add_topic_data()" name="add_package_form_submit">Add</button>
                </div>
            </form>
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
                                            ` + element.topic_name + ` <i class="fa fa-fw fa-plus crud-btn" onclick="add_topic_modal(` + element.id + `)"  style="margin-left: 5px;"></i> <i class="fa fa-fw fa-trash crud-btn" onclick="delete_child_topic(` + element.id + `,'` + element.topic_name + `','single',` + element.chapter_id + `,` + element.institute_id + `)"></i>
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
                                    <i class="fa fa-fw fa-plus crud-btn" onclick="add_topic_modal(` + element.id + `)" style="margin-left: 5px;"></i> <i class="fa fa-fw fa-trash crud-btn" onclick="delete_child_topic(` + element.id + `,'` + element.topic_name + `','single',` + element.chapter_id + `,` + element.institute_id + `)" ></i>
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
                                    <i class="fa fa-fw fa-plus crud-btn" onclick="add_topic_modal(` + element.id + `)" style="margin-left: 5px;"></i> <i class="fa fa-fw fa-trash crud-btn" onclick="delete_child_topic(` + element.id + `,'` + element.topic_name + `','single',` + element.chapter_id + `,` + element.institute_id + `)"></i>
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
                                         ` + element.topic_name + `
                                    <i class="fa fa-fw fa-plus crud-btn" onclick="add_topic_modal(` + element.id + `)" style="margin-left: 5px;"></i> <i class="fa fa-fw fa-trash crud-btn" onclick="delete_child_topic(` + element.id + `,'` + element.topic_name + `','single',` + element.chapter_id + `,` + element.institute_id + `)"></i>
                                </a> <ul> ` + add_child_chapter4(result, element.id) + `
                                           
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
                                     <i class="fa fa-fw fa-trash crud-btn" onclick="delete_child_topic(` + element.id + `,'` + element.topic_name + `','single',` + element.chapter_id + `,` + element.institute_id + `)"></i>
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
</script>