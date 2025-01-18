<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
}

if(isset($_POST['userID'])){
	$id = $_POST['userID'];
	$key;
	$stmt2 = $db->prepare("SELECT key_value from tbl_keys WHERE id = ?");
	$stmt2->bind_param('s', $id);
	$stmt2->execute();
	$result = $stmt2->get_result();

    if(($row = $result->fetch_assoc()) !== null){
        $key = $row['key_value'];
    }
	
	$stmt = $db->prepare("DELETE from tbl_keys WHERE id = ?");
	$stmt->bind_param('s', $id);
	
	if($stmt->execute()){
		echo json_encode(
	        array(
	            "status"=> "success", 
	            "message"=> "Key ". $key ." is removed successfully!"
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
} else{
	echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Failed to get key"
        )
    );
}
?>