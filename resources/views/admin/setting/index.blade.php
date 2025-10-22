@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Настройки</h5>
    <a class="w-100"  href="{{ URL::previous() }}"><i class="las la-angle-left">Назад</i></a>
@endsection
@section('content')
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            @include('admin.layouts.includes.messages')

            <div class="card card-custom gutter-b mt-5">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            Настройки
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-line mb-5">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#users">
                                <span class="nav-icon"></span>
                                <span class="nav-text">Пользователи</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#wialon_setting">
                                <span class="nav-icon"></span>
                                <span class="nav-text">Настройки Wialon</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-5" id="myTabContent">
                        <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users">
                            <div class="col">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-10"></div>
                                        <div class="col-2 text-center">
                                            <div class="mb-7">
                                                <button data-toggle="modal" data-target="#createUser"
                                                        class="btn btn-success font-weight-bold">
                                                    Добавить пользователя
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center">
                                            <thead>
                                            <tr>
                                                <th class="pl-0 text-center">
                                                    Id
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Роль
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Имя
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Email
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Дата создания
                                                </th>
                                                <th class="pr-0 text-center">
                                                    Действие
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody id="items">
                                            @foreach($users as $user)
                                                <tr>
                                                    <td class="text-center pl-0 td">
                                                        {{ $user->id }}
                                                    </td>
                                                    <td class="text-center pr-0 td">
                                                        {{ $user->role->name }}
                                                    </td>
                                                    <td class="text-center pr-0 td">
                                                        {{ $user->name . ' ' . $user->surname }}
                                                    </td>
                                                    <td class="text-center pr-0 td">
                                                        {{ $user->email }}
                                                    </td>
                                                    <td class="text-center pr-0 td">
                                                        {{ date('d.m.Y', strtotime($user->created_at)) }}
                                                    </td>
                                                    <td class="text-center pr-0">
                                                        <form action="{{ route('user.destroy') }}" method="POST">
                                                            <a href="javascript:;"
                                                               class="btn btn-sm btn-clean btn-icon userEditBtn"
                                                               data-toggle="modal" data-target="#editUser"
                                                               data-id="{{ $user->id }}">
                                                                <i class="las la-edit"></i>
                                                            </a>
                                                            @csrf
                                                            <button type="button"
                                                                    data-user_id="{{ $user->id }}"
                                                                    data-toggle="modal"
                                                                    data-target="#deleteUserModal"
                                                                    class="btn btn-sm btn-clean btn-icon btn_delete deleteUserBtn"
                                                                    title="Delete"><i class="las la-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="tab-pane fade" id="wialon_setting" role="tabpanel" aria-labelledby="wialon_setting">
                            <form action="{{ route('update_setting') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>API Токен авторизации</label>
                                        <input type="text" class="form-control" name="access_token"
                                               value="{{ $setting->value }}" required/>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <a href="https://hosting.wialon.com/login.html?access_type=-1&duration=0&lang=ru&redirect_uri={{ route('token_redirect') }}"
                                           class="btn btn-primary mr-2 mt-8">Подключиться к Wialon</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-2">
                                        <label class="mr-2">Отключить синхронизацию:</label>
                                    </div>
                                    <div class="form-group col-3">
                                         <span class="switch switch-sm">
                                            <label>
                                                <input type="checkbox" name="status" @if($setting->status == true) checked="checked" @endif/>
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Сохранить</button>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        @include('admin.user.modals.create')
        @include('admin.user.modals.edit')
        @include('admin.user.modals.delete')
        @endsection

        @section('js_after')
            <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/input-mask.js') }}"></script>
            <script src="{{ asset('super_admin/js/pages/crud/ktdatatable/base/html-table.js') }}"></script>
            <script src="{{ asset('super_admin/plugins/custom/ckeditor/ckeditor-classic.bundle.js') }}"></script>
@endsection
