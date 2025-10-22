@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Оборудование</h5>
    <a class="w-100" href="{{ URL::previous() }}"><i class="las la-angle-left">Назад</i></a>

    <!--end::Title-->
    <!--begin::Separator-->
    <div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-5 bg-gray-200"></div>
    <!--end::Separator-->
@endsection
@section('content')
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">

        <!--begin::Container-->
        <div class="container-fluid">
        @include('admin.layouts.includes.messages')
        <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-body pb-3">
                    <div class="row">
                        <div class="col-9 d-flex align-items-center">
                            <div class="mb-7 w-100">
                                <form method="GET">
                                    <div class="input-icon">
                                        <input id="search_equipments" type="text" name="search"
                                               data-type="{{ request()->get('type') }}"
                                               class="form-control form-control-solid"
                                               placeholder="Поиск" value="{{ request()->input('search') }}"/>
                                        <span><i class="flaticon2-search-1 text-muted"></i></span>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-3 d-flex align-items-center justify-content-end">
                            <div class="mb-7">
                                <a href="{{ route('equipments.check_wialon') }}"
                                        class="btn btn-primary font-weight-bold">
                                    Посмотреть информацию об устройстве
                                </a>
                                <button data-toggle="modal" data-target="#createEquipments"
                                        class="btn btn-success font-weight-bold">
                                    Добавить устройство
                                </button>
                                <a href="{{ route('equipments.export') }}" target="_blank"
                                   class="btn btn-success font-weight-bold">
                                    Экспорт
                                </a>
                            </div>
                        </div>
                    </div>

                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center">
                            <thead>
                            <tr>
                                <th class="pl-0 text-center" width="10%">
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
                                    Владелец
                                </th>
                                <th class="pr-0 text-center">
                                    Действие
                                </th>
                            </tr>
                            </thead>
                            <tbody id="items">
                            @foreach($equipments as $equipment)
                                <tr>
                                    <td class="text-left">
                                        <span
                                            class="label label-xl label-dot {{ $equipment->getDotStatus() }} mr-2"></span>
                                        {{ $equipment->id }} {{ $equipment->object }}
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
                                    <td class="text-center pl-0">
                                        {{ $equipment->date_start ? date('d.m.Y', strtotime($equipment->date_start)) : ''}}
                                    </td>
                                    <td class="text-center pl-0">
                                        {{ $equipment->date_end ? date('d.m.Y', strtotime($equipment->date_end)) : '' }}
                                    </td>
                                    <td class="text-center pl-0">
                                        @if($equipment->client)
                                            <a href="{{ route('client', $equipment->client->id) }}">
                                                {{ $equipment->client->name }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-center pr-0">
                                        <form action="{{ route('equipment.destroy') }}" method="POST">
                                            <a href="javascript:;"
                                               class="btn btn-sm btn-clean btn-icon equipmentEditBtn"
                                               data-toggle="modal" data-target="#editEquipment"
                                               data-id="{{ $equipment->id }}">
                                                <i class="las la-edit"></i>
                                            </a>
                                            @if(auth()->user()->role_id == \App\Models\User::ADMIN)
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $equipment->id }}">
                                                <button type="submit" class="btn btn-sm btn-clean btn-icon btn_delete"
                                                        onclick="return confirm('Вы действительно хотите удалить устройство {{ $equipment->title }}?')"
                                                        title="Delete"><i class="las la-trash"></i>
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                {{ $equipments->appends([
                        'paginate' => request()->get('paginate'),
                        'sort' => request()->get('sort')
                    ])->links('vendor.pagination.super_admin_pagination') }}
                <!--end::Table-->
                </div>

                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
    @include('admin.equipment.modals.create')
    @include('admin.equipment.modals.edit')
@endsection
