<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Exception;

class Users_Controller extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $activities = Users::all();

            return response()->json([
                'success' => true,
                'status'  => 200,
                'data'    => $activities,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate user login by email and password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = Users::where('email', $request->email)->first();

            $isValid = $user && $user->password === $request->password;

            return response()->json([
                'success' => true,
                'status'  => 200,
                'valid'   => $isValid,
                'data'    => $isValid ? [
                    'email'    => $user->email,
                    'username' => $user->username,
                    'uuid'     => $user->uuid,
                ] : null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
            ], 500);
        }
    }
}
