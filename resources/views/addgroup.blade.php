@extends('layouts.app')

@section('content')
<style>
    .delete-member{
        outline: none;
        background: transparent;
        border: none    
    }
    .delete-member:hover{
        font-size: 12px;
    }
</style>
<div class="container">
    @if (session('success'))
    <div class="alert alert-success" role="alert">
        added successfully
      </div>
    @endif
    <div class="row">

        <form class="col-md-6 offset-md-3" id="groupForm" action="{{ route('addgroup') }}" method="POST">
            @csrf
            <div class="form-row ">
              <div class="col m-3">
                <label for="memberEmail">Group Name</label>
                <input type="text" name="group_name" class="form-control" id="group_name" placeholder="First name" value="{{ old('group_name') }}">
                @error('group_name')
                <div class="alert alert-danger" role="alert">{{ $message }}</div>
                @enderror
              </div>
              @php
              $count = 1;
              @endphp
              <div class="m-3 row">
                <p>{{ Auth::user()->name }} ({{ Auth::user()->email }})</p>
                <label for="name" class="col-md-3">Name</label>
                <label for="name" class="col-md-3 offset-md-3">Email</label>
              </div>

              <div class="" style="display: flex">
                
                  <div class="col-md-5 m-3">
                    <input type="text" id="memberName" class="form-control name" name="memberName[{{ $count }}]" placeholder="Enter member name" value="">
                </div>
       
                <div class="col-md-6 offset-md-1 m-3">
                    <input type="text" id="memberEmail" class="form-control email"  name="memberEmail[{{ $count }}]" placeholder="Enter member email">
                </div>
                <button type="button" class="delete-member" onclick="deleteMember(this)">x</button>
              </div>

            <div class="member-container" id="additionalMembers"></div>
            <button type="button" class="form-control m-3 w- 25" style="font-size:10px" id="addMemberButton">Add Another Member</button>
              <div class="col-md-12 m-3">
                  <select class="custom-select form-control" name="group_type" id="group_type">
                      <option selected disabled value="">Group Type</option>
                      <option value="hostal" name="">Hostel</option>
                      <option value="home" name="">Home</option>
                      <option value="trip" name="">Trip</option>
                      <option value="couple" name="">Couple</option>
                      <option value="others" name="">Others</option>
                    </select>
                    @error('group_type')
                    <div class="alert alert-danger" role="alert">{{ $message }}</div>
                    @enderror
              </div>
              <a href="{{ route('addgroup') }}"  class="form-control m-3 w-50 text-center" style="text-decoration: none" onclick="event.preventDefault(); check();">Add Group</a>
    
            </div>
          </form>
    </div>
</div>
<script>
    const addMemberButton = document.getElementById('addMemberButton');
    const additionalMembers = document.getElementById('additionalMembers');

    addMemberButton.addEventListener('click', () => {
        {{ $count++ }}
        const memberFields = `
            <div class="" style="display: flex">
                  <div class="col-md-5 m-3">
                    <input type="text" id="memberName" class="form-control name" name="memberName[{{ $count }}]" placeholder="Enter member name">
                </div>
       
                <div class="col-md-6 offset-md-1 m-3">
                    <input type="text" id="memberEmail" class="form-control email"  name="memberEmail[{{ $count }}]" placeholder="Enter member email">
                </div>
                  <button type="button" class="delete-member" onclick="deleteMember(this)">x</button>
              </div>
        `;
        additionalMembers.insertAdjacentHTML('beforeend', memberFields);
    });
    function deleteMember(button) {
            button.parentElement.remove();
        }
    
    function check() {
    let memberEmail = document.querySelectorAll('.email');
    let memberName = document.querySelectorAll('.name');
    let groupName = document.querySelector('#group_name');
    let groupType = document.querySelector('#group_type');

    let count = 0; // Counter for validation errors

    // Validate Group Name
    if (groupName.value.trim() === "") {
        showAlert(groupName, 'Group name is required');
        count++;
    } else {
        removeAlert(groupName);
    }

    // Validate Group Type
    if (!groupType.value.trim()) {
        showAlert(groupType, 'Group type is required');
        count++;
    } else {
        removeAlert(groupType);
    }

    // Check Email Validity and Uniqueness
    let emailSet = new Set([`{{ Auth::user()->email }}`]); // Start with the authenticated user's email

    memberEmail.forEach((input) => {
        let email = input.value.trim();

        if (!isValidEmail(email)) {
            showAlert(input, 'Enter a valid email');
            count++;
        } 
        // else if (emailSet.has(email)) {
        //     showAlert(input, 'Email must be unique');
        //     count++;
        // }
         else {
            removeAlert(input);
            emailSet.add(email); // Add email to the set
        }
    });

    // Validate Member Names
    memberName.forEach((input) => {
        if (input.value.trim() === "") {
            showAlert(input, 'Name is required');
            count++;
        } else {
            removeAlert(input);
        }
    });

    // If no validation errors, submit the form
    if (count === 0) {
        document.getElementById('groupForm').submit();
    }
}

// Utility Function: Show Alert
function showAlert(input, message) {
    const parent = input.closest('.m-3');
    const existingAlert = parent.querySelector('.alert-danger');
    if (existingAlert) {
        existingAlert.textContent = message;
    } else {
        const danger = document.createElement('div');
        danger.className = 'alert alert-danger mt-2';
        danger.setAttribute('role', 'alert');
        danger.textContent = message;
        parent.appendChild(danger);
    }
}

// Utility Function: Remove Alert
function removeAlert(input) {
    const parent = input.closest('.m-3');
    const alert = parent.querySelector('.alert-danger');
    if (alert) alert.remove();
}

// Utility Function: Validate Email Format
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
</script>
@endsection