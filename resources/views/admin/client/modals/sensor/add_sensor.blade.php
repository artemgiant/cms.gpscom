<!-- Modal -->
<div class="modal fade" id="addSensor" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Добавить датчик</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>

            <form id="form_add_sensor" action="{{ route('client.sensor.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="equipment_id" id="sensorEquipmentId">
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <div class="modal-body">

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Имя:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="name" placeholder="Имя"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Тип:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="type" placeholder="Тип"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Id:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="sensor_id" placeholder="Тип"/>
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
                    <button type="button" class="btn btn-lg btn-primary mr-2 addSensorBtn">Сохранить</button>
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
