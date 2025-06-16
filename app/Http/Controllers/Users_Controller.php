<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
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
}
