<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
}

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$status = 'Y';
	
	if ($stmt2 = $db->prepare("UPDATE users SET key_flag=? WHERE id=?")) {
		$stmt2->bind_param('ss', $status, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();
			
			echo json_encode(
    	        array(
    	            "status"=> "success", 
    	            "message"=> "Enabled Key Flag"
    	        )
    	    );
		} else{
		    echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> $stmt2->error
    	        )
    	    );
		}
	} 
	else{
	    echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Somthings wrong"
	        )
	    );
	}
} 
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>
