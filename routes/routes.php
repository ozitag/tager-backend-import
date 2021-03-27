<?php

use Illuminate\Support\Facades\Route;
use OZiTAG\Tager\Backend\Export\Controllers\ExportController;

Route::group(['prefix' => 'admin/tager', 'middleware' => ['passport:administrators', 'auth:api']], function () {
    Route::get('/import/strategies', [ExportController::class, 'strategies']);
    Route::get('/import', [ExportController::class, 'index']);
    Route::post('/import', [ExportController::class, 'store']);
});
