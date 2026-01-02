<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LicenseVerificationController;

Route::post('/licenses/verify', [LicenseVerificationController::class, 'verify']);
