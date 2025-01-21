<link rel="stylesheet" href="{{ asset('Dashboard.css') }}">
@extends('layouts.app')
@section('content')
    
<div class="container">
    <div class="nav-inner">
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
        <div class="db">
            <h3>Dashboard</h3>
            <button type="button" class="btn btn-primary btn-lg">Add Group</button>
        </div>

        <div class="dashboard1">
            @isset($groups)
            <h1 class="mt-3">Groups</h1>
            @foreach ($groups as $key=> $item)
            <a href="{{ route('group',$item->id) }}" class="btn btn-success m-3">{{ $item->group_name }}</a>
            @endforeach
            @endisset
        </div>
    </div>
</div>
@endsection
