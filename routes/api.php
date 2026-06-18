<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\MutationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/login", [AuthController::class, "login"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout", [AuthController::class, "logout"]);

    Route::get("/user", function (Request $request) {
        return $request->user();
    });

    Route::get("/barang", [ItemController::class, "index"]);

    Route::post("/barang", [ItemController::class, "store"]);

    Route::put("/barang/{id}", [ItemController::class, "update"]);

    Route::delete("/barang/{id}", [ItemController::class, "destroy"]);

    Route::get("/mutasi", [MutationController::class, "index"]);

    Route::post("/mutasi", [MutationController::class, "store"]);
});
