<!-- Generate and display the QR Code -->
<div style="text-align: center;">
    <img src="<?= $oQRC->get(500); ?>" alt="QR Code" style="display:block;margin:auto;" />
    <h2><?= $institute_details['institute_name']; ?></h2>
    <p><b>Scan QR Code - Link where Student can login</b></p>
</div>