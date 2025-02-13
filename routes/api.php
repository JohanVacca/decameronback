<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ParametricasController;

//Route::resource('hotel', HotelController::class);
Route::post('hotel/store', [HotelController::class, 'crearHotel']);
Route::put('hotel/{hotel}', [HotelController::class, 'actualizarHotel']);
Route::delete('hotel/{hotel}', [HotelController::class, 'eliminarHotel']);
Route::get('hotel', [HotelController::class, 'obtenerHoteles']);
Route::get('hotel/{hotel}', [HotelController::class, 'obtenerHotel']);

Route::get('/parametricas', [ParametricasController::class,'obtenerParametricas']);
