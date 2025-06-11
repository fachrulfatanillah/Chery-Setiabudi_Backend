<?php

use App\Http\Controllers\Service_Activity_Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('/')->group(function () {
    Route::get('/', function () {
        return response()->json(['message' => 'Hello API!']);
    });
});

Route::apiResource('service-activities', Service_Activity_Controller::class);
Route::post('/service-activities/{uuid}/image', [Service_Activity_Controller::class, 'updateImage']);
