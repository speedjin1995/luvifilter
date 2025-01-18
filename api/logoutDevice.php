<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

if(isset($_POST['key'])){
	$key = $_POST['key'];
	$stmt = $db->prepare("UPDATE tbl_keys SET device = null, hostname = null, last_update = null WHERE key_value = ?");
	$stmt->bind_param('s', $key);
	
	if($stmt->execute()){
		echo json_encode(
	        array(
	            "status"=> "success", 
	            "message"=> "Logged Out"
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
            "message"=> "Failed to get key"
        )
    );
}
?>