<?php
    session_start();

    require_once 'functions.php';
    require_once 'account.class.php';

    $username = $password = '';
    $accountObj = new Account();
    $loginErr = '';

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $username = clean_input(($_POST['username']));
        $password = clean_input($_POST['password']);

        if($accountObj->login($username, $password)) {
            //if user exists, fetch its data and store it in SESSION
            $data = $accountObj->fetch($username);
            $_SESSION['account'] = $data;

            //direct them based on their respective roles
            if($_SESSION['account']['is_staff']) {
                //for staffs and admins, check if is_staff == 1
                header('location: dashboard.php');
            } else if ($_SESSION['account']['role'] == "customer") {
                //for customers
                header('location: landing.php');
            }

        } else {
            $loginErr = 'Invalid username/password';
        }
        
    } else {
        //this block will run if the user tries to navigate to this page but the session is already set (user has already logged in)
        
        //depending on their roles, this will redirect them back to their respective landing pages (dashboard & landing) with set of links/page they have access
        if(isset($_SESSION['account'])) {

            if($_SESSION['account']['is_staff']) {
                //for staffs and admins, check if is_staff == 1
                header('location: dashboard.php');
                
            } else if ($_SESSION['account']['role'] == "customer") {
                //for customers
                header('location: landing.php');
            }
        }
        //if session is not set, it lets the user log in (proceeds to html)
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        .error{
            color: red;
        }
    </style>
</head>
<body>
    <h1>Login</h1>
    <form action="login.php" method="post">
        <!-- username -->
        <label for="username">Username/Email</label><br>
        <input type="text" name="username" id="username" value="<?=$username?>">

        <div></div>

        <!-- password -->
        <label for="password">Password</label><br>
        <input type="password" name="password" id="password" value="<?=$password?>">
        
        <div></div>

        <input type="submit" value="Login" name="login">

        <?php
        if (!empty($loginErr)){
        ?>
            <p class="error"><?= $loginErr ?></p>
        <?php
        }
        ?>
    </form>
</body>
</html>