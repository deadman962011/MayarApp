@extends('layout.master')


@section('Style')
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css"> -->
@endsection

@section('Content')
@include('includes.BigBossNavbar')
@include('includes.error')
@include('includes.BigBossSideNav')
<div id='Content' style="background:transparent;padding:0;border-raduis:0;">

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">


        </div>
    </div>


    <div class="col-sm-12 ">
        <div class="DBOX">
            <h4 class="text-center" style="font-weight: bold">{{ trans('lang.OrderDailyTitle')}}</h4>
            <canvas id="DayAreaChart"></canvas>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="DBOX">
            <h4 class="text-center" style="font-weight: bold">{{ trans('lang.OrderMonthlyTitle') }}</h4>
            <canvas id="MonthChart"></canvas>
        </div>

    </div>

    <div class="col-sm-6">
        <div class="DBOX">
            <h4 class="text-center" style="font-weight: bold">{{ trans('lang.PaymentChartTitle') }}</h4>
           <canvas id="myPieChart"></canvas>
        </div>

    </div>

    <div class="col-sm-12">
        <div class="DBOX">
            <h4 class="text-center" style="font-weight: bold">{{ trans('lang.CustChartTitle') }}</h4>
            <canvas id="CustDayAreaChart"></canvas>
        </div>
    </div>
    


</div>

@endsection

@section('Script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script >
       
        $.post({
        url:"{{ route('ChartsGet') }}",
        data:{ _token: '{{ csrf_token() }}'}
        }).done(function(resp,textStatus){

        console.log(resp)

        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';

        // Area Chart Example
        var ctx = document.getElementById("MonthChart");
        var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: resp.AreaChart.months,
            datasets: [{
            label: "Orders",
            lineTension: 0.3,
            backgroundColor: "rgba(2,117,216,0.2)",
            borderColor: "rgba(2,117,216,1)",
            pointRadius: 5,
            pointBackgroundColor: "rgba(2,117,216,1)",
            pointBorderColor: "rgba(255,255,255,0.8)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(2,117,216,1)",
            pointHitRadius: 50,
            pointBorderWidth: 2,
            data:resp.AreaChart.Orders,
            }],
        },
        options: {
            scales: {
            xAxes: [{
                time: {
                unit: 'date'
                },
                gridLines: {
                display: false
                },
                ticks: {
                maxTicksLimit: 8
                }
            }],
            yAxes: [{
                ticks: {
                min: 0,
                max: resp.AreaChart.MaxOrders,
                maxTicksLimit: 8
                },
                gridLines: {
                color: "rgba(0, 0, 0, .125)",
                }
            }],
            },
            legend: {
            display: false
            }
        }
        });


        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';

        // Pie Chart Example
        var ctx = document.getElementById("myPieChart");
        var myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ["Local", "Credit Card", "PayPal","Wait"],
            datasets: [{
            data:resp.PaeChart.PaymentWay,
            backgroundColor: ['#0cc300','#007bff','#ffc107','#a3a3a3'],
            }],
        },
        });


        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';

        // Area Chart Example
        var ctx = document.getElementById("DayAreaChart");
        var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: resp.DayChart.Days,
            datasets: [{
            label: "Orders",
            lineTension: 0.3,
            backgroundColor: "rgba(2,117,216,0.2)",
            borderColor: "rgba(2,117,216,1)",
            pointRadius: 5,
            pointBackgroundColor: "rgba(2,117,216,1)",
            pointBorderColor: "rgba(255,255,255,0.8)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(2,117,216,1)",
            pointHitRadius: 50,
            pointBorderWidth: 2,
            width:450,
            data:resp.DayChart.Orders,
            }],
        },
        options: {
            scales: {
            xAxes: [{
                time: {
                unit: 'date'
                },
                gridLines: {
                display: false
                },
                ticks: {
                min:0,
                max:7,
                maxTicksLimit: 8
                }
            }],
            yAxes: [{
                ticks: {
                min: 0,
                max: resp.DayChart.MaxOrders,
                maxTicksLimit: 8
                },
                gridLines: {
                color: "rgba(0, 0, 0, .125)",
                }
            }],
            },
            legend: {
            display: false
            }
        }
        });



        // Area Chart Customers
        var ctx = document.getElementById("CustDayAreaChart");
        var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: resp.CustChart.Days,
            datasets: [{
            label: "Customers",
            lineTension: 0.3,
            backgroundColor: "rgba(2,117,216,0.2)",
            borderColor: "rgba(2,117,216,1)",
            pointRadius: 5,
            pointBackgroundColor: "rgba(2,117,216,1)",
            pointBorderColor: "rgba(255,255,255,0.8)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(2,117,216,1)",
            pointHitRadius: 50,
            pointBorderWidth: 2,
            width:450,
            data:resp.CustChart.Custs,
            }],
        },
        options: {
            scales: {
            xAxes: [{
                time: {
                unit: 'date'
                },
                gridLines: {
                display: false
                },
                ticks: {
                maxTicksLimit: 8
                }
            }],
            yAxes: [{
                ticks: {
                min: 0,
                max: resp.CustChart.MaxCusts,
                maxTicksLimit: 8
                },
                gridLines: {
                color: "rgba(0, 0, 0, .125)",
                }
            }],
            },
            legend: {
            display: false
            }
        }
        });
        })
    </script>
@endsection