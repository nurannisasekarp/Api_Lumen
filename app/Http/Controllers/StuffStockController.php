<?php

namespace App\Http\Controllers;

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

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Successfully Get All Stuff Stock Data',
                    'data' => $getStuffStock,
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                400
            );
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

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Successfully Create A Stuff Stock Data',
                    'data' => $createStock,
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                400
            );
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
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Stock Stuff Not Found'
                    ],
                    404
                );
            } else {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Successfully Get A Stuff Stock Data',
                        'data' => $getStuffStock,
                    ],
                    200
                );
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                400
            );
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
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Stock Stuff Not Found'
                    ],
                    404
                );
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

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Update A Stuff Data',
                            'data' => $updatedStuffStock,
                        ],
                        200
                    );
                }
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                400
            );
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
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Stuff Stock Not Found',
                    ],
                    404
                );
            } else {
                $deleteStuff = $getStuffStock->delete();

                if ($deleteStuff) {
                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Delete A Stuff Stock Data',
                        ],
                        200
                    );
                }
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                400
            );
        }
    }

    public function recycleBin()
    {
        try {

            $stuffStockDeleted = StuffStock::onlyTrashed()->get();

            if (!$stuffStockDeleted) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Deletd Data Stuff Stock Doesnt Exists',
                    ],
                    404
                );
            } else {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Successfully Get Deleted Stuff Stock Data',
                        'data' => $stuffStockDeleted,
                    ],
                    200
                );
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                400
            );
        }
    }

    public function restore($id)
    {
        try {

            $getStuffStock = StuffStock::onlyTrashed()->where('id', $id);

            if (!$getStuffStock) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Restored Data Stuff Stock Doesnt Exists',
                    ],
                    404
                );
            } else {
                $restoreStuff = $getStuffStock->restore();

                if ($restoreStuff) {
                    $getRestore = StuffStock::find($id);

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Restore A Deleted Stuff Stock Data',
                            'data' => $getRestore,
                        ],
                        200
                    );
                }
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                400
            );
        }
    }

    public function forceDestroy($id)
    {
        try {

            $getStuffStock = StuffStock::onlyTrashed()->where('id', $id);

            if (!$getStuffStock) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Stuff Stock for Permanent Delete Doesnt Exists',
                    ],
                    404
                );
            } else {
                $forceStuff = $getStuffStock->forceDelete();

                if ($forceStuff) {

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Permanent Delete A Stuff Stock Data',
                        ],
                        200
                    );
                }
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                400
            );
        }
    }

    public function addStock(Request $request, $id)
    {
        try {

            $getStuffStock = StuffStock::find($id);

            if (!$getStuffStock) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Stuff Stock Not Found',
                    ],
                    404
                );
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

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Add A Stock Of Stuff Stock Data',
                            'data' => $getStockAdded,
                        ],
                        200
                    );
                }
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                400
            );
        }
    }

    public function subStock(Request $request, $id)
    {
        try {

            $getStuffStock = StuffStock::find($id);

            if (!$getStuffStock) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Stuff Stock Not Found',
                    ],
                    404
                );
            } else {
                $this->validate($request, [
                    'total_available' => 'required',
                    'total_defac' => 'required',
                ]);

                $isStockAvailable = $getStuffStock['total_available'] - $request->total_available;
                $isStockDefac = $getStuffStock['total_defac'] - $request->total_defac;

                if ($isStockAvailable < 0 || $isStockDefac < 0) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'A Substraction Stock Cant Less Than A Stock Stored',
                        ],
                        400
                    );
                } else {
                    $subStock = $getStuffStock->update([
                        'total_available' => $isStockAvailable,
                        'total_defac' => $isStockDefac,
                    ]);

                    if ($subStock) {
                        $getStockSub = StuffStock::where('id', $id)->with('stuff')->first();

                        return response()->json(
                            [
                                'success' => true,
                                'message' => 'Successfully Add A Stock Of Stuff Stock Data',
                                'data' => $getStockSub,
                            ],
                            200
                        );
                    }
                }
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                400
            );
        }
    }
}
