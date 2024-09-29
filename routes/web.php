<?php

use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\StudentResource\Pages\AdmissionDocuments;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect('/admin/login');
    return view('welcome');
});


Route::get('/admin/student-resource/{record}/admission-documents', AdmissionDocuments::class)
    ->name('filament.resources.student-resource.admission-documents');

//Route::get('/admin/expenses/create/{agent?}',[ExpenseResource::class, 'create'])->name('filament.admin.resources.expenses.create');
