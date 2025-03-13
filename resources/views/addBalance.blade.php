
<style>
     body {
            background-color: #f8f9fa;
        }
        .balance-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            width: 100%;
        }
</style>
@extends('layouts.app')
@section('content')
@php
      $expense = $expense ?? (object) [
        'id' => 0,
        'description' => '',
        'amount' => 0,
    ];
@endphp
<div class="balance-container">
    <h1>Add Your Balance {{ Auth::user()->name }}</h1>
    <form action="{{ route('storeBalance',$expense->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control" placeholder="Enter description" value="{{ $expense->description }}" style="border:1px solid black" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" class="form-control" placeholder="Enter amount" value="{{ $expense->amount }}" style="border:1px solid black" required step="0.01">
        </div>
        <button type="submit" class="btn btn-primary">Add Balance</button>
    </form>
</div>
@endsection