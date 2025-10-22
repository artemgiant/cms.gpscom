<!-- Modal -->
<div class="modal fade" id="editInstallation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Монтажные работы</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>

            <form id="form_edit_installation" action="{{ route('client.installation.update') }}" method="POST"
                  enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="installation_id" id="installationId">
                <input type="hidden" name="equipment_id" id="installationEditEquipmentId">
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <div class="modal-body">

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Дата:</label>
                                    <div class="col-sm-12">
                                        <div class="input-group date" id="kt_datetimepicker_30"
                                             data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input installationDateCreate"
                                                   name="date_create"
                                                   placeholder="Дата" data-target="#kt_datetimepicker_30"/>
                                            <div class="input-group-append" data-target="#kt_datetimepicker_30"
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
                                           class="col-sm-12 col-form-label font-weight-bold">Тип:</label>
                                    <div class="col-sm-12">
                                        <select class="form-control" name="type" id="installationType" data-size="7" data-live-search="true">
                                            @foreach(\App\Services\InstallationService::getType() as $id => $type)
                                                <option value="{{ $id }}">{{ $type }}</option>
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
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Коментарий:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="comment" id="installationComment"
                                               placeholder="Коментарий"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Стоимость:</label>
                                    <div class="col-sm-12">
                                        <input type="number" min="0" step="0.01" class="form-control"
                                               name="price" id="installationPrice"
                                               placeholder="Стоимость"/>
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
                    <button type="button" class="btn btn-lg btn-primary mr-2 editInstallationBtn">Сохранить</button>
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
