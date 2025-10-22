<!-- Modal -->
<div class="modal fade" id="editEquipment" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Редактировать устройство</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>

            <form id="form_equipment_update" action="{{ route('equipment.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" id="equipmentId" name="id">
                <div class="modal-body">

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Объект:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="object" id="equipmentObject" placeholder="Объект" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Устройство:</label>
                                    <div class="col-sm-12">
                                        <div class="typeahead">
                                            <input class="form-control equipmentDevice" name="device" id="kt_typeahead_1" type="text" dir="ltr" placeholder="Устройство" />
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
                                        <input type="text" class="form-control" name="imei" id="equipmentImei" placeholder="IMEI" required/>
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
                                        <select class="form-control" name="tariff_id" id="equipmentTariff" data-size="7" data-live-search="true">
                                            @foreach($tariffs as $tariff)
                                                <option value="{{ $tariff->id }}">{{ $tariff->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Номер
                                        устройства:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control phone equipmentPhone" name="phone"
                                               placeholder="Номер устройства" id="kt_phone_input3" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Номер устройства
                                        2:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control phone equipmentPhone2" name="phone2"
                                               placeholder="Номер устройства 2" id="kt_phone_input3"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-lg btn-light-primary font-weight-bold" data-dismiss="modal">
                        Закрыть
                    </button>
                    <button type="button" class="btn btn-lg btn-primary mr-2 updateEquipmentBtn">Сохранить</button>
                </div>

            </form>

        </div>
    </div>
</div>
@section('js_after')
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/bootstrap-datetimepicker.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/jquery-mask.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/bootstrap-select.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/file-upload/image-input.js') }}"></script>
@endsection
