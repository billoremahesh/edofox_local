<div id="generate_weekly_student_logins_chart"> Data not found </div>
<script>
    function generate_weekly_student_logins_chart() {
        // high chart common settings
        Highcharts.setOptions({
            colors: ['#5C8AD5', '#0070C0', '#555ABE', '#71601C', '#338B8A', '#CA762F', '#AF1A3E', '#D78942', '#ED5E5E', '#B77351', '#FFD301', '#FE0D27', '#3A546A', '#E871FE', '#00FEDE', '#1AFE00'],
            lang: {
                thousandsSep: ','
            }
        });

        new Highcharts.chart('generate_weekly_student_logins_chart', {
            exporting: {
                buttons: {
                    contextButton: {
                        menuItems: [{
                                textKey: 'printChart',
                                onclick: function() {
                                    this.print();
                                }
                            },
                            'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG', 'downloadCSV', 'downloadXLS',
                            {
                                textKey: 'viewData',
                                onclick: function() {
                                    this.viewData();
                                }
                            },
                            'label', 'labelHideDataTable'
                        ]
                    }
                }
            },
            credits: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            chart: {
                type: 'areaspline',
                // Ref: https://api.highcharts.com/highcharts/chart.height
                height: (1 / 3 * 100) + '%' // 16:9 ratio

            },
            title: {
                text: "",
                style: {
                    fontSize: '22px',
                    fontFamily: 'Karla, Roboto, sans-serif'
                }
            },
            subtitle: {
                text: 'No. of students login per day',
                style: {
                    fontSize: '16px',
                    fontFamily: 'Karla, Roboto, sans-serif'
                }
            },
            xAxis: {
                visible: false,
                type: 'category',
                labels: {
                    rotation: -45,
                    formatter: function() {
                        return '<b>Week ' + this.value + '<b>';
                    },
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Karla, Roboto, sans-serif'
                    }
                }
            },
            yAxis: {
                visible: false,
                min: 0,
                title: {
                    text: 'Tests'
                },
                style: {
                    fontSize: '12px',
                    fontFamily: 'Karla, Roboto, sans-serif'
                }
            },
            legend: {
                enabled: false
            },
            tooltip: {
                pointFormat: '<b>Number of students login: {point.y}</b>',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Karla, Roboto, sans-serif'
                }
            },
            series: [{
                name: 'Population',
                data: [

                    <?php if (!empty($weekly_student_logins)) : foreach ($weekly_student_logins as $student_login_cnt) :
                            echo "['" . $student_login_cnt['login_date'] . "'," . $student_login_cnt['cnt'] . "],";
                        endforeach;
                    endif; ?>
                ],
                color: {
                    linearGradient: {
                        x1: 1,
                        y1: 0,
                        x2: 0,
                        y2: 0
                    },
                    stops: [
                        [0, Highcharts.Color('#eae2f7').setOpacity(0.4).get('rgba')],
                        [1, Highcharts.Color('#6200ee').setOpacity(0.4).get('rgba')],
                    ]
                },
                fillColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops: [
                        [0, Highcharts.Color('#eae2f7').setOpacity(0.3).get('rgba')],
                        [1, Highcharts.Color('#6200ee').setOpacity(0).get('rgba')],
                    ]
                }
            }]
        });
    }
</script>