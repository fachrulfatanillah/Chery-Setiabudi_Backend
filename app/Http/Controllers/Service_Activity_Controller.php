<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service_Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Exception;

class Service_Activity_Controller extends Controller
{

    /**
     * Display a listing of the resource.
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

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image'        => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'image_title'  => 'required|string|max:255',
        ]);

        try {
            $imagePath = $request->file('image')->store('service_activities', 'public');

            $activity = new Service_Activity([
                'uuid'        => Str::uuid()->toString(),
                'image_url'   => $imagePath,
                'image_title' => $validated['image_title'],
                'status'      => 1,
                'create_on'   => now(),
                'update_on'   => now(),
            ]);

            $activity->save();

            return response()->json([
                'success' => true,
                'status'  => 201,
                'data'    => $activity
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
