<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
} 
else{
	$user = $_SESSION['userDetail'];
	$id = $user->getId();
}

if(isset($_POST['time'])){
	$time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING);

	if ($stmt = $db->prepare("UPDATE users SET default_time=? WHERE id=?")) {
		$stmt->bind_param('ss', $time, $id);
		
		if($stmt->execute()){
			$user->setDefaultTime($time);
			$_SESSION['userDetail']=$user;
			$stmt->close();
			$db->close();
			
			echo json_encode(
		        array(
		            "status"=> "success", 
		            "message"=> "Your Default Time is Saved Successfully!!" 
		        )
		    );
		} else{
		    echo json_encode(
		        array(
		            "status"=> "failed", 
		            "message"=> $stmt->error
		        )
		    );
		}
	} 
	else{
		echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Something went wrong!"
	        )
	    );
	}
} 
else{
	echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please enter your default time"
        )
    ); 
}
?>
