<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'anonymous',
        'status',
        'subject',
        'location',
        'description',
        'proposal',
        'email',
        'sms_number',
        'catastrophic_high_risk',
        'company_name',
    ];

//    public function location(): BelongsTo
//    {
//        return $this->belongsTo(Location::class);
//    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
