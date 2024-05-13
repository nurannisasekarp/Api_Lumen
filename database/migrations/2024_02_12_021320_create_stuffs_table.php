<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    // Method up() berfungsi untuk mendefinisikan perubahan yang akan dilakukan pada skema database (menambah tabel, kolom,  atau index pada tabel)
    // method up() berjalan ketika menjalankan php artisan migrate

    {
        Schema::create('stuffs', function (Blueprint $table) { // syarat penamaan tabel di laravel adalah diakhiri oleh huruf s
            $table->id(); // Default kolom dari suatu migration nantinya nama kolomnya adalah id yang bertipe data bigInteger dengan sequence auto increment dan constraint primary key

            // Jika ingin membuat kolom primary key tetapi nama kolomnya bukan id, bisa gunakan attribute primary() seperti berikut
            // $table->primary('no');
            //Jika ingin ditambah increment menjadi seperti ini
            // $table->bigIncrements('no')->primary();

            $table->string('name');
            // struktur dasar menambah kolom dalam suatu tabel
            // $table->tipeData('namaKolom');

            // suatu kolom bisa memiliki lebih dari satu attribute
            // $table->string('name')->nullable()->min(3);
            // struktur diatas akan membuat kolom name dengan tipe data varchar(255) dan kolom boleh tidak memiliki value tetapi jika memiliki value memiliki minimal panjang karakter 3. 

            $table->enum('category', ['HTL', 'KLN', 'Teknisi/Sarpras']);
            $table->timestamps();
            $table->softDeletes();
            // Soft Deletes adalah fitur dari laravel untuk membuat penghapusan data sementara, jadi data yang dihapus dari suatu table itu tidak benar-benar langsung terhapus, masih tersimpan dalam tabel tapi tidak ditampilkan ketika dilakukan pemanggilan.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    // Method down() berfungsi untuk mendefinisikan pembatalan perubahan yang akan dilakukan pada skema database (mengembalikan status pada posisi sebelum method up dijalankan)
    // php artisan migrate:rollback
    {
        Schema::dropIfExists('stuffs');
    }
};

// php artisan migrate
// menjalankan semua migration agar terjadi perubahan/penambahan di database seuai dengan isi dari migration (menjalankan method up())

// php artisan migrate:rollback
// mengembalikan kondisi sebelum migration dijalankan (menjalankan method down)

// php artisan migrate:rollback --step=2
// mengembalikan kondisi sebelum dijalankan hanya pada migration yang termasuk kedalam batch 2 saja, batch dapat dilihat ditabel migrations

// php artisan migrate:refresh
// perintah tersebuht akan menjalankan php artisan migrate:rollback lalu setelahnya menjalankan php artisan migrate