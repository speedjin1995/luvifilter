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

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	
	if ($stmt = $db->prepare("SELECT * FROM tbl_keys WHERE user_id=?")) {
		$stmt->bind_param('s', $id);
		
		if($stmt->execute()){
			$result = $stmt->get_result();
            $keys = array();
            
            while($row = $result->fetch_assoc()){
                $keys[] = array(
            		'key' => $row['key_value'],
            		'lastUpdate' => $row['last_update']
            	); 
            }
			
			echo json_encode(
    	        array(
    	            "status"=> "success", 
    	            "message"=> $keys
    	        )
    	    );
		} 
		else{
		    echo json_encode(
    	        array(
    	            "status"=> "success", 
    	            "message"=> $stmt->error
    	        )
    	    );
		}
	} 
	else{
	    echo json_encode(
	        array(
	            "status"=> "success", 
	            "message"=> "Somthings wrong"
	        )
	    );
	}
} 
else{
    echo json_encode(
        array(
            "status"=> "success", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}
?>
