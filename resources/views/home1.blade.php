@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
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
                    <a class="btn btn-primary" href="{{ route('addgroup') }}">Add Group</a>

                    {{ __('You are logged in!') }}
                    <br>
                    @isset($groups)
                    <h1 class="mt-3">Groups</h1>
                    @foreach ($groups as $key=> $item)
                    <a href="{{ route('group',$item->id) }}" class="btn btn-success m-3">{{ $item->group_name }}</a>
                        
                    @endforeach
                    @endisset
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
