<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::post('/produto', [ ProdutoController::class, 'cadastrarProduto' ]);
Route::post('/empresa', [ EmpresaController::class, 'cadastrarEmpresa' ]);
Route::post('/empresa/buscar-pelo-cnpj', [ EmpresaController::class, 'buscarEmpresaPeloCnpj' ]);
Route::post('/categoria', [ CategoriaController::class, 'cadastrarCategoria' ]);
Route::put('/categoria/alterar-status', [ CategoriaController::class, 'alterarStatusCategoria' ]);
Route::get('/categoria/{id}', [ CategoriaController::class, 'buscarCategoriaPeloId' ]);
Route::get('/categoria/empresa/{idEmpresa}', [ CategoriaController::class, 'buscarTodasCategoriasEmpresa' ]);
Route::get('/produto/empresa/{idEmpresa}', [ ProdutoController::class, 'buscarTodosProdutosEmpresa' ]);