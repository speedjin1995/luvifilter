<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
    echo '<script type="text/javascript">';
	echo 'window.location.href = "../login.html";</script>';
}
else{
    // Set back user detail
    $user = $_SESSION['userDetail'];
    $userID = $user->getId();
    $role = $user->getRole();
    $status = $user->getStatus();
    $expiredDate = $user->getExpiredDate();
    $keyFlag = $user->getKeyFlag();
    $defaultDate = $user->getDefaultTime();
    $todayDate = date("Y-m-d");
    $toshow = false;
    
    if($todayDate >= date('Y-m-d', strtotime($expiredDate. '- 7 days'))){
        $toshow = true;
    }
    
    // Retrieve all Keys
    $stmt = $db->prepare("SELECT * from tbl_keys where user_id= ?");
    $stmt->bind_param('s', $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Load user role details
    $stmt2 = $db->prepare("SELECT renewal_fee, role_name from roles where role_code= ?");
    $stmt2->bind_param('s', $role);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $row2 = $result2->fetch_assoc();
}
?>

<!DOCTYPE html>
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Dashboard</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">Dashboard</li>
				</ol>
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
	    <div class="row" style="margin-bottom:15px;">
	        <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box" style="height:100%; width:100%;">
                    <span class="info-box-icon bg-info"><i class="fas fa-user"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="color:#1888CA">Role</span>
                        <span class="info-box-number" style="line-height:1.5rem; font-size:1.5rem"><?=$row2['role_name'] ?></span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
	        
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box" style="height:100%; width:100%;">
                    <?php
                        if( $status == "active" ) {
                            echo '<span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>';
                        }
                        else {
                            echo '<span class="info-box-icon bg-danger"><i class="fas fa-exclamation"></i></span>';
                        }
                    ?>
                    <div class="info-box-content">
                        <span class="info-box-text" style="color:#1888CA">Status</span>
                        <?php
                            if($status == "active") {
                                if($role == 'PRIADMIN'){
                                    echo '<span class="info-box-number" style="line-height:1.5rem; font-size:1.5rem">Active</span>';
                                }
                                else if($expiredDate != null || $expiredDate != "") {
                                    echo '<span class="info-box-number" style="line-height:1.5rem; font-size:1.5rem">Active until </br>' . date("d-m-Y", strtotime($expiredDate)) . '</span>';    
                                }
                                else {
                                    echo '<span class="info-box-number" style="line-height:1.5rem; font-size:1.5rem">Active</span>';
                                }
                            }
                            else {
                                echo '<span class="info-box-number" style="line-height:1.5rem; font-size:1.5rem">Inactive</span>';
                            }
                        ?>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
          
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box" style="height:100%; width:100%;">
                    <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="color:#1888CA">Amount Due</span>
                        <?php if($role == 'PRIADMIN' || (!$toshow && $keyFlag == 'Y')){?>
                            <span class="info-box-text" style="line-height:2rem; font-size:1.5rem"><b>-</b></span>
                        <?php } else{ ?>
                            <span class="info-box-text" style="line-height:2rem; font-size:1.5rem"><b>RM <?=number_format($row2['renewal_fee'], 2, '.', ',') ?></b></span>
                            <span class="info-box-number">
                                <button type="button" class="btn btn-warning btn-sm float-right" id="checkout-button"><i class="fas fa-redo-alt"></i> Renew</button>
                            </span>
                        <?php } ?>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
        </div>
        
        <div class="row" style="margin-bottom:15px;">
	        <div class="col-md-12 col-sm-12 col-12">
                <div class="info-box" style="height:100%; width:100%;">
                    <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <form role="form" id="timeForm" novalidate="novalidate">
            				<div class="form-group">
            					<label for="time">Default Time (You can sync your default time in extension)</label>
            					<input type="time" class="form-control" id="time" name="time" step=".001" value="<?=$defaultDate ?>">
            				</div>
                			<button class="btn btn-success" id="saveTime"><i class="fas fa-save"></i> Save</button>
                		</form>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
        </div>
	    
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
					    <span class="info-box-text" style="font-size:1.5rem; color:#1888CA; float:left;">My Keys</span>
						<button type="button" class="btn btn-outline-primary btn-add" style="float: right;"><i class="fas fa-plus"></i> Add Key</button>
					</div>
					<!-- /.card-header -->
					<div class="card-body">
						<table id="keyTables" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Key</th>
									<th>Last Active Device</th>
									<th>Last Active Time</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody id="keys">
								<?php while($row = $result->fetch_assoc()){ ?>
									<tr class="keyDetail" data-index="<?= $row['id'] ?>">
										<td><?= $row['key_value'] ?></td>
										<td><?= $row['device'] ?></td>
										<td><?= $row['last_update'] ?></td>
										<td>
										<?php
											if($row['hostname'] == null || $row['hostname'] == ""){
												echo 'Not in Use';
											}
											else{
												echo 'In Use';
											}
										?>
										</td>
										<td> 
											<div class="row">
												<div class="col-12">
													<button type="button" id="delete<?= $row['id'] ?>" onclick="deleteKey(<?= $row['id'] ?>)" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
												</div>
											</div>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div><!-- /.card-body -->
				</div><!-- /.card -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</section><!-- /.content -->

<script>
var stripe = Stripe('pk_live_51HTgJxCpn182ZxSC7T3g3QKdJlc7x9la8ywfdIYeQxSQaMQFe6c1AaxrSWxePwogClaHMMs2V3wDXMMdlOXNSQ3y00cMwYKZp5');

$(function () {
    $.validator.setDefaults({
        submitHandler: function () {
            $.post('php/updateTime.php', $('#timeForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    toastr["success"](obj.message, "Success:");
                    
                    $.get('dashboard.php', function(data) {
                        $('#mainContents').html(data);
                    });
        		}
        		else if(obj.status === 'failed'){
        		    toastr["error"](obj.message, "Failed:");
                }
        		else{
        			toastr["error"]("Failed to save default time", "Failed:");
        		}
            });
        }
    });
    
    $("#keyTables").DataTable({
        "responsive": true,
        "autoWidth": false,
    });
    
    $('#checkout-button').on("click", function () {
        fetch("php/create-session.php", {
            method: "POST",
        })
        .then(function (response) {
          return response.json();
        })
        .then(function (session) {
          return stripe.redirectToCheckout({ sessionId: session.id });
        })
        .then(function (result) {
            if (result.error) {
                toastr["error"](result.error.message);
            }
        })
        .catch(function (error) {
            toastr["error"]("Failed to make payment!!!", "Error:");
        });
    });
    
    $(".btn-add").on("click", function(){
        $.post("php/createKey.php", function( data ) {
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                toastr["success"]("Added " + obj.message, "Success:");
                
                $.get('dashboard.php', function(data) {
                    $('#mainContents').html(data);
                });
            }
            else if(obj.status === 'failed'){
                toastr["error"](obj.message, "Failed:");
            }
            else{
                toastr["error"]("unknown error", "Failed:");
            }
            
        });
    });
    
    $('#timeForm').validate({
        rules: {
            time: {step: false}
        },
        messages: {
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

function deleteKey(id){
    $.post('php/deleteKey.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
		if(obj.status === 'success'){
			toastr["success"](obj.message, "Success:");
			$.get('dashboard.php', function(data) {
                $('#mainContents').html(data);
            });
		}
		else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
        }
		else{
		    toastr["error"]("Something wrong when delete", "Failed:");
		}
    });
}
</script>