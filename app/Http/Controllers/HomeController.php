<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\user_expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Expense_participant;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $memberId = Auth::user()->id;
        $groups = Group::where('group_members', 'LIKE', "%,$memberId,%")
                    ->orWhere('group_members', 'LIKE', "$memberId,%")
                    ->orWhere('group_members', 'LIKE', "%,$memberId")
                    ->orWhere('group_members', '=', "$memberId")
                    ->get();
        $expenses = Expense_participant::where('user_id',$memberId)
                    ->select(DB::raw('SUM(amount) as group_total'),'group_id')
                    ->groupBy('group_id')
                    ->get()
                    ->keyBy('group_id');
        $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                    ->where('user_id', Auth::user()->id)
                    ->first();
        if($total_amount->total_amount === null){
            $total_amount->total_amount = 0;
        }
        return view('home', compact('groups','total_amount','expenses'));
    }
}
