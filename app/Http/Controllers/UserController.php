<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

use App\Mail\ActiveUser;
use App\Mail\ValidateUser;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function allUsers()
    {
        $data = User::all();
        return response()->json($data, 200);
    }

    public function registerUser(Request $request)
    {
        Log::info($request);
        $validator = Validator::make($request->all(), [
            'user_name' => 'required',
            'name' => 'required',
            'lastname' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            
            'password' => ['required',
                Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::where([
            'user_name' => $request['user_name']
        ])->get();

        if ($user->count()) {
            return response()->json(['error', 'User exists'], 400);
        }

        $user = User::create([
            'user_name' => $request->get('user_name'),
            'name' => $request->get('name'),
            'lastname' => $request->get('lastname'),
            'email' => Str::lower($request->get('email')),
            'password' => Hash::make($request->get('password')),
            'active' => false
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user', 'token'), 201);
    }

    public function validateUser(Request $request)
    {
        Log::info($request);
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::where([
            'user_name' => $request['user_name']
        ])->get();

        if ($user->count()) {
            $api_token = Str::random(50);
            User::query()->where('user_name', $request->user_name)->update(['api_token' => $api_token]);

            $email = DB::table('users')->where('user_name', $request->user_name)->value('email');

            Mail::to($email)->send(new ValidateUser($request->user_name, $api_token));
            
        }else {
            return response()->json(['error' => 'User does not exists im DB'], 400);
        }

        return response()->json(['message' => 'User validated successfull'], 200);
    }

    public function ActiveUser(Request $request)
    {
        Log::info($request);
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::where([
            'user_name' => $request['user_name']
        ])->get();

        if ($user->count()) {
            User::query()->where('user_name', $request->user_name)->update(['active' => true]);
        }else {
            return response()->json(['error' => 'User does not exists in DB'], 400);
        }

        $email = DB::table('users')->where('user_name', $request->user_name)->value('email');

        Mail::to($email)->send(new ActiveUser($request->user_name));

        return response()->json(['message' => 'User Actived successfull'], 200);
    }

    public function deleteUser($id_user)
    {
        $data = User::find($id_user);

        try {
            $data->delete();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error to delete user'], 400);
        }

        return response()->json(['message' => 'User deleted'], 200);
    }
}
