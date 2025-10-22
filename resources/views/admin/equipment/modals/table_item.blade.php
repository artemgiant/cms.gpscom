@foreach($equipments as $equipment)
    <tr>
        <td class="text-left">
            <span
                class="label label-xl label-dot {{ $equipment->getDotStatus() }} mr-2"></span>
            {{ $equipment->object }}
        </td>
        <td class="text-center pl-0">
            {{ $equipment->device }}
        </td>
        <td class="text-center pl-0">
            {{ $equipment->imei }}
        </td>
        <td class="text-center pl-0">
            {{ $equipment->phone ? '+' . $equipment->phone : '' }}
        </td>
        <td class="text-center pl-0">
            {{ $equipment->phone2 ? '+' . $equipment->phone2 : '' }}
        </td>
        <td class="text-center pl-0">
            {{ $equipment->date_start ? date('d.m.Y', strtotime($equipment->date_start)) : ''}}
        </td>
        <td class="text-center pl-0">
            {{ $equipment->date_end ? date('d.m.Y', strtotime($equipment->date_end)) : '' }}
        </td>
        <td class="text-center pl-0">
            @if($equipment->client)
                <a href="{{ route('client', $equipment->client->id) }}">
                    {{ $equipment->client->name }}
                </a>
            @endif
        </td>
        <td class="text-center pr-0">
            <form action="{{ route('equipment.destroy') }}" method="POST">
                <a href="javascript:;"
                   class="btn btn-sm btn-clean btn-icon equipmentEditBtn"
                   data-toggle="modal" data-target="#editEquipment"
                   data-id="{{ $equipment->id }}">
                    <i class="las la-edit"></i>
                </a>
                @if(auth()->user()->role_id == \App\Models\User::ADMIN)
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $equipment->id }}">
                    <button type="submit" class="btn btn-sm btn-clean btn-icon btn_delete"
                            onclick="return confirm('Вы действительно хотите удалить устройство {{ $equipment->title }}?')"
                            title="Delete"><i class="las la-trash"></i>
                    </button>
                @endif
            </form>
        </td>
    </tr>
@endforeach
