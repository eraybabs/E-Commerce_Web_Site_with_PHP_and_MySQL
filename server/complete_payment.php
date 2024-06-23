<?php

session_start();

include ('SQLManager.php');

$sqlManager = new SQLManager();

if (isset($_GET['transaction_id']) && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $order_status = "paid";
    $transaction_id = $_GET['transaction_id'];
    $user_id = $_SESSION['user_id'];
    $payment_date = date('Y-m-d H.i.s');

    // Change order status to paid
    $sqlManager->updateOrderStatus($order_status, $order_id);

    // Store payment info
    $sqlManager->storePaymentInfo($order_id, $user_id, $transaction_id, $payment_date);

    // Go to user account
    header("location: ../account.php?payment_message=paid successfully, thanks for your shopping with us ");
} else {
    header("location: index.php");
    exit;
}

?>
