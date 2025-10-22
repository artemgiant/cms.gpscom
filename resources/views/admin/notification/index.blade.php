@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Уведомления</h5>
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
                                        <input id="search_notification" type="text" name="search"
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
                                <a href="{{ route('notifications.destroy') }}" class="btn btn-danger font-weight-bold">
                                    Удалить все
                                </a>
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
                                    Сообщение
                                </th>
                                <th class="pr-0 text-center">
                                    Статус
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
                            @foreach($notifications as $notification)
                                <tr>
                                    <td class="text-center pl-0 td">
                                        {{ $notification->id }}
                                    </td>
                                    <td class="text-center pr-0 td">
                                        @if($notification->client_id)
                                            <a href="{{ route('client', $notification->client_id) }}">{{ $notification->message }}</a>
                                        @elseif(isset($notification->equipment->object))
                                            <a href="{{ route('equipments', ['q' => $notification->equipment->object]) }}">{{ $notification->message }}</a>
                                        @elseif($notification->sim_card_id)
                                            <a href="{{ route('sim_cards', ['q' => $notification->sim_card->phone ?? '']) }}">{{ $notification->message }}</a>
                                        @else
                                            {{ $notification->message }}
                                        @endif
                                    </td>
                                    <td class="text-center pr-0 td">
                                        <div class="btn-group" style="width: 150px;">
                                            <select id="status" class="form-control selectpicker notification_status"
                                                    data-style="@if($notification->status) btn-primary @else btn-success @endif"
                                                    data-id="{{ $notification->id }}">
                                                <option value="opened"
                                                        @if($notification->status == 1) selected @endif>Новый
                                                </option>
                                                <option value="closed"
                                                        @if($notification->status == 0) selected @endif>Прочтенный
                                                </option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center pr-0 td">
                                        {{ date('d.m.Y H:i:s', strtotime($notification->created_at)) }}
                                    </td>
                                    <td class="text-center pr-0">
                                        <form action="{{ route('notification.destroy') }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="id" value="{{ $notification->id }}">
                                            <button type="submit" class="btn btn-sm btn-clean btn-icon btn_delete"
                                                    onclick="return confirm('Вы действительно хотите удалить сообщение?')"
                                                    title="Удаліть"><i class="las la-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                {{ $notifications->appends([
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
@endsection
