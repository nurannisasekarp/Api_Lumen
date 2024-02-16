<?php

namespace App\Http\Controllers;

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

            $getInboundStuff = InboundStuff::with('stuff')->get();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Successfully Get All Inbound Stuff Data',
                    'data' => $getInboundStuff,
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
                'total' => 'required',
                'date' => 'required',
                'proff_file' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            if ($request->hasFile('proff_file')) {
                $proof = $request->file('proff_file');
                $destinationPath = 'proof/';
                $proofName = date('YmdHis') . "." . $proof->getClientOriginalExtension();
                $proof->move($destinationPath, $proofName);
            }

            $createStock = InboundStuff::create([
                'stuff_id' => $request->stuff_id,
                'total' => $request->total,
                'date' => $request->date,
                'proff_file' => $proofName,
            ]);

            if ($createStock) {
                $getStuff = Stuff::where('id', $request->stuff_id)->first();
                $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();

                if ($getStuffStock) {
                    $updateStock = StuffStock::create([
                        'stuff_id' => $request->stuff_id,
                        'total_available' => $getStuffStock['total_available'] + $request->total,
                        'total_defac' => 0,
                    ]);
                } else {
                    $updateStock = $getStuffStock->update(
                        [
                            'stuff_id' => $request->stuff_id,
                            'total_available' => $getStuffStock['total_available'] + $request->total,
                            'total_defac' => 0,
                        ]);    
                }
                
                if ($updateStock) {
                    $stuff = [
                        'stuff' => $getStuff,
                        'inboundStuff' => $createStock,
                        'stuffStock' => $getStuffStock,
                    ];

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Create A Stuff Stock Data',
                            'data' => $stuff,
                        ],
                        200
                    );
                } else {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Failed To Update A Stuff Stock Data',
                        ],
                        400
                    );
                }
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Failed To Create A Inbound Stuff Data',
                    ],
                    400
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $getInboundStuff = InboundStuff::with('stuff')->find($id);

            if (!$getInboundStuff) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Inbound Stuff Not Found'
                    ],
                    404
                );
            } else {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Successfully Get A Inbound Stock Data',
                        'data' => $getInboundStuff,
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
            $getInboundStuff = InboundStuff::find($id);

            if (!$getInboundStuff) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Inbound Stuff Not Found'
                    ],
                    404
                );
            } else {

                $this->validate($request, [
                    'stuff_id' => 'required',
                    'total' => 'required',
                    'date' => 'required',
                ]);

                if ($request->hasFile('proff_file')) {
                    $proof = $request->file('proff_file');
                    $destinationPath = 'proof/';
                    $proofName = date('YmdHis') . "." . $proof->getClientOriginalExtension();
                    $proof->move($destinationPath, $proofName);
                } else {
                    $proffName = $getInboundStuff['proff_file'];
                }

                $getStuff = Stuff::where('id', $getInboundStuff['stuff_id'])->first();
                $getStuffStock = StuffStock::where('stuff_id', $getInboundStuff['stuff_id'])->first();

                // if ($getStuff['id'] != $request->stuff_id) {

                $updatedStock = $getStuffStock->update([
                    'total_available' => $getStuffStock['total_available'] - $getInboundStuff['total'],
                ]);

                $updateInbound = $getInboundStuff->update([
                    'stuff_id' => $request->stuff_id,
                    'total' => $request->total,
                    'date' => $request->date,
                    'proff_file' => $proffName,
                ]);

                if ($updateInbound) {
                    $updateStock = $getStuffStock->updateOrCreate([
                        'stuff_id' => $request->stuff_id, 
                    ],
                    [
                        'stuff_id' => $request->stuff_id,
                        'total_available' => $getStuffStock['total_available'] + $request->total,
                        'total_defac' => 0,
                    ]);
                }

                $stuff = [
                    'stuff' => $getStuff,
                    'inboundStuff' => $getInboundStuff,
                    'stuffStock' => $getStuffStock,
                ];

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Successfully Update A Stuff Stock Data',
                        'data' => $stuff,
                    ],
                    200
                );
                // }  else {
                //     $updateInbound = 
                // }

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
        //
    }
}
