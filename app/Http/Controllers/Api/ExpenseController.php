<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Group;
use App\Models\recipt;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\user_expense;
use Illuminate\Support\Facades\DB;
use App\Models\Expense_participant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function showExpenses($gid){
        $group = Group::find($gid);
        if($group){
            $members = explode(',',$group->group_members);
            if(in_array(Auth::user()->id,$members)){
               $expenses= Expense::where('group_id',$gid)
                ->join('users','expenses.user_paid','=','users.id')
                ->join('recipts','expenses.recipt_id','=','recipts.id')
                ->select('expenses.id', 'expenses.*', 'users.name as user_name','users.id as user_id','recipts.id as rid','recipts.location')
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
                $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                ->where('user_id', Auth::user()->id)
                ->first();
                if($total_amount->total_amount === null){
                    $total_amount->total_amount = 0;
                }
                return response()->json([
                    'status'=>true,
                    'total_mem'=>$total_mem,
                    'expenses'=>$expenses,
                    'group'=>$group,
                    'total_amount'=>$total_amount,
                ],200);
            }
            else{
                return response()->json([
                    'status'=>false,
                    'message'=>"Group not found",
                    'group_id'=>$gid,
                ],401);
            }
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>"Group not found",
                'group_id'=>$gid,
            ],401);    
        }
    }

    public function showAddExpenses($gid){
        $group = Group::find($gid);
        if($group){
            $members = explode(',',$group->group_members);
            if(in_array(Auth::user()->id,$members)){
                $users;
                foreach($members as $member){
                    $user = User::where('id',$member)->where('user_status','approved')->first();
                    if($user){
                        $users[] = $user;
                    }
                }
                $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                ->where('user_id', Auth::user()->id)
                ->first();
                if($total_amount->total_amount === null){
                    $total_amount->total_amount = 0;
                }
                return response()->json([
                    'status'=>true,
                    'group'=>$group,
                    'users'=>$users,
                    'total_amount'=>$total_amount,
                ],200);
            }
            else{
                return response()->json([
                    'status'=>false,
                    'message'=>"Group not found",
                    'group_id'=>$gid,
                ],401);
            }
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>"Group not found",
                'group_id'=>$gid,
            ],401);

        }
    }

    public function addexpences($gid, Request $req){
        
        $validateExpense = Validator::make($req->all(),[
            'description'=>'required',
            'amount'=>'required|numeric',
            'paid_by'=>'required|numeric',
            'split_method'=>'required',
            'date'=>'required|date_format:Y-m-d',
            'recipt' => 'mimes:jpg,jpeg,png,pdf',
        ]);
        if($validateExpense->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Validation Error',
                'error'=> $validateExpense->errors()->all()
            ],401);
        }

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
            $len = 0;
            foreach($group_members as $mem){
                $u = User::find($mem);
                if($u){
                    if($u->user_status == 'pending'){
                        continue;
                    }
                    $len++;
                }else{
                    continue;
                }
            }
        }
        $each = $req->amount/$len;
        
        // recipts data storing
            if($req->hasFile('recipt')){
                $file = $req->file('recipt');
                $path = $file->store('recipts','public');
            }
            else{
                $path = "No recipt";
            }
            $recipt = recipt::create([
                            'location' => $path,
                        ]);

        $expence = Expense::create([
            'user_paid'=>$req->paid_by,
            'group_id'=>$gid,
            'amount'=>$req->amount,
            'date'=>$req->date,
            'description'=>$req->description,
            'status'=>"Not Settled",
            'split_type'=>$req->split_method,
            'recipt_id'=>$recipt->id
        ]);
        $expence_id = Expense::orderBy('id','desc')->first();
    
        $addUserExpense = user_expense::create([
            'user_id'=>$req->paid_by,
            'description'=>$req->description,
            'amount'=>-$req->amount,
            'catigory'=>$group->group_name,
            'expense_id'=>$expence_id->id,
            'expense_in'=>'group'
        ]);

        // if the split method is equally
        if($req->split_method == 'equally'){
            if($group_members != null){
                foreach($group_members as $member){
                    $u = User::find($member);
                    if($u->user_status == 'pending'){
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> 0,
                            'group_id'=>$gid,
                        ]);
                        continue;
                    }

                    if($member == $req->paid_by){
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $each - $req->amount,
                            'group_id'=>$gid,
                        ]);
                    }
                    else{
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $each,
                            'group_id'=>$gid,
                        ]);
                    }
                }
                return response()->json([
                    'status'=>true,
                    'message'=>"Expenses added",
                    'group_id'=>$gid,
                ],200);
            }
        }
        elseif($req->split_method == 'exact_amount'){
            
            if($group_members != null){
                foreach($group_members as $member){
                    $u = User::find($member);
                    if($u->user_status == 'pending'){
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> 0,
                            'group_id'=>$gid,
                        ]);
                        continue;
                    }
                    if($member == $req->paid_by){
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $req->$member - $req->amount,
                            'group_id'=>$gid,
                        ]);
                    }
                    else{
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $req->$member,
                            'group_id'=>$gid,
                        ]);
                    }
                }
                return response()->json([
                    'status'=>true,
                    'message'=>"Expenses added",
                    'group_id'=>$gid,
                ],200);
                // return redirect()->route('expenses',$gid)->with('Success','Expenses added');
            }
        }

        elseif($req->split_method == 'percentage'){
            if($group_members != null){
                foreach($group_members as $member){
                    $amount = ($req->$member * $req->amount)/100;
                    $u = User::find($member);
                    if($u->user_status == 'pending'){
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> 0,
                            'group_id'=>$gid,
                        ]);
                        continue;
                    }
                    if($member == $req->paid_by){
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $amount - $req->amount,
                            'group_id'=>$gid,
                        ]);
                    }
                    else{
                        $expence_participant = Expense_participant::create([
                            'expense_id'=>$expence_id->id,
                            'user_id'=> $member,
                            'amount'=> $amount,
                            'group_id'=>$gid,
                        ]);
                    }
                }
                return response()->json([
                    'status'=>true,
                    'message'=>"Expenses added",
                    'group_id'=>$gid,
                ],200);
                // return redirect()->route('expenses',$gid)->with('Success','Expenses added');
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
                    ->select('users.name','payments.paid_by', DB::raw('SUM(payments.amount) as total_amount'))
                    ->groupBy('payments.paid_by','users.name')
                    ->get();

                    $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                            ->where('user_id', Auth::user()->id)
                            ->first();
                    if($total_amount->total_amount === null){
                        $total_amount->total_amount = 0;
                    }
                    if($payments !='[]'){
                        return response()->json([
                            'status'=>true,
                            'expenses'=>$expenses,
                            'gid'=>$gid,
                            'payments'=>$payments,
                            'total_amount'=>$total_amount,
                        ],200);
                    }
                    else{
                        $payments = '';
                        return response()->json([
                            'status'=>true,
                            'expenses'=>$expenses,
                            'gid'=>$gid,
                            'payments'=>$payments,
                            'total_amount'=>$total_amount,
                        ],200);
                    }
                }
                else{
                    return response()->json([
                        'status'=>false,
                        'message'=>"Expense not found",
                        'gid'=>$gid,
                    ],401);
                }
            }else{
                return response()->json([
                    'status'=>false,
                    'message'=>"Group not found",
                    'gid'=>$gid,
                ],401);

            }
        }else{
            return response()->json([
                'status'=>false,
                'message'=>"Group not found",
                'gid'=>$gid,
            ],401);
        }
    }

    public function repay( Request $req){
        $validateExpense = Validator::make($req->all(),[
            'paidBy'=>'required',
            'group_id'=>'required',
            'paidTo'=>'required',
            'eId'=>'required',
            'amount'=>'required'
        ]);
        if($validateExpense->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Validation Error',
                'error'=> $validateExpense->errors()->all()
            ],401);
        }

        $group = Group::find($req->group_id);

        // subtract the dues 
        $expenses = Expense_participant::where('expense_id',$req->eId)
        ->where('user_id',$req->paidBy)
        ->first();
        // select date and description
        $exp = Expense::join('users as u', 'expenses.user_paid', '=', 'u.id')
        ->select('expenses.id as eid', 'expenses.*', 'u.*')
        ->where('expenses.id', $req->eId)
        ->first();

        if($expenses->amount != 0 && $expenses->amount >= $req->amount){
            $expenses->amount = $expenses->amount-$req->amount;
            $expenses->save();

            $addUserExpense = user_expense::create([
                'user_id'=>$req->paidBy,
                'description'=>"You paid the dues $exp->date $exp->description",
                'amount'=>-$req->amount,
                'catigory'=>$group->group_name,
                'expense_in'=>'group'
            ]);

        }else{
            return response()->json([
                'status'=>false,
                'message'=>"Payments already cleared",
            ],401);
        }
        
        // add the amount to the user who paid the bill 
        $user_pay_back = User::find($req->paidBy);
        $addUserExpense = user_expense::create([
            'user_id'=>$req->paidTo,
            'description'=>"$user_pay_back->name pay you the dues $exp->date $exp->description",
            'amount'=>$req->amount,
            'catigory'=>$group->group_name,
            'expense_in'=>'group'
        ]);
        $expenses_get = Expense_participant::where('expense_id',$req->eId)
        ->where('user_id',$req->paidTo)
        ->first();
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
        return response()->json([
            'status'=>true,
            'eid'=>$req->eId,
            'gid'=>$req->group_id,
        ],200);
    }
    public function delete($id){
        $payment = Payment::where('expense_id',$id)->get();
        $expense = Expense::find($id);
        if ($payment->isEmpty()) {  
            $expense->delete();
            return response()->json([
                'status'=>true,
                'group_id'=>$expense->group_id,
                'message'=>'Expense deleted Successfully',
            ],200);
            // return redirect()->route('expenses', $expense->group_id)->with('success', 'Expense deleted successfully.');
        } else {
            return response()->json([
                'status'=>false,
                'group_id'=>$expense->group_id,
                'message'=>'Expense cannot be deleted because a payment is already done in deleted Successfully',
            ],200);

            // return redirect()->route('expenses', $expense->group_id)
            //     ->with('danger', "Expense cannot be deleted because a payment is linked to it.");
        }
    }
}
