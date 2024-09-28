<?php
    session_start();

    //check if the user has logged in; otherwise redirect to login page
    if (isset($_SESSION['account'])) {

        //if user is not staff/admin, redirect to login page
        //login page will redirect them back to their respective landing page based on user roles if user is already logged in

        if (!$_SESSION['account']['is_staff']) {
            header('location: login.php');
        }

    } else {
        header('location: login.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?=$_SESSION['account']['first_name']?>!</h1><br>
    <a href="products.php" class="products">Show Products</a><br>
    <a href="logout.php" class="logout">Logout</a>
</body>
</html>