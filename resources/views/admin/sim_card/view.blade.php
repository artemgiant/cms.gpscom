@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Sim-карты</h5>
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
                <div class="card-body pb-3">
                <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center">
                            <thead>
                            <tr>
                                <th class="pr-0 text-center">
                                    Номер
                                </th>
                                <th class="pr-0 text-center">
                                    Действие
                                </th>
                                <th class="pr-0 text-center">
                                    Дата действии
                                </th>
                                <th class="pr-0 text-center">
                                    Оборудование
                                </th>
                                <th class="pr-0 text-center">
                                  Новый акаунт
                                </th>
                                <th class="pr-0 text-center">
                                  Пользователь
                                </th>
                                <th class="pr-0 text-center">
                                </th>
                            </tr>
                            </thead>
                            <tbody id="items">
                            @foreach($histories as $history)
                                <tr>
                                    <td class="text-center pr-0">
                                        {{ '+' . $card->phone }}
                                    </td>
                                    <td class="text-center pr-0">
                                        {{ $history->operation }}
                                    </td>
                                    <td class="text-center pr-0">
                                        {{ $history->operation_date_time }}
                                    </td>
                                    <td class="text-center pr-0">
                                        {{ $history->getEquipment() }}
                                    </td>
                                    <td class="text-center pr-0">
                                        {{ $history->getNewAcc() }}
                                    </td>
                                    <td class="text-center pr-0">
                                        {{ $history->getUser() }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                {{ $histories->appends([
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
@endsection
