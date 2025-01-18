<?php
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
    echo '<script type="text/javascript">';
	echo 'window.location.href = "../login.html";</script>';
}
else{
    $user = $_SESSION['userDetail'];
    $userId = $user->getId();
    $role = $user->getRole();
    $keyFlag = $user->getKeyFlag();
    
    if($keyFlag == 'Y'){
        $maxKey = 1;
        $stmt3 = $db->prepare("SELECT max_key FROM roles WHERE role_code = ?");
        $stmt3->bind_param('s', $role);
    	$stmt3->execute();
    	$result3 = $stmt3->get_result();
    
        if($row3 = $result3->fetch_assoc()){
            $maxKey = $row3['max_key'];
        }
        
        if ($stmt = $db->prepare("SELECT id FROM tbl_keys WHERE user_id = ?")){
    		$stmt->bind_param('s', $userId);
    		$stmt->execute();
    		$stmt->store_result();
    		
    		if ($stmt->num_rows >= $maxKey){
    			echo json_encode(
    		        array(
    		            "status"=> "failed", 
    		            "message"=> "Maximum key per user is ".$maxKey." keys" 
    		        )
    		    );
    		}
    		else{
    		    $key = generateKey(6);
    		    $key = 'Cube'.$key;
    		    $existKey = true;
    		    
    		    while ($existKey){
        		    $stmt2 = $db->prepare("SELECT id FROM tbl_keys WHERE key_value = ?");
            		$stmt2->bind_param('s', $key);
            		$stmt2->execute();
            		$stmt2->store_result();
            		
            		if ($stmt2->num_rows > 0){
            		    $key = generateKey(6);
            		    $key = 'Cube'.$key;
            		}
            		else{
            		    $existKey= false;
            		}
        		}
        		
        		if ($insert_stmt = $db->prepare("INSERT INTO tbl_keys (user_id, key_value) VALUES (?, ?)")){
    				$insert_stmt->bind_param('ss', $userId, $key);
    				
    				// Execute the prepared query.
    				if (! $insert_stmt->execute()){
    				    echo json_encode(
            		        array(
            		            "status"=> "failed", 
            		            "message"=> $insert_stmt->error
            		        )
            		    );
    				} else{
    				    $stmt3 = $db->prepare("SELECT id FROM tbl_keys WHERE key_value = ?");
                		$stmt3->bind_param('s', $key);
                		$stmt3->execute();
                		$result = $stmt3->get_result();
                		$keyID;
    
                        if(($row2 = $result->fetch_assoc()) !== null){
                            $keyID = $row2['id'];
                        }
    				    
    					echo json_encode(
                	        array(
                	            "status"=> "success", 
                	            "message"=> $key,
                	            "id" => $keyID
                	        )
                	    );
    				}
    			} else{
    				echo json_encode(
            	        array(
            	            "status"=> "failed", 
            	            "message"=> "Failed to create new key" 
            	        )
            	    );
    			}
    		}
        }
        else{
            echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> "Failed to create new key" 
    	        )
    	    );
        }
    }
    else{
        echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Please Renew to add new key" .$keyFlag
	        )
	    );
    }
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