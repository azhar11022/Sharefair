<?php

use App\Http\Controllers\RequestJoin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\expensesController;
use App\Http\Controllers\BalanceManageController;

Auth::routes();

Route::get('/', function (){
    return view('welcome');
});
Route::get('/home', [HomeController::class, 'index'])->name('home');

//Groups routes
Route::get('/addgroup',[GroupsController::class,'showAddGroup'])->middleware('auth')->name('addgroup');

Route::get('groups/{id}', [GroupsController::class, 'Show'])->middleware('auth')->name('group');
Route::get('deleteMember/{gid}/{mid}', [GroupsController::class, 'deleteMember'])->middleware('auth')->name('deleteMember');
Route::post('addMember/{gid}', [GroupsController::class, 'addMember'])->middleware('auth')->name('addMember');
Route::post('/addgroup',[GroupsController::class,'add'])->middleware('auth')->name('addgroup');

//expenses routes
Route::get('expenses/{gid}',[expensesController::class,'showExpenses'])->middleware('auth')->name('expenses');
Route::get('addExpenses/{gid}',[expensesController::class,'showAddExpenses'])->middleware('auth')->name('addexpenses');
Route::post('addExpenses/{gid}',[expensesController::class,'addexpences'])->middleware('auth')->name('addexpenses');

Route::get('viewExpense/{eid}/g/{gid}',[expensesController::class,'viewExpense'])->middleware('auth')->name('viewExpense');

Route::post('repay',[expensesController::class,'repay'])->middleware('auth')->name('repay');

Route::get('/requestJoin/{email}/name/{name}',[RequestJoin::class,'showPage'])->middleware('auth')->name('requestJoin');
Route::post('/requestJoin',[RequestJoin::class,'addUser'])->middleware('auth')->name('requestJoins');

Route::get('/deleteExpense/{id}',[expensesController::class,'delete'])->middleware('auth')->name('deleteExpense');




// add user Balance and Expense 

Route::get('/addBalance/{id?}',[BalanceManageController::class,'addBalance'])->middleware('auth')->name('addUserBalance');

Route::post('/addBalance/{id}',[BalanceManageController::class,'storeBalance'])->middleware('auth')->name('storeBalance');

Route::get('/addUserExpense/{id?}',[BalanceManageController::class,'addUserExpense'])->middleware('auth')->name('addUserExpense');
Route::post('/addUserExpense/{id}',[BalanceManageController::class,'storeExpense'])->middleware('auth')->name('storeExpense');

// user expenses view
Route::get('/userExpenses',[BalanceManageController::class, 'view'])->middleware('auth')->name('userExpenses');
Route::post('/userExpenses',[BalanceManageController::class, 'viewSearch'])->middleware('auth')->name('userExpenses');

// today
Route::get('/today',[BalanceManageController::class, 'today'])->middleware('auth')->name('toDay');
// thisweek
Route::get('/thisweek',[BalanceManageController::class, 'thisweek'])->middleware('auth')->name('thisWeek');
// thismonth
Route::get('/thismonth',[BalanceManageController::class, 'thismonth'])->middleware('auth')->name('thisMonth');

// delete user expense
Route::get('/deleteUserExpense/{id}',[BalanceManageController::class, 'deleteUserExpense'])->middleware('auth')->name('deleteUserExpense');

