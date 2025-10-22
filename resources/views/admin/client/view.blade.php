@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Список подключенных устройств</h5>
    <a class="w-100" href="{{ URL::previous() }}"><i class="las la-angle-left">Назад</i></a>

    <!--end::Title-->
    <!--begin::Separator-->
    <div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-5 bg-gray-200"></div>
    <!--end::Separator-->
@endsection
@section('content')

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            @include('admin.layouts.includes.messages')

            <div class="d-flex flex-row">
                <!--begin::Aside-->
                <div class="flex-row-auto offcanvas-mobile w-300px w-xl-350px" id="kt_profile_aside">
                    <!--begin::Profile Card-->
                    <div class="card card-custom card-stretch">
                        <!--begin::Body-->
                        <div class="card-body pt-4">

                            <!--begin::User-->
                            <div class="d-flex align-items-center">
                                <div>
                                    <a href="#"
                                       class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">{{ $client->name }}</a>
                                    <div class="text-muted">{{ $client->person }}</div>
                                </div>
                            </div>
                            <!--end::User-->
                            <!--begin::Contact-->
                            <div class="py-9">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-bold mr-2"><b>Аккаунт</b>:</span>
                                    <a href="#" class="text-muted text-hover-primary">{{ $client->account }}</a>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-bold mr-2"><b>Контакты</b>:</span>
                                    <span class="text-muted">{{ $client->phone ? '+' . $client->phone : '' }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-bold mr-2"><b>Номер договора</b>:</span>
                                    <span class="text-muted">{{ $client->contract_number }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-bold mr-2"><b>Менеджер</b>:</span>
                                    <span
                                        class="text-muted">{{ isset($client->getManager) ? $client->getManager->name . ' ' . $client->getManager->surname : '' }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-bold mr-2"><b>Номер бухгалтера</b>:</span>
                                    <span
                                        class="text-muted">{{ $client->accountant_phone }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-bold mr-2"><b>Email</b>:</span>
                                    <span
                                        class="text-muted">{{ $client->email }}</span>
                                </div>
                                <hr>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-bold mr-2">Кол-во подключенных устройств:</span>
                                    <span class="text-muted">{{ $client->getEquipments() }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-bold mr-2">Кол-во активных устройств:</span>
                                    <span class="text-muted">{{ $client->getActiveEquipments() }}</span>
                                </div>
                                <hr>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-bold mr-2">Тарифы клиента</span>
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#addClientTariff">+
                                    </button>
                                </div>
                                @foreach($client->getClientTariffs() as $item)
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <a href="#"
                                           class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary clientTariffUpdate"
                                           data-client_id="{{ $client->id }}"
                                           data-clients_tariff_id="{{ $item->clients_tariff_id }}"
                                           data-tariff_id="{{ $item->id }}">
                                            <span class="font-weight-bold mr-2">{{ $item->title }}</span>
                                        </a>
                                        <span class="text-muted">{{ $item->getTariffPrice() }}</span>
                                    </div>
                                @endforeach
                                <hr>
                                <div class="d-flex align-items-center justify-content-between">

                                    <span class="font-weight-bold mr-2">
                                        <form id="client_equipments_export"
                                              action="{{ route('client.equipments.export') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                                        <div class="w-100">
                                                <div class="input-group date" id="kt_datetimepicker_40"
                                                     data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                           name="date" value="{{ date('Y.m') }}"
                                                           data-target="#kt_datetimepicker_40"/>
                                                    <div class="input-group-append" data-target="#kt_datetimepicker_40"
                                                         data-toggle="datetimepicker">
                                                        <span class="input-group-text">
                                                            <i class="ki ki-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                         </div>
                                      </form>
                                    </span>
                                    <button form="client_equipments_export" type="submit" class="btn btn-primary">
                                        Экспортировать
                                    </button>
                                </div>
                                <hr>
                            </div>
                            <!--end::Contact-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Profile Card-->
                </div>
                <!--end::Aside-->
                <!--begin::Content-->
                <div class="flex-row-fluid ml-lg-8">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="card card-custom card-stretch gutter-b">
                            <div class="card-header flex-wrap py-3">
                                <div class="card-title">
                                    <form method="GET">
                                        <div class="input-icon">
                                            <input id="search_client_equipments" type="text" name="search"
                                                   data-type="{{ request()->get('type') }}"
                                                   data-client_id="{{ $client->id }}"
                                                   class="form-control form-control-solid"
                                                   placeholder="Поиск" value="{{ request()->input('search') }}"/>
                                            <span><i class="flaticon2-search-1 text-muted"></i></span>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-toolbar">
                                    <a href="#" class="btn btn-primary font-weight-bolder ml-2" data-toggle="modal"
                                       data-target="#createEquipments">Создать и поключить обьєкт</a>
                                    <a href="#" class="btn btn-primary font-weight-bolder ml-2" data-toggle="modal"
                                       data-target="#addEquipment">Добавить обьєкт</a>
                                    <a href="{{ route('client.update_wialon', $client->id) }}"
                                       class="btn btn-light-success spinner-darker-success spinner-left mr-3 ml-2 updateClientDataBtn">
                                        Обновить данные клиента
                                    </a>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-3">
                                <div class="tab-content">
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center"
                                               style="margin-bottom: 200px;">
                                            <thead>
                                            <tr>
                                                <th class="pl-0 text-center" width="15%">
                                                    Обьект
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Устройство
                                                </th>
                                                <th class="pr-0 text-center">
                                                    IMEI
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Номер устройства
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Номер устройства 2
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Дата подключения
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Дата отключения
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Тариф
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Действие
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody id="client_equipments">
                                            @foreach($equipments as $equipment)
                                                <tr class="equipment_item equipment_{{ $equipment->id }} @if($equipment->status == \App\Models\Equipment::DEACTIVE) deactive_equipment @endif">
                                                    <td class="text-left pl-0">
                                                        <span
                                                            class="label label-xl label-dot dot_{{ $equipment->id}} {{ $equipment->getDotStatus() }} mr-2"></span>
                                                        {{ $equipment->object }}
                                                    </td>
                                                    <td class="text-center pl-0">
                                                        {{ $equipment->device }}
                                                    </td>
                                                    <td class="text-center pl-0">
                                                        {{ $equipment->imei }}
                                                    </td>
                                                    <td class="text-center pl-0">
                                                        {{ $equipment->phone ? '+' . $equipment->phone : '' }}
                                                    </td>
                                                    <td class="text-center pl-0">
                                                        {{ $equipment->phone2 ? '+' . $equipment->phone2 : '' }}
                                                    </td>
                                                    <td class="text-center pl-0 date_start">
                                                        {{ $equipment->date_start ? date('d.m.Y', strtotime($equipment->date_start)) : '' }}
                                                    </td>
                                                    <td class="text-center pl-0 date_end">
                                                        {{ $equipment->date_end ? date('d.m.Y', strtotime($equipment->date_end)) : '' }}
                                                    </td>
                                                    <td class="text-center pl-0">
                                                        {{ $equipment->getTariff() }}
                                                    </td>
                                                    <th class="pr-0 text-center">
                                                        <div class="card-toolbar">
                                                            <div class="dropdown dropdown-inline">
                                                                <a href="#"
                                                                   class="btn btn-clean btn-hover-light-primary btn-sm btn-icon"
                                                                   data-toggle="dropdown" aria-haspopup="true"
                                                                   aria-expanded="false">
                                                                    <i class="ki ki-bold-more-ver"></i>
                                                                </a>
                                                                <div
                                                                    class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                                    <!--begin::Navigation-->
                                                                    <ul class="navi navi-hover">
                                                                        <li class="navi-item">
                                                                            <a href="#"
                                                                               class="navi-link start_equipment"
                                                                               data-equipment_id="{{ $equipment->id }}">
                                                                                <span
                                                                                    class="navi-text equipment_status_text_{{ $equipment->id }}">Подключить</span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="navi-item">
                                                                            <a href="#"
                                                                               class="navi-link end_equipment"
                                                                               data-equipment_id="{{ $equipment->id }}">
                                                                                <span
                                                                                    class="navi-text equipment_status_text_{{ $equipment->id }}">Отключить</span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="navi-item">
                                                                            <a href="#"
                                                                               class="navi-link deactive_equipment"
                                                                               data-equipment_id="{{ $equipment->id }}">
                                                                                <span
                                                                                    class="navi-text equipment_status_text_{{ $equipment->id }}">Деактивирван</span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="navi-item">
                                                                            <a href="#" class="navi-link show_equipment"
                                                                               data-equipment_id="{{ $equipment->id }}">
                                                                                <span
                                                                                    class="navi-text">Редактировать</span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="navi-item">
                                                                            <a href="#" class="navi-link move_equipment"
                                                                               data-equipment_id="{{ $equipment->id }}">
                                                                                <span
                                                                                    class="navi-text">Переместить</span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="navi-item">
                                                                            <a href="#"
                                                                               class="navi-link delete_equipment"
                                                                               data-equipment_id="{{ $equipment->id }}">
                                                                                <span class="navi-text">Удалить</span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </th>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!--end::Body-->
                        </div>
                    </div>
                </div>
                <!--end::Content-->
            </div>
        </div>
    </div>
    <!--end::Entry-->

    @include('admin.client.modals.equipment.create_quipment')
    @include('admin.client.modals.equipment.edit_quipment')
    @include('admin.client.modals.equipment.add_quipment')
    @include('admin.client.modals.equipment.move_quipment')

    @include('admin.client.modals.tariff.add_client_tariff')
    @include('admin.client.modals.tariff.update_client_tariff')

    @include('admin.client.modals.sensor.add_sensor')
    @include('admin.client.modals.sensor.edit_sensor')

    @include('admin.client.modals.installation.add_installation')
    @include('admin.client.modals.installation.edit_installation')

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
