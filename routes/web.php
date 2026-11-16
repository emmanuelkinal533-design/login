<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
})->name('login');

// Rutas de Login y Registro (GET para mostrar la vista, POST para procesar los datos)
Route::post('/login', [LoginController::class, 'authenticate']);

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', [LoginController::class, 'register']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas del panel
Route::middleware('auth')->group(function () {
    Route::get('/panel', function () {
        return view('panel');
    });

    Route::get('/api/usuarios', function () {
        return User::select('id', 'name', 'email', 'created_at')->orderByDesc('id')->get();
    });

    Route::delete('/api/usuarios/{id}', function ($id) {
        if (auth()->id() == $id) {
            return response()->json(['message' => 'No puedes eliminar tu propio usuario activo.'], 400);
        }
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    });
});