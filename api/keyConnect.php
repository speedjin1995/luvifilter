<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

if(isset($_POST['key'], $_POST['device'], $_POST['hostname'])){
    $key = $_POST['key'];
    $device_in = $_POST['device'];
    $hostname_in = $_POST['hostname'];
    $lastDate = date("Y-m-d H:i:s");
    
    $stmt = $db->prepare("SELECT tbl_keys.id, tbl_keys.device, tbl_keys.hostname, users.status FROM tbl_keys, users WHERE key_value = ? AND users.id = tbl_keys.user_id");
	$stmt->bind_param('s', $key);
	$stmt->execute();
	$stmt->store_result();
	
	if ($stmt->num_rows > 0){
	    $stmt->bind_result($keyid, $device, $hostname, $userStatus);
	    $stmt->fetch();
	    
	    if($device != null && $device != "" && $hostname!=$hostname_in){
	        echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> "Key has been logged in at ".$device
    	        )
    	    );
	    }
	    else if($userStatus == "inactive"){
	        echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> "This key has been deactivated"
    	        )
    	    );
	    }
	    else{
	        if ($stmt2 = $db->prepare("UPDATE tbl_keys SET device=?, hostname=?, last_update=? WHERE key_value=?")) {
    			$stmt2->bind_param('ssss', $device_in, $hostname_in, $lastDate, $key);
    			
    			if($stmt2->execute()){
    				$stmt2->close();
    				$db->close();
    				
    				echo json_encode(
            	        array(
            	            "status"=> "success", 
            	            "message"=> "ok"
            	        )
            	    );
    			} 
    			else{
    			    echo json_encode(
            	        array(
            	            "status"=> "failed", 
            	            "message"=> $stmt2->error
            	        )
            	    );
    			}
    		}
	    }
	}
	else{
	    echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Key not match"
	        )
	    );
	}
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Failed to validate key"
        )
    );
}
?>