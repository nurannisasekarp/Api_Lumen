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
     *2
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

                if ($request->password) {
                $updateUser = $getUser->update([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => app('hash')->make($request->password),
                    'role' => $request->role,
                ]);
            } else {
                $updateUser = $getUser->update([
                    'username' => $request->username,
                    'email' => $request->email,
                    'role' => $request->role,
                ]);
            }

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
                $restoreUser = User::onlyTrashed()->where('id', $id)->restore();
                // where => mencari berdasarkan kolom spesifik yang ingin dicari
                // find => mencari berdasarkan kolom primary key
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
                'password' => 'required|min:8',
            ], [
                'email.required' => 'Email harus diisi',
                'password.required' => 'Password harus diisi',
                'password.min' => 'Password minimal 8 karakter'
            ]);

            $user = User::where('email', $request->email)->first(); // Mencari dan mendapatkan data user berdasarkan email yang digunakan untuk login

            if (!$user) {
                // Jika email tidak terdafatar maka akan dikembalikan response error
                return ResponseFormatter::sendResponse(400, false, 'Login Failed! User Doesnt Exists');
            } else {
                // Jika email terdaftar, selanjutnya pencocokan password yang diinput dengan password di database dengan menggunakan Hash::check().
                $isValid = Hash::check($request->password, $user->password);

                if (!$isValid) {
                    // Jika password tidak cocok maka akan dikembalikan dengan response error
                    return ResponseFormatter::sendResponse(400, false, 'Login Failed! Password Doesnt Match');
                } else {
                    // Jika password sesuai selanjutnya akan membuat token
                    // bin2hex digunakan untuk dapat mengonversi string karakter ASCII menjadi nilai heksadesimal
                    // random_bytes menghasilkan byte pseudo-acak yang aman secara kriptografis dengan panjang 40 karakter
                    $generateToken = bin2hex(random_bytes(40));
                    // Token inilah nanti yang digunakan pada proses authentication user yang login

                    $user->update([
                        'token' => $generateToken
                        // update kolom token dengan value hasil dari generateToken di row user yang ingin login
                    ]);

                    return ResponseFormatter::sendResponse(200, true, 'Login Successfully', $user);
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return ResponseFormatter::sendResponse(400, false, 'Login Failed! User Doesnt Exists');
            } else {
                if (!$user->token) {
                    return ResponseFormatter::sendResponse(400, false, 'Logout Failed! User Doesnt Login Scien');
                } else {
                    $logout = $user->update(['token' => null]);

                    if ($logout) {
                        return ResponseFormatter::sendResponse(200, true, 'Logout Successfully');
                    }
                }
            }
        } catch (\Exception $e) {
            return ResponseFormatter::sendResponse(400, false, $e->getMessage());
        }
    }
}
