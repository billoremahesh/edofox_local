<div id="generate_weekly_tests_chart"> Data not found </div>
<script>
    function generate_weekly_tests_chart() {
        // high chart common settings
        Highcharts.setOptions({
            colors: ['#5C8AD5', '#0070C0', '#555ABE', '#71601C', '#338B8A', '#CA762F', '#AF1A3E', '#D78942', '#ED5E5E', '#B77351', '#FFD301', '#FE0D27', '#3A546A', '#E871FE', '#00FEDE', '#1AFE00'],
            lang: {
                thousandsSep: ','
            }
        });

        new Highcharts.chart('generate_weekly_tests_chart', {
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
                text: 'No. of Tests per week',
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
                pointFormat: '<b>Tests in week {point.x}: {point.y}</b>',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Karla, Roboto, sans-serif'
                }
            },
            series: [{
                name: 'Population',
                data: [

                    <?php if (!empty($weekly_tests_count_data)) : foreach ($weekly_tests_count_data as $tests_count) :
                            echo "['" . $tests_count['week_date'] . "'," . $tests_count['no_of_tests'] . "],";
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
                        [0, Highcharts.Color('#ed4c05').setOpacity(0.4).get('rgba')],
                        [1, Highcharts.Color('#ed4c05').setOpacity(0.4).get('rgba')],
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
                        [0, Highcharts.Color('#ed4c05').setOpacity(0.3).get('rgba')],
                        [1, Highcharts.Color('#ffffff').setOpacity(0).get('rgba')],
                    ]
                }
            }]
        });
    }
</script>