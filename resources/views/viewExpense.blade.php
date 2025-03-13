@extends('layouts.app')
@section('content')
<div class="container mt-5">
   
    <!-- Detailed Expenses -->
    @if (session('danger'))
      <div class="alert alert-danger" role="alert">
          {{ session('danger') }}
      </div>
    @endif
    <div class="mb-3" id="form_repay">
            @if ($errors)
            @foreach ($errors->all() as $item)
                {{ $item }} <br>
            @endforeach
            @endif
    </div>
    <div class="card-header bg-primary text-white">
        <div class="row">
            
            <div class=" col-md-6 ">
                <h3 class="m-3">Detailed Expenses</h3>
            </div>
            <div class="col-md-5 text-end">
                <a href="{{ route('expenses',$gid) }}" class="btn btn-success m-3"> Back</a>
            </div>
        </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-md-12" >
          @if (!empty($payments))
          <h3  style="line-height: 40px" class="text-center">
            <marquee behavior="alternate" direction="left" class="text-center">

              @foreach ($payments as $payment )
              <span style="margin-right: 100px" class="text-center">
                {{ $payment->name }} paid dues {{ $payment->total_amount }} 
              </span>
              @endforeach
            </marquee>
          @endif
          </h3>
        </div>
      </div>
    </div>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Date</th>
          <th>Name</th>
          <th>Description</th>
          <th>Amount</th>
          <th>Pay</th>
        </tr>
      </thead>
      <tbody>
        @if ($expenses)
        @foreach ($expenses as $expense)
        <tr>
          <td>{{ $expense->date }}</td>
          <td>{{ $expense->name }}</td>
          <td>{{ $expense->description }}</td>
          @if ($expense->p_amount < 0)
          <td>
            Get back <br>
            {{ abs($expense->p_amount) }}
          </td>
          @else
          <td>
            Dues <br>
            {{ $expense->p_amount }}
          </td>
          @endif
          @if (Auth::user()->id == $expense->user_paid && $expense->p_amount >0)
                    <td><a href="#" onclick="event.preventDefault(); show({{ $expense->id}},{{ $expense->p_amount}},{{ $expense->u_id }},{{ $expense->user_paid }},{{ $expense->e_id }} )" class="btn btn-primary">Pay</a></td>
          @endif
         
        </tr>
        @endforeach 
        @endif
        <!-- Add more expenses here -->
      </tbody>
    </table>
    <script>
        function show(expense_p_id,amount,paid_by,paid_to,e_id){
            let repay = document.getElementById('form_repay');
            let route = "{{ route('repay')}}"
            
            let form = `
            <form action="${route}" method="POST" id="form">
                @csrf
                <input type="number" name="paidBy" value="${paid_by}" hidden >
                <input type="number" name="group_id" value="{{ $gid }}" hidden >
                <input type="number" name="paidTo" value="${paid_to}" hidden >
                <input type="number" name="eId" value="${e_id}" hidden >
                    <div class="form-group">
                        <label for="amount">Enter Repayment Amount:</label>
                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            class="form-control"
                            placeholder="Enter amount to repay"
                            step="0.01"
                            min="1"
                            max="${amount}"
                            required
                        >
                        <small class="form-text text-muted">Amount due: ${amount}</small>
                    </div>
                
                    <div class="mt-3">
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Submit</button>
                        
                        <!-- Full Settle Button -->
                        <button
                            type="button"
                            class="btn btn-success"
                            onclick="document.getElementById('amount').value = ${amount}; document.querySelector('#form').submit();"
                        >
                            Full Settle
                        </button>
                    </div>
                </form>
            `;
            
            repay.innerHTML = form;
        }
    </script>
  </div>
@endsection