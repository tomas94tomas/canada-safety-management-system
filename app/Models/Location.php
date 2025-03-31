<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'value',
        'sensus_id',
    ];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
