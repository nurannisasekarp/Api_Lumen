<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Lending;
use App\Models\Restoration;
use App\Models\StuffStock;
use Illuminate\Http\Request;

class LendingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $getLending = Lending::with('stuff', 'user')->get();

            return ResponseFormatter::sendResponse(200, true, 'Successfully Get All Lending Data', $getLending);
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
                'stuff_id' => 'required',
                'date_time' => 'required',
                'name' => 'required',
                'user_id' => 'required',
                'notes' => 'required',
                'total_stuff' => 'required',
            ]);

            $createLending = Lending::create([
                'stuff_id' => $request->stuff_id,
                'date_time' => $request->date_time,
                'name' => $request->name,
                'user_id' => $request->user_id,
                'notes' => $request->notes,
                'total_stuff' => $request->total_stuff,
            ]);

            $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
            $updateStock = $getStuffStock->update([
                'total_available' => $getStuffStock['total_available'] - $request->total_stuff,
            ]);

            return ResponseFormatter::sendResponse(200, true, 'Successfully Create A Lending Data', $createLending);
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
            $data = Lending::where('id', $id)->with('user','restoration','restoration.user','stuff','stuffstock')->first();
                return ResponseFormatter::sendResponse(202,'suscces', $data);
        } catch (\Exception $err) {
            return ResponseFormatter::sendResponse(400, 'bad request', $err->getMessage());
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

            $getLending = Lending::find($id);

            if ($getLending) {
                $this->validate($request, [
                    'stuff_id' => 'required',
                    'date_time' => 'required',
                    'name' => 'required',
                    'user_id' => 'required',
                    'notes' => 'required',
                    'total_stuff' => 'required',
                ]);


                $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first(); //get stock berdasarkan request stuff id
                $getCurrentStock = StuffStock::where('stuff_id', $getLending['stuff_id'])->first();

                if ($request->stuff_id == $getCurrentStock['stuff_id']) {
                    $updateStock = $getCurrentStock->update([
                        'total_available' => $getCurrentStock['total_available'] + $getLending['total_stuff'] - $request->total_stuff,
                    ]); //total available lama akan di jumlahkan dengan total peminjaman barang lama lalu di kurangin dengan total peminjaman yg baru
                } else {
                    $updateStock = $getCurrentStock->update([
                        'total_available' => $getCurrentStock['total_available'] + $getLending['total_stuff'],
                    ]); //total available lama di jumlahkan dengan total pinjaman yg lama
                    $updateStock = $getStuffStock->update([
                        'total_available' => $getStuffStock['total_available'] - $request['total_stuff'],
                    ]);
                } //total available baru di kurangi dengan total pinjaman baru 

                $updateLending = $getLending->update([
                    'stuff_id' => $request->stuff_id,
                    'date_time' => $request->date_time,
                    'name' => $request->name,
                    'user_id' => $request->user_id,
                    'notes' => $request->notes,
                    'total_stuff' => $request->total_stuff,
                ]);

                $getUpdatedLending = Lending::where('id', $id)->with('stuff', 'user')->first();

                return ResponseFormatter::sendResponse(200, true, 'Successfully Update A Lending Data', $getUpdatedLending);
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
        // lending = data peminjaman
        // stuff = data barang
        // stuff_stocks = data stok barang
        // restorations = data pengembalian (buat nyatet kapan dikembaliin/dikembaliin sama siapa, dll)

        try {
            $getLending = Lending::find($id);

            if (!$getLending) { // apabila data lending/pinjamannya TIDAK ADA << kalo gada datanya
                return ResponseFormatter::sendResponse(404, false, 'Data lending dengan id tersebut tidak ditemukan');
            } else if ($getLending->restoration) { // apabila pinjaman MEMILIKI data restorasi (sudah pernah di cancel sebelumnya) lanjut ke else deh, dia pasti masuk else kalau nge skip semua if di atasnya, paham?paham buat id nya kita pilih acak apa gimana antara 4/5 aja, kenapa? karena yang lain itu stuff_id nya 6, liat deh
                return ResponseFormatter::sendResponse(404, false, 'Data lending ini memiliki data restoration (pernah di cancel sebelumnya)');
            } else { // artinya JIKA data PINJAMAN nya ADA dan BELUM PERNAH DI CANCEL (tidak ada di data restoration) << alias pinjaman ini bisa dicancel karena orderan tersebut VALID ada datanya & blm pernah dicancel siapapun

                // kita insert data restoration/pengembaliannya ke table, agar tercatat data nya
                Restoration::create([
                    'user_id' => $getLending->user_id, // user_id pemminjam
                    'lending_id' => $getLending->id, // id pinjaman
                    'date_time' => \Carbon\Carbon::now(), // jam sekarang
                    'total_good_stuff' => 0,
                    'total_defac_stuff' => 0,
                ]);

                // logic buat ngembaliin stock yang di cancel
                // cari stock berdasarkan barang yg dikembalikan, lalu total stock nya ditambahkan sesuai dengan total yang dipinjam
                // increment itu fungsinya buat nambahin value dari kolom total_vailable
                StuffStock::where('stuff_id', $getLending->stuff_id)->increment('total_available', $getLending->total_stuff);

                // ketika stock sudah berhasil dikembalikan, maka dia akan menghapus orderan/pinjaman tersebut KARENA ya emang di cancel jadi dihapus dari list Lending (kolom deleted_at akan terisi)
                $getLending->delete();

                // beritahu user bahwa restoration/pengembalian pinjaman sudah success
                return ResponseFormatter::sendResponse(200, true, 'Successfully restore lending data');
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    //  (int) itu buat ngubah dari string/integer ke integer, contoh



    // $data = "5000";
    // $hasil = (int)$data;
    // >>>>>>> maka $hasil = 5000;

    // $data = 5000;
    // $hasil = (int)$data;
    // >>>>>>> maka $hasil = 5000;

    public function recycleBin()
    {
        try {

            $lendingDeleted = Lending::onlyTrashed()->get();

            if (!$lendingDeleted) {
                return ResponseFormatter::sendResponse(404, false, 'Deletd Data Lending Doesnt Exists');
            } else {
                return ResponseFormatter::sendResponse(200, true, 'Successfully Get Delete All Lending Data', $lendingDeleted);
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {

            $getLending = Lending::onlyTrashed()->where('id', $id);

            if (!$getLending) {
                return ResponseFormatter::sendResponse(404, false, 'Restored Data Lending Doesnt Exists');
            } else {
                $restoreLending = $getLending->restore();

                if ($restoreLending) {
                    $getRestore = Lending::find($id);
                    $addStock = StuffStock::where('stuff_id', $getRestore['stuff_id'])->first();
                    $updateStock = $addStock->update([
                        'total_available' => $addStock['total_available'] - $getRestore['total_stuff'],
                    ]);

                    return ResponseFormatter::sendResponse(200, true, 'Successfully Restore A Deleted Lending Data', $getRestore);
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function forceDestroy($id)
    {
        try {

            $getLending = Lending::onlyTrashed()->where('id', $id);

            if (!$getLending) {
                return ResponseFormatter::sendResponse(404, false, 'Data Lending for Permanent Delete Doesnt Exists');
            } else {
                $forceStuff = $getLending->forceDelete();

                if ($forceStuff) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Permanent Delete A Lending Data');
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }
}
