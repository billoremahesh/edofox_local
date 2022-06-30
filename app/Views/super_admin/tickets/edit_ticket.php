<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tickets/ticket_timeline.css'); ?>" rel="stylesheet">

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

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-2 flex_header_div">
                        <div>
                            <label class="h5 text-gray-800"> Ticket Details </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                            <tr>
                                    <td> #Ticket </td>
                                    <td><?php echo $ticket_data['ticket_number']; ?></td>
                                </tr>
                                <tr>
                                    <td> Name </td>
                                    <td><?php echo $ticket_data['student_name']; ?></td>
                                </tr>
                                <tr>
                                    <td> Mobile </td>
                                    <td><?php echo $ticket_data['student_mobile_number']; ?></td>
                                </tr>
                                <tr>
                                    <td> Email </td>
                                    <td><?php echo $ticket_data['student_email']; ?></td>
                                </tr>
                                <tr>
                                    <td> Institute </td>
                                    <td><?php echo $ticket_data['institute_name']; ?></td>
                                </tr>
                                <tr>
                                    <td> Reason </td>
                                    <td><?php echo $ticket_data['reason_name']; ?></td>
                                </tr>
                                <?php if(!empty($ticket_data['test_name'])): ?>
                                    <tr>
                                        <td> Exam Name </td>
                                        <td><a style="cursor:pointer" onclick="openStudentResultPage(<?=$ticket_data['test_id']?>, <?=$ticket_data['student_id']?>)" > <?php echo $ticket_data['test_name']; ?></a></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td> Initial Comments </td>
                                    <td><?php echo $ticket_data['comment']; ?></td>
                                </tr>
                                <tr>
                                    <td> Status </td>
                                    <td><?php echo $ticket_data['status']; ?></td>
                                </tr>
                                <tr>
                                    <td> Priority </td>
                                    <td>
                                    <?php
                                    if(ucwords($ticket_data['priority'])=="Low")
                                    {
                                        $priority_class="class='round_box_cutom_sucess_bg'";
                                    }
                                    else if(ucwords($ticket_data['priority'])=="Medium")
                                    {
                                        $priority_class="class='round_box_cutom_medium_bg'";
                                    }
                                    else if(ucwords($ticket_data['priority'])=="High")
                                    {
                                        $priority_class="class='round_box_cutom_danger_bg'";
                                    }
                                    else
                                    {
                                        $priority_class="class='round_box_cutom_warning_bg'";
                                    }
                                    ?>
                                    <label <?php echo $priority_class;?>><?php echo $ticket_data['priority']; ?></label>
                                    </td>
                                </tr>

                                <?php if ($ticket_data['attachment'] != "") : ?>
                                    <tr>
                                        <td> <label for="file_attachment"> Attachment </label></td>
                                        <td> <a href="<?php echo BASE_URL.$ticket_data['attachment']; ?>" target="_blank" title="download"> <span class="fa fa-download"></span></a></td>
                                    </tr>

                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-2 flex_header_div">
                        <div>
                            <label class="h5 text-gray-800"> All Replies </h5>
                        </div>
                    </div>
                    <div class="card-body" style="padding-top: 0px;">
                        <!-- *********************************************************** -->
                        <?php if (!empty($ticket_replies_data)) : 
                            ?>

                            <section id="timeline">
                                <ul class="timeline">
                                    <?php
                                    foreach ($ticket_replies_data as $reply_data) {
                                    ?>
                                        <li class="event" data-date="<?php echo date('d M Y h:i a',strtotime($reply_data['created_at'])); ?>">
                                            <?php echo ($reply_data['student_name'] != '')? '<h6 class="text-danger font-weight-bold">'.$reply_data['student_name'].'</h6>' : '<h6 class="text-success font-weight-bold">'.$reply_data['username'].'</h6>'; ?>
                                            <p><?php echo $reply_data['comment']; ?></p>
                                            <div class="ticket-reply-date"><?php echo date('d M Y h:i a', strtotime($reply_data['created_at'])); ?></div>
                                            <?php if (!empty($reply_data['attachment'])) : ?> 
                                                <a href="<?php echo base_url($reply_data['attachment']); ?>" target="_blank" title="download"> <span class="fa fa-download"></span></a>
                                            <?php endif; ?>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </section>

                            <!-- *********************************************************** -->
                        <?php endif; ?>
                        <hr>
                        <div class="py-2">
                            <label class="h5">Add Replies</label><br>
                        </div>
                        <form class="update_ticket" method="POST" action="<?php base_url('Tickets/update_ticket_submit.php'); ?>" class="cmxform"  id="updateTicketForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="comment">Add Comments <span class="req_color">*</span></label></label>
                                        
                                        <div class="document-editor">
                                            <div class="document-editor__toolbar"></div>
                                            <div class="document-editor__editable-container">
                                                <div class="document-editor__editable">

                                                </div>
                                            </div>
                                        </div>
                                        <textarea style="visibility: hidden;" id="comment" name="comment"></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="attachment"> Upload Attachment </label>
                                        <input type="file" name="attachment" class="form-control form-control-user" id="attachment">
                                        <p style="color: #858796;padding-top:8px;"> * The file should be an image ( png, jpeg, jpg ) with max size 500KB. </p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label for="assign">Assign <span class="req_color">*</span></label></label>
                                    <select class="form-control" name="assign" id="assign" required = "required">
                                    <option value="">Select Staff</option>
                                    <?php
                                    if(!empty($staff_assign_data)){
                                        foreach($staff_assign_data as $staff_data){ ?>
                                    <option value="<?php echo $staff_data['id']; ?>"  <?php if ($staff_data['id'] == $ticket_data['staff_id']) : echo 'selected="selected"';
                                                                        endif; ?>><?php echo $staff_data['username']; ?></option>     
                                    <?php   }
                                    }
                                    ?>
                                    </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="req_color">*</span></label></label>
                                        <select class="form-control" name="status" id="status" required = "required">
                                            <option  value=""></option>
                                            <option value="Resolved" <?php if ($ticket_data['status'] == 'Resolved') : echo 'selected="selected"';
                                                                        endif; ?>>Resolved</option>
                                            <option value="In Process" <?php if ($ticket_data['status'] == 'In Process') : echo 'selected="selected"';
                                                                        endif; ?>>In Process</option>
                                            <option value="Pending" <?php if ($ticket_data['status'] == 'Pending') : echo 'selected="selected"';
                                                                    endif; ?>>Pending</option>
                                            <option value="Invalid" <?php if ($ticket_data['status'] == 'Invalid') : echo 'selected="selected"';
                                                                    endif;  ?>>Invalid</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority">Priority <span class="req_color">*</span></label></label>
                                        <select class="form-control" name="priority" id="priority" required = "required">
                                            <option value=""></option>
                                            <option value="Low" <?php if ($ticket_data['priority'] == 'Low') : echo 'selected="selected"';
                                                                endif; ?>>Low</option>
                                            <option value="Medium" <?php if ($ticket_data['priority'] == 'Medium') : echo 'selected="selected"';
                                                                    endif; ?>>Medium</option>
                                            <option value="High" <?php if ($ticket_data['priority'] == 'High') : echo 'selected="selected"';
                                                                    endif; ?>>High</option>
                                            <option value="Critical" <?php if ($ticket_data['priority'] == 'Critical') : echo 'selected="selected"';
                                                                        endif;  ?>>Critical</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="ticket_id" value="<?php echo $ticket_data['id']; ?>" />
                            </div>
                            <br/>
                            <div class="text-right">
                                <button type="submit" name="update_ticket_submit" id="update_ticket_submit" class="btn btn-primary text-white">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/jquery.validate.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/jquery.validate.min.js'); ?>"></script>

<script>
	DecoupledEditor
		.create(document.querySelector('.document-editor__editable'), {})
				.then(editor => {
					const toolbarContainer = document.querySelector('.document-editor__toolbar');
					toolbarContainer.appendChild(editor.ui.view.toolbar.element);
					window.editor = editor;
				})
				.catch(err => {
					console.error(err);
				});


			// Assuming there is a <button id="submit">Submit</button> in your application.
			document.querySelector('#update_ticket_submit').addEventListener('click', () => {
				const editorData = editor.getData();
				document.getElementById("comment").value = editorData;
			});
</script>



<script>
    
    $(function() {
        $("#updateTicketForm").validate({
            rules:{
                comment: "required",
                assign : "required",
                status : "required",
                priority : "required"
            },
            messages: {
                comment: "Please enter comment",
                assign : "Please assign ticket to staff",
                status : "Please select status",
                priority : "Please select ticket priority"
            },
            submitHandler: function(form) {
                $('#update_ticket_submit').prop('disabled', true);
                form.submit();
            }
        });
    });
</script>

<script>
    $('#attachment').change(function(){
        var filesize = this.files[0].size;
        filesize = filesize/1024;
        filesize = Math.round(filesize);
        if(filesize > 500){
            $('#upload_error').css('font-weight','bold');
            $('#upload_error').css('color','#FF6666');
            $('#upload_error').css('font-size','12px');
            $('#upload_error').html('Please, Upload File Size maximun 500kb');
        }else{
            $('#upload_error').html('');
        }
    }); 

    function openStudentResultPage(testId, studentId) {
        localStorage.studentId = studentId;
        localStorage.testId = testId;
        var pathArray = window.location.pathname.split( '/' );
        var base_url = "";
        for(var i=0; i < pathArray.length; i++) {
            if(pathArray[i].indexOf("super_admin") >= 0) {
                break;
            }
            base_url = base_url + pathArray[i];
            console.log("URL is " + base_url);
        }
        window.open(window.location.origin + "/" + base_url + '/result.html', '_blank').focus();
    }
</script>