<?php

use App\Filament\Resources\ExpenseResource;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect('/admin/login');
    return view('welcome');
});



//Route::get('/admin/expenses/create/{agent?}',[ExpenseResource::class, 'create'])->name('filament.admin.resources.expenses.create');
