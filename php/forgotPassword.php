<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

if(isset($_POST['userEmail'])){
	$email = filter_input(INPUT_POST, 'userEmail', FILTER_SANITIZE_EMAIL);

    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        $stmt = $db->prepare("SELECT * from users where email= ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if(($row = $result->fetch_assoc()) !== null){   
            $newPassword = generateKey(6);
            $random_salt = $row['salt'];
            $password = hash('sha512', $newPassword . $random_salt);
            $userId = $row['id'];
    
            if ($stmt2 = $db->prepare("UPDATE users SET password=? WHERE id=?")) {
    			$stmt2->bind_param('ss', $password, $userId);
    			
    			if($stmt2->execute()){
    			    $to      = $email;
                    $subject = 'Reset Passowrd';
                    $message = 'Your temporary password is '.$newPassword;
                    $headers = 'From: <support@sneakercube.io>' . "\r\n";
                    $headers .= 'Cc: sneakercube123@gmail.com' . "\r\n";
                    
                    mail($to, $subject, $message, $headers);
                    
                    echo '<script type="text/javascript">alert("A Temporary Password is Sent to '.$email.'");';
				    echo 'location.href = "../login.html";</script>';
    			}
    			else{
    			    echo '<script type="text/javascript">alert("'.$stmt2->error.'");';
				    echo 'location.href = "../forgotPassword.html";</script>';
    			}
            }
        }
        else{
            echo '<script type="text/javascript">alert("Email is Wrong");';
		    echo 'location.href = "../forgotPassword.html";</script>';
        }
    }
    else{
        echo '<script type="text/javascript">alert("Please Enter a valid email");';
		echo 'location.href = "../forgotPassword.html";</script>';
    }
}
else{
    echo '<script type="text/javascript">alert("Please Key in all fields");';
	echo 'location.href = "../forgotPassword.html";</script>';
}

function getRandomBytes($nbBytes = 16){
    $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);
    if (false !== $bytes && true === $strong) {
        return $bytes;
    }
    else {
        throw new \Exception("Unable to generate secure token from OpenSSL.");
    }
}

function generateKey($length){
    return substr(preg_replace("/[^a-zA-Z0-9]/", "", base64_encode(getRandomBytes($length+1))),0,$length);
}
?>