<?php
define('PATH', dirname(__FILE__));
require_once(PATH . "/../php/includes/db_connect.php");

$date = new DateTime();
$todayDate = $date->format('Y-m-d');
$status = 'inactive';
$key_flag = 'N';

$stmt = $db->prepare("UPDATE users SET status = ?, key_flag = ? where expired_date < ?");
$stmt->bind_param('sss', $status, $key_flag, $todayDate);
$stmt->execute();

$stmt2 = $db->prepare("DELETE from tbl_keys WHERE user_id in (SELECT id FROM users WHERE status = ?)");
$stmt2->bind_param('s', $status);
$stmt2->execute();
?>