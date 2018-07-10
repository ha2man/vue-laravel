<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    public $table = 'addresses';

    protected $guard_name = 'api';

    protected $fillable = [
        'user_id',
        'is_primary',
        'address',
        'street_name',
        'street',
        'city',
        'state',
        'phones',
        'emails',
    ];
}
