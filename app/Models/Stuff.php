<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stuff extends Model
{
    use SoftDeletes;

    // protected $primaryKey = 'no';
    // Set kolom primary key jika kolom primary key bukan lah kolom id, karena default primary key pada suatu tabel di laravel terdapat di kolom id
    
    protected $fillable = [ 
        'name',
        'category',
    ];

    public function stuffStock()
    {
        return $this->hasOne(stuffStock::class);
    }

    public function inboundStuffs()
    {
        return $this->hasMany(InboundStuff::class);
    }

    public function lendings()
    {
        return $this->hasMany(Lending::class);
    }
}
