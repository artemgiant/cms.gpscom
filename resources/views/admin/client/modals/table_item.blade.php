@foreach($clients as $client)
    <tr class="client_tr" data-route="{{ route('client', $client->id) }}">
        <td class="text-center pl-0 td">
            {{ $client->name }}
        </td>
        <td class="text-center pr-0 td">
            {{ $client->account }}
        </td>
        <td class="text-center pr-0 td">
            {{ $client->contract_number }}
        </td>
        <td class="text-center pr-0 td">
            @if(isset($client->contract_date))
                {{ date('d.m.Y', strtotime($client->contract_date)) }}
            @endif
        </td>
        <td class="text-center pr-0 td">
            {{ $client->person }}
        </td>
        <td class="text-center pr-0 td">
            {{ $client->phone ? '+' . $client->phone : '' }}
        </td>
        <td class="text-center pr-0 td">
            @if($client->getManager)
                {{ $client->getManager->name . ' ' . $client->getManager->surname }}
            @endif
        </td>
        <td class="text-center pr-0 td">
            {{ $client->getActiveEquipments() }}
        </td>
        <td class="text-center pr-0 td">
            {{ $client->status ? 'Подключено' : 'Отключено' }}
        </td>
        <td class="text-center pr-0 td">
            {{ $client->getClientType() }}
        </td>
        <td class="text-center pr-0 td">
            {{ $client->accountant_phone }}
        </td>
        <td class="text-center pr-0 td">
            {{ $client->email }}
        </td>
        <td class="text-center pr-0">
            <form action="{{ route('client.destroy') }}" method="POST">
                <a href="javascript:;"
                   class="btn btn-sm btn-clean btn-icon clientEditBtn"
                   data-toggle="modal" data-target="#editClient"
                   data-id="{{ $client->id }}">
                    <i class="las la-edit"></i>
                </a>
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" value="{{ $client->id }}">
                <button type="submit" class="btn btn-sm btn-clean btn-icon btn_delete"
                        onclick="return confirm('Вы действительно хотите удалить клиента {{ $client->name }}?')"
                        title="Delete"><i class="las la-trash"></i>
                </button>
            </form>
        </td>
    </tr>
@endforeach
