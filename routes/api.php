<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EmpresaController;
use Illuminate\Support\Facades\Route;

Route::post('/empresa', [ EmpresaController::class, 'cadastrarEmpresa' ]);
Route::post('/empresa/buscar-pelo-cnpj', [ EmpresaController::class, 'buscarEmpresaPeloCnpj' ]);
Route::post('/categoria', [ CategoriaController::class, 'cadastrarCategoria' ]);
Route::get('/categoria/{id}', [ CategoriaController::class, 'buscarCategoriaPeloId' ]);