<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{
    use HasFactory;

    public $table = 'kycs';

    protected $guard_name = 'api';

    protected $fillable = [
        'user_id',
        'bvn',
        'birthday',
        'status',
    ];

    public function user() {
        return $this->hasOne(User::class);
    }

    public function documents() {
        return $this->hasMany(Document::class);
    }
}
