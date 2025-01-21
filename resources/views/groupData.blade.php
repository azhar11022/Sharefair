@extends('layouts.app')
@section('content')
<div class="container my-5">
    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
    @if (session('danger'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('danger') }}
                        </div>
                    @endif
    <h1 class="text-center">Groups and Members</h1>

    <!-- Loop through each group -->
    @if(isset($group))
            <div class="card my-3">
                <div class="card-header bg-primary text-white">
                    <div class="row">

                        <div class=" col-md-6 ">
                            <h2>{{ $group->group_name }}</h2>
                        </div>
                        <div class="col-md-5 text-end">
                            <a href="{{ route('home') }}" class="btn btn-success"> Back</a>
                        </div>
                    </div>
                </div>
                <div class="container my-5">
                    <div class="row">
                        <h1 class="text-center col-md-3">Users List</h1>
                        @if ($group->created_by == Auth::user()->id)
                        <button class="btn btn-primary col-md-3 mb-3 " id="toggleAddMemberForm">
                            Add Member
                        </button>
                        
                        @endif
                        <a href="{{ route('addexpenses', $group->id) }}" class="col-md-2 mb-3  mx-2 btn btn-success"> Add Expences</a>
                        @if ($expense)
                        <a href="{{ route('expenses', $group->id) }}" class="col-md-3 mb-3   btn btn-primary"> View Expences</a>
                        <form action="{{ route("addMember", $group->id) }}" class="col-md-6 offset-md-3" id="addMemberForm" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="memberName" class="form-label">Member Name</label>
                                <input type="text" class="form-control" name="memberName" id="memberName" placeholder="Enter member name" required>
                            </div>
                            <div class="mb-3">
                                <label for="memberEmail" class="form-label">Member Email</label>
                                <input type="email" class="form-control" name="memberEmail" id="memberEmail" placeholder="Enter member email" required>
                            </div>
                            <button type="submit" class="btn btn-success mb-3">
                                Submit
                            </button>
                            
                        </form>
                        @endif
                    </div>
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                @if ($group->created_by == Auth::user()->id)
                                <th>Delete Member</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @isset($users)
                                @foreach ($users as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        @if ($group->created_by == Auth::user()->id)
                                        @if ($user->id == $group->created_by)
                                            @continue;
                                        @endif
                                        <td><a href="{{ route('deleteMember',["gid"=>$group->id,"mid"=>$user->id]) }}" onclick="event.preventDefault(); delconfirm({{ $user->id }},'{{ $user->name }}');" class="btn btn-danger"> delete</a></td>
                                        <form action="{{ route('deleteMember',["gid"=>$group->id,"mid"=>$user->id]) }}" class="d-none" id="{{ $user->id }}">@csrf</form>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center">No users found!</td>
                                </tr>
                            @endisset
                        </tbody>
                    </table>
    @else
        <p class="text-center">No groups found!</p>
    @endif
</div>
<script>
        const form = document.getElementById('addMemberForm');
        form.style.display = 'none';
    document.getElementById('toggleAddMemberForm').addEventListener('click', function () {
        form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
    });

    function delconfirm(id, name) {
        if (confirm(`Would you like to delete ${name}?`)) {
            document.getElementById(id).submit();
        }
    }
</script>
@endsection