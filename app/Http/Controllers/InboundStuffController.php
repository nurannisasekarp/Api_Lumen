<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\InboundStuff;
use App\Models\Stuff;
use App\Models\StuffStock;
use Illuminate\Http\Request;

class InboundStuffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $getInboundStuff = InboundStuff::with('stuff','stuff.stuffStock')->get();

            return ResponseFormatter::sendResponse(200, true, 'Successfully Get All Inbound Stuff Data', $getInboundStuff);
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
    public function store(Request $request) // proof file disesuaikan
    {
        try {
            $this->validate($request, [
                'stuff_id' => 'required',
                'total' => 'required',
                'date' => 'required',
                'proff_file' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            $checkStuff = Stuff::where('id',$request->stuff_id)->first();

            if (!$checkStuff){
                return ResponseFormatter::sendresponse(400,false,'Data stuff does not exixts');
            }else {
                if ($request->hasFile('proff_file')) { // ngecek ada file apa engga
                    $proof = $request->file('proff_file'); // get filenya
                    $destinationPath = 'proof/'; // sub path di folder public
                    //20240308102130
                    $proofName = date('YmdHis') . "." . $proof->getClientOriginalExtension(); // modifikasi nama file, tahunbulantanggaljammenitdetik.extension
                    $proof->move($destinationPath, $proofName); // file yang sudah di get diatas dipindahkan ke folder public/proof dengan nama sesaui yang di variabel proofname
                }
    
                $createStock = InboundStuff::create([
                    'stuff_id' => $request->stuff_id,
                    'total' => $request->total,
                    'date' => $request->date,
                    'proff_file' => $proofName,
                ]);
    
                if ($createStock) {
                    $getStuff = Stuff::where('id', $request->stuff_id)->first();
                    // $getStuff = Stuff::find($request->stuff_id);
                    $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
                    // $getStuffStock = StuffStock::firstWhere('stuff_id', $request->stuff_id);
    
                    if (!$getStuffStock) {
                        $updateStock = StuffStock::create([
                            'stuff_id' => $request->stuff_id,
                            'total_available' => $request->total,
                            'total_defac' => 0,
                        ]);
                    } else {
                        $updateStock = $getStuffStock->update([
                            'stuff_id' => $request->stuff_id,
                            'total_available' => $getStuffStock['total_available'] + $request->total,
                            'total_defac' => $getStuffStock['total_defac'],
                        ]);
                    }
    
                    if ($updateStock) {
                        $getStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
                        // $getStock = StuffStock::firstWhere('stuff_id', $request->stuff_id);
                        $stuff = [
                            'stuff' => $getStuff,
                            'inboundStuff' => $createStock,
                            'stuffStock' => $getStock,
                        ];
    
                        return ResponseFormatter::sendResponse(200, true, 'Successfully Create A Inbound Stuff Data', $stuff);
                    } else {
                        return ResponseFormatter::sendResponse(400, false, 'Failed To Update A Stuff Stock Data');
                    }
                } else {
                    return ResponseFormatter::sendResponse(400, false, 'Failed To Create A Inbound Stuff Data');
                }
            }
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

            $getInboundStuff = InboundStuff::with('stuff','stuff.stuffStock')->find($id);

            if (!$getInboundStuff) {
                return ResponseFormatter::sendResponse(404, false, 'Data Inbound Stuff Not Found');
            } else {
                return ResponseFormatter::sendResponse(200, true, 'Successfully Get A Inbound Stuff Data', $getInboundStuff);
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
            $getInboundStuff = InboundStuff::find($id);

            if (!$getInboundStuff) {
                return ResponseFormatter::sendResponse(404, false, 'Data Inbound Stuff Not Found');
            } else {

                $this->validate($request, [
                    'stuff_id' => 'required',
                    'total' => 'required',
                    'date' => 'required',
                    'proff_file' => 'required|mines:jpeg,png,jpg,pdf|max:2024',
                ]);

                if ($request->hasFile('proff_file')) { //ngecek data ada apa ngga
                    $proof = $request->file('proff_file'); //get file nya
                    $destinationPath = 'proof/'; //sub path di folder public
                    $proofName = date('YmdHis') . "." . $proof->getClientOriginalExtension(); //modifikasi nama file,tahunbulantanggal jammenitdetik
                    $proof->move($destinationPath, $proofName);
                } else {
                    $proffName = $getInboundStuff['proff_file'];
                }

                $getStuff = Stuff::where('id', $getInboundStuff['stuff_id'])->first();
                $getStuffStock = StuffStock::where('stuff_id', $getInboundStuff['stuff_id'])->first(); // stuuf_id request tidak berubah
                $getCurrentStock = StuffStock::where('stuff_id', $request['stuff_id'])->first(); // berubah

                if ($getStuffStock['stuff_id'] == $request['stuff_id']) {
                    $updateStock = $getStuffStock->update([
                        'total_available' => $getStuffStock['total_available'] - $getInboundStuff['total'] + $request->total,
                    ]); // update data yang stuuf_id tidak berubah dengan merubah total available di kurangi totoal data lama di tambah total data baru
                } else {
                    $updateStock = $getStuffStock->update([
                        'total_available' => $getStuffStock['total_available'] - $getInboundStuff['total'],
                    ]); // update data yang stuff_id tidak berubah dengan mengurangi total available dengan total yang baru
                    $updateStock = $getCurrentStock->update([
                        'total_available' => $getStuffStock['total_available']  + $request->total,
                    ]);
                }

                $updateInbound = $getInboundStuff->update([
                    'stuff_id' => $request->stuff_id,
                    'total' => $request->total,
                    'date' => $request->date,
                    'proff_file' => $proffName,
                ]);
                unlink(base_path('public/proof/' . $getInboundStuff['proff_file']));

                $getStock = StuffStock::where('stuff_id', $request['stuff_id'])->first();
                $getInbound = InboundStuff::find($id);
                $getCurrentStuff = Stuff::where('id', $request['stuff_id'])->first();

                $stuff = [
                    'stuff' => $getCurrentStuff,
                    'inboundStuff' => $getInbound,
                    'stuffStock' => $getStock,
                ];

                return ResponseFormatter::sendResponse(200, true, 'Successfully Update A Inbound Stuff Data', $stuff);
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
            $getInboundStuff = InboundStuff::find($id);

            if (!$getInboundStuff) {
                return ResponseFormatter::sendResponse(404, false, 'Data Inbound Stuff Not Found');
            } else {
                $subStock = StuffStock::where('stuff_id', $getInboundStuff['stuff_id'])->first();

                $total_available_pada_stuff_stocks = $subStock->total_available;
                $total_pada_inbounds = $getInboundStuff->total;

                if ($total_available_pada_stuff_stocks < $total_pada_inbounds) {
                    return ResponseFormatter::sendResponse(400, 'bad request', 'Jumlah total inbound yang akan dihapus lebih besar dari total available stuff saat ini!');
                } else {
                    $deleteStuff = $getInboundStuff->delete();
                }

                $updateStock = $subStock->update([
                    'total_available' => $subStock['total_available'] - $getInboundStuff['total'],
                ]);

                if ($deleteStuff && $updateStock) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Delete A Inbound Stuff Data');
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function recycleBin()
    {
        try {

            $inboundStuffDeleted = InboundStuff::onlyTrashed()->get();

            if (!$inboundStuffDeleted) {
                return ResponseFormatter::sendResponse(404, false, 'Deletd Data Inbound Stuff Doesnt Exists');
            } else {
                return ResponseFormatter::sendResponse(200, true, 'Successfully Get Delete All Inbound Stuff Data', $inboundStuffDeleted);
            }

            // if($inboundStuffDeleted = InboundStuff::onlyTrashed()->get()) return ResponseFormatter::sendResponse(200, true, 'Successfully Get Delete All Inbound Stuff Data', $inboundStuffDeleted);
            // return ResponseFormatter::sendResponse(404, false, 'Deletd Data Inbound Stuff Doesnt Exists');
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function restore($id)
    { //menghapus inbound stuff berdasarkan id (parameter id yang diberikan)
        try {

            $getInboundStuff = InboundStuff::onlyTrashed()->where('id', $id); //mencara data inound berdasarkan id yang diberikan

            if (!$getInboundStuff) {
                return ResponseFormatter::sendResponse(404, false, 'Restored Data Inbound Stuff Doesnt Exists');
            } else {
                $restoreStuff = $getInboundStuff->restore();

                if ($restoreStuff) {
                    $getRestore = InboundStuff::find($id);
                    $addStock = StuffStock::where('stuff_id', $getRestore['stuff_id'])->first();
                    $updateStock = $addStock->update([
                        'total_available' => $addStock['total_available'] + $getRestore['total'],
                    ]);

                    return ResponseFormatter::sendResponse(200, true, 'Successfully Restore A Deleted Inbound Stuff Data', $getRestore);
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function forceDestroy($id)
    {
        try {

            $getInboundStuff = InboundStuff::onlyTrashed()->where('id', $id);

            if (!$getInboundStuff) {
                return ResponseFormatter::sendResponse(404, false, 'Data Inbound Stuff for Permanent Delete Doesnt Exists');
            } else {
                $forceStuff = $getInboundStuff->forceDelete();
                unlink(base_path('public/proof/' . $getInboundStuff['proff_file']));

                if ($forceStuff) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Permanent Delete A Inbound Stuff Data');
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }
    public function deletePermanent(InboundStuff $inboundStuff, Request $request, $id)
    {
        try {
            $getInbound = InboundStuff::onlyTrashed()->where('id', $id)->first();

            unlink(base_path('public/proff/' . $getInbound->proff_file));
            // Menghapus data dari database
            $checkProses = InboundStuff::where('id', $id)->forceDelete();

            // Memberikan respons sukses
            return ResponseFormatter::sendResponse(200, 'success', 'Data inbound-stuff berhasil dihapus permanen');
        } catch (\Exception $err) {
            // Memberikan respons error jika terjadi kesalahan
            return ResponseFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
    public function trash()
    {
        try {
            $data = InboundStuff::onlyTrashed()->get();

            return ResponseFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ResponseFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
}
