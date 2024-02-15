<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::all();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Successfully Get All User Data',
                    'data' => $users,
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
                'username' => 'required|unique:users',
                'email' => 'required|unique:users',
                'password' => 'required',
                'role' => 'required',
            ]);

            $createUser = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => app('hash')->make($request->password),
                'role' => $request->role,
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Successfully Create A User Data',
                    'data' => $createUser,
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
            $getUser = User::find($id);

            if (!$getUser) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data User Not Found',
                    ],
                    404
                );
            } else {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Successfully Get A User Data',
                        'data' => $getUser,
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

            $getUser = User::find($id);

            if (!$getUser) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data User Not Found',
                    ],
                    404
                );
            } else {
                $this->validate($request, [
                    'username' => 'required',
                    'email' => 'required',
                    'password' => 'required',
                    'role' => 'required'
                ]);

                $updateUser = $getUser->update([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => app('hash')->make($request->password),
                    'role' => $request->role,
                ]);

                if ($updateUser) {
                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Update A User Data',
                            'data' => $getUser,
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
            
            $getUser = User::find($id);

            if (!$getUser) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data User Not Found'
                    ],
                    404
                );
            } else {
                $deleteUser = $getUser->delete();

                if ($deleteUser) {
                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Delete A Data User',
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

            $userDeleted = User::onlyTrashed()->get();

            if (!$userDeleted) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Deleted Data User Doesnt Exists',
                    ],  
                    400
                );
            } else {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Successfully Get Data User Deleted',
                        'data' => $userDeleted,
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

            $getUser = User::onlyTrashed()->where('id', $id);

            if (!$getUser) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Restored Data User Doesnt Exists',
                    ],
                    404
                );
            } else {
                $restoreUser = $getUser->restore();

                if ($restoreUser) {
                    $getRestore = User::find($id);

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Restore A Deleted User Data',
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

            $getUser = User::onlyTrashed()->where('id', $id);

            if (!$getUser) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data User for Permanent Delete Doesnt Exists',
                    ],
                    404
                );
            } else {
                $forceUser = $getUser->forceDelete();

                if ($forceUser) {

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Successfully Permanent Delete A User Data',
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
