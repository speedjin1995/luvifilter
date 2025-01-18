<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
}

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$status = 'active';
	
	$stmt = $db->prepare("SELECT joined_date from users WHERE id=?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if(($row = $result->fetch_assoc()) !== null){
        if($row['joined_date'] != null){
            if ($stmt2 = $db->prepare("UPDATE users SET status=? WHERE id=?")) {
        		$stmt2->bind_param('ss', $status, $id);
        		
        		if($stmt2->execute()){
        			$stmt2->close();
        			$db->close();
        			
        			echo json_encode(
            	        array(
            	            "status"=> "success", 
            	            "message"=> "Activated"
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
            $date = new DateTime();
        	$joinedDate = $date->format('Y-m-d H:i:s');
        	
        	if ($stmt2 = $db->prepare("UPDATE users SET status=?, joined_date=?, expired_date = null WHERE id=?")) {
        		$stmt2->bind_param('sss', $status, $joinedDate, $id);
        		
        		if($stmt2->execute()){
        			$stmt2->close();
        			$db->close();
        			
        			echo json_encode(
            	        array(
            	            "status"=> "success", 
            	            "message"=> "Activated"
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
    }
	else{
	    echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> "User not found!!!"
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
