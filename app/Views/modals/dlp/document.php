<div id="dlp_document_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php
            $prefix =  "../";
            if (strpos($docUrl, "http") >= 0) {
                $prefix = "";
            }
            $docUrlDownload = $prefix . $docUrl;

            if ($prefix != "") {
                $docUrlPreview = "https://reliancedlp.edofox.com/" . $docUrl;
                $docUrlPreview = "https://test.edofox.com/" . $docUrl;
            } else {
                $docUrlPreview = $docUrl;
            }
            ?>
            <div class="modal-header">
                <h5 class="modal-title"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" id="updateResourceModalBody">
                <div class="row">
                    <div class="text-center" style="margin-bottom: 16px;"><a class="btn btn-default" href="<?= $docUrlDownload ?>" target="_blank">Download PDF</a></div>
                    <iframe class="pdf-viewer" src="https://docs.google.com/viewer?url=<?= rawurlencode($docUrlPreview) ?>&embedded=true" style="width: 100%; height: 600px;" frameborder="0" scrolling="no"></iframe>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>