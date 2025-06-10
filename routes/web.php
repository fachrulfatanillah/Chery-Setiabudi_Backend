<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/')->group(function () {
    Route::get('/', function () {
        return response()->json(['message' => 'Hello API!']);
    });
});
