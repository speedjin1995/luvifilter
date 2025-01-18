<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

session_start();
//$renewed = $_SESSION['renewed'];

if(!isset($_SESSION['userDetail'])){
    echo '<script type="text/javascript">';
	echo 'window.location.href = "../login.html";</script>';
}
/*else if($renewed == "true"){
    echo '<script type="text/javascript">';
	echo 'window.location.href = "https://sneakercube.io/";</script>';
}*/
else{
    // Set back user detail
    $user = $_SESSION['userDetail'];
    $userID = $user->getId();
    $expiredDate = $user->getExpiredDate();
    $role = $user->getRole();
    $todayDate = date("Y-m-d");
    
    if($expiredDate == null || $expiredDate < $todayDate){
        $expiredDate = date("Y-m-d");
    }
    
    $status = "active";
    $key_flag = "Y";
    //$renewed = "true";
    
    $stmt2 = $db->prepare("SELECT * FROM roles WHERE role_code = ?");
    $stmt2->bind_param('s', $role);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    
    if($row = $result2->fetch_assoc()){
        if($row['number_of_days'] != null && $row['number_of_days'] != ''){
            $newExpiredDate = date('Y-m-d', strtotime($expiredDate. '+ '.$row['number_of_days'].' days'));
        }
        else{
            $newExpiredDate = date('Y-m-d', strtotime($expiredDate. '+ 1 month'));
        }
        
        if($row['change_to'] != null && $row['change_to'] != ''){
            $role = $row['change_to'] ;
        }
    }
    
    /*if($role == 'New_Cuber'){
        $newExpiredDate = date('Y-m-d', strtotime($expiredDate. '+ 3 months'));
        $role = 'TR1';
    }
    else if($role == 'New_Cuber_1'){
        $newExpiredDate = date('Y-m-d', strtotime($expiredDate. '+ 6 months'));
        $role = 'TR2';
    }
    else if($role == 'New_Cuber_2'){
        $newExpiredDate = date('Y-m-d', strtotime($expiredDate. '+ 3 months'));
        $role = 'TR10';
    }
    else if($role == 'New_Cuber_3'){
        $newExpiredDate = date('Y-m-d', strtotime($expiredDate. '+ 6 months'));
        $role = 'TR11';
    }
    else if($role == 'New_Cuber_4'){
        $newExpiredDate = date('Y-m-d', strtotime($expiredDate. '+ 3 months'));
        $role = 'TR12';
    }
    else if($role == 'New_Cuber_5'){
        $newExpiredDate = date('Y-m-d', strtotime($expiredDate. '+ 6 months'));
        $role = 'TR13';
    }
    else{
        $newExpiredDate = date('Y-m-d', strtotime($expiredDate. '+ 1 month'));
    }*/
	
	if ($stmt2 = $db->prepare("UPDATE users SET expired_date=?, status=?, role_code=?, key_flag=? WHERE id=?")) {
    	$stmt2->bind_param('sssss', $newExpiredDate, $status, $role, $key_flag, $userID);
    	
    	if($stmt2->execute()){
    		$stmt2->close();
    		$db->close();
    		$user->setExpiredDate($newExpiredDate);
    		$user->setStatus($status);
    		$user->setKeyFlag($key_flag);
    		$_SESSION['userDetail'] = $user;
    		//$_SESSION['renewed'] = $renewed;
    	}
    	else{
    	    $newExpiredDate = $expiredDate;
    	}
    }
    else{
        $newExpiredDate = $expiredDate;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SneakerCube | Successful Renewal</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" href="images/favicon.png" type="image"/>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,700;1,400&display=swap" rel="stylesheet">
</head>

<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <img src="images/logo-white.png" alt="Sneakercube Logo" class="brand-image" style="width:100%">
  </div>
  <!-- /.login-logo -->
  <div class="card" style="background-color: transparent; box-shadow: none;">
    <div class="card-body login-card-body" style="background: transparent">
      <p class="login-box-msg" style="font-size:1.25rem; color:#ffffff">Thanks for renewing! Your account is now active until</p>
      <p class="login-box-msg" style="font-size:2rem; color:#ffffff"><?=$newExpiredDate ?></p>
        <div class="row" style="padding-bottom: 1.5rem;">
          <div class="col-12">
            <a href="https://sneakercube.io/"><button type="submit" class="btn btn-primary btn-block" style="border-color:#ffffff">Return to Dashboard</button></a>
          </div>
          <!-- /.col -->
        </div>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>

</body>
</html>
