<!-- Modal -->
<div class="modal fade" id="updateClientTariff" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Редактировать тариф</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>

            <form action="{{ route('client.tariff.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="clients_tariff_id" id="clientsTariffId">
                <input type="hidden" name="client_id" id="clientId" value="{{ $client->id }}">
                <input type="hidden" name="tariff_id" id="tariffId">
                <div class="modal-body">

                    <div class="row">
                        <div class="col">
                            <div class="row d-flex justify-content-around">
                                <div class="form-group w-100">
                                    <label for="createSeller" class="col-sm-12 col-form-label font-weight-bold">Стоимость:</label>
                                    <div class="col-sm-12">
                                        <input type="number" min="0" step="0.01" class="form-control" name="price" id="tariffPrice" placeholder="Стоимость" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-lg btn-light-primary font-weight-bold" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-lg btn-light-danger font-weight-bold clientTariffDelete">Удалить тариф</button>
                    <button type="submit" class="btn btn-lg btn-primary mr-2">Сохранить</button>
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
