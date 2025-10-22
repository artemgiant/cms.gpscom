@forelse($freeEquipments as $equipment)
    <tr>
        <td class="text-center">
            <div class="checkbox-inline" style="margin-left: 20px">
                <label class="checkbox">
                    <input type="checkbox" name="equipments[]" value="{{ $equipment->id }}"/>
                    <span></span></label>
            </div>
        </td>
        <td class="text-center">{{ $equipment->object }}</td>
        <td class="text-center">{{ $equipment->device }}</td>
        <td class="text-center">{{ $equipment->imei }}</td>
        <td class="text-center">{{ '+' . $equipment->phone }}</td>
    </tr>
@empty
    <tr>
        <td class="text-center" colspan="5"><h3>Нет даних</h3></td>
    </tr>
@endforelse
