<!-- Modal -->
<div class="modal fade" id="editClient" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Редактировать клиента</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>

            <form id="form_client_update" action="{{ route('client.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" id="clientId" name="id">
                <div class="modal-body">

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Тип:</label>
                                    <div class="col-sm-12">
                                        <select class="form-control" name="client_type" id="clientType" data-size="7"
                                                data-live-search="true">
                                            <option value="ip">ИП</option>
                                            <option value="fl">ФЛ</option>
                                            <option value="too">ТОО</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Наименование клиента:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="name" id="clientName" placeholder="Наименование клиента" required/>
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
                                           class="col-sm-12 col-form-label font-weight-bold">Аккаунт:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="account" id="clientAccount" placeholder="Аккаунт" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Номер договора:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="contract_number" id="clientContractNumber" placeholder="Номер договора" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Дата договора:</label>
                                    <div class="col-sm-12">
                                        <div class="input-group date" id="kt_datetimepicker_20" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input clientContractDate" name="contract_date"
                                                   placeholder="Дата договора" data-target="#kt_datetimepicker_20"/>
                                            <div class="input-group-append" data-target="#kt_datetimepicker_20"
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
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Контактное лицо:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="person" id="clientPerson" placeholder="Контактное лицо" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Контакты:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control phone clientPhone" name="phone"
                                               placeholder="Контакти" id="kt_phone_input2" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Ответственный менеджер:</label>
                                    <div class="col-sm-12">
                                        <select class="form-control" name="manager" id="clientManager" data-size="7"
                                                data-live-search="true">
                                            @foreach(\App\Services\ClientService::getClientManagers() as $manager)
                                                <option value="{{ $manager->id }}">{{ $manager->name . ' ' . $manager->surname }}</option>
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
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Номер бухгалтера:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control clientAccountantPhone" name="accountant_phone"
                                               placeholder="Номер бухгалтера" id="kt_phone_input3" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Email:</label>
                                    <div class="col-sm-12">
                                        <input type="email" class="form-control" name="email" id="clientEmail"
                                               placeholder="Email" required/>
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
                                           class="col-sm-12 col-form-label font-weight-bold">Id клиента c Wialon:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" id="clientIdWialon" name="client_id"
                                               placeholder="Id клиента c Wialon" required/>
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
                    <button type="button" class="btn btn-lg btn-primary mr-2 editClientBtn">Сохранить</button>
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
