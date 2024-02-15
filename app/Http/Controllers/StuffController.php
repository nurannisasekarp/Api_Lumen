<?php

namespace App\Http\Controllers;

use App\Models\Stuff;
use Illuminate\Http\Request;

class StuffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $stuffs = Stuff::all();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Successfully Get All Stuff Data',
                    'data' => $stuffs,
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

            $createStuff = Stuff::create($request->all());

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Successfully Create A Stuff Data',
                    'data' => $createStuff,
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

            $getStuff = Stuff::find($id);

            if (!$getStuff) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Stuff Not Found',
                    ],
                    404
                );
            } else {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Successfully Get A Stuff Data',
                        'data' => $getStuff,
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

            $getStuff = Stuff::find($id);

            if (!$getStuff) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Stuff Not Found',
                    ],
                    404
                );
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

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Update A Stuff Data',
                            'data' => $getUpdate,
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

            $getStuff = Stuff::find($id);

            if (!$getStuff) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Stuff Not Found',
                    ],
                    404
                );
            } else {
                $deleteStuff = $getStuff->delete();

                if ($deleteStuff) {
                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Delete A Stuff Data',
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

            $stuffDeleted = Stuff::onlyTrashed()->get();

            if (!$stuffDeleted) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Deletd Data Stuff Doesnt Exists',
                    ],
                    404
                );
            } else {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Successfully Get Deleted Stuff Data',
                        'data' => $stuffDeleted,
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

            $getStuff = Stuff::onlyTrashed()->where('id', $id);

            if (!$getStuff) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Restored Data Stuff Doesnt Exists',
                    ],
                    404
                );
            } else {
                $restoreStuff = $getStuff->restore();

                if ($restoreStuff) {
                    $getRestore = Stuff::find($id);

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Restore A Deleted Stuff Data',
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

            $getStuff = Stuff::onlyTrashed()->where('id', $id);

            if (!$getStuff) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data Stuff for Permanent Delete Doesnt Exists',
                    ],
                    404
                );
            } else {
                $forceStuff = $getStuff->forceDelete();

                if ($forceStuff) {

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Permanent Delete A Stuff Data',
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
}
