<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Jobs\addMember;
use App\Models\Expense;
use App\Models\user_expense;
use Illuminate\Http\Request;
use App\Mail\joinRequestMail;
use Illuminate\Support\Facades\DB;
use App\Models\Expense_participant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class GroupsController extends Controller
{
    public function add(Request $req){
        $req->validate([
            'group_name'=>'required',
            'group_type'=>'required',
            'memberName'=>'required',
            'memberEmail'=>'required',
        ]);
        $group_name = $req->group_name;
        $group_type = $req->group_type;
        $created_by = Auth::user()->id;
        $group_members[0] = Auth::user()->id;

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
                // Mail::to($email)->send(new joinRequestMail(Auth::user()->name,$group_name,$req->memberName[$index],$email));
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
        return redirect()->route('home')->with('success',"Group add successfully");
    }
    

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

                $user_total = Expense_participant::join('users as u', 'expense_participants.user_id', '=', 'u.id')
                ->select(
                    DB::raw('SUM(expense_participants.amount) as user_total'),
                    'expense_participants.user_id',
                    'u.name' // Include only the necessary columns
                    )
                    ->groupBy('expense_participants.user_id', 'u.name')
                    ->get();
                $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                            ->where('user_id', Auth::user()->id)
                            ->first();
                if($total_amount->total_amount === null){
                    $total_amount->total_amount = 0;
                }
                return view('groupData',compact('group','users','expense','total_amount','user_total'));
            }
            else{
                return redirect()->route('home')->with('danger',"Group not found");
            }
        }
        else{
            return redirect()->route('home')->with('danger',"Group not found");
        }
    }

    public function showAddGroup(){
                $total_amount = user_expense::select(DB::raw('SUM(amount) as total_amount'))
                            ->where('user_id', Auth::user()->id)
                            ->first();
                if($total_amount->total_amount === null){
                    $total_amount->total_amount = 0;
                }
            return view('addgroup',compact('total_amount'));
    }

    public function deleteMember($gid,$mid){
        $group = Group::find($gid);
        $user_expenses = Expense_participant::select(DB::raw('SUM(amount) as total_amount'))
                ->where('user_id',$mid)
                ->groupBy('user_id')
                ->first();
        if(!$user_expenses || $user_expenses->total_amount == 0.00){
            $members = $group->group_members;
            $users = explode(',',$members);
            $updatedMembers = array_diff($users, [$mid]);
            $user = implode(',',$updatedMembers);
            $group->group_members = $user;
            $group->save();
            return redirect()->route('group',$gid)->with('success',"Deleted Successfully");
        }
        else{
            return redirect()->route('group',$gid)->with('danger',"Deletion failed");
        }
    }
    
    public function addMember($gid, Request $req){
        $req->validate([
            'memberName'=>'required',
            'memberEmail'=>'required',
        ]);
        $group = Group::find($gid);
        $members = $group->group_members;
        $users = explode(',',$members);
        $member = User::where('email',$req->memberEmail)->first();
        $member_id = null;
        if($member){
            $member_id = $member->id;
            $members =null;
            if(in_array($member_id, $users)){
                return redirect()->route('group',$gid)->with('danger',"User Already exist in this group");
            }
            else{
                $users[] = $member_id;
                $update_members = implode(',',$users);
                $group->group_members = $update_members;
                $group->save();
                return redirect()->route('group',$gid)->with('success',"User added successfully");
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
                return redirect()->route('group',$gid)->with('success',"User added successfully");
            }
        }
   
    }
}
