<?php
define('PATH', dirname(__FILE__));
require_once(PATH . "/../php/includes/db_connect.php");

//The URL that we want to GET.
$url = 'https://api.nike.com/product_feed/threads/v2/?anchor=0&count=36&filter=marketplace%28MY%29&filter=language%28en-GB%29&filter=upcoming%28true%29&filter=channelId%28010794e5-35fe-4e32-aaff-cd2c74f89d61%29&sort=effectiveStartSellDateAsc';
$webhookurl = "https://discord.com/api/webhooks/795847943197687828/E_lFvWIZV-fOlHc2uXJjBcmtMCwgAp8uq3aVN87PFBdjqw0Qc28z9ROq-LSxJQYRDnbJ";
$timestamp = date("c", strtotime("now"));

// START: Pull product from Nike
$contents = file_get_contents($url);
 
if($contents !== false){
    $all = json_decode($contents);
    $products = array();
    $productData = array();
    $productDetails = '';
    
    foreach ($all->objects as $object) {
        $productDetails = $object->publishedContent->properties->seo->slug;
        
        foreach ($object->productInfo as $product) {
            $fields = array();
            $fields2 = array();
            $productFound = false;
            
            if($product->launchView->productId != null && $product->launchView->productId != ''){
                $select_stmt = $db->prepare("SELECT id FROM shoes WHERE product_id = ?");
                $select_stmt->bind_param('s', $product->launchView->productId);
                $select_stmt->execute();
    		    $select_stmt->store_result();
    		    
    		    if ($select_stmt->num_rows > 0){
                    $productFound = true;
    		    }
    		    
		        for($i=0; $i<count($product->skus); $i++){
                    $field = array(
                        "name" => $product->skus[$i]->nikeSize,
                        "value" => $product->availableSkus[$i]->level,
                        "inline" => true
                    );
                    
                    $field2 = array(
                        "skuId" => $product->skus[$i]->id,
                        "size" => $product->skus[$i]->nikeSize,
                        "level" => $product->availableSkus[$i]->level
                    );
                    
                    array_push($fields, $field);
                    array_push($fields2, $field2);
                }
    		    
    		    $launchdate = strtotime($product->launchView->startEntryDate);
                $newlaunchdate = date("Y-m-d H:i:sa", $launchdate);
                
                $embed = array(
                    "title" => $product->productContent->title,
                    "type" => "rich",
                    "description" => 'Price: '.$product->merchPrice->currency.' '.$product->merchPrice->currentPrice.PHP_EOL.'Launch: '.$newlaunchdate,
                    "url" => 'https://www.nike.com/my/launch/t/'.$productDetails,
                    "timestamp" => $timestamp,
                    "color" => hexdec( "3366ff" ),
                     "footer" => [
                        "text" => "Sneakercube.io",
                        "icon_url" => "https://sneakercube.io/images/logo-cube.png"
                    ],
                    "image" => [
                        "url" => $product->imageUrls->productImageUrl
                    ],
                    "author" => [
                        "name" => "New Launch Detected",
                        "url" => ""
                    ],
                    "fields" => $fields
                );
                
                array_push($products, $embed);
                
                $productDetail = array(
                    "productId" => $product->launchView->productId,
                    "productName" => $product->productContent->title,
                    "entryDate" => $product->launchView->startEntryDate,
                    "image" => $product->imageUrls->productImageUrl,
                    "skuDetails" => $field2
                );
                
                array_push($productData, $productDetail);
                
                if(!$productFound){
                    $insert_stmt = $db->prepare("INSERT INTO shoes (product_id, product_name, product_image, sizes, launch_date, price, method, early_link) VALUES (?,?,?,?,?,?,?,?)");
    	            $insert_stmt->bind_param('ssssssss', $product->launchView->productId, $product->productContent->title, $product->imageUrls->productImageUrl, json_encode($fields2), $newlaunchdate, $product->merchPrice->currentPrice, $product->launchView->method, $productDetails);
    	            $insert_stmt->execute();
                }
                else{
                    $select_stmt->bind_result($shoes_id);
                    
                    $update_stmt = $db->prepare("UPDATE shoes SET product_id = ?, product_name = ?, product_image = ?, sizes = ?, launch_date = ?, price = ?, method = ?, early_link = ? WHERE id = ?");
                    $update_stmt->bind_param('sssssssss', $product->launchView->productId, $product->productContent->title, $product->imageUrls->productImageUrl, json_encode($fields2), $newlaunchdate, $product->merchPrice->currentPrice, $product->launchView->method, $productDetails, $shoes_id);
                    $update_stmt->execute();
                }
            }
        }
    }
    
    $data = json_encode($products);
    $stmt = $db->prepare("UPDATE miscellaneous SET data = ? WHERE id = 1");
    $stmt->bind_param('s', $data);
    $stmt->execute();
    
    $data2 = json_encode($productData);
    $stmt2 = $db->prepare("UPDATE miscellaneous SET data = ? WHERE id = 2");
    $stmt2->bind_param('s', $data2);
    $stmt2->execute();
    
    // Because Discord only allow max 10 products
    while(count($products) > 10){
        array_pop($products);
    }
}
else{
    echo "error";
}
// FINISH: Pull product from Nike

if(count($products) > 0){
    $json_data = json_encode([
        "content" => "Hello everyone!!! The new launched are detected",     // Message
        "username" => "CubeACO",                                            // Username
        "avatar_url" => "https://sneakercube.io/images/logo-cube.png",      // Avatar URL.
        "tts" => false,                                                     // Text-to-speech
        "embeds" => $products                                               // Embeds Array
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    
    $ch = curl_init( $webhookurl );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    
    $response = curl_exec( $ch );
    curl_close( $ch );
    echo $response;
}

?>