<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimCardHistory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sim_card_histories';

    protected $fillable = [
        'sim_card_id',
        'operation_date_time',
        'operation',
        'replace_to_acc_id',
        'equipment_id',
        'user_id'
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
}
