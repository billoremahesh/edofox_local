
<html xmlns="http://www.w3.org/1999/xhtml">
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-60995650-2"></script>
    <script src="<?php echo base_url('assets/gtag.js'); ?>"></script>

    <meta name="robots" content="noindex,nofollow" />

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/favicon.png'); ?>" />
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title><?= $title; ?> | Edofox Online Mock Tests for IIT-JEE, NEET, AIIMS, CET</title>

    <!-- Font-Awesome 5.15.4-web -->
    <link rel="stylesheet" href="<?php echo base_url('assets/fontawesome-5.15.4-web/css/all.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/multiselect_dropdown.css'); ?>">
    
    <!-- Bootstrap-5.0.2 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap-5.0.2-dist/css/bootstrap.min.css'); ?>">

    <!-- Datatable CSS For Bootstrap 5  -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.11.3/af-2.3.7/b-2.0.1/b-colvis-2.0.1/b-html5-2.0.1/b-print-2.0.1/cr-1.5.4/fc-4.0.0/fh-3.2.0/r-2.2.9/sc-2.0.5/sb-1.2.2/datatables.min.css" />


    <!-- Datepicker -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/datetimepicker/bootstrap-datetimepicker.min.css'); ?>">
    
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/select2-4.0.13/css/select2.min.css'); ?>">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">


    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Toastr CSS -->
    <link href="<?php echo base_url('assets/plugins/toastr/toastr.css'); ?>" rel="stylesheet" />

    <!-- Snackbar -->
    <link href="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.css'); ?>" rel="stylesheet" />
   <!-- Datepick CSS lib -->
    <link href="<?php echo base_url('assets/plugins/datepick/themes/default.css'); ?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/plugins/datepick/themes/default.date.css'); ?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/plugins/datepick/themes/default.time.css'); ?>" rel="stylesheet" />


    <!-- Awesomplet JS lib -->
    <link href="<?php echo base_url('assets/plugins/awesomplete/awesomplete.css'); ?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo base_url('assets/css/common.css?v=20220406'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/material_design.css?v=20220413'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/loading_indicater.css?v=20210902'); ?>" rel="stylesheet">
   
    
     <!-- timepicker  css -->
    <link href="<?php echo base_url('assets/css/new_timepicker/new_timepicker.css'); ?>" rel="stylesheet" />

    <!-- for treeview css --> 
    <link rel="stylesheet" href="<?php echo base_url('assets/treeview/css/metisMenu.css'); ?>" />  
    <link rel="stylesheet" href="<?php echo base_url('assets/treeview/css/app.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/treeview/css/mm-folder.css'); ?>"> 

 
</head>


<body>

    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- NavBar Header -->
            <?php include_once(APPPATH . "Views/layouts/navbar.php"); ?>

            <?php include_once(APPPATH . "Views/layouts/sidebar.php"); ?>