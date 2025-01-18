<?php
require_once('stripe/init.php');
require_once 'includes/db_connect.php';
require_once 'includes/users.php';

\Stripe\Stripe::setApiKey('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');

header('Content-Type: application/json');

$YOUR_DOMAIN = 'https://sneakercube.io';

session_start();

if(!isset($_SESSION['userDetail'])){
    echo '<script type="text/javascript">';
	echo 'window.location.href = "../login.html";</script>';
}
else{
    $user = $_SESSION['userDetail'];
    $role = $user->getRole();
    
    $renewed = "false";
    $_SESSION['renewed'] = $renewed;
    
    // Load user role details
    $stmt = $db->prepare("SELECT renewal_fee from roles where role_code= ?");
    $stmt->bind_param('s', $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $amount = floatval($row['renewal_fee']) * 100;
    
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'myr',
                'unit_amount' => $amount,
                'product_data' => [
                    'name' => 'User License Renewal'
                ],
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'allow_promotion_codes' => true,
        'success_url' => $YOUR_DOMAIN . '/success.php',
        'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
    ]);
    
    echo json_encode(['id' => $checkout_session->id]);
}
?>