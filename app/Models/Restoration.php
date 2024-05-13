<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restoration extends Model
{
    use SoftDeletes;

    // protected $table = 'restoration';

    protected $fillable = [
        'user_id',
        'lending_id',
        'date_time',
        'total_good_stuff',
        'total_defac_stuff'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'akun_id', 'id');
        // return $this->belongsTo(User::class, 'kolom fk', 'kolom pk');

        // jika nama kolom fk tidak sesuai maka perlu di definisikan lagi, yaitu argumen ke dua kolom fk dan argumen ke tiga adalah kolom pk
    }

    public function lending()
    {
        return $this->belongsTo(Lending::class);
    }
}
