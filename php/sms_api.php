<?php
function generateOTP($apiKey, $secret, $mobileNo) {
    $url = "https://api.esms.com.my/sms/otp/generate";
    
    $postData = [
        "apiKey" => $apiKey,
        "secret" => $secret,
        "mobileNo" => $mobileNo,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] == "success") {
            echo "OTP sent successfully.";
        } else {
            echo "Error generating OTP: " . $data['message'];
        }
    } else {
        echo "Failed to connect to the API.";
    }
}

function verifyOTP($apiKey, $secret, $mobileNo, $otp) {
    $url = "https://api.esms.com.my/sms/otp/verify";
    
    $postData = [
        "apiKey" => $apiKey,
        "secret" => $secret,
        "mobileNo" => $mobileNo,
        "otp" => $otp,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] == "success") {
            echo "OTP verified successfully.";
        } else {
            echo "Invalid OTP: " . $data['message'];
        }
    } else {
        echo "Failed to connect to the API.";
    }
}

?>