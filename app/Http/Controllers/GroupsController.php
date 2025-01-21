<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        }
        // just for printing ids in array 
        // foreach($group_members as $data){
        //     echo $data."<br>";
        // }

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
                    $users[$index] = User::find($member);
                }
                $expense = Expense::where('group_id',$group_id)->get();
                return view('groupData',compact('group','users','expense'));
            }
            else{
                return redirect()->route('home')->with('danger',"Group not found");
            }
        }
        else{
            return redirect()->route('home')->with('danger',"Group not found");
        }
    }

    public function deleteMember($gid,$mid){
        $group = Group::find($gid);
        $members = $group->group_members;
        $users = explode(',',$members);
        $updatedMembers = array_diff($users, [$mid]);
        $user = implode(',',$updatedMembers);
        $group->group_members = $user;
        $group->save();
        return redirect()->route('group',$gid)->with('success',"Deleted Successfully");
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
    // if user not regestered
    else{
        return redirect()->route('group',$gid)->with('danger',"User not found");
    } 
    }
}
