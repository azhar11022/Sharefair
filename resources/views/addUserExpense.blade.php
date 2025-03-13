@extends('layouts.app')
@section('content')
<style>
    body {
        background-color: #f8f9fa;
    }
    .expense-container {
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
@php
      $expense = $expense ?? (object) [
        'id' => 0,
        'description' => '',
        'amount' => 0,
    ];
@endphp
<div class="expense-container">
    <h3 class="text-center mb-4">Add Expense</h3>
    <form action="{{ route('storeExpense',$expense->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control" placeholder="Enter description" required value="{{ $expense->description }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select" required>
                @if (isset($expense->catigory))
                <option value="{{ $expense->catigory }}"  selected>{{ $expense->catigory }}</option>
                @else
                <option value="" disabled selected>Select Category</option>
                @endif
                <option value="Food & Groceries">Food & Groceries</option>
                <option value="Rent">Rent</option>
                <option value="Utilities">Utilities</option>
                <option value="Transportation">Transportation (Fuel, Transport)</option>
                <option value="Clothing & Accessories">Clothing & Accessories</option>
                <option value="Health & Fitness">Health & Fitness</option>
                <option value="Personal Care">Personal Care</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" class="form-control" placeholder="Enter amount" required step="0.01" value="{{ abs($expense->amount) }}">
        </div>

        <button type="submit" class="btn btn-primary">Add Expense</button>
    </form>
</div>

@endsection