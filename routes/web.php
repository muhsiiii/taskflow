<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('Landing'));
Route::get('/dashboard', fn() => Inertia::render('Dashboard'));
Route::get('/login', fn() => Inertia::render('Login'));
Route::get('/register', fn() => Inertia::render('Register'));
Route::get('/projects', fn() => Inertia::render('Projects'));
Route::get('/projects/{id}', fn() => Inertia::render('ProjectDetail'));
Route::get('/tasks', fn() => Inertia::render('Tasks'));
Route::get('/docs', fn() => Inertia::render('Docs'));
Route::get('/dashboard/docs', fn() => Inertia::render('DashboardDocs'));