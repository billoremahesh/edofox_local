<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/realtime_overview.css?v=20210915'); ?>" rel="stylesheet">
<style>
    #map_canvas {
        height: 400px;
        width: 100%;
        background-color: grey;
    }

   
</style>
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
            <div class="text-center my-2">
                <h4><?= $test_details['test_name']; ?></h4>
            </div>
            <div id="map_canvas" style="border: 2px solid #3872ac;"></div>
        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    var testId = <?= $test_id ?>;
    var institudeId = <?= $instituteID ?>;
</script>


<script>
    var map;
    var locations = [
        ['Location 1 Name', 'New York, NY', 'Location 1 URL'],
        ['Location 2 Name', 'Newark, NJ', 'Location 2 URL'],
        ['Location 3 Name', 'Philadelphia, PA', 'Location 3 URL']
    ];

    var bounds;

    // function initMap() {

    //     //your code here
    //     console.log("Map Init done!");

    //     var geocoder;
    //     var map;
    //     bounds = new google.maps.LatLngBounds();

    //     google.maps.event.addDomListener(window, "load", initialize);



    // }

    function addLocationsToMap(response) {
        var center = {
            lat: Number(response.institute.latitude),
            lng: Number(response.institute.longitude)
        };
        //             var locations = [
        //                 ['Philz Coffee<br>\
        //     801 S Hope St A, Los Angeles, CA 90017<br>\
        //    <a href="https://goo.gl/maps/L8ETMBt7cRA2">Get Directions</a>', 34.046438, -118.259653],
        //                 ['Philz Coffee<br>\
        //     525 Santa Monica Blvd, Santa Monica, CA 90401<br>\
        //    <a href="https://goo.gl/maps/PY1abQhuW9C2">Get Directions</a>', 34.017951, -118.493567],
        //                 ['Philz Coffee<br>\
        //     146 South Lake Avenue #106, At Shoppers Lane, Pasadena, CA 91101<br>\
        //     <a href="https://goo.gl/maps/eUmyNuMyYNN2">Get Directions</a>', 34.143073, -118.132040],
        //                 ['Philz Coffee<br>\
        //     21016 Pacific Coast Hwy, Huntington Beach, CA 92648<br>\
        //     <a href="https://goo.gl/maps/Cp2TZoeGCXw">Get Directions</a>', 33.655199, -117.998640],
        //                 ['Philz Coffee<br>\
        //     252 S Brand Blvd, Glendale, CA 91204<br>\
        //    <a href="https://goo.gl/maps/WDr2ef3ccVz">Get Directions</a>', 34.142823, -118.254569]
        //             ];

        var locations = response.students;
        var map = new google.maps.Map(document.getElementById('map_canvas'), {
            zoom: 9,
            center: center
        });
        var infowindow = new google.maps.InfoWindow({});
        var marker, count;
        for (count = 0; count < locations.length; count++) {
            var currLocation = locations[count];
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(Number(currLocation.latitude), Number(currLocation.longitude)),
                map: map,
                title: currLocation.name
            });
            console.log("added marker ", currLocation);
            google.maps.event.addListener(marker, 'click', (function(marker, count) {
                return function() {
                    // infowindow.setContent(currLocation);
                    // infowindow.open(map, marker);
                }
            })(marker, count));
        }

    }

    function loadLocations() {
        //Load tokens first
        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                    var url = rootAdmin + 'getTestResults';
                    var request = {
                        "test": {
                            "id": testId
                        },
                        "institute": {
                            "id": institudeId
                        }
                    };
                    $.ajax({
                        type: 'POST',
                        beforeSend: function(request) {
                            request.setRequestHeader("AuthToken", resp.data.admin_token);
                        },
                        url: url,
                        data: JSON.stringify(request),
                        success: function(result) {
                            console.log(result);
                            addLocationsToMap(result);
                            // $("#test_name").text(result.test.name);
                        },
                        dataType: 'json',
                        contentType: 'application/json',
                    });
                } else {
                    alert("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                // An error occurred
                // alert("Exception: " + error);
            });
    }

    function initMap() {
        loadLocations();
    }
</script>


<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKGL3W6F6WMqwkeViU_4UbChetFZ7lMzk&callback=initMap" type="text/javascript"></script>