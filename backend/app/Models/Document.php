<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    public $table = 'documents';

    protected $guard_name = 'api';

    protected $fillable = [
        'kyc_id',
        'origin_name',
        'upload_name',
        'mime_type',
        'size'
    ];
}
