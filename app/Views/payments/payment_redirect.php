<div style='margin-top:64px; text-align:center'>

    <img class="loading-div" src="<?= base_url('assets/img/loading.gif'); ?>" style="width:60px;" />
    <p>Loading... Please wait. Do not refresh this page or go back.</p>
</div>


<!-- included jquery for ajax call -->
<script src="<?php echo base_url('assets/jquery-3.6.0.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js'); ?>"></script>

<script src="<?php echo base_url('assets/js/url.js'); ?>"></script>


<script>
    // Checking whether the payment ID has been alloted by webhook process
    const interval = setInterval(function() {

        $.get(base_url + "/payments/ajax_check_payment_alloted", {
                action: "check"
            },
            function(data, status) {
                // alert("Data: " + data + "\nStatus: " + status);
                console.log(data);
                if (data == "SUCCESS") {
                    clearInterval(interval);
                    window.location = base_url + "/payments/response";
                }

            });

    }, 3000);
</script>