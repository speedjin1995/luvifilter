<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
    echo '<script type="text/javascript">';
	echo 'window.location.href = "../login.html";</script>';
}
else{
    /*$stmt = $db->prepare("SELECT users.id, users.name, users.email, users.joined_date, users.expired_date, users.status, roles.role_name from users, roles WHERE users.role_code = roles.role_code");
    $stmt->execute();
    $result = $stmt->get_result();*/
    
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
				<h1 class="m-0 text-dark">Members</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">Members</li>
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
					<div class="card-header"></div>
					
					<div class="card-body">
						<table id="memberTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Name</th>
									<th>Email</th>
									<th>Role</th>
									<th>Joined Date</th>
									<th>Expired Date</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
						</table>
					</div><!-- /.card-body -->
				</div><!-- /.card -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</section><!-- /.content -->

<div class="modal fade" id="keyModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Keys</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header"></div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <table id="keyTable" class="table table-bordered table-striped">
                      <thead>
                          <tr>
                            <th>Key</th>
                            <th>Last Active Date</th>
                          </tr>
                      </thead>
                      <tbody id="modelkeys"></tbody>
                    </table>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="assignModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="assignForm">
            <div class="modal-header">
              <h4 class="modal-title">Set Role</h4>
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
						<label>Role Code *</label>
						<select class="form-control" name="userRole" required>
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
              <button type="submit" class="btn btn-primary" name="submit">Submit</button>
            </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="extendModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="extendForm">
            <div class="modal-header">
              <h4 class="modal-title">Extend Membership</h4>
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
    					<label for="name">Number of Days *</label>
    					<input type="number" class="form-control" name="numDays" id="numDays" step="1" min="1" placeholder="Number of Days" required>
    				</div>
    			</div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit" id="submitExtend">Submit</button>
            </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- ./wrapper -->

<script>
$(function () {
    $("#memberTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':'php/loadMembers.php'
        },
        'columns': [
            { data: 'name' },
            { data: 'email' },
            { data: 'role_name' },
            { data: 'joined_date' },
            { data: 'expired_date' },
            { data: 'status' },
            { 
                data: 'id',
                render: function ( data, type, row ) {
                    return '<div class="row"><div class="col-3"><button type="button" id="activate'+data+'" onclick="activate('+data+')" class="btn btn-success btn-sm"><i class="fas fa-play"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-stop"></i></button></div><div class="col-3"><button type="button" id="addKey'+data+'" onclick="addKey('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-plus"></i></button></div><div class="col-3"><button type="button" id="assignRole'+data+'" onclick="assignRole('+data+')" class="btn btn-primary btn-sm"><i class="fas fa-user"></i></button></div><div class="col-3"><button type="button" id="extendMembership'+data+'" onclick="extendMembership('+data+')" class="btn btn-warning btn-sm"><i class="fas fa-calendar"></i></button></div><div class="col-3"><button type="button" id="viewKey'+data+'" onclick="viewKey('+data+')" class="btn btn-info btn-sm"><i class="fas fa-key"></i></button></div></div>';
                }
            }
        ]
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            if($('#assignModal').hasClass('show')){
                $.post('php/updateRole.php', $('#assignForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    
                    if(obj.status === 'success'){
                        $('#assignModal').modal('hide');
                        toastr["success"](obj.message, "Success:");
                        
            			$.get('members.php', function(data) {
                            $('#mainContents').html(data);
                        });
            		}
            		else if(obj.status === 'failed'){
                        toastr["error"](obj.message, "Failed:");
                    }
            		else{
            			alert("Something wrong when edit");
            		}
                });
            }
            else if($('#extendModal').hasClass('show')){
                $.post('php/extendMember.php', $('#extendForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    
                    if(obj.status === 'success'){
                        $('#extendModal').modal('hide');
                        toastr["success"](obj.message, "Success:");
                        
            			$.get('members.php', function(data) {
                            $('#mainContents').html(data);
                        });
            		}
            		else if(obj.status === 'failed'){
                        toastr["error"](obj.message, "Failed:");
                    }
            		else{
            			alert("Something wrong when edit");
            		}
                });
            }
        }
    });
});

function activate(id){
    $.post('php/updateStatus.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
		if(obj.status === 'success'){
			toastr["success"](obj.message, "Success:");
			$.get('members.php', function(data) {
                $('#mainContents').html(data);
            });
		}
		else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
        }
		else{
		    toastr["error"]("Something wrong when activate", "Failed:");
		}
    });
}

function deactivate(id){
    $.post('php/deactivateUser.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
		if(obj.status === 'success'){
			toastr["success"](obj.message, "Success:");
			$.get('members.php', function(data) {
                $('#mainContents').html(data);
            });
		}
		else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
        }
		else{
		    toastr["error"]("Something wrong when activate", "Failed:");
		}
    });
}

function addKey(id){
    $.post('php/updateKey.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
		if(obj.status === 'success'){
			toastr["success"](obj.message, "Success:");
			$.get('members.php', function(data) {
                $('#mainContents').html(data);
            });
		}
		else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
        }
		else{
		    toastr["error"]("Something wrong when activate", "Failed:");
		}
    });
}

function assignRole(id){
    $('#assignModal').find('#ID').val(id);
    $('#assignModal').modal('show');
    
    $('#assignForm').validate({
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

function extendMembership(id){
    $('#extendModal').find('#numDays').val('');
    $('#extendModal').find('#ID').val(id);
    $('#extendModal').modal('show');
    
    $('#extendForm').validate({
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

function viewKey(id){
    $.post('php/getKeys.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
		if(obj.status === 'success'){
		    $('#modelkeys').html('');
		    
		    for(var i=0; i<obj.message.length; i++){
		        var allkey = '<tr><td>'+obj.message[i].key+'</td><td>'+obj.message[i].lastUpdate+'</td></tr>';
		        $('#modelkeys').append(allkey);
		    }
		    $('#keyModal').modal('show'); 
		}
		else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
        }
		else{
		    toastr["error"]("Something wrong when retrive data", "Failed:");
		}
    });
}
</script>
</body>
</html>
