@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Тарифы</h5>
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
                    <div class="row">
                        <div class="col-10"></div>
                        <div class="col-2 text-center">
                            <div class="mb-7">
                                <button data-toggle="modal" data-target="#createTariff" class="btn btn-success font-weight-bold">
                                    Добавить тариф
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center">
                            <thead>
                            <tr>
                                <th class="pr-0 text-center">
                                    Тариф
                                </th>
                                <th class="pr-0 text-center">
                                    Стаимость
                                </th>
                                <th class="pr-0 text-center">
                                    Оператор
                                </th>
                                <th class="pr-0 text-center">
                                    Действие
                                </th>
                            </tr>
                            </thead>
                            <tbody id="items">
                            @foreach($tariffs as $tariff)
                                <tr>
                                    <td class="text-center pr-0">
                                        {{ $tariff->title }}
                                    </td>
                                    <td class="text-center pr-0">
                                        {{ $tariff->price }}
                                    </td>
                                    <td class="text-center pr-0">
                                        {{ isset($tariff->operator) ? $tariff->operator->title : '' }}
                                    </td>
                                    <td class="text-center pr-0">
                                        <form action="{{ route('tariff.destroy') }}" method="POST">
                                            <a href="javascript:;" class="btn btn-sm btn-clean btn-icon tariffEditBtn"
                                               data-toggle="modal" data-target="#editTariff"
                                               data-id="{{ $tariff->id }}">
                                                <i class="las la-edit"></i>
                                            </a>
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="id" value="{{ $tariff->id }}">
                                            <button type="submit" class="btn btn-sm btn-clean btn-icon btn_delete"
                                                    onclick="return confirm('Вы действительно хотите удалить тариф {{ $tariff->title }}?')"
                                                    title="Delete"><i class="las la-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                {{ $tariffs->appends([
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
        @include('admin.tariff.modals.create')
        @include('admin.tariff.modals.edit')
@endsection
