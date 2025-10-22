@foreach($equipments as $equipment)
    <tr class="equipment_item equipment_{{ $equipment->id }} @if($equipment->status == \App\Models\Equipment::DEACTIVE) deactive_equipment @endif">
        <td class="text-left pl-0">
            <span class="label label-xl label-dot dot_{{ $equipment->id}} {{ $equipment->getDotStatus() }} mr-2"></span>
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
        <td class="text-center pl-0 date_start">
            {{ $equipment->date_start ? date('d.m.Y', strtotime($equipment->date_start)) : '' }}
        </td>
        <td class="text-center pl-0 date_end">
            {{ $equipment->date_end ? date('d.m.Y', strtotime($equipment->date_end)) : '' }}
        </td>
        <td class="text-center pl-0">
            {{ isset($equipment->operatorData->tariff) ? $equipment->operatorData->tariff->title : '' }}
        </td>
        <th class="pr-0 text-center">
            <div class="card-toolbar">
                <div class="dropdown dropdown-inline">
                    <a href="#"
                       class="btn btn-clean btn-hover-light-primary btn-sm btn-icon"
                       data-toggle="dropdown" aria-haspopup="true"
                       aria-expanded="false">
                        <i class="ki ki-bold-more-ver"></i>
                    </a>
                    <div
                        class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                        <!--begin::Navigation-->
                        <ul class="navi navi-hover">
                            <li class="navi-item">
                                <a href="#"
                                   class="navi-link start_equipment"
                                   data-equipment_id="{{ $equipment->id }}">
                                                                                <span
                                                                                    class="navi-text equipment_status_text_{{ $equipment->id }}">Подключить</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#"
                                   class="navi-link end_equipment"
                                   data-equipment_id="{{ $equipment->id }}">
                                                                                <span
                                                                                    class="navi-text equipment_status_text_{{ $equipment->id }}">Отключить</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#"
                                   class="navi-link deactive_equipment"
                                   data-equipment_id="{{ $equipment->id }}">
                                                                                <span
                                                                                    class="navi-text equipment_status_text_{{ $equipment->id }}">Деактивирван</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link show_equipment"
                                   data-equipment_id="{{ $equipment->id }}">
                                                                                <span
                                                                                    class="navi-text">Редактировать</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link move_equipment"
                                   data-equipment_id="{{ $equipment->id }}">
                                                                                <span
                                                                                    class="navi-text">Переместить</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#"
                                   class="navi-link delete_equipment"
                                   data-equipment_id="{{ $equipment->id }}">
                                    <span class="navi-text">Удалить</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </th>
    </tr>
@endforeach
