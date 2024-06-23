<?php

include ("layouts/header.php");
include ('server/SQLManager.php');

$sqlManager = new SQLManager();

if (isset($_SESSION['logged_in'])) {
    header('location: account.php');
    exit;
}

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $user = $sqlManager->getUserByEmailAndPassword($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['user_email'] = $user['user_email'];
        $_SESSION['logged_in'] = true;
        header('location: account.php?login_success=logged in successfully');
    } else {
        header('location: login.php?error=could not verify account');
    }
}

?>

<!--Login-->

<section class="my-5 pt-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Login</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
        <form id="login-form" method="POST" action="login.php">
            <p style="color: red" class="text-center"><?php if (isset($_GET['error'])) { echo $_GET['error']; } ?></p>
            <div class="form-group">
                <label>E-mail</label>
                <input type="text" class="form-control" id="login-email" name="email" placeholder="Email" required/>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" id="login-password" name="password" placeholder="Password" required/>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" id="login-btn" name="login_btn" value="Login">
            </div>
            <div class="form-group">
                <a id="register-url" href="register.php" class="btn">Don't have account? Register</a>
            </div>
        </form>
    </div>
</section>

<?php

include "layouts/footer.php";

?>
