<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lending extends Model
{
    use SoftDeletes;

    // Karena penamaan tabel lendings sudah sesuai dengan standar laravel maka tidak perlu lagi menambahkan protected $table = 'nama_tabel';
    // Karena jika penamaan tabel yang dituju tidak sesuai dengan penulisan nama tabel laravel, maka perlu ditambah perintah protected $table = ‘nama_table’;


    protected $fillable = [ // Memberikan perizinan kepada laravel column mana saja yang dapat diisi value melalui request laravel.
        'stuff_id',
        'date_time',
        'name',
        'user_id',
        'notes',
        'total_stuff',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stuff()
    {
        return $this->belongsTo(Stuff::class); // Kolom FK berada di model Lending/tabel lendings dan kolom PK berada di model Stuff/table stuffs
    }

    public function restoration()
    {
        return $this->hasOne(Restoration::class); // Kolom PK berada di model Lending/tabel lendings dan kolom FK berada di model Restoration/table restorations
    }
}
