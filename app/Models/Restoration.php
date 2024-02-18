<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restoration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'lending_id',
        'date_time',
        'total_goof_stuff',
        'total_defac_stuff'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lending() 
    {
        return $this->belongsTo(Lending::class);
    }
}
