<!-- Add Solution PDF Modal -->
<div id="uploadSolutionsPdfModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Upload here:</label>
                                <input type="file" accept="application/pdf" id="solutions_pdf_file" name="solutions_pdf_file" required onchange="validatePDFFile(this);" />
                            </div>
                        </div>
                    </div>
                    <p id="error" style="color:red"></p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="test_id" value="<?= $test_id; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Upload File</button>
                </div>
            </form>
        </div>

    </div>
</div>


<script>
    //To validate solution PDF in modal
    function validatePDFFile(file) {
        //Validating file type
        var ext = $(file).val().split('.').pop().toLowerCase();
        if ($.inArray(ext, ['pdf']) == -1) {
            alert('File type is invalid. Please upload PDF file only!');
            $(file).val("");
            return;
        }
    }

    //Solutions PDF upload through service
    $('#uploadSolutionsPdfModal').submit(function(evt) {
        evt.preventDefault();
        console.log("Submit prevented!");
        var testId = $("#test_id").val();
        var f = document.getElementById('solutions_pdf_file').files[0];

        var fd = new FormData();
        fd.append("file", f);
        var request = {
            test: {
                id: testId
            },
            requestType: 'SolutionPdf'
        }
        fd.append("request", JSON.stringify(request));
        // console.log("Type " + resourceType + " URL " + video_url);
        $("#error").text("Saving .. Please wait ..");
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
                            // Snakbar Message
                            Snackbar.show({
                                pos: 'top-center',
                                text: 'Solution PDF added successfully'
                            });

                            window.location.reload();
                        } else {
                            $("#error").text("Error in saving .. Please try again ..");
                        }

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#error").text("Error in uploading .. Please try again ..");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
        });
    });
</script>