<?php

session_start();

include('SQLManager.php');

$sqlManager = new SQLManager();

// if user is not logged in
if (!isset($_SESSION['logged_in'])) {
    header('location: ../checkout.php?message=Please login/register to place an order');
    exit;
} else {
    if (isset($_POST['place_order'])) {
        // 1. get user info and store it in database
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $city = $_POST['city'];
        $address = $_POST['address'];
        $order_cost = $_SESSION['total'];
        $order_status = "not paid";
        $user_id = $_SESSION['user_id'];
        $order_date = date('Y-m-d H.i.s');

        // 2. issue new order and store order info in database

        $order_id = $sqlManager->createOrder($order_cost, $order_status, $user_id, $phone, $city, $address, $order_date);

        // 3. get products from cart (from session)
        foreach ($_SESSION['cart'] as $key => $value) {
            $product = $_SESSION['cart'][$key];
            $product_id = $product['product_id'];
            $product_name = $product['product_name'];
            $product_image = $product['product_image'];
            $product_price = $product['product_price'];
            $product_quantity = $product['product_quantity'];

            // 4. store each single item in order_items database
            $sqlManager->storeOrderItem($order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date);
        }

        $_SESSION['order_id'] = $order_id;

        // 6. inform user whether everything is fine or there is a problem
        header('location: ../payment.php?order_status="order placed successfully"');
    }
}

?>
