<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('to_do_lists', 'ToDoListController')->middleware('auth:api');
