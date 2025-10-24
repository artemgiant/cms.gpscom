<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimStatus extends Model
{
    protected $table = 'sim_statuses';

    protected $fillable = [
        'name',
        'code',
        'color',
        'description',
        'is_active',
        'is_working',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_working' => 'boolean'
    ];

    // Відношення
    public function simCardHistories()
    {
        return $this->hasMany(SimCardHistory::class, 'status_id');
    }

    public function previousHistories()
    {
        return $this->hasMany(SimCardHistory::class, 'previous_sim_status_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeWorking($query)
    {
        return $query->where('is_working', true);
    }

    // Accessor для badge
    public function getBadgeHtmlAttribute()
    {
        return sprintf(
            '<span class="badge" style="background-color: %s; color: white;">%s</span>',
            $this->color,
            $this->name
        );
    }
}
