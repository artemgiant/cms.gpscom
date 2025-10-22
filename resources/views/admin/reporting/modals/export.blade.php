<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Экспортировать в Excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="export_form" class="form" action="{{ route('reporting.export') }}">
                    <input id="reportingFormType" type="hidden" name="type" value="{{ request()->get('type') ? request()->get('type') : \App\Models\Client::REPORT_PER_MONTH }}">
                    <input id="reportingFormClientType" type="hidden" name="client_type" value="{{ request()->get('client_type') ? request()->get('client_type') : \App\Models\Client::IP }}">
                    <input id="reportingFormMount" type="hidden" name="mount" value="{{ date('Y.m') }}">

                    <div class="form-group row">
                        <label class="col-3 col-form-label">Список полей</label>
                        <div class="col-9 col-form-label">
                            <div class="checkbox-list">
                                <label class="checkbox">
                                    <input type="checkbox" name="rows[client_id]"/>
                                    <span></span>
                                    Wialon id
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" checked="checked" name="rows[name]"/>
                                    <span></span>
                                    Клиент
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" checked="checked" name="rows[phone]"/>
                                    <span></span>
                                    Контакты
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" checked="checked" name="rows[account]"/>
                                    <span></span>
                                    Аккаунт
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" checked="checked" name="rows[contract_number]"/>
                                    <span></span>
                                    Номер договора
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" checked="checked" name="rows[contract_date]"/>
                                    <span></span>
                                    Дата договора
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" name="rows[person]"/>
                                    <span></span>
                                    Контактное лицо
                                </label>

                                <label class="checkbox">
                                    <input type="checkbox" name="rows[manager]"/>
                                    <span></span>
                                    Ответственный менеджер
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" name="rows[accountant_phone]"/>
                                    <span></span>
                                    Номер бухгалтера
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" name="rows[email]"/>
                                    <span></span>
                                    Email
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" name="rows[client_type]"/>
                                    <span></span>
                                    Тип
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Закрыть
                </button>
                <button form="export_form" type="submit" class="btn btn-primary font-weight-bold">Экспортировать</button>
            </div>
        </div>
    </div>
</div>
