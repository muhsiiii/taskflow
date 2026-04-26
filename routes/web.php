<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/',         fn() => Inertia::render('Dashboard'));
Route::get('/login',    fn() => Inertia::render('Login'));
Route::get('/projects', fn() => Inertia::render('Projects'));
Route::get('/tasks',    fn() => Inertia::render('Tasks'));