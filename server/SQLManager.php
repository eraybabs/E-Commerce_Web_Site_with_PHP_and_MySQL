<?php

// SQLManager.php

class SQLManager {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect("localhost", "root", "", "php_project") or die("Couldn't connect to database");
    }

    public function getCoatsProducts() {
        $stmt = $this->conn->prepare("SELECT products.*, categories.category_name FROM products INNER JOIN categories ON products.category_id = categories.category_id WHERE categories.category_name = 'coats' LIMIT 4");

        $stmt->execute();

        return $stmt->get_result();
    }

    public function updateOrderStatus($order_status, $order_id) {
        $stmt = $this->conn->prepare("UPDATE orders SET order_status=? WHERE order_id=?");
        $stmt->bind_param('si', $order_status, $order_id);
        $stmt->execute();
    }

    public function storePaymentInfo($order_id, $user_id, $transaction_id, $payment_date) {
        $stmt = $this->conn->prepare("INSERT INTO payments (order_id, user_id, transaction_id, payment_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiss', $order_id, $user_id, $transaction_id, $payment_date);
        $stmt->execute();
    }

    public function getFeaturedProducts() {
        $stmt = $this->conn->prepare("SELECT * FROM products LIMIT 4");
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getShoes() {
        $stmt = $this->conn->prepare("SELECT products.*, categories.category_name FROM products INNER JOIN categories ON products.category_id = categories.category_id WHERE categories.category_name = 'shoes' LIMIT 4");
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getWatches() {
        $stmt = $this->conn->prepare("SELECT products.*, categories.category_name FROM products INNER JOIN categories ON products.category_id = categories.category_id WHERE categories.category_name = 'watches' LIMIT 4");
        $stmt->execute();
        return $stmt->get_result();
    }

    public function createOrder($order_cost, $order_status, $user_id, $phone, $city, $address, $order_date) {
        $stmt = $this->conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_address, order_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('isiisss', $order_cost, $order_status, $user_id, $phone, $city, $address, $order_date);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function storeOrderItem($order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date) {
        $stmt = $this->conn->prepare("INSERT INTO order_items(order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iissiiis', $order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date);
        $stmt->execute();
    }

    public function updatePassword($password, $user_email) {
        $stmt = $this->conn->prepare('UPDATE users SET user_password=? WHERE user_email=?');
        $stmt->bind_param('ss', $password, $user_email);
        return $stmt->execute();
    }

    public function getUserOrders($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id=?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getUserByEmailAndPassword($email, $password) {
        $stmt = $this->conn->prepare('SELECT user_id, user_name, user_email FROM users WHERE user_email = ? AND user_password = ? LIMIT 1');
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }

    public function getOrderItems($order_id) {
        $stmt = $this->conn->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function calculateTotalOrderPrice($order_details) {
        $total = 0;
        foreach ($order_details as $row) {
            $product_price = $row['product_price'];
            $product_quantity = $row['product_quantity'];
            $total += ($product_price * $product_quantity);
        }
        return $total;
    }

    public function createUser($name, $email, $password) {
        $stmt = $this->conn->prepare("INSERT INTO users (user_name, user_email, user_password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $password);
        $stmt->execute();

        // return the inserted user ID or false if the insertion failed
        return ($stmt->affected_rows === 1) ? $stmt->insert_id : false;
    }

    public function getProductsByCategoryAndPrice($category, $price, $page_no) {
        $total_records_per_page = 8;
        $offset = ($page_no - 1) * $total_records_per_page;

        $stmt = $this->conn->prepare("SELECT products.*, categories.category_name FROM products INNER JOIN categories ON products.category_id = categories.category_id WHERE categories.category_name=? AND products.product_price<=? LIMIT ?, ?");
        $stmt->bind_param("siii", $category, $price, $offset, $total_records_per_page);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getAllProductsByPage($page_no) {
        $total_records_per_page = 8;
        $offset = ($page_no - 1) * $total_records_per_page;

        $stmt = $this->conn->prepare("SELECT * FROM products LIMIT ?, ?");
        $stmt->bind_param("ii", $offset, $total_records_per_page);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getProductById($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = [];
        while ($row = $result->fetch_assoc()) {
            $product[] = $row;
        }
        return $product;
    }

    public function getRelatedProducts($category, $product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE category = ? AND product_id != ? LIMIT 4");
        $stmt->bind_param("si", $category, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $related_products = [];
        while ($row = $result->fetch_assoc()) {
            $related_products[] = $row;
        }
        return $related_products;
    }

}


?>