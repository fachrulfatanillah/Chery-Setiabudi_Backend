<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            $user = Users::where('email', $request->email)->first();

            $isValid = $user && Hash::check($request->password, $user->password);

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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => 500,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created user in storage.
     *
     * Validates the incoming request, creates a new user with a UUID,
     * hashes the password, and saves the data to the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'username' => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            $user = new Users();
            $user->uuid       = Str::uuid();
            $user->username   = $request->input('username');
            $user->email      = $request->input('email');
            $user->password   = Hash::make($request->input('password'));
            $user->create_on  = now();
            $user->update_on  = now();
            $user->save();

            return response()->json([
                'success' => true,
                'status'  => 201,
                'data'    => [
                    'uuid'     => $user->uuid,
                    'username' => $user->username,
                    'email'    => $user->email,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => 500,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified user from storage based on UUID.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        try {
            $user = Users::where('uuid', $uuid)->firstOrFail();
            $user->delete();

            return response()->json([
                'success' => true,
                'status'  => 200,
                'message' => 'User deleted successfully.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status'  => 404,
                'error'   => 'User not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => 500,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified user's username and/or password by UUID.
     *
     * This endpoint expects a PUT request with a JSON body.
     * Only the fields included in the request will be updated.
     *
     * ### Endpoint
     * PUT /users/{uuid}
     *
     * ### Request Headers
     * Content-Type: application/json
     *
     * ### Request Body (JSON)
     * {
     *   "username": "new_username",       // optional
     *   "password": "new_secure_password" // optional
     * }
     *
     * ### Successful Response (200 OK)
     * {
     *   "success": true,
     *   "status": 200,
     *   "message": "User updated successfully.",
     *   "data": {
     *     "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *     "username": "new_username",
     *     "email": "user@example.com"
     *   }
     * }
     *
     * ### Error Response (404 Not Found)
     * {
     *   "success": false,
     *   "status": 404,
     *   "error": "User not found."
     * }
     *
     * ### Error Response (500 Internal Server Error)
     * {
     *   "success": false,
     *   "status": 500,
     *   "error": "Error message here"
     * }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        try {
            $request->validate([
                'username' => 'sometimes|string|max:255',
                'password' => 'sometimes|string|min:6',
            ]);

            $user = Users::where('uuid', $uuid)->firstOrFail();

            if ($request->filled('username')) {
                $user->username = $request->input('username');
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            $user->update_on = now();
            $user->save();

            return response()->json([
                'success' => true,
                'status'  => 200,
                'message' => 'User updated successfully.',
                'data'    => [
                    'uuid'     => $user->uuid,
                    'username' => $user->username,
                    'email'    => $user->email,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status'  => 404,
                'error'   => 'User not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => 500,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
