@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Отчетность</h5>
    <a class="w-100" href="{{ URL::previous() }}"><i class="las la-angle-left">Назад</i></a>
    <div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-5 bg-gray-200"></div>
@endsection
@section('content')
    @include('admin.reporting.modals.edit')

    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            @include('admin.layouts.includes.messages')
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-toolbar">
                        <form action="{{ route('reporting') }}">
                            <div class="row">
                                <div class="col-3">
                                    <label>Тип отчета:</label>
                                    <select id="reportType" class="input-large selectpicker users_sort" name="type"
                                            data-size="5">
                                        <option value="{{ \App\Models\Client::REPORT_PER_MONTH }}"
                                                @if(request()->get('type') == \App\Models\Client::REPORT_PER_MONTH) selected @endif>
                                            Отчет за
                                            месяц
                                        </option>
                                        <option value="{{ \App\Models\Client::REPORT_ADVANCE }}"
                                                @if(request()->get('type') == \App\Models\Client::REPORT_ADVANCE) selected @endif>
                                            Авансовый
                                            отчет
                                        </option>
                                        <option value="{{ \App\Models\Client::REPORT_MOUNTING }}"
                                                @if(request()->get('type') == \App\Models\Client::REPORT_MOUNTING) selected @endif>
                                            Монтажные
                                            работы
                                        </option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label>Тип компании:</label>
                                    <select id="reportClientType" class="form-control" name="client_type" data-size="7"
                                            data-live-search="true">
                                        <option value="{{ \App\Models\Client::IP }}"
                                                @if(request()->get('client_type') == \App\Models\Client::IP) selected @endif>
                                            ИП
                                        </option>
                                        <option value="{{ \App\Models\Client::FL }}"
                                                @if(request()->get('client_type') == \App\Models\Client::FL) selected @endif>
                                            ФЛ
                                        </option>
                                        <option value="{{ \App\Models\Client::Тoo }}"
                                                @if(request()->get('client_type') == \App\Models\Client::Тoo) selected @endif>
                                            ТОО
                                        </option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label>Месяц:</label>
                                    <div class="col-sm-12">
                                        <div class="input-group date" id="kt_datetimepicker_3"
                                             data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input reportMount"
                                                   name="mount"
                                                   data-target="#kt_datetimepicker_3"
                                                   value="{{ request()->get('mount') ?? date('Y.m') }}"/>
                                            <div class="input-group-append" data-target="#kt_datetimepicker_3"
                                                 data-toggle="datetimepicker">
                                            <span class="input-group-text">
                                             <i class="ki ki-calendar"></i>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label>Отчет:</label>
                                    <button class="btn btn-success font-weight-bold">
                                        Сформировать отчет
                                    </button>
                                </div>
                                <div class="col-2">
                                    <label>Экспорт:</label>
                                    <button type="button" class="btn btn-success font-weight-bold" data-toggle="modal"
                                            data-target="#exportModal">
                                        Экспортировать в Excel
                                    </button>
                                </div>
                            </div>
                        </form>
                        <!--end::Select-->
                    </div>
                </div>
                <div class="card-body">
                    @if(request()->get('type') == \App\Models\Client::REPORT_PER_MONTH  || !request()->get('type'))
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center">
                                <thead>
                                <tr>
                                    <th class="pl-0 text-center">
                                        Компания
                                    </th>
                                    <th class="pr-0 text-center">
                                        Аккаунт
                                    </th>
                                    <th class="pr-0 text-center">
                                        Номер договора
                                    </th>
                                    <th class="pr-0 text-center">
                                        Дата договора
                                    </th>
                                    <th class="pr-0 text-center">
                                        Начальное кол-во
                                    </th>
                                    <th class="pr-0 text-center">
                                        Конечное кол-во
                                    </th>
                                    <th class="pr-0 text-center">
                                        Стоимость
                                    </th>
                                    <th class="pr-0 text-center">
                                        Действие
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="items">
                                @php($total = 0)
                                @foreach($clients as $client)
                                    @php($price = $client->getPerMountPrice())
                                    @if($price > 0)
                                        <tr class="client_tr" data-route="{{ route('reporting', $client->id) }}">
                                            <td class="text-center pl-0 td">
                                                {{ $client->name }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                {{ $client->account }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                {{ $client->contract_number }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                @if($client->contract_date)
                                                    {{ date('d.m.Y', strtotime($client->contract_date)) }}
                                                @endif
                                            </td>
                                            <td class="text-center pr-0 td">
                                                {{ $client->getEquipments() }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                {{ $client->getReportingEquipment() }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                @php( $total += $price)
                                                {{ $price }}
                                            </td>
                                            <td class="text-center pr-0">
                                                <a href="{{ route('reporting.register', $client->id) }}"
                                                   title=""
                                                   class="btn btn-sm btn-clean btn-icon">
                                                    <i class="fas fa-list-ol"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                <tr>
                                    <td class="text-left pr-0 td" colspan="7">
                                        Итого: {{ $total }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>


                    @elseif(request()->get('type') == \App\Models\Client::REPORT_ADVANCE )
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center">
                                <thead>
                                <tr>
                                    <th class="pl-0 text-center">
                                        Компания
                                    </th>
                                    <th class="pr-0 text-center">
                                        Аккаунт
                                    </th>
                                    <th class="pr-0 text-center">
                                        Контактный номер
                                    </th>
                                    <th class="pr-0 text-center">

                                    </th>
                                    <th class="pr-0 text-center">
                                        Стоимость
                                    </th>
                                    <th class="pr-0 text-center">
                                        Действие
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="items">
                                @php($total = 0)
                                @foreach($clients as $client)
                                    @php($price = $client->getPerMountPrice())
                                    @if($price > 0)
                                        <tr class="client_tr" data-route="{{ route('reporting', $client->id) }}">
                                            <td class="text-center pl-0 td">
                                                {{ $client->name }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                {{ $client->account }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                {{ $client->phone }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                {{ $client->getReportingEquipment() }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                @php( $total += $price)
                                                {{ $price }}
                                            </td>
                                            <td class="text-center pr-0">
                                                <a href="javascript:;"
                                                   class="btn btn-sm btn-clean btn-icon reportEditBtn"
                                                   data-toggle="modal" data-target="#editReport"
                                                   data-id="{{ $client->id }}">
                                                    <i class="las la-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                <tr>
                                    <td class="text-left pr-0 td" colspan="7">
                                        Итого: {{ $total }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    @elseif(request()->get('type') == \App\Models\Client::REPORT_MOUNTING)
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center">
                                <thead>
                                <tr>
                                    <th class="pl-0 text-center">
                                        Акртикул
                                    </th>
                                    <th class="pr-0 text-center">
                                        Оборудование
                                    </th>
                                    <th class="pr-0 text-center">
                                        Дата работы
                                    </th>
                                    <th class="pr-0 text-center">
                                        Тип работы
                                    </th>
                                    <th class="pr-0 text-center">
                                        Комментарий
                                    </th>
                                    <th class="pr-0 text-center">
                                        Стоимость
                                    </th>
                                    <th class="pr-0 text-center">
                                        Действие
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="items">
                                @php($total = 0)
                                @foreach($installation as $installatio)
                                    @php($price = $installatio->price)
                                    @if($price > 0)
                                        <tr class="client_tr" data-route="{{ route('reporting', $installatio->id) }}">
                                            <td class="text-center pl-0 td">
                                                {{ $installatio->client->account }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                {{ $installatio->equipment->imei }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                {{ date('d.m.Y', strtotime($installatio->date_create)) }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                {{ $installatio->type }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                {{ $installatio->comment }}
                                            </td>
                                            <td class="text-center pr-0 td">
                                                @php( $total += $price)
                                                {{ $price }}
                                            </td>
                                            <td class="text-center pr-0">
                                                <a href="javascript:;"
                                                   class="btn btn-sm btn-clean btn-icon reportEditBtn"
                                                   data-toggle="modal" data-target="#editReport"
                                                   data-id="{{ $installatio->id }}">
                                                    <i class="las la-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                <tr>
                                    <td class="text-left pr-0 td" colspan="7">
                                        Итого: {{ $total ?? 0 }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>

                <!--end::Body-->
            </div>
        </div>
    </div>

    @include('admin.reporting.modals.export')
@endsection

@section('js_after')
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/bootstrap-datetimepicker.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/jquery-mask.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/bootstrap-select.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/file-upload/image-input.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/ktdatatable/base/html-table.js') }}"></script>
    <script src="{{ asset('super_admin/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/datatables/basic/basic.js') }}"></script>
@endsection
