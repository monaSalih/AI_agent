<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\StudioimageController;

Route::resource('studios', StudioController::class);
