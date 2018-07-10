<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public $table = 'invoices';

    public $timestamps = false;

    protected $guard_name = 'api';

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'weight',
        'weight_unit',
        'width',
        'height',
        'total',
        'cost',
        'insurance',
        'notes',
        'paid',
        'created_by',
        'created_at',
        'received_at',
        'dispatched_at',
        'collected_at',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
