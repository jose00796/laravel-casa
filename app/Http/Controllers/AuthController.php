<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
//use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /* public function __construct()
    {
        $this->middleware(
            'auth:api', ['except' => ['login']]
        );
    } */
    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
        $credentials = $request->only(['user_name', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED );
        }
        return $this->respondWithToken($token);
    }
    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    
}
