@extends('layouts.app')
@section('content')

<style>
    /* Internal CSS */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f9;
    }

    header {
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        text-align: center;
    }

    header h1 {
        margin: 0;
    }

    #filterForm {
        margin-top: 15px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    #filterForm label {
        font-weight: bold;
    }

    #filterForm input, #filterForm select, #filterForm button {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    #filterForm button {
        background-color: #45a049;
        color: white;
        cursor: pointer;
    }

    #filterForm button:hover {
        background-color: #3d8b6c;
    }

    #expenseSection {
        margin: 20px;
        overflow-x: auto;
    }

    #expenseTable {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
    }

    #expenseTable th, #expenseTable td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    #expenseTable th {
        background-color: #4CAF50;
        color: white;
    }

    #expenseTable tr:hover {
        background-color: #f1f1f1;
    }

    .editBtn, .deleteBtn {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .editBtn {
        background-color: #ffc107;
        color: black;
    }

    .deleteBtn {
        background-color: #dc3545;
        color: white;
    }

    .editBtn:hover {
        background-color: #e0a800;
    }

    .deleteBtn:hover {
        background-color: #c82333;
    }
    .btn-color{
        color: #fff;
        background-color: #45a049;
    }
    .btn-color:hover{
        background-color: #3d8b6c;
    }
</style>

<header class="mt-4">
    <h1>Expense Tracker</h1>
    <form id="filterForm" action="{{ route('userExpenses') }}" method="POST">
        @csrf
        <div class="group">
            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate" value="">
        </div>
        <div class="group">
            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate" value="">
        </div>

        <a type="button" id="today" class="btn btn-color" href="{{ route('toDay') }}">Today</a>
        <a type="button" id="thisWeek" class="btn btn-color" href="{{ route('thisWeek') }}">This Week</a>
        <a type="button" id="thisMonth" class="btn btn-color" href="{{ route('thisMonth') }}">This Month</a>
        <div class="group">

            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="All">All</option>
                <!-- Categories will be dynamically populated from the database -->
                @foreach($categories as $category)
                    <option value="{{ $category->catigory }}">{{ $category->catigory }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" id="searchButton">Search</button>
    </form>
</header>

<section id="expenseSection">
    <table id="expenseTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <!-- Expense rows will be dynamically populated here -->
            @foreach($expenses as $expense)
                <tr>
                    <td>{{ $expense->created_date }}</td>
                    @if ($expense->expense_in == 'user')

                    <td>{{ $expense->catigory }}</td>
                    @else
                    <td>{{ $expense->catigory }} <b>(Group)</b></td>
                        
                    @endif
                    <td>{{ $expense->description }}</td>
                    <td>${{ number_format($expense->amount, 2) }}</td>
                    @if ($expense->expense_in == 'user')
                    <td><button class="editBtn" data-id="{{ $expense->id }}" data-cat={{ $expense->catigory }}>Edit</button></td>
                    <td><button class="deleteBtn" data-id="{{ $expense->id }}">Delete</button></td>
                    @else
                    <td><button class="btn btn-warning disabled" >Edit</button></td>
                    <td><button class="btn btn-danger disabled">Delete</button></td>

                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</section>

<script>
    const endDate = document.getElementById('endDate').value =new Date().toISOString().split('T')[0];
    // Internal JavaScript
    document.addEventListener('DOMContentLoaded', function () {
        // Handle Edit button
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', function () {
                const expenseId = this.getAttribute('data-id');
                const expenseCat = this.getAttribute('data-cat');
                if(expenseCat === 'add'){
                    // alert('add');
                    if (confirm('Are you sure you want to Edit this expense?')) {
                        window.location.href = `/addBalance/${expenseId}`;
                    }
                }
                else if(confirm('Are you sure you want to Edit this expense?')) {
                    window.location.href = `/addUserExpense/${expenseId}`;
                }
            });
        });

        // Handle Delete button
        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener('click', function () {
                const expenseId = this.getAttribute('data-id');
                const expenseCat = this.getAttribute('data-cat');
                
                if (confirm('Are you sure you want to delete this expense? ')) {
                    window.location.href = `/deleteUserExpense/${expenseId}`;
                }
            });
        });
    });
</script>

@endsection