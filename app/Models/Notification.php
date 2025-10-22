<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'notifications';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id',
        'equipment_id',
        'sim_card_id',
        'client_id',
        'message',
        'status'
    ];
    public $timestamps = true;


    public function equipment()
    {
        return $this->hasOne(Equipment::class, 'id', 'equipment_id');
    }

    public function sim_card()
    {
        return $this->hasOne(SimCard::class, 'id', 'sim_card_id');
    }
}
