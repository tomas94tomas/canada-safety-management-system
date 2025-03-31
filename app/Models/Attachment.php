<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_id',
        'file_original_name',
        'file_hashed_name',
        'file_path',
        'file_mime_type',
        'main_source',
    ];
}
