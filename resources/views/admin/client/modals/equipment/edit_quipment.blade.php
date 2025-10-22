<!-- Modal -->
<div class="modal fade" id="updateEquipment" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Редактировать устройство</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">
                        <div class="card p-5">
                            <form id="form_equipment_update" action="{{ route('equipment.update') }}"
                                  method="POST"
                                  enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="id" id="equipmentId">
                                <input type="hidden" name="client_id" value="{{ $client->id }}">

                                <div class="row">
                                    <div class="col">
                                        <div class="row d-flex justify-content-around">
                                            <div class="form-group w-100">
                                                <label for="createSeller"
                                                       class="col-sm-12 col-form-label font-weight-bold">Дата
                                                    подключения:</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group date" id="kt_datetimepicker_11"
                                                         data-target-input="nearest">
                                                        <input type="text"
                                                               class="form-control datetimepicker-input equipmentDateStart"
                                                               name="date_start"
                                                               placeholder="Дата подключения"
                                                               data-target="#kt_datetimepicker_11"/>
                                                        <div class="input-group-append"
                                                             data-target="#kt_datetimepicker_11"
                                                             data-toggle="datetimepicker">
                                        <span class="input-group-text">
                                            <i class="ki ki-calendar"></i>
                                        </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="row d-flex justify-content-around">
                                            <div class="form-group w-100">
                                                <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Дата отключения:</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group date" id="kt_datetimepicker_20"
                                                         data-target-input="nearest">
                                                        <input type="text"
                                                               class="form-control datetimepicker-input equipmentDateEnd"
                                                               name="date_end"
                                                               placeholder="Дата отключения"
                                                               data-target="#kt_datetimepicker_20"/>
                                                        <div class="input-group-append"
                                                             data-target="#kt_datetimepicker_20"
                                                             data-toggle="datetimepicker">
                                                          <span class="input-group-text">
                                                              <i class="ki ki-calendar"></i>
                                                          </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="row d-flex justify-content-around">
                                            <div class="form-group w-100">
                                                <label for="createSeller"
                                                       class="col-sm-12 col-form-label font-weight-bold">Объект:</label>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" name="object"
                                                           id="equipmentObject"
                                                           placeholder="Объект"
                                                           required/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="row d-flex justify-content-around">
                                            <div class="form-group w-100">
                                                <label for="createSeller"
                                                       class="col-sm-12 col-form-label font-weight-bold">Устройство:</label>
                                                <div class="col-sm-12">
                                                    <div class="typeahead">
                                                        <input class="form-control equipmentDevice" name="device"
                                                               id="kt_typeahead_1"
                                                               type="text" dir="ltr" placeholder="Устройство"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="row d-flex justify-content-around">
                                            <div class="form-group w-100">
                                                <label for="createSeller"
                                                       class="col-sm-12 col-form-label font-weight-bold">IMEI:</label>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" name="imei"
                                                           id="equipmentImei"
                                                           placeholder="IMEI"
                                                           required/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="row d-flex justify-content-around">
                                            <div class="form-group w-100">
                                                <label for="createSeller"
                                                       class="col-sm-12 col-form-label font-weight-bold">Номер
                                                    устройства:</label>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control phone equipmentPhone"
                                                           name="phone"
                                                           placeholder="Номер устройства" id="kt_phone_input3" required/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="row d-flex justify-content-around">
                                            <div class="form-group w-100">
                                                <label for="createSeller"
                                                       class="col-sm-12 col-form-label font-weight-bold">Номер
                                                    устройства
                                                    2:</label>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control phone equipmentPhone2"
                                                           name="phone2"
                                                           placeholder="Номер устройства 2" id="kt_phone_input3"/>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col">
                                        <div class="row d-flex justify-content-around">
                                            <div class="form-group w-100">
                                                <label for="createSeller"
                                                       class="col-sm-12 col-form-label font-weight-bold">Тариф:</label>
                                                <div class="col-sm-12">
                                                    <select class="form-control" name="tariff_id" data-size="7"
                                                            id="equipmentTariff"
                                                            data-live-search="true">
                                                        @foreach($tariffs as $tariff)
                                                            <option
                                                                value="{{ $tariff->id }}">{{ $tariff->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-lg btn-light-primary font-weight-bold"
                                            data-dismiss="modal">
                                        Закрыть
                                    </button>
                                    <button type="button" class="btn btn-lg btn-primary mr-2 updateEquipmentBtn">
                                        Сохранить
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="card card-custom gutter-b ">
                            <div class="card-header card-header-tabs-line">
                                <div class="card-toolbar">
                                    <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab"
                                               href="#kt_tab_pane_1_2">Датчики</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_2_2">Монтажниє
                                                робиты</a>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="kt_tab_pane_1_2" role="tabpanel"
                                         aria-labelledby="kt_tab_pane_2">

                                        <div class="card card-custom">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h3 class="card-label">Датчики</h3>
                                                </div>
                                                <div class="card-toolbar">
                                                    <a href="#" class="btn btn-success font-weight-bold"
                                                       data-toggle="modal"
                                                       data-target="#addSensor">
                                                        <i class="ki ki-solid-plus"></i>Добавить</a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%" title="Field #1">Имя</th>
                                                        <th class="text-center" width="5%" title="Field #2">Тип</th>
                                                        <th class="text-center" width="10%" title="Field #3">Id</th>
                                                        <th class="text-center" width="10%" title="Field #4">Действие
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="sensors_items"></tbody>
                                                </table>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="tab-pane fade" id="kt_tab_pane_2_2" role="tabpanel"
                                         aria-labelledby="kt_tab_pane_2">

                                        <div class="card card-custom">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h3 class="card-label">Монтажниє робиты</h3>
                                                </div>
                                                <div class="card-toolbar">
                                                    <a href="#" class="btn btn-success font-weight-bold"
                                                       data-toggle="modal"
                                                       data-target="#addInstallation">
                                                        <i class="ki ki-solid-plus"></i>Добавить</a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%" title="Field #1">Дата</th>
                                                        <th class="text-center" width="5%" title="Field #2">Тип</th>
                                                        <th class="text-center" width="10%" title="Field #3">
                                                            Коментарий
                                                        </th>
                                                        <th class="text-center" width="10%" title="Field #4">Стоимость
                                                        </th>
                                                        <th class="text-center" width="10%" title="Field #5">Действие
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="installations_items"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@section('js_after')
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/bootstrap-datetimepicker.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/jquery-mask.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/bootstrap-select.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/file-upload/image-input.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/typeahead.js') }}"></script>
@endsection
