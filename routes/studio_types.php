<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudiotypesController;

Route::resource('studiotypes', StudiotypesController::class);