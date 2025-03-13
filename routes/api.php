<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\BalanceManageController;

Route::post('register',[UserController::class,'Register']);
Route::post('verifyOtp',[UserController::class,'verifyOtp']);
Route::post('login',[UserController::class,'login']);
Route::post('logout',[UserController::class,'logout'])->middleware('auth:sanctum');
Route::get('logout',[UserController::class,'logout'])->middleware('auth:sanctum');

// all group for home page
Route::get('groups',[GroupController::class,'index'])->middleware('auth:sanctum');
//single group
Route::get('group/{gid}',[GroupController::class,'show'])->middleware('auth:sanctum');
//adding groups
Route::get('addgroup',[GroupController::class,'showAdd'])->middleware('auth:sanctum');
Route::post('addgroup',[GroupController::class,'add'])->middleware('auth:sanctum');
// deleting member
Route::get('delMember/{gid}/{mid}',[GroupController::class,'deleteMember'])->middleware('auth:sanctum');
// add member later to group
Route::post('addMember/{gid}',[GroupController::class,'addMember'])->middleware('auth:sanctum');

// expense
Route::get('allExpenses/{gid}',[ExpenseController::class,'showExpenses'])->middleware('auth:sanctum');
// single expense
Route::get('viewExpense/{eid}/{gid}',[ExpenseController::class,'viewExpense'])->middleware('auth:sanctum');
// add expense page
Route::get('addExpense/{gid}',[ExpenseController::class,'showAddExpenses'])->middleware('auth:sanctum');
// add expense
Route::post('addExpense/{gid}',[ExpenseController::class,'addexpences'])->middleware('auth:sanctum');
// repay
Route::post('repay',[ExpenseController::class,'repay'])->middleware('auth:sanctum');

Route::get('deleteExpense/{eid}',[ExpenseController::class,'delete'])->middleware('auth:sanctum');



// add user Balance and Expense 

Route::get('/addBalance/{id?}',[BalanceManageController::class,'addBalance'])->middleware('auth:sanctum');

Route::post('/addBalance/{id?}',[BalanceManageController::class,'storeBalance'])->middleware('auth:sanctum');

Route::get('/addUserExpense/{id?}',[BalanceManageController::class,'addUserExpense'])->middleware('auth:sanctum');
Route::post('/addUserExpense/{id}',[BalanceManageController::class,'storeExpense'])->middleware('auth:sanctum');

// user expenses view
Route::get('/userExpenses',[BalanceManageController::class, 'view'])->middleware('auth:sanctum');
Route::post('/userExpenses',[BalanceManageController::class, 'viewSearch'])->middleware('auth:sanctum');

// today
Route::get('/today',[BalanceManageController::class, 'today'])->middleware('auth:sanctum');
// thisweek
Route::get('/thisweek',[BalanceManageController::class, 'thisweek'])->middleware('auth:sanctum');
// thismonth
Route::get('/thismonth',[BalanceManageController::class, 'thismonth'])->middleware('auth:sanctum');

// delete user expense
Route::get('/deleteUserExpense/{id}',[BalanceManageController::class, 'deleteUserExpense'])->middleware('auth:sanctum');

