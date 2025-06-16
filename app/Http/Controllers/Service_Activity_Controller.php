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
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // $activities = Service_Activity::all();
            $activities = Service_Activity::select('uuid', 'image_url', 'image_title', 'status', 'create_on', 'update_on')->get();
            // $activities = Service_Activity::paginate(1);

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
            $file = $request->file('image');

            $filename = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';

            $tempPath = $file->store('temp', 'public');
            $fullTempPath = storage_path('app/public/' . $tempPath);

            $destination = storage_path('app/public/service_activities/' . $filename);

            $mime = $file->getMimeType();
            switch ($mime) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($fullTempPath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($fullTempPath);
                    imagepalettetotruecolor($image);
                    break;
                default:
                    throw new \Exception('Only PNG, JPG, and JPEG image formats are supported.');
            }

            imagewebp($image, $destination, 100);
            imagedestroy($image);

            if (file_exists($fullTempPath)) {
                unlink($fullTempPath);
            }

            $webpRelativePath = 'service_activities/' . $filename;

            $data = new Service_Activity([
                'uuid'        => Str::uuid()->toString(),
                'image_url'   => $webpRelativePath,
                'image_title' => $validated['image_title'],
                'status'      => 1,
                'create_on'   => now(),
                'update_on'   => now(),
            ]);

            $data->save();

            return response()->json([
                'success' => true,
                'status'  => 201,
                'data'    => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        try {
            $activity = Service_Activity::where('uuid', $uuid)->firstOrFail();

            $imagePath = storage_path('app/public/' . $activity->image_url);

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $activity->delete();

            return response()->json([
                'success' => true,
                'status'  => 200,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status'  => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        try {
            $validated = $request->validate([
                'image_title' => 'sometimes|required|string|max:255',
                'status'      => 'sometimes|required|in:0,1',
            ]);

            $activity = Service_Activity::where('uuid', $uuid)->firstOrFail();

            if ($request->has('image_title')) {
                $activity->image_title = $request->input('image_title');
            }

            if ($request->has('status')) {
                $activity->status = $request->input('status') === '1' ? true : false;
            }

            $activity->update_on = now();
            $activity->save();

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $activity,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateImage(Request $request, string $uuid): JsonResponse
    {
        try {
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $activity = Service_Activity::where('uuid', $uuid)->firstOrFail();

            $file = $request->file('image');
            $filename = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';

            $tempPath = $file->store('temp', 'public');
            $fullTempPath = storage_path('app/public/' . $tempPath);
            $destination = storage_path('app/public/service_activities/' . $filename);

            $mime = $file->getMimeType();
            switch ($mime) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($fullTempPath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($fullTempPath);
                    imagepalettetotruecolor($image);
                    break;
                default:
                    throw new \Exception('Only PNG, JPG, and JPEG formats are supported.');
            }

            imagewebp($image, $destination, 100);
            imagedestroy($image);

            if (file_exists($fullTempPath)) {
                unlink($fullTempPath);
            }

            $oldPath = storage_path('app/public/' . $activity->image_url);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }

            $activity->image_url = 'service_activities/' . $filename;
            $activity->update_on = now();
            $activity->save();

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $activity,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
