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
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/testTemplates'); ?>"> Test Templates </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card p-4">
            <table class="table table-bordered table-condensed" id="templates_rules_tble">
                <thead>
                    <tr>
                        <th> Rule Name </th>
                        <th> Value </th>
                        <th> From Question </th>
                        <th> To Question </th>
                        <th> Actions </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($templates_rules)) {
                        foreach ($templates_rules as $template_rule) {
                            $enc_template_id = encrypt_string($template_rule['template_id']);
                            $enc_template_rule_id = encrypt_string($template_rule['id']);
                    ?>
                            <tr>
                                <td> <?= $template_rule['display_name']; ?></td>
                                <td> <?= $template_rule['value']; ?> </td>
                                <td> <?= $template_rule['from_question']; ?> </td>
                                <td> <?= $template_rule['to_question']; ?> </td>
                                <td>
                                    <button class='btn btn-sm' data-bs-toggle="tooltip" title="Update Template Details" onclick="show_edit_modal('modal_div','update_template_rule_modal','/testTemplates/update_template_rule/<?= $enc_template_rule_id; ?>/<?= $enc_template_id; ?>');"><i class='material-icons material-icon-small text-primary'>edit</i></button>

                                    <button class='btn btn-sm' data-bs-toggle="tooltip" title="Delete Template" onclick="show_edit_modal('modal_div','delete_template_rule_modal','/testTemplates/disable_template_rule/<?= $enc_template_rule_id; ?>/<?= $enc_template_id; ?>');"><i class='material-icons material-icon-small text-primary'>delete</i></button>
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
        $('#templates_rules_tble').DataTable({
            pageLength: 50,
            "lengthChange": false
        });
    });
</script>