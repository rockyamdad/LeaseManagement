var chartData = {
    x:[
        '9 Dec',
        '8 Dec',
        'Last 7 Days'
    ],
    y: [{
        name: 'Draft',
        data: [100, 71.5, 106.4]

    }, {
        name: 'Verify',
        data: [83.6, 78.8, 98.5]

    }, {
        name: 'Compare',
        data: [48.9, 38.8, 39.3]

    }, {
        name: 'Approve',
        data: [42.4, 33.2, 34.5]

    }]
};

$(function() {

    var chartOption = {
        chart: {
            type: 'column'
        },
        title: {
            text: 'গত মাসের পরিসংখ্যান'
        },
        subtitle: {
            text: 'সূত্রঃ রেকর্ডরুম'
        },
        series: [],
        xAxis: {
            categories: {},
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'সব আবেদনের পরিসংখ্যান'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:16px"><b>{point.key}</b></span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        }
    };

    function blockUIWhiteLoadDashboardData(){
        Metronic.blockUI({
            target: $('#statistics-data'),
            animate: true,
            overlayColor: 'black'
        });
        Metronic.blockUI({
            target: $('#statistics-chart'),
            animate: true,
            overlayColor: 'black'
        });
    }
    function loadDashboardData(date)
    {
        $.ajax({
            url: '/udc-statistics?date=' + date,
            dataType: 'json',
            success: function(data){
                $.unblockUI();
                $('#statistics-data').html(data.table);

                chartOption.xAxis.categories = data.chart.categories;
                chartOption.series = data.chart.series;

                $('#statistics-chart').highcharts(chartOption);
            },
            beforeSend: function(data){
                blockUIWhiteLoadDashboardData();
            },
            error: function(){
                $.unblockUI();
                console.warn('Enable to load Dashboard Data');
            }
        });
    }

    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            rtl: Metronic.isRTL(),
            orientation: "center",
            todayHighlight: true,
            language: 'bn'
        }).on('changeDate', function(date){
            loadDashboardData(date.format('yyyy-mm-dd'));
        });
    }

    loadDashboardData('');
});