<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<?php
header('Cross-Origin-Opener-Policy: same-origin');
header('Cross-Origin-Embedder-Policy: require-corp');
?>
<!-- <link type="text/css" rel="stylesheet" href="https://source.zoom.us/2.3.0/css/bootstrap.css" /> -->
<link type="text/css" rel="stylesheet" href="https://source.zoom.us/2.3.0/css/react-select.css" />
<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/video_proctoring.css?v=20220331'); ?>" rel="stylesheet">
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

        <div class="text-center">
            <span id="info"></span>
            <div class="text-center" id="meetingSDKElement">
                <!-- Zoom Meeting SDK Rendered Here -->
            </div>
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script src="https://source.zoom.us/2.3.0/lib/vendor/react.min.js"></script>
<script src="https://source.zoom.us/2.3.0/lib/vendor/react-dom.min.js"></script>
<script src="https://source.zoom.us/2.3.0/lib/vendor/redux.min.js"></script>
<script src="https://source.zoom.us/2.3.0/lib/vendor/redux-thunk.min.js"></script>
<script src="https://source.zoom.us/2.3.0/lib/vendor/lodash.min.js"></script>

<!-- For Component View -->
<script src="https://source.zoom.us/2.3.0/zoom-meeting-embedded-2.3.0.min.js"></script>
<!-- For Client View -->
<!-- <script src="https://source.zoom.us/zoom-meeting-2.2.0.min.js"></script> -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


<script src="<?php echo base_url('assets/js/video_proctoring.js?v=20220418'); ?>"></script>

<script>
    const client = ZoomMtgEmbedded.createClient();
    const role = 1
    const userName = 'Admin';
    $(document).ready(function() {
        let meetingSDKElement = document.getElementById('meetingSDKElement');
        client.init({
            debug: true,
            zoomAppRoot: meetingSDKElement,
            language: 'en-US',
            customize: {
                video: {
                    isResizable: true,
                    viewSizes: {
                        default: {
                            width: 1000,
                            height: 600
                        },
                        ribbon: {
                            width: 1000,
                            height: 600
                        }
                    }
                },
                meetingInfo: [
                    'topic',
                    'host',
                ],
            },
        });
        //Create meeting at server and join from here!
        joinRoom(<?= $meeting_id ?>, <?= $meeting_password ?>);
    });
</script>