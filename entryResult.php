<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
    echo '<script type="text/javascript">';
	echo 'window.location.href = "../login.html";</script>';
}
else{
    $stmt = $db->prepare("SELECT * from entries");
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Entry Results</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">Entry Result</li>
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
						<table id="entryTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Key</th>
									<th>Email</th>
									<th>Shoes Model</th>
									<th>Entry Date</th>
									<th>Creation Date</th>
									<th>Status</th>
									<th>Reason</th>
								</tr>
							</thead>
							<tbody id="keys">
								<?php while($row = $result->fetch_assoc()){ ?>
									<tr class="entryDetail" data-index="<?= $row['id'] ?>">
										<td><?= $row['keyValue'] ?></td>
										<td><?= $row['email'] ?></td>
										<td><?= $row['model'] ?></td>
										<td><?php 
											if($row['entryDate'] != null && $row['entryDate'] != ""){
												echo date("d-m-Y", strtotime($row['entryDate']));
											}
											else{
												echo '-';
											}
										?></td>
										<td><?php 
											if($row['createdAt'] != null && $row['createdAt'] != ""){
												echo date("d-m-Y", strtotime($row['createdAt']));
											}
											else{
												echo '-';
											}
										?></td>
										<td><?= $row['status'] ?></td>
										<td><?= $row['reason'] ?></td>
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
$(function () {
    $("#entryTable").DataTable({
        "responsive": true,
        "autoWidth": false,
    });
});
</script>
</body>
</html>
