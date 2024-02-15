<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stuff extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category',
    ];

    public function stuffStock() 
    {
        return $this->hasOne(stuffStock::class);
    }

    public function inboundStuff()
    {
        return $this->hasMany(InboundStuff::class);
    }

    public function lending()
    {
        return $this->hasMany(Lending::class);
    }
}
