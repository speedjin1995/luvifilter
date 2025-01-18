<?php
require_once '../php/includes/db_connect.php';

$userId = '1';    // Change here for user id to create
$keyToCreate = 1; // Change here for number of key to create
$keyCount = 0;

while ($keyCount < $keyToCreate){
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
    	if (!$insert_stmt->execute()){
    	    echo json_encode(array("status"=> "failed", "message"=> $insert_stmt->error));
    	    break;
    	} 
    	else{
            echo json_encode(array("status"=> "success", "message"=> $key));
    	    $keyCount++;
    	}
    } 
    else{
    	echo json_encode(array("status"=> "failed", "message"=> "Failed to create new key"));
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