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
    {
        Schema::table('students', function (Blueprint $table) {
            DB::statement("ALTER TABLE students ALTER COLUMN gender TYPE ENUM('Laki-Laki', 'Perempuan')");

             // Jika ingin mengganti tipe data dari suatu kolom, dapat menggunakan query sql langsung dengan perintah ALTER.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
        });
    }
};
