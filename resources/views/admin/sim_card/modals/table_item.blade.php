@foreach($simCards as $card)
    <tr>
        <td class="text-center pr-0">
            {{ '+' . $card->phone }}
        </td>
        <td class="text-center pr-0">
            {{ $card->getStatus() }}
        </td>
        <td class="text-center pr-0">
            {{ $card->getOperatorTitle() }}
        </td>
        <td class="text-center pr-0">
            <form action="{{ route('sim_card.destroy') }}" method="POST">
                <a  class="btn btn-sm btn-clean"  href="{{ route('sim-card', $card->id) }}">История</a>
                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon simdCardEditBtn"
                   data-toggle="modal" data-target="#editSim"
                   data-id="{{ $card->id }}">
                    <i class="las la-edit"></i>
                </a>
                @if(auth()->user()->role_id == \App\Models\User::ADMIN)
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $card->id }}">
                    <button type="submit" class="btn btn-sm btn-clean btn-icon btn_delete"
                            onclick="return confirm('Вы действительно хотите удалить Sim-карту {{ $card->title }}?')"
                            title="Delete"><i class="las la-trash"></i>
                    </button>
                @endif
            </form>
        </td>
    </tr>
@endforeach
