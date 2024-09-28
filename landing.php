<?php 
    if (isset($_SESSION['account'])) {
        // customer should only be the ones to have access to this page
        if ($_SESSION['account']['role'] !== 'customer') {
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
    <title>Landing</title>
</head>
<body>
    <h1>Welcome, <?=$_SESSION['account']['first_name']?>!</h1><br>
    <a href="logout.php" class="logout">Sign up</a><br>
    <a href="logout.php" class="logout">Logout</a>
</body>
</html>