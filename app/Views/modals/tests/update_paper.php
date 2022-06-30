    <!-- Upload paper PDF modal  -->
    <div id="uploadPaperPdfModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                    <h6 class="modal-title">Upload Paper PDF</h6>
                </div>
                <form action="sql_operations/upload_test_paper_file_submit.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body" id="uploadPaperPdfModalBody">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="upload_test_paper_file_submit">Upload File</button>
                    </div>
                </form>
            </div>

        </div>
    </div>