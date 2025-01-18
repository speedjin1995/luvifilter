<?php
require_once 'includes/db_connect.php';
//require_once 'includes/users.php';

session_start();

/*(!isset($_SESSION['userDetail'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
} else{
	$user = $_SESSION['userDetail'];
	$role = $user->getId();
}*/

if(isset($_POST['userEmail'], $_POST['userPassword'], $_POST['userName'])){
    $email = filter_input(INPUT_POST, 'userEmail', FILTER_SANITIZE_EMAIL);
	$email = filter_var($email, FILTER_VALIDATE_EMAIL);
	$password = filter_input(INPUT_POST, 'userPassword', FILTER_SANITIZE_STRING);
	$name = filter_input(INPUT_POST, 'userName', FILTER_SANITIZE_STRING);
	$role = "USER";
	
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
		echo '<script type="text/javascript">alert("Please enter a valid email address");';
		echo 'location.href = "../register.html";</script>';  
	} else{
		if ($stmt = $db->prepare("SELECT id FROM users WHERE email = ?")){
			$stmt->bind_param('s', $email);
			$stmt->execute();
			$stmt->store_result();
			
			if ($stmt->num_rows > 0){
				echo '<script type="text/javascript">alert("Email address already exist");';
				echo 'location.href = "../register.html";</script>';
			} else{
				$random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
				$password = hash('sha512', $password . $random_salt);
				
				if ($insert_stmt = $db->prepare("INSERT INTO users (name, email, password, salt, role_code) VALUES (?, ?, ?, ?, ?)")){
					$insert_stmt->bind_param('sssss', $name, $email, $password, $random_salt, $role);
					
					// Execute the prepared query.
					if (! $insert_stmt->execute()){
						echo '<script type="text/javascript">alert("'.$insert_stmt->error.'");';
						echo 'location.href = "../register.html";</script>';
					} else{
						echo '<script type="text/javascript">alert("You registered successfully");';
						echo 'location.href = "../login.html";</script>';
					}
				} else{
					echo '<script type="text/javascript">alert("Something wrong with the server");';
					echo 'location.href = "../register.html";</script>';
				}
			}
		} else{
			echo '<script type="text/javascript">alert("Database Error");';
			echo 'location.href = "../register.html";</script>';
		}
	}
} else{
	echo '<script type="text/javascript">alert("Please fill in all the fields");';
	echo 'location.href = "../register.html";</script>';       
}