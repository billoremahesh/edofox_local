<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/classrooms/overview.css?v=20220331'); ?>" rel="stylesheet">


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





        <div class="bg-white rounded shadow p-4" style="width: 500px; margin: 0 auto;" >
        
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
                                                <ul id="chapater" >
                                                </ul>
                                            <li>
                                                <a href="#">
                                                    <span class="fa fa-fw fa-file-pdf"></span>
                                                    PDF
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
          status: status
        },
        success: function(result) {
          
          var attendance_data = get_chepter_list(result); 
          $("#chapater").html(attendance_data);
          toggle_custom_loader(false, "custom_loader"); 
        }
      }); 
     });

  function get_chepter_list(result){
    html ="";
    result.forEach((element) => {
        html = html +`<li><a href="#"><span class="fa fa-fw fa-file" style="color:#28a5d5;" ></span><input type="checkbox" id="" /> `+element.chapter_name+`</a></li>`;
    });
    return html;
      
  }
</script>