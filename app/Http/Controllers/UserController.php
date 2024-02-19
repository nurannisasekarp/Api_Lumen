<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

            return ResponseFormatter::sendResponse(200, true, 'Successfully Get All User Data', $users);
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
                'username' => 'required|unique:users',
                'email' => 'required|unique:users',
                'password' => 'required',
                'role' => 'required',
            ]);

            $createUser = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return ResponseFormatter::sendResponse(200, true, 'Successfully Create A User Data', $createUser);
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
            $getUser = User::find($id);

            if (!$getUser) {
                return ResponseFormatter::sendResponse(404, false, 'Data User Not Found');
            } else {
                return ResponseFormatter::sendResponse(200, true, 'Successfully Get A User Data', $getUser);
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

            $getUser = User::find($id);

            if (!$getUser) {
                return ResponseFormatter::sendResponse(404, false, 'Data User Not Found');
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
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Update A User Data', $getUser);
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

            $getUser = User::find($id);

            if (!$getUser) {
                return ResponseFormatter::sendResponse(404, false, 'Data User Not Found');
            } else {
                $deleteUser = $getUser->delete();

                if ($deleteUser) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Delete A User Data');
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function recycleBin()
    {
        try {

            $userDeleted = User::onlyTrashed()->get();

            if (!$userDeleted) {
                return ResponseFormatter::sendResponse(404, false, 'Deletd Data User Doesnt Exists');
            } else {
                return ResponseFormatter::sendResponse(200, true, 'Successfully Get Delete All User Data', $userDeleted);
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $getUser = User::onlyTrashed()->where('id', $id);

            if (!$getUser) {
                return ResponseFormatter::sendResponse(404, false, 'Data User Not Found');
            } else {
                $restoreUser = $getUser->restore();

                if ($restoreUser) {
                    $getRestore = User::find($id);

                    return ResponseFormatter::sendResponse(200, true, 'Successfully Restore A Deleted User Data', $getRestore);
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function forceDestroy($id)
    {
        try {

            $getUser = User::onlyTrashed()->where('id', $id);

            if (!$getUser) {
                return ResponseFormatter::sendResponse(404, false, 'Data User for Permanent Delete Doesnt Exists');
            } else {
                $forceUser = $getUser->forceDelete();

                if ($forceUser) {
                    return ResponseFormatter::sendResponse(200, true, 'Successfully Permanent Delete A User Data');
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return ResponseFormatter::sendResponse(400, false, 'Login Failed! User Doesnt Exists');
            } else {
                $isValid = Hash::check($request->password, $user->password);

                if (!$isValid) {
                    return ResponseFormatter::sendResponse(400, false, 'Login Failed! Password Doesnt Match');
                } else {
                    $generateToken = bin2hex(random_bytes(40));

                    $user->update([
                        'token' => $generateToken
                    ]);

                    return ResponseFormatter::sendResponse(200, true, 'Login Successfully', $user);
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }
}
