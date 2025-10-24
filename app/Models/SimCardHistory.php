<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimCardHistory extends Model
{
    use HasFactory;

    protected $table = 'sim_card_histories';

    protected $fillable = [
        'sim_card_id',
        'equipment_id',
        'operation_date_time',
        'operation',
        'replace_to_acc_id',
        'user_id',
        'status_id',
        'previous_sim_status_id',
        'changes_data',
        'change_source',
        'change_reason',
        'notes'
    ];

    protected $casts = [
        'changes_data' => 'array',
        'operation_date_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getNewAcc()
    {
      if (!empty($this->replace_to_acc_id)) {
        return Client::where('id', $this->replace_to_acc_id)->first()->account;
      }else {
        return '';
      }
    }
    public function getUser()
    {
        return User::find($this->user_id)->name??'';
    }
    public function getEquipment()
    {
        return Equipment::find($this->equipment_id)->object??'';
    }

    // Відношення
    public function simCard()
    {
        return $this->belongsTo(SimCard::class, 'sim_card_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(SimStatus::class, 'status_id');
    }

    public function previousStatus()
    {
        return $this->belongsTo(SimStatus::class, 'previous_sim_status_id');
    }



    // Форматований вивід змін
    public function getFormattedChangesAttribute()
    {
        if (!$this->changes_data) {
            return [];
        }

        $formatted = [];

        foreach ($this->changes_data as $field => $change) {
            $formatted[] = [
                'field' => $this->getFieldLabel($field),
                'old_value' => $change['old'] ?? 'N/A',
                'new_value' => $change['new'] ?? 'N/A',
                'changed' => ($change['old'] ?? null) !== ($change['new'] ?? null)
            ];
        }

        return $formatted;
    }

    // Отримати текстовий опис змін
    public function getChangesDescriptionAttribute()
    {
        if (!$this->changes_data) {
            return 'Без змін';
        }

        $descriptions = [];

        foreach ($this->changes_data as $field => $change) {
            $label = $this->getFieldLabel($field);
            $old = $change['old'] ?? 'N/A';
            $new = $change['new'] ?? 'N/A';

            $descriptions[] = "{$label}: {$old} → {$new}";
        }

        return implode('; ', $descriptions);
    }

    private function getFieldLabel($field)
    {
        $labels = [
            'status' => 'Статус',
            'equipment' => 'Обладнання',
            'balance' => 'Баланс',
            'signal_strength' => 'Сила сигналу',
            'network_type' => 'Тип мережі',
            'operator' => 'Оператор',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    // Scope для фільтрації по джерелу
    public function scopeBySource($query, $source)
    {
        return $query->where('change_source', $source);
    }

    // Scope для фільтрації по статусу
    public function scopeByStatus($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }

}
