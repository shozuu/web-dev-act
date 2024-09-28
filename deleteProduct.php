<?php

session_start();

if (isset($_SESSION['account'])) {
    // make sure that only the admin is able to access this page
    if (!$_SESSION['account']['is_admin']) {
        header('location: login.php');
    }
} else {
    header('location: login.php');
}

require_once('product.class.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $productObj = new Product();
    $response = $productObj->delete($id) == true ? true : false;
    
    if ($response) {
        return true;
    } else {
        return false;
    }
}