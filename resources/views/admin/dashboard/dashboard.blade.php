@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Главная</h5>
@endsection
@section('content')
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            @include('admin.layouts.includes.messages')
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->

@endsection

