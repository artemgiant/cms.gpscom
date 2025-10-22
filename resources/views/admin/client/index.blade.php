@extends('admin.layouts.app')
@section('title')
  <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Клиенты</h5>
  <a class="w-100"  href="{{ URL::previous() }}"><i class="las la-angle-left">Назад</i></a>
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
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <div class="mb-7">
                                <form method="GET">
                                    <div class="input-icon">
                                        <input id="search_clients" type="text" name="search"
                                               data-type="{{ request()->get('type') }}"
                                               class="form-control"
                                               placeholder="Поиск" value="{{ request()->input('search') }}"/>
                                        <span><i class="flaticon2-search-1 text-muted"></i></span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-2 text-center">
                            <div class="mb-7">
                                <button data-toggle="modal" data-target="#createClient"
                                        class="btn btn-success font-weight-bold">
                                    Добавить клиента
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center">
                            <thead>
                            <tr>
                                <th class="pl-0 text-center">
                                    Наименование клиента
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
                                    Контактнок лицо
                                </th>
                                <th class="pr-0 text-center">
                                    Контакты
                                </th>
                                <th class="pr-0 text-center">
                                    Менеджер
                                </th>
                                <th class="pr-0 text-center">
                                    Кол-во
                                </th>
                                <th class="pr-0 text-center">
                                    Статус
                                </th>
                                <th class="pr-0 text-center">
                                    Тип
                                </th>
                                <th class="pr-0 text-center">
                                    Номер бухгалтера
                                </th>
                                <th class="pr-0 text-center">
                                    Email
                                </th>
                                <th class="pr-0 text-center">
                                    Действие
                                </th>
                            </tr>
                            </thead>
                            <tbody id="items">
                            @foreach($clients as $client)
                                <tr class="client_tr" data-route="{{ route('client', $client->id) }}">
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
                                        @if(isset($client->contract_date))
                                            {{ date('d.m.Y', strtotime($client->contract_date)) }}
                                        @endif
                                    </td>
                                    <td class="text-center pr-0 td">
                                        {{ $client->person }}
                                    </td>
                                    <td class="text-center pr-0 td">
                                        {{ $client->phone ? '+' . $client->phone : '' }}
                                    </td>
                                    <td class="text-center pr-0 td">
                                        @if($client->getManager)
                                            {{ $client->getManager->name . ' ' . $client->getManager->surname }}
                                        @endif
                                    </td>
                                    <td class="text-center pr-0 td">
                                        {{ $client->getActiveEquipments() }}
                                    </td>
                                    <td class="text-center pr-0 td">
                                        {{ $client->status ? 'Подключено' : 'Отключено' }}
                                    </td>
                                    <td class="text-center pr-0 td">
                                        {{ $client->getClientType() }}
                                    </td>
                                    <td class="text-center pr-0 td">
                                        {{ $client->accountant_phone }}
                                    </td>
                                    <td class="text-center pr-0 td">
                                        {{ $client->email }}
                                    </td>
                                    <td class="text-center pr-0">
                                        <form action="{{ route('client.destroy') }}" method="POST">
                                            <a href="javascript:;"
                                               class="btn btn-sm btn-clean btn-icon clientEditBtn"
                                               data-toggle="modal" data-target="#editClient"
                                               data-id="{{ $client->id }}">
                                                <i class="las la-edit"></i>
                                            </a>
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="id" value="{{ $client->id }}">
                                            <button type="submit" class="btn btn-sm btn-clean btn-icon btn_delete"
                                                    onclick="return confirm('Вы действительно хотите удалить клиента {{ $client->name }}?')"
                                                    title="Delete"><i class="las la-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                {{ $clients->appends([
                        'type' =>  request()->get('type'),
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

    @include('admin.client.modals.create')
    @include('admin.client.modals.edit')
@endsection
