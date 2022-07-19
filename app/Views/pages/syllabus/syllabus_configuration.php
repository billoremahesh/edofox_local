<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<style>
    .crud-btn {
        width: 30px !important;
        height: 30px;
        background-color: rgb(11, 59, 233);
        color: #fff;
        border: 0px;
        border-radius: 50%;
        margin-right: 7px;
        text-align: center !important;
        line-height: 30px !important;
        display:flex
    }

    .crud-btn:hover {
        background-color: rgb(107, 126, 197);
        color: #fff;

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





        <div class="bg-white rounded shadow p-4" style="width: 500px; margin: 0 auto;">

            <div class="d-flex justify-content-between my-1">

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <nav class="nav">
                                <ul class="metisFolder metismenu">
                                    <li>
                                        <a href="#">
                                            <span class="fa fa-fw fa-folder"></span>
                                            <?php echo strtoupper($syllabus_details['syllabus_name']); ?>
                                        </a>
                                        <ul>

                                            <li>
                                                <a href="#">
                                                    <span class="fa fa-fw fa-folder"></span>
                                                    <?php echo strtoupper($syllabus_details['subject']); ?>
                                                </a>
                                                <div style="margin-left: 33px;">
                           
                                                    <span class="inln" >
                                                        <?php $syllabus_id=$syllabus_details['id']; ?>
                                                        <a href="#" onclick="show_add_modal('modal_div','add_syllabus_modal','syllabus/add_syllabus_configuration_modal/<?= $syllabus_id ?>');" data-toggle='tooltip' title='Add new  Topic in Syllabus' style="display: inherit;" >
                                                            <i class="fa fa-fw fa-plus crud-btn"></i>
                                                        </a>
                                                    </span>
                                                    <span class="inln" >
                                                        <?php $syllabus_id=$syllabus_details['id']; ?>
                                                        <a href="#" onclick="show_add_modal('modal_div','add_syllabus_modal','syllabus/update_syllabus_configuration_modal/<?= $syllabus_id ?>');" data-toggle='tooltip' title='Update topic in Syllabus' style="display: inherit;" >
                                                           <i class="fa fa-pencil crud-btn"></i>
                                                        </a>
                                                    </span>
                                                    <span class="inln" >
                                                        <?php $syllabus_id=$syllabus_details['id']; ?>
                                                        <a href="#" onclick="show_add_modal('modal_div','delete_syllabus_topic_modal','syllabus/delete_syllabus_configuration_modal/<?= $syllabus_id ?>');" data-toggle='tooltip' title='Delete Topic in Syllabus' style="display: inherit;" >
                                                        <i class="fa fa-fw fa-trash crud-btn"></i>
                                                        </a>
                                                    </span>
                                                  
                                                    <!-- <span><i class="fa fa-fw fa-eye crud-btn"></i></span> -->
                                                </div>
                                                <ul id="chapater">
                                                </ul>
                                            <li>
                                                <a href="<?php echo base_url('/syllabus'); ?>">
                                                    <span><i class="fa fa-fw fa-plus"> </i></span>  ADD SYLLABUS

                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </nav>
                        </div>


                    </div>


                </div>
            </div>
            <div id="custom_loader"></div>

        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    $(document).ready(function() {
        toggle_custom_loader(true, "custom_loader");
        jQuery.ajax({
            url: base_url + '/syllabus/get_syllabus_details',
            type: 'POST',
            dataType: 'json',
            data: {
                subject_id: <?= $syllabus_details['subject_id'] ?>,
                syllabus_id: <?= $syllabus_details['id'] ?>
            },
            success: function(result) {
                console.log(result,'result');
                var attendance_data = get_chepter_list(result);

                $("#chapater").html(attendance_data);
                toggle_custom_loader(false, "custom_loader");
            }
        });
    });

    function get_chepter_list(result) {
        console.log(result,'result'); 
        html = "";
        result.forEach((element) => {

            // html = html + `<li><a href="#"><span class="fa fa-fw fa-file" style="color:#28a5d5;" ></span><input type="checkbox" name="add_syllabus_topic" id="add_syllabus_topic` + element.id + `" class="add_syllabus_topic" onclick="addSyllabusTopic(` + element.id + `)" value=` + element.id + ` /> ` + element.chapter_name + `</a></li>`;
            html = html + `<li><a href="#"> &nbsp;&nbsp;&nbsp;` + element.topic_name + `</a></li>`;
        });
        return html;
    }




    $(".add_syllabus_topic").click(function() {
        console.log('hello');
    });

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
</script>