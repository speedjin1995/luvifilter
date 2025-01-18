<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

session_start();

$username=$_POST['userEmail'];
$password=$_POST['userPassword'];
$now = date("Y-m-d H:i:s");

$stmt = $db->prepare("SELECT * from users where email= ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if(($row = $result->fetch_assoc()) !== null){
	if($row['status']=="active") {
		$password = hash('sha512', $password . $row['salt']);
		
		if($password == $row['password']){
		    if($row['expired_date'] != null){
		        $user = new user();
    			$user->setId($row['id']);
    			$user->setName($row['name']);
    			$user->setEmail($row['email']);
    			$user->setRole($row['role_code']);
    			$user->setStatus($row['status']);
    			$user->setExpiredDate($row['expired_date']);
    			$user->setKeyFlag($row['key_flag']);
    			$user->setDefaultTime($row['default_time']);
    			$_SESSION['userDetail']=$user;
    			
    			$stmt->close();
    			$db->close();
    			
    			echo '<script type="text/javascript">';
    			echo 'window.location.href = "https://sneakercube.io/";</script>';
		    }
		    else{ // New User
		        $new_time = date("Y-m-d H:i:s", strtotime('+24 hours', strtotime($row['joined_date'])));
		    
    		    if($new_time > $now){
    		        $user = new user();
        			$user->setId($row['id']);
        			$user->setName($row['name']);
        			$user->setEmail($row['email']);
        			$user->setRole($row['role_code']);
        			$user->setStatus($row['status']);
        			$user->setExpiredDate($row['expired_date']);
        			$user->setKeyFlag($row['key_flag']);
        			$user->setDefaultTime($row['default_time']);
        			$_SESSION['userDetail']=$user;
        			
        			$stmt->close();
        			$db->close();
        			
        			echo '<script type="text/javascript">';
        			echo 'window.location.href = "https://sneakercube.io/";</script>';
    		    }
    		    else{
    		        $status = 'inactive';
    		        $id = $row['id'];
    		        
    		        $stmt2 = $db->prepare("UPDATE users SET status=? WHERE id=?");
            		$stmt2->bind_param('ss', $status, $id);
            		$stmt2->execute();
    		        
    		        echo '<script type="text/javascript">alert("User account is not active");';
    			    echo 'window.location.href = "../login.html";</script>';
    		    }
		    }
		} 
		else{
			echo '<script type="text/javascript">alert("Login unsuccessful, password or username is not matched");';
			echo 'window.location.href = "../login.html";</script>';
		}
	} 
	else{
	    $new_time = date("Y-m-d H:i:s", strtotime('+72 hours', strtotime($row['expired_date'])));
	    
	    if($new_time > $now){
	        $user = new user();
			$user->setId($row['id']);
			$user->setName($row['name']);
			$user->setEmail($row['email']);
			$user->setRole($row['role_code']);
			$user->setStatus($row['status']);
			$user->setExpiredDate($row['expired_date']);
			$user->setKeyFlag('Y');
			$user->setDefaultTime($row['default_time']);
			$_SESSION['userDetail']=$user;
			
			$stmt->close();
			$db->close();
			
			echo '<script type="text/javascript">';
			echo 'window.location.href = "https://sneakercube.io/";</script>';
	    }
	    else{
	        echo '<script type="text/javascript">alert("User account is not active");';
		    echo 'window.location.href = "../login.html";</script>';
	    }
	}
} 
else{
	 echo '<script type="text/javascript">alert("Login unsuccessful, password or username is not matched");';
	 echo 'window.location.href = "../login.html";</script>';
}
?>
