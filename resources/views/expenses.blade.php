@extends('layouts.app')
@section('content')
<div class="container mt-5">
   
    <!-- Detailed Expenses -->
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
          <th>View</th>
          <th>Status</th>
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
          <td><a href="{{ route('viewExpense',["eid"=>$expense->id,"gid"=>$group->id]) }}" class="btn btn-primary">view</a></td>
          <td>{{ $expense->status }}</td>
        </tr>
        @endforeach 
        @endif
        <!-- Add more expenses here -->
      </tbody>
    </table>

  </div>
@endsection