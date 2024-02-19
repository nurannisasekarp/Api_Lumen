<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Lending;
use App\Models\Restoration;
use App\Models\StuffStock;
use App\Models\User;
use Illuminate\Http\Request;

class RestorationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $restorations = Restoration::with('lending', 'user')->get();

            return ResponseFormatter::sendResponse(200, true, 'Successfully Get All Restoration Data', $restorations);
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
                'user_id' => 'required',
                'lending_id' => 'required',
                'date_time' => 'required',
                'total_goof_stuff' => 'required',
                'total_defac_stuff' => 'required',
            ]);

            $getLending = Lending::where('id', $request->lending_id)->first();
            $totalStuff = $request->total_goof_stuff + $request->total_defac_stuff;
            if ($getLending['total_stuff'] != $totalStuff) {
                return ResponseFormatter::sendResponse(400, false, 'The amount of items returned does not match the amount borrowed');
            } else {
                $getStuffStock = StuffStock::where('stuff_id', $getLending['stuff_id'])->first();

                $createRestoration = Restoration::create([
                    'user_id' => $request->user_id,
                    'lending_id' => $request->lending_id,
                    'date_time' => $request->date_time,
                    'total_goof_stuff' => $request->total_goof_stuff,
                    'total_defac_stuff' => $request->total_defac_stuff,
                ]);

                $updateStock = $getStuffStock->update([
                    'total_available' => $getStuffStock['total_available'] + $request->total_goof_stuff,
                    'total_defac' => $getStuffStock['total_defac'] + $request->total_defac_stuff,
                ]);

                if ($createRestoration && $updateStock) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Create A Restoration Data', $createRestoration);
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
            $getRestoration = Restoration::where('id', $id)->with('lending', 'user')->first();
            if ($getRestoration) {
                return ResponseFormatter::sendResponse(200, true, 'Successfully Get A Restoration Data', $getRestoration);
            } else {
                return ResponseFormatter::sendResponse(404, false, 'Restoration Data Not Found');
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
            $getRestoration = Restoration::find($id);

            if (!$getRestoration) {
                return ResponseFormatter::sendResponse(404, false, 'Restoration Data Not Found');
            } else {
                $this->validate($request, [
                    'user_id' => 'required',
                    'lending_id' => 'required',
                    'date_time' => 'required',
                    'total_goof_stuff' => 'required',
                    'total_defac_stuff' => 'required',
                ]);

                $getLending = Lending::where('id', $getRestoration['lending_id'])->first();
                if ($getLending) {
                    $totalStuff = $request->total_goof_stuff + $request->total_defac_stuff;

                    if ($getLending['total_stuff'] != $totalStuff) {
                        return ResponseFormatter::sendResponse(400, false, 'The amount of items returned does not match the amount borrowed');
                    } else {
                        $getStuffStock = StuffStock::where('stuff_id', $getLending['stuff_id'])->first();
                        $currentLending = Lending::where('id', $request->lending_id)->first();
                        $currentStock = StuffStock::where('stuff_id', $currentLending['stuff_id'])->first();

                        if ($currentLending) {
                            if ($getLending['id'] == $currentLending['id']) {
                                $updateStock = $getStuffStock->update([
                                    'total_available' => $getStuffStock['total_available'] - $getRestoration['total_goof_stuff'] + $request->total_goof_stuff,
                                    'total_defac' => $getStuffStock['total_defac'] - $getRestoration['total_defac_stuff'] + $request->total_defac_stuff,
                                ]);
                            } else {
                                $updateStock = $getStuffStock->update([
                                    'total_available' => $getStuffStock['total_available'] - $getRestoration['total_goof_stuff'],
                                    'total_defac' => $getStuffStock['total_defac'] - $getRestoration['total_defac_stuff'],
                                ]);

                                $updateStock = $currentStock->update([
                                    'total_available' => $currentStock['total_available'] + $getRestoration['total_goof_stuff'],
                                    'total_defac' => $currentStock['total_defac'] + $getRestoration['total_defac_stuff'],
                                ]);
                            }
                        } else {
                            return ResponseFormatter::sendResponse(404, false, 'Lending Data Doenst Match');
                        }

                        $updateRestoration = $getRestoration->update([
                            'user_id' => $request->user_id,
                            'lending_id' => $request->lending_id,
                            'date_time' => $request->date_time,
                            'total_goof_stuff' => $request->total_goof_stuff,
                            'total_defac_stuff' => $request->total_defac_stuff,
                        ]);

                        $currentRestoration = Restoration::where('id', $id)->with('lending', 'user')->first();

                        if ($updateRestoration && $updateStock) {
                            return ResponseFormatter::sendResponse(200, true, 'Successfully Update A Restoration Data', $currentRestoration);
                        }
                    }
                } else {
                    return ResponseFormatter::sendResponse(404, false, 'Lending Data Doesnt Match');
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

            $getRestoration = Restoration::find($id);

            if (!$getRestoration) {
                return ResponseFormatter::sendResponse(404, false, 'Data Restoration Not Found');
            } else {
                $getLending = Lending::where('id', $getRestoration['lending_id'])->first();
                $subStock = StuffStock::where('stuff_id', $getLending['stuff_id'])->first();
                $updateStock = $subStock->update([
                    'total_available' => $subStock['total_available'] - $getRestoration['total_goof_stuff'],
                    'total_defac' => $subStock['total_available'] - $getRestoration['total_defac_stuff'],
                ]);

                $deleteRestoration = $getRestoration->delete();

                if ($deleteRestoration && $updateStock) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Delete A Restoration Data');
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function recycleBin()
    {
        try {

            $restorationDeleted = Restoration::onlyTrashed()->get();

            if (!$restorationDeleted) {
                return ResponseFormatter::sendResponse(404, false, 'Deletd Data Restoration Doesnt Exists');
            } else {
                return ResponseFormatter::sendResponse(200, true, 'Successfully Get Delete All Restoration Data', $restorationDeleted);
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {

            $getRestoration = Restoration::onlyTrashed()->where('id', $id);

            if (!$getRestoration) {
                return ResponseFormatter::sendResponse(404, false, 'Restored Data Restoration Doesnt Exists');
            } else {
                $restoreRestoration = $getRestoration->restore();

                if ($restoreRestoration) {
                    $getRestore = Restoration::find($id);
                    $getLending = Lending::where('id', $getRestore['lending_id'])->first();
                    $addStock = StuffStock::where('stuff_id', $getLending['stuff_id'])->first();
                    $updateStock = $addStock->update([
                        'total_available' => $addStock['total_available'] + $getRestore['total_goof_stuff'],
                        'total_defac' => $addStock['total_available'] + $getRestore['total_defac_stuff']
                    ]);

                    return ResponseFormatter::sendResponse(200, true, 'Successfully Restore A Deleted Restoration Data', $getRestore);
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function forceDestroy($id)
    {
        try {

            $getRestoration = Restoration::onlyTrashed()->where('id', $id);

            if (!$getRestoration) {
                return ResponseFormatter::sendResponse(404, false, 'Data Restoration for Permanent Delete Doesnt Exists');
            } else {
                $forceStuff = $getRestoration->forceDelete();

                if ($forceStuff) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Permanent Delete A Restoration Data');
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }
}
