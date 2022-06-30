<div class="modal fade" id="edit_invoice_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('Invoices/update_invoice_details_submit'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6 mb-2">
                        <label class="form-label" for="due_date">Due date</label>
                        <input type="text" class="form-control form_datetime" id="due_date" name="due_date" value="<?= $invoice_deatils['due_date']; ?>" autocomplete="off" required>
                    </div>


                    <div class="col-6 mb-2">
                        <label class="form-label" for="invoice_status"> Invoice Status </label>
                        <select class="form-select" name="invoice_status" id="invoice_status" required>
                            <option></option>
                            <option value="Paid" <?php if($invoice_deatils['status'] == 'Paid'){ echo "selected"; } ?> > Paid </option>
                            <option value="Pending" <?php if($invoice_deatils['status'] == 'Pending'){ echo "selected"; } ?> > Pending </option>
                            <option value="OnHold" <?php if($invoice_deatils['status'] == 'OnHold'){ echo "selected"; } ?> > OnHold </option>
                            <option value="Rejected" <?php if($invoice_deatils['status'] == 'Rejected'){ echo "selected"; } ?> > Rejected </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="invoice_id" name="invoice_id" value="<?= $invoice_id; ?>" required />
                <input type="hidden" id="redirect" name="redirect" value="/invoices" required />
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success"> Update </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<script>
    var date = new Date();
    var current_year = date.getFullYear();
    var current_month = ("0" + (date.getMonth() + 1)).slice(-2);
    var month_max_range = ("0" + (date.getMonth() + 1)).slice(-2) + 3;
    var current_day = ("0" + date.getDate()).slice(-2);
    var today = current_year + "-" + current_month + "-" + current_day;
    var end_date = current_year + "-" + month_max_range + "-" + current_day;
    $(".form_datetime").datetimepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayBtn: true,
        fontAwesome: "font-awesome",
        pickerPosition: "bottom",
        pickTime: false,
        minView: 2,
    });
</script>