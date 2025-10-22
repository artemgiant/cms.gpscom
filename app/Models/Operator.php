<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory;

    /**
     * Operators
     */
    const BEELINE = 1;
    const KCELL = 2;
    const M2M = 3;

    protected $table = 'operators';
    protected $fillable = [
        'title',
        'slug'
    ];

    public function tariff()
    {
        return $this->hasOne(Tariff::class, 'operator_id', 'id');
    }
}
