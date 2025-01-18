<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
} 
else{
	$user = $_SESSION['userDetail'];
	$userID = $user->getId();
}

if(isset($_POST['newExpiredDate'])){
	$newExpiredDate = filter_input(INPUT_POST, 'newExpiredDate', FILTER_SANITIZE_STRING);
	$status = "active";
    $key_flag = "Y";
	
	if ($stmt2 = $db->prepare("UPDATE users SET expired_date=?, status=?, key_flag=? WHERE id=?")) {
    	$stmt2->bind_param('ssss', $newExpiredDate, $status, $key_flag, $userID);
    	
    	if($stmt2->execute()){
    		$stmt2->close();
    		$db->close();
    		$user->setExpiredDate($newExpiredDate);
    		$user->setStatus($status);
    		$user->setKeyFlag($key_flag);
    		$_SESSION['userDetail'] = $user;
    		
    		echo '<script type="text/javascript">';
	        echo 'window.location.href = "https://sneakercube.io/";</script>';
    	}
    	else{
    	    echo '<script type="text/javascript">';
	        echo 'window.location.href = "https://sneakercube.io/";</script>';
    	}
    }
    else{
        echo '<script type="text/javascript">';
	    echo 'window.location.href = "https://sneakercube.io/";</script>';
    }
}