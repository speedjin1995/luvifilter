<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

session_start();
	
if(!isset($_SESSION['userDetail'])){
	header("location:login.html");
} else{
	$user = $_SESSION['userDetail'];
	$role = $user->getRole();
}
?>

<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Profile</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item"><a href="#">Settings</a></li>
					<li class="breadcrumb-item active">Profile</li>
				</ol>
			</div><!-- /.col -->
		</div>
	</div>
</section>

<section class="content" style="min-height:700px;">
	<div class="card">
		<form role="form" id="profileForm" novalidate="novalidate">
			<div class="card-body">
				<div class="form-group">
					<label for="name">Full Name *</label>
					<input type="text" class="form-control" id="userName" name="userName" value="<?=$user->getName() ?>" placeholder="Enter Full Name" required>
				</div>
				
				<div class="form-group">
					<label for="name">Email *</label>
					<input type="email" class="form-control" id="userEmail" name="userEmail" value="<?=$user->getEmail() ?>" placeholder="Enter Email" readonly>
				</div>
			</div>
			
			<div class="card-footer">
				<button class="btn btn-success" id="saveProfile"><i class="fas fa-save"></i> Save</button>
			</div>
		</form>
	</div>
</section>

<script>
$(function () {
    $.validator.setDefaults({
        submitHandler: function () {
            $.post('php/updateProfile.php', $('#profileForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    toastr["success"](obj.message, "Success:");
                    
                    $.get('myProfile.php', function(data) {
                        $('#mainContents').html(data);
                    });
        		}
        		else if(obj.status === 'failed'){
        		    toastr["error"](obj.message, "Failed:");
                }
        		else{
        			toastr["error"]("Failed to update profile", "Failed:");
        		}
            });
        }
    });
    
    $('#profileForm').validate({
        rules: {
            email: {
                required: true,
                email: true,
            },
            text: {
                required: true
            }
        },
        messages: {
            email: {
                required: "Please enter a email address",
                email: "Please enter a vaild email address"
            },
            text: {
                required: "Please fill in this field"
            }
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