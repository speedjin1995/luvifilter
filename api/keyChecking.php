<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

if(isset($_POST['key'])){
    $key = $_POST['key'];
    
    $stmt = $db->prepare("SELECT tbl_keys.id, users.status FROM tbl_keys, users WHERE key_value = ? AND users.id = tbl_keys.user_id AND users.status = 'active'");
	$stmt->bind_param('s', $key);
	$stmt->execute();
	$stmt->store_result();
	
	if ($stmt->num_rows > 0){
	    echo json_encode(
	        array(
	            "status"=> "success"
	        )
	    );
	}
	else{
	    echo json_encode(
	        array(
	            "status"=> "failed"
	        )
	    );
	}
}
else{
    echo json_encode(
        array(
            "status"=> "failed"
        )
    );
}
?>