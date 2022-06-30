<!-- Add Session Schedule Modal -->
<div id="holiday_calender" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <h1>Codeigniter 4 Fullcalendar example - laratutorials.com</h1>
                    <div class="row" style="width:50%">
                        <div class="col-md-12">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


 <!-- holiday calender -->
 <script src='http://fullcalendar.io/js/fullcalendar-2.1.1/lib/moment.min.js'></script>
<script src='http://fullcalendar.io/js/fullcalendar-2.1.1/lib/jquery.min.js'></script>
<script src="http://fullcalendar.io/js/fullcalendar-2.1.1/lib/jquery-ui.custom.min.js"></script>
<script src='http://fullcalendar.io/js/fullcalendar-2.1.1/fullcalendar.min.js'></script>

    <script>
       

        <?php
        $data['data'][0]['title'] = 'test holiday calender';
        $data['data'][0]['start'] = '2022-60-06';
        $data['data'][0]['end'] = '2022-60-10';
        $data['data'][0]['backgroundColor'] = "#00a65a";
        ?>
        var events = <?php echo json_encode($data) ?>;

        var date = new Date()
        var d = date.getDate(),
            m = date.getMonth(),
            y = date.getFullYear()

        $('#calendar').fullCalendar({ 
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            buttonText: {
                today: 'today',
                month: 'month',
                week: 'week',
                day: 'day'
            },
            events: events
        })
    </script>