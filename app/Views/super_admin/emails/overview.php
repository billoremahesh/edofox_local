<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<style>
    .social-media-links {
        padding: 8px;
    }

    .social-media-icons {
        width: 24px;
    }
</style>
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
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow m-2">

                <div class="card-body edit_institute cmxform">
                    <div class="row display-flex">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="institute_name" class="form-label"> Email Type <span class="req_color">*</span></label>
                                <select id="mail_type" class="form-control" onchange="emailTypeChanged()">
                                    <option value="Registered">Registered Institutes</option>
                                    <option value="Custom">Custom</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="institute_id" class="form-label">Insitute Name <span class="req_color">*</span></label>
                                <select class="form-control optimized_institute_dropdown" name="institute_id" id="institute_id">
                                    <option> Select </option>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="new_package_id" class="form-label">Select Classroom: </label>
                                <select class="form-control" name="new_package_id" id="new_package_id" required>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="contact" class="form-label">Email Subject <span class="req_color">*</span></label>
                                <input type="text" name="subject" class="form-control form-control-user" id="subject" placeholder="Email Subject">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="contact" class="form-label">Email Title <span class="req_color">*</span></label>
                                <input type="text" name="title" class="form-control form-control-user" id="title" placeholder="Email Title" onblur="titleChanged()">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="contact" class="form-label">Button Title <span class="req_color">*</span></label>
                                <input type="text" name="btnTitle" class="form-control form-control-user" id="btnTitle" placeholder="Button title" onblur="buttonChanged()">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="contact" class="form-label">Button URL <span class="req_color">*</span></label>
                                <input type="text" name="btnUrl" class="form-control form-control-user" id="btnUrl" placeholder="Button URL" onblur="buttonChanged()">
                            </div>
                        </div>
                    </div>

                    <div class="row display-flex" id="receipients_div">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="username" class="form-label"> Receipients <span class="req_color"></span></label>
                                <input type="text" name="receipients" class="form-control form-control-user" id="receipients" placeholder="Comma separated emails">
                            </div>
                        </div>
                    </div>

                    <div class="row display-flex" id="bcc_div">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="bcc_list" class="form-label"> BCC List </label>
                                <input type="text" name="bcc_list" class="form-control form-control-user" id="bcc_list" placeholder="Comma separated emails">
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row display-flex">
                        <div class="col-md-12">
                            <div class="mb-3">

                                <div class="document-editor">
                                    <div class="document-editor__toolbar"></div>
                                    <div class="document-editor__editable-container" style="border: 1px solid #B8B9C2;">
                                        <div class="document-editor__editable">
                                        </div>
                                    </div>
                                </div>

                                <textarea style="visibility: hidden;" id="email_content" name="email_content"></textarea>

                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="button" name="send_email" onclick="sendEmail()" class="btn btn-primary" id="send_email"> Send </button>
                    </div>

                </div>

            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow m-2">
                <div class="card-body">
                    <h2>Preview</h2>

                    <div style="text-align: center;margin-top: 32px">
                        <a href="https://edofox.com/" target="_blank"><img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/edofox-name-logo-black.png" style="height:50px; margin-top: 0px" /></a>
                    </div>


                    <hr style="border: 1px solid #eee;" />
                    <h3 style="text-align: center;" id="email_title_preview"></h3>

                    <div>

                        <div id="email_content_preview">
                            <!-- Content goes here -->

                        </div>

                        <!-- For bottom border after the final content -->
                        <hr style="border: 1px solid #eee;" />

                    </div>



                    <div style="display: flex; justify-content: center;">
                        <a href="#" target="_blank" style="text-transform: uppercase; margin: 16px auto; background-color: #eca100; color: white; font-weight: bold; padding: 8px; border-radius: 50px; border: 0px; outline: none; text-decoration: none; font-size: 14px;" id="button_title_preview"></a>
                    </div>


                    <hr style="border: 1px solid #eee;" />

                    <div style="display: flex; justify-content: center;">
                        <a class="social-media-links" href="https://www.facebook.com/edofoxonline" target="_blank"><img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/social-media/iconfinder_1217141_fb_icon_64px.png" class="social-media-icons" /></a>

                        <a class="social-media-links" href="https://www.instagram.com/edofox_official/" target="_blank"><img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/social-media/iconfinder_1217174_instagram_icon_64px.png" class="social-media-icons" /></a>

                        <a class="social-media-links" href="https://www.linkedin.com/company/edofox/" target="_blank"><img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/social-media/iconfinder_1217173_linkedin_icon_64px.png" class="social-media-icons" /></a>

                        <a class="social-media-links" href="https://wa.me/917350182285" target="_blank"><img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/social-media/iconfinder_1217136_whatsapp_icon_64px.png" class="social-media-icons" /></a>

                        <a class="social-media-links" href="mailto:support@edofox.com" target="_blank"><img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/social-media/iconfinder_1217177_gmail_icon_64px.png" class="social-media-icons" /></a>

                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script src="<?php echo base_url('assets/js/emails.js'); ?>"></script>