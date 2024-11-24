<?php

namespace App\Http\Controllers;

use App\Http\Requests\registerRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
                'email'=>'required|email',
                'password'=>'required'
            ]);
        $credentials = $request->only('email','password');

            if (Auth::attempt($credentials))
            {
                $user = Auth::user();
                $token = $user->createToken('API Token')->accessToken;

                return response(['token'=>$token,'user'=>$user],200);
            }

            return response()->json(['error'=>'Unauthorized'], status: 401);


    }

    public function register (registerRequest $request):JsonResponse
    {
        try {
            $user = User::create([
                'name'=> $request->name,
                'email'=> $request->email,
                'password' => Hash::make($request->password),
                'addressee'=> $request->addressee,
                'phoneNumber'=> $request->phoneNumber,
                'role'=> $request->role,

            ]);

            $token = $user->createToken('API Token')->accessToken;

            return  response()->json(['token'=>$token,'user',$user]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'errors' => [$e->getMessage()],
            ], 500);
        }


    }
}
