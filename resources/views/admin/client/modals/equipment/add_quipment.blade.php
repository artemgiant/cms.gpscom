<!-- Modal -->
<div class="modal fade" id="addEquipment" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить устройство</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body" style="height: 500px;">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-7">
                            <form method="GET">
                                <div class="input-icon">
                                    <input id="search_free_equipments" type="text" name="search"
                                           data-type="{{ request()->get('type') }}"
                                           class="form-control form-control-solid"
                                           placeholder="Поиск" value="{{ request()->input('search') }}"/>
                                    <span><i class="flaticon2-search-1 text-muted"></i></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <form id="form_free_equipments" action="{{ route('client.free_equipment.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <div class="row">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center" width="5%" title="Field #1">#</th>
                                <th class="text-center" width="5%" title="Field #1">Объект</th>
                                <th class="text-center" width="10%" title="Field #2">Устройство</th>
                                <th class="text-center" width="40%" title="Field #3">IMEI</th>
                                <th class="text-center" width="20%" title="Field #4">Номер устройства</th>
                            </tr>
                            </thead>
                            <tbody id="free_equipments">
                            @forelse($freeEquipments as $equipment)
                                <tr>
                                    <td class="text-center">
                                        <div class="checkbox-inline" style="margin-left: 20px">
                                            <label class="checkbox">
                                                <input type="checkbox" name="equipments[]"
                                                       value="{{ $equipment->id }}"/>
                                                <span></span></label>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $equipment->object }}</td>
                                    <td class="text-center">{{ $equipment->device }}</td>
                                    <td class="text-center">{{ $equipment->imei }}</td>
                                    <td class="text-center">{{ isset($equipment->phone) ? '+' . $equipment->phone : '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="5"><h3>Нет даних</h3></td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-light-primary font-weight-bold" data-dismiss="modal">
                    Закрыть
                </button>
                <button form="form_free_equipments" type="submit" class="btn btn-lg btn-primary mr-2">Добавить</button>
            </div>
        </div>
    </div>
</div>



@section('js_after')
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/bootstrap-datetimepicker.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/jquery-mask.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/forms/widgets/bootstrap-select.js') }}"></script>
    <script src="{{ asset('super_admin/js/pages/crud/file-upload/image-input.js') }}"></script>
@endsection
