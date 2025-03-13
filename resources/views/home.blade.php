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
            <a class="btn btn-success" href="{{ route('addgroup') }}">Add group</a>
            <a class="btn btn-primary" href="{{ route('addUserBalance') }}">Add balance</a>
            <a class="btn btn-warning" href="{{ route('addUserExpense') }}">Add daily expense</a>

        </div>
        <div class="row">
            <div class="dashboard1 col-md-9">
                @isset($groups)
                <h1 class="mt-3">Groups</h1>
            
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Group No</th>
                        <th>Gorup Name</th>
                        <th>Balance</th>
                        <th>Details</th>
                    </tr>
                    </thead>
                    <tbody>

                        @foreach ($groups as $key=> $item)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $item->group_name }}</td>

                        @php
                        // Retrieve the corresponding expense for this group by group_id
                        $expense = $expenses->get($item->id);
                    @endphp
            
                    <td>
                        @if ($expense && $expense->group_total)
                            
                            {{ abs($expense->group_total) }}
                            @if ($expense->group_total > 0)
                                dues
                            @elseif($expense->group_total <0)
                            Get Back
                            @endif
                        @else
                            0
                        @endif
                    </td>
                        <td>
                        <a href="{{ route('group',$item->id) }}" class="btn btn-success m-3">View</a>
                        </td>
                    </tr>
                    @endforeach
                    <!-- Add more expenses here -->
                    </tbody>
                </table>
                @endisset
            </div>
                
            <div class="dashboard1 col-md-3">
                <h1>Expenses</h1>
                <table class="table table-bordered" >
                    <tr class="row">

                            <a href="{{ route('userExpenses') }}" class="btn btn-primary col-md-12 mt-3">
                            view All expenses
                            </a>
                            <form action="{{ route('userExpenses') }}" class="mt-3" method="POST" id="searchForm">
                                @csrf
                                <input type="date" class="mt-3 form-control" style="border: 1px solid black" name="date" id="date">
                                <br>
                                <div class="alert alert-danger " role="alert" id="alert">
                                    please select the date
                                </div>
                                <br>
                                <a href="#" onclick="event.preventDefault();" id="viewExpense" class="btn btn-primary">View</a>
                            </form>
                    </tr>
                    
                </table>
            </div>
        </div>

    </div>
</div>
<script>
    let form = document.getElementById('searchForm');
    let date = document.getElementById('date');
    let view = document.getElementById('viewExpense');
    let alert = document.getElementById('alert');
    alert.style.display = 'none';
    view.addEventListener('click',(e)=>{
        if(date.value == ''){
            console.log(date.value);
            
            alert.style.display = 'block';
            setTimeout(() => {
            alert.style.display = 'none';
            }, 5000);
        }
        else{
            form.submit();
        }
    })
</script>
@endsection
