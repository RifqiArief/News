<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
// use Validator;


class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'nama'          => 'required',
            'email'         => 'required',
            'password'      => 'required|min:6',
            'id_role'       => 'required'
        ]);

        if ($validator->fails()) {
            $res = array(
                'code'      =>99,
                'message'   => $validator->messages()->first(),
            );
            return response()->json($res, 200);
        }

        $user = new User;
        $user->name         = $request->nama;
        $user->email        = $request->email;
        $user->password     = bcrypt($request->password);
        $user->id_role      = $request->id_role;

        try {
            $user->save();

            $res = array(
                'code'      => 0,
                'message'   => 'sukses',
            );
            return response()->json($res, 200);
        } catch (\Exception $ex) {
            $res = array(
                'code'      => 99,
                'message'   => 'registrasi gagal',
                'error'     => $ex->getMessage()
            );
            return response()->json($res, 500);
        }
    }

    public function login(Request $request) {
        try {            
            $validator = Validator::make($request->all(), [
                'email'         => 'required',
                'password'      => 'required|min:6',
            ]);

            if ($validator->fails()) {
                $res = array(
                    'code'      => 99,
                    'message'   => $validator->messages()->first(),
                );
                return response()->json($res, 200);
            }

            $user = User::where('email', '=', $request->email)->first();

            if ($user == null || !Hash::check($request->password, $user->password)) {
                $res = array(
                    'code'      => 99,
                    'message'   => 'username atau password salah',
                );
                return response()->json($res, 200);
            }

            // if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            //     $user = Auth::user();
            //     $success['token'] =  $user->createToken('nApp')->accessToken;
            //     return response()->json(['success' => $success], 200);
            // }

            // Session::put('id_user',     $user->id);
            // Session::put('nama',        $user->name);
            // Session::put('email',       $user->email);
            // Session::put('id_role',     $user->id_role);

            $data = array(
                'id'        => $user->id,
                'nama'      => $user->name,
                'email'     => $user->email,
                'id_role'   => $user->id_role,
                'token'     => $user->createToken('nApp')->accessToken
            );

            $res = array(
                'code'      => 0,
                'message'   => 'sukses',
                'data'      => $data
            );
            return response()->json($res, 200);
           
        } catch (\Exception $ex) {
            $res = array(
                'code'      => 99,
                'message'   =>  $ex->getMessage(),
            );
            return response()->json($res, 200);
        }
    }
}
