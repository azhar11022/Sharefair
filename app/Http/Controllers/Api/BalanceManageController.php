<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\user_expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BalanceManageController extends Controller
{
    public function addBalance($id = 0){
        $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                    ->where('user_id', Auth::user()->id)
                    ->first();
        if($total_amount->total_amount === null){
            $total_amount->total_amount = 0;
        }
        if($id == 0){
            return response()->json([
                'status'=>true,
                'total_amount'=>$total_amount,
            ],200);
            // return view('addBalance',compact('total_amount'));
        }
        else{
            $expense = user_expense::find($id);
            if($expense && $expense->expense_in === 'group'){
                return response()->json([
                    'status'=>false,
                    'message'=>'you cannot edit the group expense'
                ],200);
            }
            return response()->json([
                'status'=>true,
                'total_amount'=>$total_amount,
                'expense'=>$expense
            ],200);
            // return view('addBalance',compact('total_amount','expense'));
        }
    }   
    public function storeBalance($id,Request $req){
        $validateExpense = Validator::make($req->all(),[
            'description'=>'required',
            'amount'=>'required|decimal:0,2'
        ]);
        if($validateExpense->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Validation Error',
                'error'=> $validateExpense->errors()->all()
            ],401);
        }

        if($id == 0){
            $add = user_expense::create([
                'user_id'=>Auth::user()->id,
                'description'=>$req->description,
                'amount'=>$req->amount,
                'catigory'=>"add Balance",
            ]);
            return response()->json([
                'status'=>true,
                'message'=>'Balance Added',
            ],200);
            // return redirect()->route('home')->with('success','Balance Added');
        }
        else{
            $expense = user_expense::find($id);
            if($expense && $expense->expense_in === 'group'){
                return response()->json([
                    'status'=>false,
                    'message'=>'you cannot edit the group expense'
                ],200);
            }
            $expense->description = $req->description;
            $expense->amount = $req->amount;
            $expense->save();
            return response()->json([
                'status'=>true,
                'message'=>'Balance Updated',
            ],200);
            // return redirect()->route('home')->with('success','Balance Edited');
        }
    }
    public function addUserExpense($id = 0){
        $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                    ->where('user_id', Auth::user()->id)
                    ->first();
        if($total_amount->total_amount === null){
            $total_amount->total_amount = 0;
        }

        if($id == 0){
            return response()->json([
                'status'=>true,
                'total_amount'=>$total_amount,
            ],200);
            // return view('addUserExpense',compact('total_amount'));
        }
        else{
            $expense = user_expense::find($id);
            if($expense && $expense->expense_in === 'group'){
                
                return response()->json([
                    'status'=>false,
                    'message'=>'you cannot edit the group expense'
                ],200);
            }
            return response()->json([
                'status'=>true,
                'total_amount'=>$total_amount,
                'expense'=>$expense,
            ],200);
            // return view('addUserExpense',compact('total_amount','expense'));
        }
    }

    public function storeExpense($id,Request $req){
        $validateExpense = Validator::make($req->all(),[
            'description'=>'required',
            'category'=>'required',
            'amount'=>'required|decimal:0,2'
        ]);
        if($validateExpense->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Validation Error',
                'error'=> $validateExpense->errors()->all()
            ],401);
        }
        if($id == 0){
            $add = user_expense::create([
                'user_id'=>Auth::user()->id,
                'description'=>$req->description,
                'amount'=>-$req->amount,
                'catigory'=>$req->category,
            ]);
            return response()->json([
                'status'=>true,
                'message'=>'Expense added'
            ],200);
            // return redirect()->route('home')->with('danger','Expense added');
        }
        else{
            $expense = user_expense::find($id);
            if($expense && $expense->expense_in === 'group'){
                return response()->json([
                    'status'=>false,
                    'message'=>'you cannot edit the group expense'
                ],200);
            }
            $expense->description = $req->description;
            $expense->amount = -$req->amount;
            $expense->catigory = $req->category;
            $expense->save();
            return response()->json([
                'status'=>true,
                'message'=>'Expense Updated'
            ],200);
            // return redirect()->route('home')->with('danger','Expense edited');
        }
    }
     // view expenses
     public function view(){
        $categories = user_expense::where('user_id', Auth::user()->id)
        ->select('catigory')
        ->distinct()
        ->get();
        $expenses = user_expense::where('user_id', Auth::user()->id)
            ->get()
            ->map(function ($expense) {
                // Get only the date from created_at
                $expense->created_date = $expense->created_at->toDateString();  // Format as 'YYYY-MM-DD'
                return $expense;
            });
        $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                    ->where('user_id', Auth::user()->id)
                    ->first();
        if($total_amount->total_amount === null){
            $total_amount->total_amount = 0;
        }
        return response()->json([
            'status'=>true,
            'categories'=>$categories,
            'expenses'=>$expenses,
            'total_amount'=>$total_amount,
        ],200);
        // return view('userExpenses',compact('categories','expenses','total_amount'));
    }

    public function today(){
        $categories = user_expense::where('user_id', Auth::user()->id)
        ->select('catigory')
        ->distinct()
        ->get();
        $expenses = user_expense::where('user_id', Auth::user()->id)
        ->whereDate('created_at', Carbon::today())
        ->get()
        ->map(function ($expense) {
            $expense->created_date = $expense->created_at->toDateString();
            return $expense;
        });
        $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                    ->where('user_id', Auth::user()->id)
                    ->first();
        if($total_amount->total_amount === null){
            $total_amount->total_amount = 0;
        }
        return response()->json([
            'status'=>true,
            'categories'=>$categories,
            'expenses'=>$expenses,
            'total_amount'=>$total_amount,
        ],200);
        // return view('userExpenses',compact('categories','expenses','total_amount'));
    }
    public function thisweek(){
        $categories = user_expense::where('user_id', Auth::user()->id)
        ->select('catigory')
        ->distinct()
        ->get();
        $expenses = user_expense::where('user_id', Auth::user()->id)
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->get()
        ->map(function ($expense) {
            $expense->created_date = $expense->created_at->toDateString();
            return $expense;
        });
        $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                    ->where('user_id', Auth::user()->id)
                    ->first();
        if($total_amount->total_amount === null){
            $total_amount->total_amount = 0;
        }
        return response()->json([
            'status'=>true,
            'categories'=>$categories,
            'expenses'=>$expenses,
            'total_amount'=>$total_amount,
        ],200);
        // return view('userExpenses',compact('categories','expenses','total_amount'));
    }

    public function thismonth(){
        $categories = user_expense::where('user_id', Auth::user()->id)
        ->select('catigory')
        ->distinct()
        ->get();
        $expenses = user_expense::where('user_id', Auth::user()->id)
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->get()
        ->map(function ($expense) {
            $expense->created_date = $expense->created_at->toDateString();
            return $expense;
        });
        $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                    ->where('user_id', Auth::user()->id)
                    ->first();
        if($total_amount->total_amount === null){
            $total_amount->total_amount = 0;
        }
        return response()->json([
            'status'=>true,
            'categories'=>$categories,
            'expenses'=>$expenses,
            'total_amount'=>$total_amount,
        ],200);
        // return view('userExpenses',compact('categories','expenses','total_amount'));
    }

    public function viewSearch(Request $req){

        $categories = user_expense::where('user_id', Auth::user()->id)
        ->select('catigory')
        ->distinct()
        ->get();
        $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                    ->where('user_id', Auth::user()->id)
                    ->first();
        if($total_amount->total_amount === null){
            $total_amount->total_amount = 0;
        }
        // filter 
            if($req->date && $req->date != null){
                    $expenses = user_expense::where('user_id', Auth::user()->id)
                        ->whereDate('created_at',$req->date)
                        ->get()
                        ->map(function ($expense) {
                            // Get only the date from created_at
                            $expense->created_date = $expense->created_at->toDateString();  // Format as 'YYYY-MM-DD'
                            return $expense;
                        });
                    }
                // start and date and catigory filter 
            elseif($req->startDate != null && $req->endDate != null){
                    // if catigory is all 
                    if($req->category === 'All'){
                        $expenses = user_expense::where('user_id', Auth::user()->id)
                        ->whereDate('created_at','>=',$req->startDate)
                        ->whereDate('created_at','<=',$req->endDate)
                        ->get()
                        ->map(function ($expense) {
                            // Get only the date from created_at
                            $expense->created_date = $expense->created_at->toDateString();  // Format as 'YYYY-MM-DD'
                            return $expense;
                        });
                    }

                    // if catigory is spicified
                    else{
                        $expenses = user_expense::where('user_id', Auth::user()->id)
                        ->whereDate('created_at','>=',$req->startDate)
                        ->whereDate('created_at','<=',$req->endDate)
                        ->where('catigory',$req->category)
                        ->get()
                        ->map(function ($expense) {
                            // Get only the date from created_at
                            $expense->created_date = $expense->created_at->toDateString();  // Format as 'YYYY-MM-DD'
                            return $expense;
                        });
                    }
                    
            }
            // if date and start and end date is not search the catigory will be default all and also if the catigory is specified it will search and give result 
            else{

                    // if catigory is all 
                    if($req->category === 'All'){

                        $expenses = user_expense::where('user_id', Auth::user()->id)
                        ->get()
                        ->map(function ($expense) {
                            // Get only the date from created_at
                            $expense->created_date = $expense->created_at->toDateString();  // Format as 'YYYY-MM-DD'
                            return $expense;
                        });
                    }
                    // if catigory is spicified
                    else{
                        $expenses = user_expense::where('user_id', Auth::user()->id)
                        ->where('catigory',$req->category)
                        ->get()
                        ->map(function ($expense) {
                            // Get only the date from created_at
                            $expense->created_date = $expense->created_at->toDateString();  // Format as 'YYYY-MM-DD'
                            return $expense;
                        });
                    }
            }
                
            return response()->json([
                'status'=>true,
                'categories'=>$categories,
                'expenses'=>$expenses,
                'total_amount'=>$total_amount,
            ],200);
        // return view('userExpenses',compact('categories','expenses','total_amount'));
    }

     // delete the expense
     public function deleteUserExpense($id)
     {
         $expense = user_expense::find($id);
        if($expense && $expense->expense_in === 'group'){
            return response()->json([
                'status'=>false,
                'message'=>'You cannot deleted group expense',
             ],401);
        }
         if ($expense) {
             $expense->delete();
             return response()->json([
                'status'=>true,
                'message'=>'Expense deleted successfully',
             ],200);
            //  return redirect()->route('userExpenses');
         } else {
            return response()->json([
                'status'=>true,
                'message'=>'Expense deleted successfully',
             ],200);
            //  return redirect()->route('userExpenses');
         }
     }


}
