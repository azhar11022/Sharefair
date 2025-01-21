@extends('layouts.app')

@section('content')

<style>
    body {
        background-color: transparent;
        font-family: Arial, sans-serif;
    }
    .expense-form {
        max-width: 400px;
        background-color: transparent;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(250, 238, 238, 0.1);
        padding: 20px;
        margin: 50px auto;
    }
    .expense-form h2 {
        font-size: 1.5rem;
        color: #4caf50;
        margin-bottom: 20px;
    }
    .form-group label {
        font-weight: bold;
    }
    .form-control {
        margin-bottom: 15px;
    }
    .btn-save {
        background-color: #4caf50;
        color: white;
        border: none;
    }
    .btn-save:hover {
        background-color: #45a049;
    }
        /* Container for the select element */
        .dropdown-container {
            position: relative;
            width: 100%; /* Adjust the width as needed */
        }

        /* Style the select element */
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            appearance: none; /* Remove default styling for select */
            background: white;
            font-size: 16px;
        }

        /* Add a dropdown arrow icon */
        .dropdown-container::after {
            content: "▼"; /* Unicode for the arrow */
            position: absolute;
            top: 50%;
            right: 10px; /* Adjust the position of the icon */
            transform: translateY(-50%);
            pointer-events: none; /* Ignore clicks on the icon */
            font-size: 14px;
            color: #777;
        }
</style>

<div class="expense-form">
    <h2>Add an Expense</h2>
    <form action="{{ route('addexpenses',$group->id) }}" method="POST" id="addExpenseForm">
        <!-- CSRF Token -->
        @csrf
        
        <!-- Description -->
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" class="form-control" placeholder="Enter a description" >
            @error('description')
            <div class="alert alert-danger" role="alert">
                {{ $message }}
            </div>
            @enderror
        </div>
        
        <!-- Amount -->
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" step="0.01" min="0" id="amount" name="amount" class="form-control" placeholder="Enter amount" >
            @error('amount')
            <div class="alert alert-danger" role="alert">
                {{ $message }}
            </div>
            @enderror
        </div>
        
        <!-- Paid By -->
        <div class="form-group">
            <label for="paid_by">Paid By</label>
            <div class="dropdown-container">
            <select id="paid_by" name="paid_by" class="form-control">
                @isset($users)
                @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }} &nbsp; | &nbsp;{{ $user->email }}</option>
                @endforeach
                @endisset
            </select>
            </div>
            @error('paid_by')
            <div class="alert alert-danger" role="alert">
                {{ $message }}
            </div>
            @enderror
        </div>
        
        <!-- Split Equally -->
        <div class="form-group">
            <label for="split_method">Split Method</label>
            <div class="dropdown-container">
            <select id="split_method" name="split_method" class="form-control">
                <option value="equally" selected>Equally</option>
                <option value="exact_amount" >Exact amount</option>
                <option value="percentage" >Parcentage</option>
            </select>
            </div>
            @error('split_method')
            <div class="alert alert-danger" role="alert">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="" id="error_div">
            
        </div>
        {{-- split method exact ammount --}}
        
        <div class="form-group" id="exact_amount">
            <label for="group">Divide amount on Members</label><br><br>
            
            @foreach ($users as $user)
            <label for="name">{{ $user->name }} paid</label>
            <input type="text" id="group" name="{{ $user->id }}" class="form-control exact_amount_input ex_a" value="0"  >
            @endforeach
        </div>
        {{-- split method percentage --}}
        
        <div class="form-group" id="percentage">
            <label for="group">Divide percentage on Members</label><br><br>
            
            @foreach ($users as $user)
            <label for="name">{{ $user->name }} paid</label>
            <input type="text" id="group" name="{{ $user->id }}" class="form-control percentage_input ex_p" value="0"  >
            @endforeach
        </div>

        <!-- Group -->
        <div class="form-group">
            <label for="group">Group</label>
            <input type="text" id="group" class="form-control" value="{{ $group->group_name }}" readonly >
        </div>
        
        <!-- Date -->
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
            @error('date')
            <div class="alert alert-danger" role="alert">
                {{ $message }}
            </div>
            @enderror
        </div>
        
        <!-- Submit Button -->
        <div class="form-group text-center">
            <button class="btn btn-save" onclick="event.preventDefault();check();">Save</button>
            <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
        </div>
    </form>
</div>
<script>
    let exact_amount_div = document.getElementById('exact_amount');
    let percentage_div = document.getElementById('percentage');
    let split = document.getElementById('split_method');
    var amount_to_divide = document.getElementById('amount');
    let users_of_exect_amount = document.querySelectorAll('.exact_amount_input');
    let users_of_percentage = document.querySelectorAll('.percentage_input');
    let form_addexpense = document.getElementById('addExpenseForm');
    
    exact_amount_div.style.display = 'none';
    percentage_div.style.display = 'none';

    for(item of users_of_exect_amount){
        item.setAttribute('disabled', 'true');
    }
    for(item of users_of_percentage){
        item.setAttribute('disabled', 'true');
    }
    
    let ex_a = document.querySelectorAll('.ex_a');
    let ex_p = document.querySelectorAll('.ex_p');
    var amount_change = 0;
    var percentage_change = 100;
        // amount_change = parseFloat(amount_to_divide.value);

        for (let item of ex_a) {
            item.addEventListener('blur', (e) => {
                // Subtract the input value from amount_change when the input loses focus
                let input_value = parseFloat(item.value);
                
                if(amount_change == 0){
                    amount_change = parseFloat(amount_to_divide.value) || 0
                }

                amount_change -= input_value;
                item.classList.remove('ex_a');
                change_amount();
                
        });
    }
        for (let item of ex_p) {
            item.addEventListener('blur', (e) => {
                // Subtract the input value from amount_change when the input loses focus
                let input_value = parseFloat(item.value);
                percentage_change -= input_value;
                item.classList.remove('ex_p');
                change_percentage();
        });
    }
    function change_amount(){
        ex_a = document.querySelectorAll('.ex_a');
        let len = ex_a.length;
        let each_remaining = amount_change/len;
        for(item of ex_a){
            item.value = each_remaining;
        }
    }
    function change_percentage(){
        ex_p = document.querySelectorAll('.ex_p');
        let len = ex_p.length;
        let each_remaining = percentage_change/len;
        for(item of ex_p){
            item.value = each_remaining;
        }
    }

    
    split.addEventListener('input',(e)=>{
        if(split.value == 'equally'){
            exact_amount_div.style.display = 'none';
            percentage_div.style.display = 'none';
            for(item of users_of_exect_amount){
                item.setAttribute('disabled', 'true');
            }
            for(item of users_of_percentage){
                item.setAttribute('disabled', 'true');
            }
        }
        
        // if the split method is exact_amount
        if(split.value == 'exact_amount'){
            percentage_div.style.display = 'none';
            for(item of users_of_exect_amount){
                item.removeAttribute('disabled');
            }
            for(item of users_of_percentage){
                item.setAttribute('disabled', 'true');
            }
            c = 0;
            for(item of users_of_exect_amount){
                c++; 
            }
            let each = amount_to_divide.value/c;
            for(item of users_of_exect_amount){
                item.value = each;
            }
            exact_amount_div.style.display='block';
        }
        // if the split method is percentage
        else if(split.value == 'percentage'){
            exact_amount_div.style.display = 'none';
            percentage_div.style.display='block';
            for(item of users_of_exect_amount){
                item.setAttribute('disabled', 'true');
            }
            for(item of users_of_percentage){
                item.removeAttribute('disabled');
            }
            let len = users_of_percentage.length;
            let each = 100/len;
            for(item of users_of_percentage){
                item.value = each;
            }

        }
    })
    
    function check(){
        if(split.value == 'equally'){
            form_addexpense.submit();
        }
        if(split.value == 'exact_amount'){
            let amount = 0;
            for(item of users_of_exect_amount){
                amount += parseFloat(item.value)||0;
            }
            if(amount_to_divide.value == amount ){
                form_addexpense.submit();
            }
            else {
                   let alert = `
                                <div class="alert alert-danger" role="alert">
                                  Divide the complete amount  
                                </div>
                                `;
                    let error_div = document.getElementById('error_div');
                    error_div.innerHTML = alert;
                }
        }
        if(split.value == 'percentage'){
            let percentage = 0;
            for(item of users_of_percentage){
                percentage += parseFloat(item.value)||0;
            }
            if(percentage == 100 ){
                form_addexpense.submit();
            }
            else {
                   let alert = `
                                <div class="alert alert-danger" role="alert">
                                  Divide the complete percentage  
                                </div>
                                `;
                    let error_div = document.getElementById('error_div');
                    error_div.innerHTML = alert;
                }
        }
    }

</script>
@endsection