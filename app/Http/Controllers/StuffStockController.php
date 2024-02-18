<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\StuffStock;
use Illuminate\Http\Request;

class StuffStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $getStuffStock = StuffStock::with('stuff')->get();

            return ResponseFormatter::sendResponse(200, true, 'Successfully Get All Stuff Stock Data', $getStuffStock);
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
                'total_available' => 'required',
                'total_defac' => 'required'
            ]);

            $createStock = StuffStock::create([
                'stuff_id' => $request->stuff_id,
                'total_available' => $request->total_available,
                'total_defac' => $request->total_defac,
            ]);

            return ResponseFormatter::sendResponse(200, true, 'Successfully Create A Stuff Stock Data', $createStock);
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

            $getStuffStock = StuffStock::with('stuff')->find($id);

            if (!$getStuffStock) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff Stock Not Found');
            } else {
                return ResponseFormatter::sendResponse(200, true, 'Successfully Get A Stuff Stock Data', $getStuffStock);
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

            $getStuffStock = StuffStock::find($id);

            if (!$getStuffStock) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff Stock Not Found');
            } else {
                $this->validate($request, [
                    'stuff_id' => 'required',
                    'total_available' => 'required',
                    'total_defac' => 'required'
                ]);

                $updateStuffStock = $getStuffStock->update([
                    'stuff_id' => $request->stuff_id,
                    'total_available' => $request->total_available,
                    'total_defac' => $request->total_defac,
                ]);

                if ($updateStuffStock) {
                    $updatedStuffStock = StuffStock::where('id', $id)->with('stuff')->first();

                    return ResponseFormatter::sendResponse(200, true, 'Successfully Update A Stuff Stock Data', $updatedStuffStock);
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

            $getStuffStock = StuffStock::find($id);

            if (!$getStuffStock) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff Stock Not Found');
            } else {
                $deleteStuff = $getStuffStock->delete();

                if ($deleteStuff) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Delete A Stuff Stock Data');
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function recycleBin()
    {
        try {

            $stuffStockDeleted = StuffStock::onlyTrashed()->get();

            if (!$stuffStockDeleted) {
                return ResponseFormatter::sendResponse(404, false, 'Deletd Data Stuff Stock Doesnt Exists');
            } else {
                return ResponseFormatter::sendResponse(200, true, 'Successfully Get Delete All Stuff Stock Data', $stuffStockDeleted);
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {

            $getStuffStock = StuffStock::onlyTrashed()->where('id', $id);

            if (!$getStuffStock) {
                return ResponseFormatter::sendResponse(404, false, 'Restored Data Stuff Stock Doesnt Exists');
            } else {
                $restoreStuff = $getStuffStock->restore();

                if ($restoreStuff) {
                    $getRestore = StuffStock::find($id);

                    return ResponseFormatter::sendResponse(200, true, 'Successfully Restore A Deleted Stuff Stock Data', $getRestore);
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function forceDestroy($id)
    {
        try {

            $getStuffStock = StuffStock::onlyTrashed()->where('id', $id);

            if (!$getStuffStock) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff Stock for Permanent Delete Doesnt Exists');
            } else {
                $forceStuff = $getStuffStock->forceDelete();

                if ($forceStuff) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Permanent Delete A Stuff Stock Data');
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function addStock(Request $request, $id)
    {
        try {

            $getStuffStock = StuffStock::find($id);

            if (!$getStuffStock) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff Stock Not Found');
            } else {
                $this->validate($request, [
                    'total_available' => 'required',
                    'total_defac' => 'required',
                ]);

                $addStock = $getStuffStock->update([
                    'total_available' => $getStuffStock['total_available'] + $request->total_available,
                    'total_defac' => $getStuffStock['total_defac'] + $request->total_defac,
                ]);

                if ($addStock) {
                    $getStockAdded = StuffStock::where('id', $id)->with('stuff')->first();

                    return ResponseFormatter::sendResponse(200, true, 'Successfully Add A Stock Of Stuff Stock Data', $getStockAdded);
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function subStock(Request $request, $id)
    {
        try {

            $getStuffStock = StuffStock::find($id);

            if (!$getStuffStock) {
                return ResponseFormatter::sendResponse(404, false, 'Data Stuff Stock Not Found');
            } else {
                $this->validate($request, [
                    'total_available' => 'required',
                    'total_defac' => 'required',
                ]);

                $isStockAvailable = $getStuffStock['total_available'] - $request->total_available;
                $isStockDefac = $getStuffStock['total_defac'] - $request->total_defac;

                if ($isStockAvailable < 0 || $isStockDefac < 0) {
                    return ResponseFormatter::sendResponse(400, true, 'A Substraction Stock Cant Less Than A Stock Stored');
                } else {
                    $subStock = $getStuffStock->update([
                        'total_available' => $isStockAvailable,
                        'total_defac' => $isStockDefac,
                    ]);

                    if ($subStock) {
                        $getStockSub = StuffStock::where('id', $id)->with('stuff')->first();

                        return ResponseFormatter::sendResponse(200, true, 'Successfully Sub A Stock Of Stuff Stock Data', $getStockSub);
                    }
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }
}
