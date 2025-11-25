<?php

use Illuminate\Support\Facades\Route;
use Shetabit\Visitor\Middlewares\LogVisits;

Route::get('/', function () {
    return view('welcome');
})->middleware(LogVisits::class);
