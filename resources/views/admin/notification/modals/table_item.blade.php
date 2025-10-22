@foreach($notifications as $notification)
    <tr>
        <td class="text-center pl-0 td">
            {{ $notification->id }}
        </td>
        <td class="text-center pr-0 td">
            @if($notification->client_id)
                <a href="{{ route('client', $notification->client_id) }}">{{ $notification->message }}</a>
            @else
                {{ $notification->message }}
            @endif
        </td>
        <td class="text-center pr-0 td">
            <div class="btn-group" style="width: 150px;">
                <select id="status" class="form-control selectpicker notification_status"
                        data-style="@if($notification->status) btn-primary @else btn-success @endif"
                        data-id="{{ $notification->id }}">
                    <option value="opened"
                            @if($notification->status == 1) selected @endif>Новый
                    </option>
                    <option value="closed"
                            @if($notification->status == 0) selected @endif>Прочтенный
                    </option>
                </select>
            </div>
        </td>
        <td class="text-center pr-0 td">
            {{ date('d.m.Y H:i:s', strtotime($notification->created_at)) }}
        </td>
        <td class="text-center pr-0">
            <form action="{{ route('notification.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" value="{{ $notification->id }}">
                <button type="submit" class="btn btn-sm btn-clean btn-icon btn_delete"
                        onclick="return confirm('Вы действительно хотите удалить сообщение?')"
                        title="Удаліть"><i class="las la-trash"></i>
                </button>
            </form>
        </td>
    </tr>
@endforeach
