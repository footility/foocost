<?php

use Illuminate\Support\Facades\Route;
use Footility\Foocost\Http\Controllers\CostController;

Route::get('/foo/cost', [CostController::class, 'calculateCosts'])->name('foocost.calculate');
