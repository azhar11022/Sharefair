@extends('layouts.app')
@section('content')
<style>
  

</style>
<div class="container mt-5">
  
    <!-- Detailed Expenses -->
    @if (session('danger'))
    <div class="alert alert-danger" role="alert">
        {{ session('danger') }}
    </div>
@endif
    <div class="card-header bg-primary text-white">
        <div class="row">
            
            <div class=" col-md-6 ">
                <h3 class="m-3">Detailed Expenses</h3>
            </div>
            <div class="col-md-5 text-end">
                <a href="{{ route('group',$group->id) }}" class="btn btn-success m-3"> Back</a>
            </div>
        </div>
    </div>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Date</th>
          <th>Description</th>
          <th>Total Members</th>
          <th>Paid By</th>
          <th>Amount</th>
          <th>Split Method</th>
          <th>Status</th>
          <th>Receipts</th>
          <th>View</th>
          @if($group->created_by == Auth::user()->id)
          <th>Delete</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @if ($expenses)
        @foreach ($expenses as $expense)
        <tr>
          <td>{{ $expense->date }}</td>
          <td>{{ $expense->description }}</td>
          <td>{{ $total_mem }}</td>
          <td>{{ $expense->user_name }}</td>
          <td>{{ $expense->amount }}</td>
          <td>{{ $expense->split_type }}</td>
          <td>{{ $expense->status }}</td>
          <td>
            @if ($expense->location == 'No recipt')
                No Receipt              
            @else
              <a href="{{ asset('storage/'.$expense->location) }}"><img src="{{ asset('storage/'.$expense->location) }}" alt="" width="100" height="100"></a>
            @endif
          </td>
          <td><a href="{{ route('viewExpense',["eid"=>$expense->id,"gid"=>$group->id]) }}" class="btn btn-primary">view</a></td>
          @php
            $matchedPayment = $payments->firstWhere('expense_id', $expense->id);
          @endphp
          @if ($group->created_by == Auth::user()->id && !$matchedPayment)
            <td><button class="btn btn-danger deleteBtn" data-id="{{ $expense->id }}">Delete</button></td>
          @endif
          
        </tr>
        @endforeach 
        @endif
        <!-- Add more expenses here -->
      </tbody>
    </table>

  </div>
  <script>
      document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener('click', function () {
                const expenseId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this expense?')) {
                    window.location.href = `/deleteExpense/${expenseId}`;
                }
            });
        });
  </script>
@endsection