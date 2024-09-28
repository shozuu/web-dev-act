<?php
    // signup should only be available when user is logged out
    // currently, only customer can only access this page since admin/staff are manually added

    session_start();

    if (isset($_SESSION['account'])) {
        // this ensures that only customers are able to access the page
        if ($_SESSION['account']['is_staff']) {
            header('location: login.php');
        } else {
            // this prevents the user to signup when already logged in with another account
            echo 'psst '.$_SESSION['account']['first_name'].', you must be logged out first before signing up';
            echo '<br><a href="logout.php">Logout</a>';
            exit;
        }
    }

    require_once 'functions.php';
    require_once 'account.class.php';

    $first_name = $last_name = $username = $password = $confirmPassword = '';
    $first_nameErr = $last_nameErr = $usernameErr = $passwordErr = $strongPasswordErr = $confirmPasswordErr = '';

    $accountObj = new Account();

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $first_name = clean_input(($_POST['first_name']));
        $last_name = clean_input($_POST['last_name']);
        $username = clean_input(($_POST['username']));
        $password = clean_input($_POST['password']);
        $confirmPassword = clean_input($_POST['confirmPassword']);

        //check if empty
        if (empty($first_name)) {
            $first_nameErr = 'first name is required';
        }

        if (empty($last_name)) {
            $last_nameErr = 'last name is required';
        }

        if (empty($username)) {
            $usernameErr = 'username is required';
        }

        if (empty($password)) {
            $passwordErr = 'password is required';
        }

        if (empty($confirmPassword)) {
            $confirmPasswordErr = 'confirm password is required';
        }

        //check if username exists
        if (!empty($username)) {
            if ($accountObj->usernameExists($username)) { //if exists..
                $usernameErr = 'username already exists';
            }
        }

        //check password validity
        if (!empty($password) && !empty($confirmPassword)) {
            $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/';

            //check if passwords are same
            if ($password !== $confirmPassword) { 
                $passwordErr = 'passwords do not match';
                $confirmPasswordErr = 'passwords do not match';
            }
            
            //check if first_name/last_name/username is present in password
            $stringsToCheck = [$first_name, $last_name, $username];

            $isFound = false;
            foreach ($stringsToCheck as $string) {
                $cleanString = trim($string);

                //if found, it will return the index where the string started; otherwise false
                if (stripos($password, $cleanString) !== false) { 
                    $isFound = true;
                    break;
                }
            }

            if ($isFound) {
                $strongPasswordErr = 'password contains parts of your personal information (username, first name, or last name)';
            } else if (preg_match($regex, $password) !==1 ) {
                //preg_match returns 0 or false if patterns do not match or regex is invalid
                $strongPasswordErr = 'weak password: it should contain at least 8 chars, 1 uppercase, 1 lowercase, and 1 special character';
            }
        }

        if (empty($first_nameErr) && empty($last_nameErr) && empty($usernameErr) && empty($passwordErr) && empty($strongPasswordErr) && empty($confirmPasswordErr)){ 
            $accountObj->first_name = $first_name;
            $accountObj->last_name = $last_name;
            $accountObj->username = $username;
            $accountObj->role = 'customer';
            $accountObj->is_staff = false;
            $accountObj->is_admin = false;
        
            if ($accountObj->add()) {
                echo 'za uzer haz zuccezzfuly regiztered';
                echo '<br><a href="login.php">Login</a>';
                exit;
            } else {
                echo 'Something went wrong when signing up';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        .error{
            color: red;
        }
    </style>
</head>
<body>
    <h1>Signup</h1>
    <form action="" method="post">
        <!-- first_name -->
        <label for="first_name">First Name</label><br>
        <input type="text" name="first_name" id="first_name" value="<?= $first_name ?>">
        <p class="error"><?= $first_nameErr ?></p>

        <div></div>  

        <!-- last_name -->
        <label for="last_name">Last Name</label><br>
        <input type="text" name="last_name" id="last_name" value="<?= $last_name ?>">
        <p class="error"><?= $last_nameErr ?></p>

        <div></div>  

        <!-- username -->
        <label for="username">Username/Email</label><br>
        <input type="text" name="username" id="username" value="<?= $username ?>">
        <p class="error"><?= $usernameErr ?></p>        

        <div></div>  

        <!-- password -->
        <label for="password">Password</label><br>
        <input type="password" name="password" id="password" value="<?= $password ?>">
        <p class="error"><?= $passwordErr ?></p>
        <p class="error"><?= $strongPasswordErr ?></p>

        <div></div>  

        <!-- confirm password -->
        <label for="confirmPassword">Confirm Password</label><br>
        <input type="password" name="confirmPassword" id="confirmPassword" value="<?= $confirmPassword ?>">
        <p class="error"><?= $confirmPasswordErr ?></p>

        <div></div>  

        <input type="submit" value="signup" name="signup">
    </form>
</body>
</html>