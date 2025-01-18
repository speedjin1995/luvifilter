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

if(isset($_POST['productId'])){
	$id = filter_input(INPUT_POST, 'productId', FILTER_SANITIZE_STRING);
	
	if ($stmt = $db->prepare("SELECT details FROM shoes WHERE product_id = ?")) {
		$stmt->bind_param('s', $id);
		
		if($stmt->execute()){
			$result = $stmt->get_result();
            
            if($row = $result->fetch_assoc()){
                if($row['details'] != null && $row['details'] != ''){
                    $url = 'https://www.nike.com'.str_replace("&amp;","&",$row['details']);
                    //$contents = file_get_contents($url);
                    
                    
                    $ch = curl_init();                              // create curl resource
                    curl_setopt($ch, CURLOPT_URL, $url);            // set url
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //return the transfer as a string
                    $contents = curl_exec($ch);                     // $output contains the output string
                    curl_close($ch);                                // close curl resource to free up system resources
 
                    if($contents != ''){
                        $all = json_decode($contents);
                        echo $contents;
                        /*echo json_encode(
                	        array(
                	            "status"=> "success", 
                	            "message"=> $all->publishedContent->nodes[0]->nodes[0]->properties->squarishURL
                	        )
                	    );*/
                    }
                    else{
                        echo $url;
                        echo json_encode(
                	        array(
                	            "status"=> "failed", 
                	            "message"=> "failed to open stream"
                	        )
                	    );
                    }
                }
                else{
                    echo json_encode(
            	        array(
            	            "status"=> "failed", 
            	            "message"=> "Failed to get details"
            	        )
            	    );
                }
            }
		} 
		else{
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
