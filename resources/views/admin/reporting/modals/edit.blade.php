<!-- Modal -->
<div class="modal fade" id="editReport" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Редактировать отчет</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>

            <form id="form_report_update" action="{{ route('reporting.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" id="reportId" name="id">
                <input type="hidden" id="client_id"name="client_id">

                <div class="modal-body">

                    <div class="row">
    
                    <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Тип:</label>
                                    <div class="col-sm-12">
                                        <select class="form-control" name="client_type" id="reportType" data-size="7"
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
                                           class="col-sm-12 col-form-label font-weight-bold">Компания:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="name" id="reportName" placeholder="Наименование клиента" required/>
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
                                        <input type="text" class="form-control" name="account" id="reportAccount" placeholder="Аккаунт" required/>
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
                                        <input type="text" class="form-control" name="contract_number" id="reportContractNumber" placeholder="Номер договора" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="row">
                        
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Начальное кол-во:</label>
                                    <div class="col-sm-12">
                                        <input type="select" class="form-control" name="start" id="startCount" placeholder="Стоимость" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Конечное кол-во:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="end" id="endCount" placeholder="Стоимость" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Дата договора:</label>
                                    <div class="col-sm-12">
                                        <div class="input-group date" id="kt_datetimepicker_20" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input reportContractDate" name="contract_date"
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
                        <!-- <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller"
                                           class="col-sm-12 col-form-label font-weight-bold">Стоимость:</label>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" name="price" id="reportPrice" placeholder="Стоимость" required/>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-lg btn-light-primary font-weight-bold" data-dismiss="modal">
                        Закрыть
                    </button>
                    <button type="button" class="btn btn-lg btn-primary mr-2 editReportBtn">Сохранить</button>
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
