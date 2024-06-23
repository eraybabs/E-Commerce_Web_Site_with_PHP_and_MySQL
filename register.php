<?php

session_start();

include('server/connection.php');
include('server/SQLManager.php');

$db_manager = new SQLManager($conn);

// If user has already registered, then take user account page
if (isset($_SESSION['logged_in'])) {
    header('location: account.php');
    exit;
}

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // If passwords don't match
    if ($password !== $confirmPassword) {
        header('location: register.php?error=passwords dont match');
        exit;
    }
    // If password is less than 6 characters
    else if (strlen($password) < 6) {
        header('location: register.php?error=password must be at least 6 characters');
        exit;
    }

    // Check whether there is a user with this email or not
    $existing_user = $db_manager->getUserByEmailAndPassword($email, $password);

    // If there is a user already registered with this email
    if ($existing_user) {
        header('location: register.php?error=user with this email already exists');
        exit;
    } else {
        // Create a new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $user_id = $db_manager->createUser($name, $email, $hashed_password);

        // If account was created successfully
        if ($user_id) {
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            $_SESSION['logged_in'] = true;
            header('location: account.php?register_success=You registered successfully');
            exit;
        }
        // Account could not be created
        else {
            header('location: register.php?error=could not create an account at the moment');
            exit;
        }
    }
}

?>

<?php include "layouts/header.php"; ?>

<!--Register-->
<section class="my-5 pt-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Register</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
        <form id="register-form" method="POST" action="register.php">
            <p style="color: red"><?php if (isset($_GET['error'])) {
                    echo $_GET['error'];
                } ?></p>
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" id="register-name" name="name" placeholder="Name" required/>
            </div>
            <div class="form-group">
                <label>E-mail</label>
                <input type="text" class="form-control" id="register-email" name="email" placeholder="Email" required/>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" id="register-password" name="password" placeholder="Password" required/>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" class="form-control" id="register-confirm-password" name="confirmPassword" placeholder="Confirm Password" required/>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" id="register-btn" name="register" value="Register">
            </div>
            <div class="form-group">
                <a id="login-url" href="login.php" class="btn">Do you have an account? Login</a>
            </div>
        </form>
    </div>
</section>

<!--Footer-->
<?php include "layouts/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
