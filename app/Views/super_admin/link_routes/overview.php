<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/institutes.css?v=20210829'); ?>" rel="stylesheet">

<div class="container-fluid mt-4">


    <div class="flex-container-column">
        <div>
            <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
        </div>
        <div class="breadcrumb_div" aria-label="breadcrumb">
            <ol class="breadcrumb_custom">
                <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
            </ol>
        </div>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="py-2 d-flex flex-row-reverse">

            <!-- Moved Datatable Search box -->
            <div id="dataTables_search_box_div"></div>

            <!-- Moved Datatable Page Length Menu -->
            <div style="margin-left: 16px;" id="dataTables_length_div"></div>

            <div>
                <button data-bs-toggle='tooltip' title='Add New Route' class='action_button_plus_custom custom_btn ripple-effect' onclick="show_edit_modal('modal_div','add_route_modal','/linkRoutes/add_new_route');">
                    <i class='action_button_plus_icon material-icons'>add</i>
                </button>

            </div>

        </div>

        <div class="card-body">

            <div>

                <table id='link_route_table' class='table table-bordered table-hover table-sm' cellspacing='0' width='100%'>
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Route Name </th>
                            <th> Route </th>
                            <th> Shortcut Key </th>
                            <th> Tags </th>
                            <th> No. of Hits </th>
                            <th> Created Date </th>
                            <th> Actions </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($link_routes_data)) :
                            $i = 1;
                            foreach ($link_routes_data as $route_data) :
                                $route_id = encrypt_string($route_data["id"]);
                        ?>
                                <tr>
                                    <th STYLE='WIDTH:50px; padding:7px'><?= $i; ?></th>
                                    <th STYLE='WIDTH:250px; padding:7px'><?= $route_data["route_name"]; ?></th>
                                    <th STYLE='WIDTH:100px; padding:7px'><?= $route_data["route"]; ?></th>
                                    <th STYLE='WIDTH:200px; padding:7px'><?= $route_data["shortcut_key"]; ?></th>
                                    <th STYLE='WIDTH:100px; padding:7px'><?= $route_data["tags"]; ?></th>
                                    <th STYLE='WIDTH:100px; padding:7px'><?= $route_data["visit_total_count"]; ?></th>
                                    <th STYLE='WIDTH:100px; padding:7px'><?= $route_data["created_date"]; ?></th>

                                    <th>

                                        <button class='btn btn-sm' data-bs-toggle="tooltip" title="Update Route" onclick="show_edit_modal('modal_div','update_route_modal','/linkRoutes/update_route/<?= $route_id; ?>');"><i class='material-icons material-icon-small text-primary'>edit</i></button>

                                        <button class='btn btn-sm' data-bs-toggle="tooltip" title="Delete Route" onclick="show_edit_modal('modal_div','delete_route_modal','/linkRoutes/disable_route/<?= $route_id; ?>');"><i class='material-icons material-icon-small text-danger'>delete</i></button>

                                    </th>
                                </tr>
                        <?php
                                $i++;
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="link_route_tableExportGroup" class="export-icon-group" style="display: none">
                <img class="export-icon" onclick='dtExport("link_route_table_wrapper","excel");' src="img/icons/download-excel-512x512.png" alt='Excel' height='16' width='16'>
                <img class="export-icon" onclick='dtExport("link_route_table_wrapper","pdf");' src="img/icons/download-pdf-512x512.png" alt='PDF' height='16' width='16'>
            </div>

        </div>
    </div>

</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    $(document).ready(function() {
        $('#link_route_table').DataTable({
            stateSave: true
        });
    });
</script>