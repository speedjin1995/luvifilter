<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
} 

if(isset($_POST['ID'], $_POST['numDays'])){
	$numDays = filter_input(INPUT_POST, 'numDays', FILTER_SANITIZE_STRING);
	$ID = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING);
	
	$stmt = $db->prepare("SELECT * FROM users WHERE id=?");
	$stmt->bind_param('s', $ID);
	$stmt->execute();
	$result = $stmt->get_result();
	$expiredDate;

    if($row = $result->fetch_assoc()){
        $expiredDate = date('Y-m-d', strtotime($row['expired_date']. ' + '.$numDays.' days'));
    }
	
	if ($stmt2 = $db->prepare("UPDATE users SET expired_date=? WHERE id=?")) {
		$stmt2->bind_param('ss', $expiredDate, $ID);
		
		if($stmt2->execute()){
		    $stmt2->close();
			$db->close();
		    
			echo json_encode(
		        array(
		            "status"=> "success", 
		            "message"=> "Added ".$numDays." days!" 
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
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>
