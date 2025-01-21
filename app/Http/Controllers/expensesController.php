<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Expense_participant;
use Illuminate\Support\Facades\Auth;

class expensesController extends Controller
{
    public function showExpenses($gid){
        
        $group = Group::find($gid);
        if($group){
            $members = explode(',',$group->group_members);
            if(in_array(Auth::user()->id,$members)){
               $expenses= Expense::where('group_id',$gid)->join('users','expenses.user_paid','=','users.id')
                ->select('expenses.id', 'expenses.*', 'users.name as user_name','users.id as user_id')
                ->orderBy('id','desc')
                ->get();
                $total_mem = count($members);
                foreach($expenses as $expense){
                    $checks = Expense_participant::where('expense_id',$expense->id)->where('amount','>=',0)->get();
                    $flag = 0;
                    foreach($checks as $check){
                        $flag = $check->amount > 0 ? 0:1;
                        if($flag == 0){
                            break;
                        }
                    }
                    if($flag == 1){
                        $update_expenses_status = Expense::find($expense->id);
                        $update_expenses_status->status = "Settled";
                        $update_expenses_status->save();
                    }
                    else{
                        $update_expenses_status = Expense::find($expense->id);
                        $update_expenses_status->status = "Not Settled";
                        $update_expenses_status->save();
                    }
                }
                return view('expenses',compact('total_mem','expenses','group'));
            }
            else{
                return redirect()->route('group',$gid)->with('danger',"Group not found");
            }
        }
        else{
            return redirect()->route('group',$gid)->with('danger',"Group not found");
    
        }
    }
    public function showAddExpenses($gid){
        $group = Group::find($gid);
        if($group){
            $members = explode(',',$group->group_members);
            if(in_array(Auth::user()->id,$members)){
                $users;
                foreach($members as $member){
                    $user = User::find($member);
                    if($user){
                        $users[] = $user;
                    }
                }
                return view('addExpenses',compact('group','users'));
            }
            else{
                return redirect()->route('group',$gid)->with('danger',"Group not found");
            }
        }
        else{
            return redirect()->route('group',$gid)->with('danger',"Group not found");

        }
    }

    public function addexpences($gid, Request $req){
        $req->validate([
            'description'=>'required',
            'amount'=>'required|numeric',
            'paid_by'=>'required|numeric',
            'split_method'=>'required',
            'date'=>'required|date_format:Y-m-d'
        ]);
        $group = Group::find($gid);
        $group_members_str = null;
        $group_members = null;
        if($group){
            $group_members_str = $group->group_members;
        }
        if($group_members_str != null){
            $group_members = explode(',',$group_members_str);
        }
        if($group_members != null){
            $len = count($group_members);
        }
        $each = $req->amount/$len;
        $expence = Expense::create([
            'user_paid'=>$req->paid_by,
            'group_id'=>$gid,
            'amount'=>$req->amount,
            'date'=>$req->date,
            'description'=>$req->description,
            'status'=>"Not Settled",
            'split_type'=>$req->split_method
        ]);
        $expence_id = Expense::orderBy('id','desc')->first();

        // if the split method is equally
        if($req->split_method == 'equally'){
            if($group_members != null){
                foreach($group_members as $member){
                    if($member == $req->paid_by){
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $each - $req->amount,
                        ]);
                    }
                    else{
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $each,
                        ]);
                    }
                }
                return redirect()->route('expenses',$gid)->with('Success','Expenses added');
            }
        }
        elseif($req->split_method == 'exact_amount'){
            
            if($group_members != null){
                foreach($group_members as $member){
                    if($member == $req->paid_by){
                        
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $req->$member - $req->amount,
                        ]);
                    }
                    else{
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $req->$member,
                        ]);
                    }
                }
                return redirect()->route('expenses',$gid)->with('Success','Expenses added');
            }
        }

        elseif($req->split_method == 'percentage'){
            if($group_members != null){
                foreach($group_members as $member){
                    $amount = ($req->$member * $req->amount)/100;
                    if($member == $req->paid_by){
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $amount - $req->amount,
                        ]);
                    }
                    else{
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $amount,
                        ]);
                    }
                }
                return redirect()->route('expenses',$gid)->with('Success','Expenses added');
            }
        }

    }

    public function viewExpense($eid,$gid){
        $group = Group::find($gid);

        if($group){
            $members = explode(',',$group->group_members);
            if(in_array(Auth::user()->id ,$members)){
                $expenses = Expense_participant::where('expense_id','=',$eid)
                ->join('users','expense_participants.user_id','=','users.id')
                ->join('expenses','expense_participants.expense_id','=','expenses.id')
                ->where('expenses.group_id',$gid)
                ->select(
                    'expense_participants.id',
                    'expense_participants.amount as p_amount',
                    'expense_participants.*',
                    'expenses.id as e_id',
                    'expenses.amount as e_amount',
                    'expenses.*',
                    'users.id as u_id',
                    'users.*')
                ->get();
                if($expenses != '[]'){
                    $payments = Payment::where('expense_id', $eid)
                    ->join('users', 'paid_by', '=', 'users.id')
                    ->select('users.name', DB::raw('SUM(payments.amount) as total_amount'))
                    ->groupBy('users.name')
                    ->get();
                
                    if($payments !='[]'){
                        return view('viewExpense',compact('expenses','gid','payments'));
                    }
                    else{
                        $payments = '';
                        return view('viewExpense',compact('expenses','gid','payments'));
                    }
                }
                else{
                    return redirect()->route('expenses',$eid)->with('danger',"expense not found");
                }
            }else{
                return redirect()->route('group',$gid)->with('danger',"Group not found");

            }
        }else{
            return redirect()->route('group',$gid)->with('danger',"Group not found");
        }
    }
    
    public function repay($id, Request $req){
        $req->validate([
            'paidBy'=>'required',
            'group_id'=>'required',
            'paidTo'=>'required',
            'eId'=>'required',
            'amount'=>'required'
        ]);

        // subtract the dues 
        $expenses = Expense_participant::where('expense_id',$req->eId)->where('user_id',$req->paidBy)->first();
        $expenses->amount = $expenses->amount-$req->amount;
        $expenses->save();
        
        // add the amount to the user who paid the bill 
        $expenses_get = Expense_participant::where('expense_id',$req->eId)->where('user_id',$req->paidTo)->first();
        $expenses_get->amount = $expenses_get->amount + $req->amount;
        $expenses_get->save();

        $payment = Payment::create([
            'paid_by'=>$req->paidBy,
            'paid_to'=>$req->paidTo,
            'expense_id'=>$req->eId,
            'amount'=>$req->amount
        ]);
        $checks = Expense_participant::where('expense_id',$req->eId)->where('amount','>=',0)->get();
        $flag = 0;
        foreach($checks as $check){
            $flag = $check->amount > 0 ? 0:1;
            if($flag == 0){
                break;
            }
        }
        if($flag == 1){
            $update_expenses_status = Expense::find($req->eId);
            $update_expenses_status->status = "Settled";
            $update_expenses_status->save();
        }
        return redirect()->route('viewExpense',['eid'=>$req->eId,'gid'=>$req->group_id]);
        exit();
    }
}
