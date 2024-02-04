@extends('customer.layouts.app')
@section('styles')
  <style> 
      .customVal::after {
            content: ' *';
            color: red;
            font-weight:bold;
            font-size: 1.1rem;
        }
  </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('')}}/customer">Home</a></li>
                    <li class="breadcrumb-item active">Change Password</li>
                    </ol>        
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col sm-12 col-md-4 col-lg-4 text-left">
                    <h4 id='pageTitle'><span style="border-bottom: 2px solid black;">Change Login Password&nbsp;&nbsp;<i class="fa fa-lock"></i></span></h4>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4 col-lg-4 col-sm-12">
                    <form id="changePasswordForm">
                        <div class="form-group">
                            <label for="oldPass" class="customVal">Old Password:</label>
                            <input name="oldPass" id="oldPass" type="password" class="form-control clearFields" placeholder="Old Password" autofocus>
                        </div>
                        <div class="form-group">
                            <label for="newPass" class="customVal">New Password:</label>
                            <input name="newPass" id="newPass" type="password" class="form-control clearFields" placeholder="New Password">
                        </div>
                        <div class="form-group">
                            <label for="newPass_confirmation" class="customVal">Confirm New Password:</label>
                            <input name="newPass_confirmation" id="newPass_confirmation" type="password" class="form-control clearFields" placeholder="Confirm Password">
                        </div>
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-primary" id="changePasswordBtn">
                                    <span>Update</span>
                                    <span style="display:none;"><span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        Updating...</span>
                                </button>
                                <button type="button" class="ml-3 px-3 btn btn-secondary" id="clearFormBtn">
                                    Clear
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@section('scripts')
<script>
$("body").tooltip({ selector: '[rel=tooltip]' });
let token = $('meta[name="csrf-token"').attr('content');
let clearFormBtn = document.querySelector('#clearFormBtn');

// Set Defaults For the jquery-validator
$.validator.setDefaults({
    onkeyup: false,
    onclick: false,
    onfocusout: false,
    errorClass: 'text-danger font-weight-bold',
    errorElement: "small",
    highlight: function(element) {
        $(element).addClass("is-invalid");
    },
    unhighlight: function(element) {
        $(element).removeClass('is-invalid');
    },
    normalizer: function(value) {
        return $.trim(value);
    },
    errorPlacement: function(error, element) {
        while(element[0].nextElementSibling) {
            element[0].nextElementSibling.remove();
        }
        element[0].insertAdjacentElement('afterend', error[0]);
    }
});

// jquery validator for change password form
$('#changePasswordForm').validate({
    rules: {
        oldPass: {
            required: true,
        },
        newPass: {
            required: true,
            minlength: 3,
            pattern: /^[a-z0-9@$!%*#?&]{2,}$/i
        },
        newPass_confirmation: {
            equalTo: '[name="newPass"]'
        }    
    },
    messages: {
        oldPass: {
            required: "Old Password Is Required.",
        },
        newPass: {
            required: "New Password Is Required.",
            minlength: "Password Must Be Atleast 3 Characters.",
            pattern: "Invalid Password Format (Alphanumeric With Special Characters Allowed)."
        },
        newPass_confirmation: {
            equalTo: 'Confirm Password Must Match With New Password'
        }  
    },
    submitHandler: function (form, event) {
        event.preventDefault();
        clearForm(['changePasswordForm'], false, true);

        let customerObj = {
            oldPass: form.querySelector('input[name="oldPass"]').value.trim(),
            newPass: form.querySelector('input[name="newPass"]').value.trim(),
            newPass_confirmation: form.querySelector('input[name="newPass_confirmation"]').value.trim()
        }
        $.ajax({
            url: '{!! route('customer.updatepassword') !!}',
            method: 'POST',
            data: {
                _token: token,
                customerObj : JSON.stringify(customerObj),
                _method: 'PUT'
            },
            beforeSend: function() {
                btnLoaderAction('changePasswordBtn', 'none', 'block', true);
            },
            success: function(res) {
                btnLoaderAction('changePasswordBtn', 'block', 'none', false);
                if(res.success) {
                  clearForm(['changePasswordForm'], true, true);
                  document.querySelector('#oldPass').focus();
                  let pageTitle = document.querySelector('#pageTitle');
                  div = dismiss_alert('alert-success', res.msg, 'node');
                  div.classList.add('mt-3');
                  div.innerHTML = "<i class='fa fa-check-circle'></i> " + div.innerHTML; 
                  if(pageTitle.nextElementSibling) {
                      pageTitle.nextElementSibling.remove();
                  }
                  pageTitle.insertAdjacentElement('afterend', div);
                }
            },
            error: function(res) {
                btnLoaderAction('changePasswordBtn', 'block', 'none', false);
                res = res.responseJSON;
                
                if(!res.success) {
                    let errors = res.errors;
                    Object.keys(errors).forEach(key => {
                        let formel = form.querySelector('input[name="'+key+'"]');
                        // if formel is null, means if it is a select type element
                        if(formel === null) {
                            formel = form.querySelector('select[name="'+key+'"]');
                        }
                        if(formel) {
                            formel.classList.add('is-invalid');
                            while(formel.nextElementSibling) {
                                formel.nextElementSibling.remove();
                            }
                            let small = document.createElement('small');
                            small.className = 'text-danger font-weight-bold';
                            small.innerHTML = errors[key][0];
                            formel.insertAdjacentElement('afterend', small);
                        }

                    });
                }
                
            }
        });
    }
}); 

clearFormBtn.addEventListener('click', (e) => {
    e.preventDefault();
    clearForm(['changePasswordForm'], true, true);
    document.querySelector('#oldPass').focus();
});

</script>
@endsection