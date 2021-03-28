<?php

use Illuminate\Support\Facades\Route;
use OZiTAG\Tager\Backend\Import\Controllers\ImportController;

Route::group(['prefix' => 'admin/tager', 'middleware' => ['passport:administrators', 'auth:api']], function () {
    Route::get('/import/strategies', [ImportController::class, 'strategies']);
    Route::get('/import', [ImportController::class, 'index']);
    Route::post('/import', [ImportController::class, 'store']);
});
