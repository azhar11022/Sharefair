@extends('layouts.app')
@section('content')
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Register</h3>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="card-body">
                    <form action="{{ route('requestJoins') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="Name" class="form-label">Name:</label>
                            <input type="text" class="form-control" id="Name" name="name" value="{{ $name }}" placeholder="Enter your full name"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="Email" class="form-label">Email:</label>
                            <input type="text" class="form-control" id="Email" name="email" value="{{ $email }}" placeholder="Enter your email"
                                required readonly>
                        </div>

                        <div class="mb-3">
                            <label for="Password" class="form-label">Password:</label>
                            <input type="password" class="form-control" id="Password" name="password" placeholder="Enter your password"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="Confirmpass" class="form-label">Confirm Password:</label>
                            <input type="password" class="form-control" id="Confirmpass" name="confirm" placeholder="Confirm your password"
                                required>
                        </div>
                        <button class="btn btn-primary w-50">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection