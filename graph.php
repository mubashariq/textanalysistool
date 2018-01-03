<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<div id="container" style="height: 645px;">
    
</div>
<script type="text/javascript">
$(function () {
    $.getJSON('GetPatternsRelevancy.php', {id: <?php echo isset($_GET['pattern']) ? $_GET['pattern'] : 1?>}, function(chartData) {
        console.log(chartData.data);
        Highcharts.chart('container', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Relevancy'
            },
            subtitle: {
                text: 'Relevancy percentage of '+chartData.pattern+' to other deisgn patterns.'
            },
            xAxis: {
                type: 'category',
                title: {
                    text: 'Design patterns'
                }
            },
            yAxis: {
                max: '100',
                title: {
                    text: 'Relevancy Percentage'
                }

            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}%'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
            },

            series: [{
                name: 'Pattern',
                colorByPoint: true,
                data: chartData.data
            }]
        });
    });
}); 
</script>