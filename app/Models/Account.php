<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'account_id',
        'balance',
    ];

    public function events()
    {
        return $this->hasMany(Event::class, 'account_id', 'account_id');
    }
}