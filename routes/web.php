<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\expensesController;

Auth::routes();

Route::get('/', function (){
    return view('welcome');
});
Route::get('/home', [HomeController::class, 'index'])->name('home');

//Groups routes
Route::get('/addgroup',function(){
    return view('addgroup');
})->middleware('auth')->name('addgroup');

Route::get('groups/{id}', [GroupsController::class, 'Show'])->middleware('auth')->name('group');
Route::get('deleteMember/{gid}/{mid}', [GroupsController::class, 'deleteMember'])->middleware('auth')->name('deleteMember');
Route::post('addMember/{gid}', [GroupsController::class, 'addMember'])->middleware('auth')->name('addMember');
Route::post('/addgroup',[GroupsController::class,'add'])->middleware('auth')->name('addgroup');

//expenses routes
Route::get('expenses/{gid}',[expensesController::class,'showExpenses'])->middleware('auth')->name('expenses');
Route::get('addExpenses/{gid}',[expensesController::class,'showAddExpenses'])->middleware('auth')->name('addexpenses');
Route::post('addExpenses/{gid}',[expensesController::class,'addexpences'])->middleware('auth')->name('addexpenses');

Route::get('viewExpense/{eid}/g/{gid}',[expensesController::class,'viewExpense'])->middleware('auth')->name('viewExpense');

Route::post('repay/{id}',[expensesController::class,'repay'])->middleware('auth')->name('repay');