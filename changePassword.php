<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
	header("location:login.html");
}
else{
    $user = $_SESSION['userDetail'];
    $role = $user->getRole();
}
?>

<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Change Password</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item"><a href="#">Settings</a></li>
					<li class="breadcrumb-item active">Change Password</li>
				</ol>
			</div><!-- /.col -->
		</div>
	</div>
</section>

<section class="content">
	<div class="card">
		<form role="form" id="passwordForm">
			<div class="card-body">
				<div class="form-group">
					<label for="oldPassword">Old Password *</label>
					<input type="password" class="form-control" name="oldPassword" placeholder="Old Password" required>
				</div>
				
				<div class="form-group">
					<label for="newPassword">New Password</label>
					<input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="New Password" required>
				</div>
				
				<div class="form-group">
					<label for="confirmPassword">Confirm Password *</label>
					<input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Re-type Password" required>
				</div>
			</div>
			
			<div class="card-footer">
				<button type="submit" class="btn btn-success" name="submit"><i class="fas fa-save"></i> Save</button>
			</div>
		</form>
	</div>
</section>
		    		
<script>
$(function () {
    $.validator.setDefaults({
        submitHandler: function () {
            $.post('php/changePassword.php', $('#passwordForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    toastr["success"](obj.message, "Success:");
                    
                    $.get('changePassword.php', function(data) {
                        $('#mainContents').html(data);
                    });
        		}
        		else if(obj.status === 'failed'){
                    toastr["error"](obj.message, "Failed:");
                }
        		else{
        			toastr["error"]("Failed to update password", "Failed:");
        		}
            });
        }
    });
    
    $('#passwordForm').validate({
        rules: {
            newPassword: {
                minlength: 6
            },
            confirmPassword: {
                equalTo: "#newPassword"
            }
        },
        messages: {
            newPassword: {
                minlength: "Your password must be at least 6 characters long"
            },
            confirmPassword: " Enter Confirm Password Same as New Password"
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>