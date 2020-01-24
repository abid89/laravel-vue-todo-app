<?php

Route::get('todos/{tab}','TaskController@fetchAll');
Route::get('get_item','TaskController@item');
Route::post('todos','TaskController@store');
Route::post('edit_todo','TaskController@update');
Route::post('delete_todo/{id}','TaskController@delete');
Route::post('complete_todo/{id}','TaskController@complete');
Route::post('action_all/{token}','TaskController@action');
Route::post('clear_completed','TaskController@clear');
