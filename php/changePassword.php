<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
} else{
	$user = $_SESSION['userDetail'];
	$id = $user->getId();
}

if(isset($_POST['oldPassword'], $_POST['newPassword'], $_POST['confirmPassword'])){
	$oldPassword = filter_input(INPUT_POST, 'oldPassword', FILTER_SANITIZE_STRING);
	$newPassword = filter_input(INPUT_POST, 'newPassword', FILTER_SANITIZE_STRING);
	$confirmPassword = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_STRING);
	
	$stmt = $db->prepare("SELECT * from users where ID = ?");
	$stmt->bind_param('s', $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	if(($row = $result->fetch_assoc()) !== null){
		$oldPassword = hash('sha512', $oldPassword . $row['salt']);
		
		if($oldPassword == $row['password']){
			$password = hash('sha512', $newPassword . $row['salt']);
			$stmt2 = $db->prepare("UPDATE users SET password = ? WHERE ID = ?");
			$stmt2->bind_param('ss', $password, $id);
			
			if($stmt2->execute()){
    			$stmt2->close();
    			$db->close();
    			
    			echo json_encode(
        	        array(
        	            "status"=> "success", 
        	            "message"=> "Update successfully"
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
		} else{
		    echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> "Old password is not matched"
    	        )
    	    );
		}
	} else{
	     echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Data retrieve failed"
	        )
	    );
	}
} else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>
