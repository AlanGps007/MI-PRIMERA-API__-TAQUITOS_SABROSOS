<?php

use App\Http\Controllers\Api\TaquitoController;
use Illuminate\Support\Facades\Route;

Route::apiResource('taquitos', TaquitoController::class);
