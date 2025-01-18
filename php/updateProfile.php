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

if(isset($_POST['userName'], $_POST['userEmail'])){
	$name = filter_input(INPUT_POST, 'userName', FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'userEmail', FILTER_SANITIZE_EMAIL);
	$email = filter_var($email, FILTER_VALIDATE_EMAIL);
	
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		echo '<script type="text/javascript">alert("Please enter a valid email address");';
		echo 'location.href = "../myProfile.php";</script>';
	} 
	else{
		if ($stmt2 = $db->prepare("UPDATE users SET name=?, email=? WHERE id=?")) {
			$stmt2->bind_param('sss', $name, $email, $id);
			
			if($stmt2->execute()){
				$user->setName($name);
				$user->setEmail($email);
				$_SESSION['userDetail']=$user;
				$stmt2->close();
				$db->close();
				
				echo json_encode(
    		        array(
    		            "status"=> "success", 
    		            "message"=> "Your Name / Email is updated successfully!" 
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
} 
else{
	echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all fields"
        )
    ); 
}
?>
