<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
}

if(isset($_POST['roleName'], $_POST['numkeys'], $_POST['renewalFees'], $_POST['ID'], $_POST['numberofDays'], $_POST['changedRole'])){
	$roleName = filter_input(INPUT_POST, 'roleName', FILTER_SANITIZE_STRING);
	$numkeys = filter_input(INPUT_POST, 'numkeys', FILTER_SANITIZE_STRING);
	$renewalFees = filter_input(INPUT_POST, 'renewalFees', FILTER_SANITIZE_STRING);
	$numberofDays = filter_input(INPUT_POST, 'numberofDays', FILTER_SANITIZE_STRING);
	$changedRole = filter_input(INPUT_POST, 'changedRole', FILTER_SANITIZE_STRING);
	$id = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING);

	if ($stmt = $db->prepare("UPDATE roles SET role_name=?, max_key=?, renewal_fee=?, number_of_days=?, change_to=? WHERE id=?")) {
		$stmt->bind_param('ssssss', $roleName, $numkeys, $renewalFees, $numberofDays, $changedRole, $id);
		
		if($stmt->execute()){
			echo json_encode(
		        array(
		            "status"=> "success", 
		            "message"=> "Role Updated!" 
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
	            "message"=> "Something went wrong!"
	        )
	    );
	}
} 
else{
	echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all fields"
        )
    ); 
}
?>
