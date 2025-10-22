@extends('admin.layouts.app')
@section('title')
    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Sim-карты</h5>
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
                <div class="card-header card-header-tabs-line">
                    <div class="card-toolbar">
                        <form method="GET">
                            <div class="input-icon">
                                <input id="search_sim_cards" type="text" name="search"
                                       data-type="{{ request()->get('type') }}"
                                       class="form-control"
                                       placeholder="Поиск" value="{{ request()->input('search') }}"/>
                                <span><i class="flaticon2-search-1 text-muted"></i></span>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 row flex align-content-center justify-content-end">
                        <button data-toggle="modal" data-target="#createSim"
                                class="btn btn-success font-weight-bold">
                            Добавить Sim
                        </button>
                        <button data-toggle="modal" data-target="#importSim"
                                class="btn btn-success font-weight-bold ml-2">
                            Импортировать Sim-карты
                        </button>
                    </div>
                </div>
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
                                    Статус
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
                            @foreach($simCards as $card)
                                <tr>
                                    <td class="text-center pr-0">
                                        {{ '+' . $card->phone }}
                                    </td>
                                    <td class="text-center pr-0">
                                        {{ $card->getStatus() }}
                                    </td>
                                    <td class="text-center pr-0">
                                        {{ $card->getOperatorTitle() }}
                                    </td>
                                    <td class="text-center pr-0">
                                        <form action="{{ route('sim_card.destroy') }}" method="POST">
                                            <a class="btn btn-sm btn-clean" href="{{ route('sim-card', $card->id) }}">История</a>
                                            <a href="javascript:;" class="btn btn-sm btn-clean btn-icon simdCardEditBtn"
                                               data-toggle="modal" data-target="#editSim"
                                               data-id="{{ $card->id }}">
                                                <i class="las la-edit"></i>
                                            </a>
                                            @if(auth()->user()->role_id == \App\Models\User::ADMIN)
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $card->id }}">
                                                <button type="submit" class="btn btn-sm btn-clean btn-icon btn_delete"
                                                        onclick="return confirm('Вы действительно хотите удалить Sim-карту {{ $card->title }}?')"
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
                {{ $simCards->appends([
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
    @include('admin.sim_card.modals.create')
    @include('admin.sim_card.modals.edit')
    @include('admin.sim_card.modals.import')
@endsection
