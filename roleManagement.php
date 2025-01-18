<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
    echo '<script type="text/javascript">';
	echo 'window.location.href = "../login.html";</script>';
}
else{
    $stmt = $db->prepare("SELECT * FROM roles");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $stmt2 = $db->prepare("SELECT * FROM roles");
    $stmt2->execute();
    $result2 = $stmt2->get_result();
}
?>

<!DOCTYPE html>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Role Management</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">Role Management</li>
				</ol>
			</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
        <div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">User Roles</div>
					
					<div class="card-body">
						<table id="memberTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Role Name</th>
									<th>Max Key</th>
									<th>Renewal Fees</th>
									<th>Number of Days</th>
									<th>Role to Change</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody id="keys">
								<?php while($row = $result->fetch_assoc()){ ?>
									<tr class="rowDetail" data-index="<?= $row['id'] ?>">
										<td><?= $row['role_name'] ?></td>
										<td><?= $row['max_key'] ?></td>
										<td>RM <?= number_format($row['renewal_fee'], 2, '.', ',') ?></td>
										<td><?= $row['number_of_days'] ?></td>
										<td><?= $row['change_to'] ?></td>
										<td> 
											<div class="row">
												<div class="col-12">
												    <input type="hidden" class="form-control" id="role_name<?= $row['id'] ?>" value="<?= $row['role_name'] ?>">
												    <input type="hidden" class="form-control" id="max_key<?= $row['id'] ?>" value="<?= $row['max_key'] ?>">
												    <input type="hidden" class="form-control" id="renewal_fee<?= $row['id'] ?>" value="<?= number_format($row['renewal_fee'], 2, '.', ',') ?>">
													<input type="hidden" class="form-control" id="number_of_days<?= $row['id'] ?>" value="<?= $row['number_of_days'] ?>">
												    <input type="hidden" class="form-control" id="change_to<?= $row['id'] ?>" value="<?= $row['change_to'] ?>">
													<button type="button" onclick="edit(<?= $row['id'] ?>)" class="btn btn-block btn-success btn-sm">Edit</button>
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

<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="editRoleForm">
            <div class="modal-header">
                <h4 class="modal-title">Edit Renewal Fees</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
    					<input type="hidden" class="form-control" id="ID" name="ID">
    				</div>
    				<div class="form-group">
    					<label for="name">Role Name *</label>
    					<input type="text" class="form-control" name="roleName" id="roleName" placeholder="Role Name" required>
    				</div>
    				<div class="form-group">
    					<label for="numKey">Number of Keys *</label>
    					<input type="number" class="form-control" name="numkeys" id="numkeys" step="1" min="1" placeholder="Number of Keys" required>
    				</div>
    				<div class="form-group">
    					<label for="renewalFees">Renewal Fees *</label>
    					<input type="number" class="form-control" name="renewalFees" id="renewalFees" step="0.01" min="0.00" placeholder="Number of Days" required>
    				</div>
    				<div class="form-group">
    					<label for="numberofDays">Number of Days *</label>
    					<input type="number" class="form-control" name="numberofDays" id="numberofDays" step="1" min="0" placeholder="Number of Days" required>
    				</div>
    				<div class="form-group">
						<label for="changedRole">Change to Role</label>
						<select class="form-control" name="changedRole" id="changedRole">
						    <option select="selected" value="">Please Select</option>
						    <?php while($row2 = $result2->fetch_assoc()){ ?>
    							<option value="<?= $row2['role_code'] ?>"><?= $row2['role_name'] ?></option>
							<?php } ?>
						</select>
					</div>
    			</div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" name="submit" id="submitEdit">Submit</button>
            </div>
        </form>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
$(function () {
    $("#memberTable").DataTable({
        "responsive": true,
        "autoWidth": false,
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $.post('php/manageRoles.php', $('#editRoleForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#editModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    
        			$.get('roleManagement.php', function(data) {
                        $('#mainContents').html(data);
                    });
        		}
        		else if(obj.status === 'failed'){
                    toastr["error"](obj.message, "Failed:");
                }
        		else{
        			toastr["error"]("Failed to update role", "Failed:");
        		}
            });
        }
    });
});

function edit(id){
    $('#editModal').find('#roleName').val($('#role_name' + id).val());
    $('#editModal').find('#numkeys').val($('#max_key' + id).val());
    $('#editModal').find('#renewalFees').val($('#renewal_fee' + id).val());2
    $('#editModal').find('#numberofDays').val($('#number_of_days' + id).val());
    $('#editModal').find('#changedRole').val($('#change_to' + id).val());
    $('#editModal').find('#ID').val(id);
    $('#editModal').modal('show');

    $('#editRoleForm').validate({
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
}
</script>
</body>
</html>
