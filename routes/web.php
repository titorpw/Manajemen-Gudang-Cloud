<?php

use Illuminate\Support\Facades\Route;

Route::redirect("/", "/login");

Route::get("/login", function () {
    return view("auth.login");
});

Route::get("/dashboard", function () {
    return view("dashboard");
});

Route::get("/katalog", function () {
    return view("katalog");
});

Route::get("/mutasi", function () {
    return view("mutasi");
});
