<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/activity/overview.css?v=20211230'); ?>" rel="stylesheet">


<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/reports'); ?>"> Reports </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="p-2">

            <div class="d-flex flex-row mb-2">
                <div>

                    <?php
                    // Check Perms
                    if (!empty($perms) && in_array("all_perms", $perms)) {
                    ?>

                        <select class="form-select activityLogsTable_filters staff_list_select" id="staff_list">
                            <option value=""></option>
                            <?php
                            if (!empty($user_list)) :
                                foreach ($user_list as $staff) :
                            ?>
                                    <option value="<?= $staff['id'] ?>"><?= $staff['name'] ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    <?php
                    } else {
                    ?>
                        <input type="hidden" value="<?= $staff_id; ?>" id="staff_list" />
                    <?php
                    }
                    ?>
                </div>
            </div>


            <div class="table_custom" style="max-width: 800px;margin:auto;">
                <table class="table table-borderles w-100" id="activityLogsTable" style="width:100%;">
                    <thead class="d-none">
                        <tr>
                            <th>#</th>
                        </tr>
                    </thead>
                </table>
            </div>


        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script>
    var activityLogsTable;
    $(document).ready(function() {
        activityLogsTable = $('#activityLogsTable').DataTable({
            "processing": true,
            "stateSave": true,
            "serverSide": true,
            "pageLength": 10,
            "order": [0, "asc"],
            "ajax": {
                'url': base_url + "/activityLogs/activity_logs_data",
                "type": "POST",
                "data": function(d) {
                    d.instituteID = "<?= $instituteID; ?>",
                        d.staff_id = $('#staff_list').val()
                }
            },
            "stripeClasses": [],
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            "dataSrc": "Data"
        });

        // Moved Datatable Search box and Page length option
        // And other table settings
        $("#dataTables_search_box_div").append($(".dt-buttons"));
        $("#dataTables_search_box_div").append($("#activityLogsTable_length"));

        $("#activityLogsTable_length select").removeClass('form-select')
        $(".dt-buttons .buttons-colvis").removeClass('btn-secondary').addClass('btn-sm');

        $("#activityLogsTable_filter input").attr('placeholder', 'Search');
        $("#activityLogsTable_filter").addClass('py-2 px-1');


        // Onchange of custom filters
        $(".activityLogsTable_filters").change(function() {
            activityLogsTable.draw();
        });

    });


    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    $(document).ready(function() {
        waitForElementToDisplay("#activityLogsTable_filter", 1000, 1);
    });
</script>