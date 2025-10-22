@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Операторы</h5>
    <a class="w-100" href="{{ URL::previous() }}"><i class="las la-angle-left">Назад</i></a>
    <div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-5 bg-gray-200"></div>
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
                                <button data-toggle="modal" data-target="#createOperator"
                                        class="btn btn-success font-weight-bold">
                                    Добавить оператора
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
                                    Действие
                                </th>
                            </tr>
                            </thead>
                            <tbody id="items">
                            @foreach($operators as $operator)
                                <tr>
                                    <td class="text-center pr-0">
                                        {{ $operator->title }}
                                    </td>
                                    <td class="text-center pr-0">
                                        {{ date('Y-m-d H:i', strtotime($operator->created_at)) }}
                                    </td>
                                    <td class="text-center pr-0">
                                        <a href="javascript:;" class="btn btn-sm btn-clean btn-icon operatorEditBtn"
                                           data-toggle="modal" data-target="#editOperator"
                                           data-id="{{ $operator->id }}">
                                            <i class="las la-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
    @include('admin.operator.modals.create')
    @include('admin.operator.modals.edit')
@endsection
