<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analitic extends Model
{
    use HasFactory;

    protected $table = 'analitics';

    protected $fillable = [
        'month',
        'active_counts_ip',
        'active_counts_fl',
        'active_counts_too',
        'graphic_total_ip',
        'graphic_total_fl',
        'graphic_total_too',
        'earnings_ip',
        'earnings_fl',
        'earnings_too'
    ];
}
