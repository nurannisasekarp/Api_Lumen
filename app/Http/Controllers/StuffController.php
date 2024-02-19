<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter; //Import file ResponseFormatter.php untuk memberikan format dari hasil response kedalam JSON.
use App\Models\Stuff; // Import file Models/Stuff.php untuk mengarahkan table mana yang akan digunakan.
use Illuminate\Http\Request;

class StuffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $stuffs = Stuff::all()->toArray(); // Menadapatkan keseluruhan data dari tabel stuffs

            return ResponseFormatter::sendResponse(200, true, 'Successfully Get All Stuff Data', $stuffs);
        } catch (\Exception $e) { // Exception adalah objek yang menjelaskan kesalahan atau perilaku tak terduga dari skrip PHP
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
        // Try-Catch digunakan untuk pengecekan suatu proses berhasil atau tidak. Semua baris pada try akan dijalankan terlebih dahulu, jika  $stuffs = Stuff::all() berhasil diproses tanpa adanya error, maka akan mengembalikan response JSON berupa data hasil dari tabel dengan menggunakan static method sendResponse() dari Response Formatter. Jika ada error pada baris kode try maka prosesnya akan dialihkan pada baris kode catch yang mengembalikan response JSON error dengan menggunakan static method error() dari ResponseFormatter.
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'category' => 'required',
            ]);
            // Penggunaan validasi antara laravel dan lumen laravel berbeda penulisannya. Pada lumen untuk validasi menggunakan method validasi dari class controller yang memiliki dua argument.
            // argument pertama adalah data mana yang divalidasi
            // argument kedua berupa tipe validasi apa yang diberikan untuk sumber datanya

            $createStuff = Stuff::create($request->all());
            // Jika antara nama kolom di database dan nama key di request sama maka bisa menggunakan perintah diatas, namun jika berbeda haruslah definisikan satu persatu kolomnya seperti dibawah ini.
            // $createStuff = Stuff::create([
            //     'name' => $request->name,
            //     'category' => $request->category,
            // ]);

            return ResponseFormatter::sendResponse(200, true, 'Successfully Create A Stuff Data', $createStuff);
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $getStuff = Stuff::find($id);

            if (!$getStuff) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff Not Found', $getStuff);
            } else {
                return ResponseFormatter::sendResponse(200, true, 'Successfully Get A Stuff Data', $getStuff);
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $getStuff = Stuff::find($id);

            if (!$getStuff) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff Not Found', $getStuff);
            } else {
                $this->validate($request, [
                    'name' => 'required',
                    'category' => 'required',
                ]);

                $updateStuff = $getStuff->update([
                    'name' => $request->name,
                    'category' => $request->category,
                ]);

                if ($updateStuff) {
                    $getUpdate = Stuff::find($id);

                    return ResponseFormatter::sendResponse(200, true, 'Successfully Update A Stuff Data', $getUpdate);
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $getStuff = Stuff::find($id);

            if (!$getStuff) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff Not Found', $getStuff);
            } else {
                $deleteStuff = $getStuff->delete();

                if ($deleteStuff) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Delete A Stuff Data');
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function recycleBin()
    {
        try {

            $stuffDeleted = Stuff::onlyTrashed()->get();

            if (!$stuffDeleted) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff Not Found');
            } else {
                return ResponseFormatter::sendResponse(200, true, 'Successfully Get Delete All Stuff Data', $stuffDeleted);
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {

            $getStuff = Stuff::onlyTrashed()->where('id', $id);

            if (!$getStuff) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff Not Found');
            } else {
                $restoreStuff = $getStuff->restore();

                if ($restoreStuff) {
                    $getRestore = Stuff::find($id);

                    return ResponseFormatter::sendResponse(200, true, 'Successfully Restore A Deleted Stuff Data', $getRestore);
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function forceDestroy($id)
    {
        try {

            $getStuff = Stuff::onlyTrashed()->where('id', $id);

            if (!$getStuff) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff for Permanent Delete Doesnt Exists');
            } else {
                $forceStuff = $getStuff->forceDelete();

                if ($forceStuff) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Permanent Delete A Stuff Data');
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }
}
