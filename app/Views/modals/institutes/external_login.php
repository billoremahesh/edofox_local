<div id="external_link" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-content-start flex-wrap text-break" id="shareSignupLink">
                    <?= $external_url; ?>
                </div>
            </div>
            <p id="shareStatus"></p>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" name="copy_link" onclick="copyToClipboard()">Copy to clipboard</button>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard() {
        const str = document.getElementById('shareSignupLink').innerText;
        const el = document.createElement('textarea');
        el.value = str;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        $("#shareStatus").text("Copied to clipboard!");
    }
</script>