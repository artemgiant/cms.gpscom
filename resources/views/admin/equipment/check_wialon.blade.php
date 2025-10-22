@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Посмотреть информацию об устройстве</h5>
    <a class="w-100" href="{{ URL::previous() }}"><i class="las la-angle-left">Назад</i></a>

    <div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-5 bg-gray-200"></div>
@endsection
@section('content')
    <div class="container-fluid">
        @include('admin.layouts.includes.messages')
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">
                        Описание основных параметров: <a target="_blank" href="https://sdk.wialon.com/wiki/ru/sidebar/remoteapi/apiref/format/unit">Документація Wialon</a>
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center">
                        <thead>
                        <tr>
                            <th class="pl-0 text-center">Название параметра</th>
                            @if(!empty($item))
                                <th class="pl-0 text-center">Значение</th>
                            @endif
                            <th class="pr-0 text-center">Описание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="text-center"><b>bact</b></td>
                            @if(!empty($item))
                                <td class="text-center"><b>{{ $item->bact }}</b></td>
                            @endif
                            <td class="text-center">ID учетной записи клиента</td>
                        </tr>
                        <tr>
                            <td class="text-center"><b>ct</b></td>
                            @if(!empty($item))
                                <td class="text-center"><b>{{ \App\Services\EquipmentService::getDateStart($item) }}</b></td>
                            @endif
                            <td class="text-center">Дата подключения объекта</td>
                        </tr>
                        <tr>
                            <td class="text-center"><b>dactt</b></td>
                            @if(!empty($item))
                                <td class="text-center"><b>{{ \App\Services\EquipmentService::getDateEnd($item) }}</b></td>
                            @endif
                            <td class="text-center">Дата отключения объекта (время деактивация UNIX time, 0 - объект не
                                был деактивирован)
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center"><b>ph</b></td>
                            @if(!empty($item))
                                <td class="text-center"><b>{{ $item->ph }}</b></td>
                            @endif
                            <td class="text-center">Номер устройства</td>
                        </tr>
                        <tr>
                            <td class="text-center"><b>ph2</b></td>
                            @if(!empty($item))
                                <td class="text-center"><b>{{ $item->ph2 }}</b></td>
                            @endif
                            <td class="text-center">Номер устройства 2</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-header flex-wrap border-0 pt-6 pb-0 mt-5">
                <div class="card-title">
                    <h3 class="card-label">Посмотреть информацию об устройстве c Wialon</h3>
                </div>
                <form action="{{ route('equipments.check_wialon') }}" method="GET">
                    <div class="row">
                        <div class="col">
                            <div class="input-icon">
                                <input id="search_staff_input" type="text" name="imei"
                                       value="{{ isset($imei) ? $imei : '' }}"
                                       class="form-control form-control-solid"
                                       placeholder="Поиск по IMEI">
                                <span><i class="flaticon2-search-1 text-muted"></i></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-success font-weight-bolder searchObjectImei" type="submit">
                                <span class="svg-icon svg-icon-md searchIcon"><i class="icon flaticon-search"></i></span> Поиск объекта по IMEI
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @if(!empty($item))
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card card-custom card-stretch">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-label">Параметры объекта Array: <b>{{ $imei }}</b></h3>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center">
                                            <thead>
                                            <tr>
                                                <th class="pl-0 text-center">Название</th>
                                                <th class="pr-0 text-center">Значение</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                                $itemArray = json_decode(json_encode($item), true);
                                            @endphp
                                            @foreach($itemArray as $name => $value)
                                                <tr>
                                                    <td class="text-center">{{ $name }}</td>
                                                    <td class="text-left">
                                                        @if(is_array($value))
                                                            <pre>{{ json_encode($value) }}</pre>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card card-custom card-stretch">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-label">Параметры объекта JSON: <b>{{ $imei }}</b></h3>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <pre>{{ json_encode($item, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(!empty($imei))
                <div class="card-header bg-warning flex-wrap border-0 pb-0 text-center">
                    <div class="card-title">
                        <h3 class="card-label text-dark">{{ "Оборудование с IMEI: {$imei} не найдено!" }}</h3>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

