<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/tests_templates.css?v=20220219'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card p-4">
            <table class="table table-bordered table-condensed" id="test_templates_tble">
                <thead>
                    <tr>
                        <th> Template Name </th>
                        <th> Created Date </th>
                        <th> Actions </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($test_templates)) {
                        foreach ($test_templates as $template_data) {
                            $enc_template_id = encrypt_string($template_data['id']);
                    ?>
                            <tr>
                                <td> <?= $template_data['template_name']; ?></td>
                                <td> <?= $template_data['created_date']; ?></td>
                                <td>
                                    <a class='btn btn-sm' data-bs-toggle="tooltip" title="Template Rules" href="<?= base_url("/testTemplates/test_template_rules/".$enc_template_id); ?>"><i class='material-icons material-icon-small text-primary'>visibility</i></a>

                                    <button class='btn btn-sm' data-bs-toggle="tooltip" title="Update Template Details" onclick="show_edit_modal('modal_div','update_template_modal','/testTemplates/update_template/<?= $enc_template_id; ?>');"><i class='material-icons material-icon-small text-primary'>edit</i></button>

                                    <button class='btn btn-sm' data-bs-toggle="tooltip" title="Delete Template" onclick="show_edit_modal('modal_div','delete_template_modal','/testTemplates/disable_template/<?= $enc_template_id; ?>');"><i class='material-icons material-icon-small text-primary'>delete</i></button>

                                </td>
                            </tr>
                    <?php

                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script>
    $(document).ready(function() {
        $('#test_templates_tble').DataTable({
            stateSave: true
        });
    });
</script>