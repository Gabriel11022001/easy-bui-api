<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ LoginController::class, 'login' ]);
Route::post('/logout', [ LoginController::class, 'logout' ])->middleware(['auth:sanctum']);
Route::post('/usuario/autenticado', [ LoginController::class, 'obterUsuarioAutenticado' ])->middleware(['auth:sanctum']);

// mapeando todas as rotas que exigem autenticação
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/cliente', [ ClienteController::class, 'cadastrarCliente' ]);
    Route::post('/produto', [ ProdutoController::class, 'cadastrarProduto' ]);
    Route::post('/produto/buscar-pela-descricao', [ ProdutoController::class, 'buscarProdutosPelaDescricao' ]);
    Route::post('/empresa', [ EmpresaController::class, 'cadastrarEmpresa' ]);
    Route::post('/empresa/buscar-pelo-cnpj', [ EmpresaController::class, 'buscarEmpresaPeloCnpj' ]);
    Route::post('/categoria', [ CategoriaController::class, 'cadastrarCategoria' ]);
    Route::put('/categoria/alterar-status', [ CategoriaController::class, 'alterarStatusCategoria' ]);
    Route::get('/categoria/{id}', [ CategoriaController::class, 'buscarCategoriaPeloId' ]);
    Route::get('/produto/{id}', [ ProdutoController::class, 'buscarProdutoPeloId' ]);
    Route::get('/categoria/empresa/{idEmpresa}', [ CategoriaController::class, 'buscarTodasCategoriasEmpresa' ]);
    Route::get('/produto/empresa/{idEmpresa}', [ ProdutoController::class, 'buscarTodosProdutosEmpresa' ]);
    Route::get('/produto/empresa/abaixo-estoque-minimo/{idEmpresa}', [ ProdutoController::class, 'buscarProdutosAbaixoEstoqueMinimo' ]);
    Route::get('/produto/categoria/{idCategoria}/{idEmpresa}', [ ProdutoController::class, 'buscarProdutosPelaCategoria' ]);
    Route::get('/categoria/buscar-categorias-ativas/{idEmpresa}', [ CategoriaController::class, 'buscarCategoriasAtivas' ]);
    Route::get('/cliente/{idUsuario}', [ ClienteController::class, 'buscarTodosClientesUsuario' ]);
});
