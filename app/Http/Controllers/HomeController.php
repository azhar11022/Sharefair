<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
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
        return view('home', compact('groups'));
    }
}
