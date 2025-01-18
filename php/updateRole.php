<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
} 

if(isset($_POST['ID'], $_POST['userRole'])){
	$userRole = filter_input(INPUT_POST, 'userRole', FILTER_SANITIZE_STRING);
	$ID = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING);
	
	if ($stmt2 = $db->prepare("UPDATE users SET role_code=? WHERE id=?")) {
		$stmt2->bind_param('ss', $userRole, $ID);
		
		if($stmt2->execute()){
		    $stmt2->close();
			$db->close();
		    
			echo json_encode(
		        array(
		            "status"=> "success", 
		            "message"=> "Role Changed!"
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
