<?php

namespace App\Http\Controllers\Api;
use App\Models\User;

use App\Models\Group;
use App\Jobs\addMember;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Mail\joinRequestMail;
use Illuminate\Support\Facades\DB;
use App\Models\Expense_participant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    //Adding group api
    public function add(Request $req){
       
        $validateUser = Validator::make($req->all(),[
            'group_name'=>'required',
            'group_type'=>'required',
            'memberName'=>'required',
            'memberEmail'=>'required',
          ]);

          if($validateUser->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Validation Error',
                'error'=> $validateUser->errors()->all()
            ],401);
        }
        $group_name = $req->group_name;
        $group_type = $req->group_type;
        $created_by = $req->user()->id;
        $group_members[0] = $req->user()->id;

        foreach($req->memberEmail as $index => $email){
          $user = User::where('email',$email)->first();
          if($user){
            if(in_array($user->id,$group_members)){
                continue;
            }
            else{
            }
            $group_members[]=$user->id;
          }
          else{
            $new = User::create([
                'name'=>$req->memberName[$index],
                'email'=>$email,
                'user_status'=>'pending'
            ]);
            $group_members[]=$new->id;
            if($new){
                // mail send to job queue
                dispatch(new addMember($req->memberName[$index],$email,Auth::user()->name,$group_name));
                // Mail::to($email)->send(new joinRequestMail($req->user()->name,$group_name,$req->memberName[$index],$email));
            }
          }
        }
        $group_member = implode(',',$group_members);
        

        $data = Group::insert([
            'group_name'=>$group_name,
            'group_type'=>$group_type,
            'created_by'=>$created_by,
            'group_members'=>$group_member
        ]);
       return response()->json([
        'status'=>true,
        'message'=>'Group added Successfully',
        'group'=>$data,
       ],200);
    }// Adding group is done and end

    // All group for home page
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
        return response()->json([
            'status'=>true,
            'groups'=>$groups,
            'total_amount'=>$total_amount,
            'expenses'=>$expenses,
        ],200);
    }

    // Showing the single group
    public function Show($group_id){
        $group = Group::find($group_id);
        if($group){
            $group_member = explode(',',$group->group_members);
            if(in_array(Auth::user()->id,$group_member)){
                $users[] = null;
                foreach($group_member as $index=>$member){
                    $user = User::find($member);
                    if($user === null){
                        continue;
                    }
                    $users[$index] = $user;
                    
                }
                $expense = Expense::where('group_id',$group_id)->get();
                if($expense){
                    $expense = true;
                }
                else{
                    $expense = false;
                }
                // this is for user if the user didn't cleared the expenses so user cannot be deleted

                $user_total = Expense_participant::join('users as u', 'expense_participants.user_id', '=', 'u.id')
                                ->select(
                                    DB::raw('SUM(expense_participants.amount) as user_total'),
                                    'expense_participants.user_id',
                                    'u.name' // Include only the necessary columns
                                )
                                ->groupBy('expense_participants.user_id', 'u.name')
                                ->get();
                // getting the total balance
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
                    'expense'=>$expense,
                    'total_amount'=>$total_amount,
                    'user_total'=>$user_total,
                ],200);
            }
            else{
                return response()->json([
                    'status'=>false,
                    'message'=>'Group not found',
                ],401);
            }
        }
        else{
            return response()->json([
                'staus'=>false,
                'message'=>"Group not found"
            ],401);
        }
    }

    // Show add Group page
    public function showAdd(){
        $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                            ->where('user_id', Auth::user()->id)
                            ->first();
        if($total_amount->total_amount === null){
        $total_amount->total_amount = 0;
        }
        return response()->json([
            'status'=>true,
            'total_amount'=>$total_amount,
        ],200);
}

public function deleteMember($gid,$mid){
    $group = Group::find($gid);
    $user_expenses = Expense_participant::select(DB::raw('SUM(amount) as total_amount'))
                     ->where('user_id',$mid)
                     ->groupBy('user_id')
                     ->first();
    if($user_expenses && $user_expenses->total_amount == 0.00){
        $members = $group->group_members;
        $users = explode(',',$members);
        $updatedMembers = array_diff($users, [$mid]);
        $user = implode(',',$updatedMembers);
        $group->group_members = $user;
        $group->save();
        return response()->json([
            'status'=>true,
            'success'=>"Deleted Successfully",
            'group_id'=>$gid,
        ],200);
        // return redirect()->route('group',$gid)->with('success',"Deleted Successfully");
    }
    else{
        return response()->json([
            'status'=>false,
            'danger'=>"Deletion failed",
            'group_id'=>$gid,
        ],401);
    }
}

public function addMember($gid, Request $req){

    $validateGroup = Validator::make($req->all(),[
        'memberName'=>'required',
        'memberEmail'=>'required',
      ]);

      if($validateGroup->fails()){
        return response()->json([
            'status'=>false,
            'message'=>'Validation Error',
            'error'=> $validateGroup->errors()->all()
        ],401);
    }
    $group = Group::find($gid);
    $members = $group->group_members;
    $users = explode(',',$members);
    $member = User::where('email',$req->memberEmail)->first();
    $member_id = null;
    if($member){
        $member_id = $member->id;
        $members =null;
        if(in_array($member_id, $users)){
            return response()->json([
                'status'=>false,
                'message'=>"User already exist in this group",
            ],401);
        }
        else{
            $users[] = $member_id;
            $update_members = implode(',',$users);
            $group->group_members = $update_members;
            $group->save();
            return response()->json([
                'status'=>true,
                'message'=>"User addedd successfully",
            ],200);
            // return redirect()->route('group',$gid)->with('success',"User added successfully");
        }
    }
    else{
        $new = User::create([
            'name'=>$req->memberName,
            'email'=>$req->memberEmail,
            'user_status'=>'pending'
        ]);
        $users[]=$new->id;
        $update_members = implode(',',$users);
        $group->group_members = $update_members;
        $group->save();
        if($new){
            dispatch(new addMember($req->memberName,$req->memberEmail,Auth::user()->name,$group->group_name));

            // Mail::to($req->memberEmail)->send(new joinRequestMail(Auth::user()->name,$group->group_name,$req->memberName,$req->memberEmail));
            return response()->json([
                'status'=>true,
                'message'=>"User added successfully",
            ],200);
            // return redirect()->route('group',$gid)->with('success',"User added successfully");
        }
    }

}

}
