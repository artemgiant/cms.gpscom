@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Аналитика</h5>
    <a class="w-100" href="{{ URL::previous() }}"><i class="las la-angle-left">Назад</i></a>

    <div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-5 bg-gray-200"></div>
@endsection
@section('content')
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            @include('admin.layouts.includes.messages')
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-toolbar">
                        <form action="{{ route('analytics') }}" class="analytic">
                            <div class="row">
                                <div class="">
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th class="pl-0 text-center">
                                                    Количество объектов
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Кол-во активных объектов
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Кол-во деактивированных объектов
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Выручка
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody id="items">
                                            <tr>
                                                <td class="text-center pl-0 td">
                                                    ИП
                                                </td>
                                                <td class="text-center pl-0 td">
                                                    {{$total_ip}}
                                                </td>
                                                <td class="text-center pr-0 td">
                                                    {{$total_ip_act}}
                                                </td>
                                                <td class="text-center pr-0 td">
                                                    {{$total_ip_deact}}
                                                </td>
                                                <td class="text-center pr-0 td earnings_ip">
                                                    <i class="fa fa-spinner" aria-hidden="true"></i>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center pl-0 td">
                                                    ФЛ
                                                </td>
                                                <td class="text-center pl-0 td">
                                                    {{$total_fl}}
                                                </td>
                                                <td class="text-center pr-0 td">
                                                    {{$total_fl_act}}
                                                </td>
                                                <td class="text-center pr-0 td">
                                                    {{$total_fl_deact}}
                                                </td>
                                                <td class="text-center pr-0 td earnings_fl">
                                                    <i class="fa fa-spinner" aria-hidden="true"></i>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center pl-0 td">
                                                    ТОО
                                                </td>
                                                <td class="text-center pl-0 td">
                                                    {{$total_too}}
                                                </td>
                                                <td class="text-center pr-0 td">
                                                    {{$total_too_act}}
                                                </td>
                                                <td class="text-center pr-0 td">
                                                    {{$total_too_deact}}
                                                </td>
                                                <td class="text-center pr-0 td earnings_too">
                                                    <i class="fa fa-spinner" aria-hidden="true"></i>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center pl-0 td">
                                                    ИТОГО
                                                </td>
                                                <td class="text-center pl-0 td">
                                                    {{$total_too+$total_ip+$total_fl}}
                                                </td>
                                                <td class="text-center pr-0 td">
                                                    {{$total_too_act+$total_ip_act+$total_fl_act}}
                                                </td>
                                                <td class="text-center pr-0 td">
                                                    {{$total_too_deact+$total_fl_deact+$total_ip_deact}}
                                                </td>
                                                <td class="text-center pr-0 td earnings_all">
                                                    <i class="fa fa-spinner" aria-hidden="true"></i>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!--end::Select-->
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-around">
                        <div class="w-100">
                            <p class="mb-5">График активных объектов за последние 12 месяцев</p>
                            <div id="chart"></div>
                        </div>
                        <div class="w-100">
                            <p class="mb-5">График динамики по выручки на конец месяца за последние 12 месяцев</p>
                            <div id="chart2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_after')
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/bootstrap-select.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/features/charts/apexcharts.js') }}"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            $.ajax({
                type: 'GET',
                url: 'analytics/get-earnings',
                dataType: "json",
                success: function (data) {
                    $('.earnings_ip').html(data.earnings_ip)
                    $('.earnings_fl').html(data.earnings_fl)
                    $('.earnings_too').html(data.earnings_too)
                    $('.earnings_all').html(parseFloat(data.earnings_too + data.earnings_ip + data.earnings_fl).toFixed(2))
                }
            });
        });
        var monthNames = ["январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь"];

        var today = new Date();
        var d;
        var months = [];

        for (var i = 12; i > 0; i -= 1) {
            d = new Date(today.getFullYear(), today.getMonth() - i, 1);
            months.push(monthNames[d.getMonth()]);
        }
        months.reverse();
        var options = {
            series: [{
                name: 'ИП',
                data: <?php echo json_encode($active_counts_ip); ?>
            }, {
                name: 'ФЛ',
                data: <?php echo json_encode($active_counts_fl); ?>
            }, {
                name: 'ТОО',
                data: <?php echo json_encode($active_counts_too); ?>
            }, {
                name: 'Итого',
                data: <?php echo json_encode($activeTotal); ?>
            }],


            chart: {
                type: 'bar',
                height: 600,
                stacked: true,
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    dataLabels: {
                        total: {
                            enabled: true,
                            offsetX: 0,
                            style: {
                                fontSize: '10px',
                                fontWeight: 900
                            }
                        }
                    }
                },
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            title: {
                text: ''
            },
            xaxis: {
                categories: months,
                labels: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            yaxis: {
                title: {
                    text: undefined
                },
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            fill: {
                opacity: 1
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

        var options2 = {
            series: [{
                name: 'ИП',
                data: <?php echo json_encode($graphic_total_ip); ?>
            }, {
                name: 'ФЛ',
                data: <?php echo json_encode($graphic_total_fl); ?>
            }, {
                name: 'ТОО',
                data: <?php echo json_encode($graphic_total_too); ?>
            }, {
                name: 'Итого',
                data: <?php echo json_encode($graphicTotal); ?>
            }],
            chart: {
                type: 'bar',
                height: 600,
                stacked: true,
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    dataLabels: {
                        total: {
                            enabled: true,
                            offsetX: 0,
                            style: {
                                fontSize: '10px',
                                fontWeight: 900
                            }
                        }
                    }
                },
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            title: {
                text: ''
            },
            xaxis: {
                categories: months,
                labels: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            yaxis: {
                title: {
                    text: undefined
                },
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            fill: {
                opacity: 1
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart2"), options2);
        chart.render();
    </script>
@endsection
