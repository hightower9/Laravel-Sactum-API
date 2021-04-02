<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\{CreateUserRequest, LoginUserRequest};

class AuthController extends Controller
{
    public function register(CreateUserRequest $request){
        $user = User::create(collect($request->validated())->merge([
            'password' => Hash::make($request->validated()['password'])
        ])->toArray());

        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response, Response::HTTP_CREATED);
    }

    public function login(LoginUserRequest $request){
        $user = User::where('email', $request->validated()['email'])->firstOrFail();

        if(!Hash::check($request->validated()['password'], $user->password)){
            return response()->json(['message' => 'Invalid Password'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function logout(){
        auth()->user()->tokens()->delete();

        $response = [
            'message' => 'Logged out'
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
