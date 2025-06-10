<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service_Activity;
use Illuminate\Http\JsonResponse;
use Exception;

class Service_Activity_Controller extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        try {
            $activities = Service_Activity::all();

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
