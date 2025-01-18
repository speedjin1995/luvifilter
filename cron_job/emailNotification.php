<?php
define('PATH', dirname(__FILE__));
require_once(PATH . "/../php/includes/db_connect.php");

$status = 'active';

$stmt = $db->prepare("SELECT * FROM users WHERE DATE(expired_date) < DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status = ?");
$stmt->bind_param('s', $status);
$stmt->execute();
$result = $stmt->get_result();


while($row = $result->fetch_assoc()){
    $to = $row['email'];
    $subject = 'Reminder To Renew CubeACO Subscription';
    $message = '
<html>
<head>
  <title>Reminder To Renew CubeACO Subscription</title>
</head>
<body>
    <p> Hey '.$row['name'].'! Your CubeACO subscription is going to expire on '.date('Y-m-d', strtotime($row['expired_date'])).'. </p>
    <p> It is always our pleasure to help you copping SNKRS. </p>
    <p> So we think you are not going to take risk on:</p>
    <p> 1. Losing you exisiting role and paying higher monthly subscription fee </p>
    <p> 2. Missing chance to cop your favourite pair of sneakers </p>
    <p> 3. Not earning any side income during this pandemic period </p>
    <p> 4. Praying hard and waiting for another CubeACO limited copies restock</p>
    <p> Please login now on <a href="https://www.sneakercube.io">https://www.sneakercube.io</a> to make payment before you move to the next inbox message.</p>
    <p> Thank you.</p>
    <p>Best Regards, <br>Sneakercube.io Admin </p>
</body>
</html>';
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <support@sneakercube.io>' . "\r\n";
    $headers .= 'Cc: sneakercube123@gmail.com' . "\r\n";
    
    mail($to, $subject, $message, $headers);
}
?>