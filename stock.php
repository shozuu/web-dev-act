<!-- stock.php -->

<?php
    session_start();    

    require_once('functions.php');
    require_once('product.class.php');

    $name = $status = $quantity = '';
    $nameErr = $statusErr = $quantityErr = '';
    $productObj = new Product();

    if (isset($_SESSION['account'])) {
        // if user has logged in, check if its a staff
        if (!$_SESSION['account']['is_staff']) {
            // if not staff, login page will redirect them to their respective landing pages
            header('location: login.php');
        }
    } else {
        header('location: login.php');
    }

    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $type = $_GET['type'];
        $array = $productObj->getProductById($id);

        if (empty($array)) {
            // when id does not exist
            echo "Product does not exist";
            exit;
        } 
        
    } else {
        // when id isn't included in the link parameter
        echo "No product ID provided.";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] =='POST') {
        $name = clean_input(($_POST['name']));
        $status = clean_input(($_POST['status']));
        $quantity = clean_input(($_POST['quantity']));

        if (empty($name)) {
            $nameErr = 'Product Name cannot be empty';
        }

        if (empty($status)) {
            $statusErr = 'status is required';
        }
        
        if (empty($quantity)) {
            $quantityErr = 'quantity is required';
        }
        
        
        if (empty($nameErr) && empty($statusErr) && empty($quantityErr)){    
            if ($productObj->transact($id, $quantity, $status)) {
                header('location: products.php');
            } else {
                echo 'Something went wrong when transacting with the product';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Transactions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        a {
            text-decoration: none;
            color: #007bff;
            margin-bottom: 20px;
            display: inline-block;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin-top: 20px;
            color: #555;
            font-weight: bold;
        }

        label.radio,
        label.checkbox {
            display: inline;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 80px;
            resize: none;
        }

        input[type="radio"],
        input[type="checkbox"] {
            margin-right: 5px;
        }

        input[type="submit"] {
            width: 100%;
            margin-top: 20px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        .block {
            display: block;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .inline-label {
            display: inline-block;
            margin-right: 10px;
        }

        .range-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .range-container label {
            text-align: center;
        }

        .range-container input[type="range"] {
            width: calc(100% - 80px);
        }
    </style>
</head>
<body>
    <h1>Transact a Stock</h1>
    <a href="products.php">Show Products</a>
    <form action="" method="post">
        <!-- product name -->
        <label for="name">Product Name</label><br>  
        <input type="text" id="name" name="name" value="<?= $array['name'] ?>" readonly>
        <span class="error"><?= $nameErr ?></span>

        <div></div>  

        <!-- status could be radio button-->
        <label for="status">Transaction Type</label><br>
        <select name="status" id="status">
            <option value="" disabled hidden selected>Select a Transaction Type</option>

            <option value="stock-in" <?= $type == 'stock-in' ? 'selected' : '' ?> >Stock-In</option>
            
            <!-- stock-out is only exclusive to admin -->
            <?php if ($_SESSION['account']['is_admin']) { ?>
                <option value="stock-out" <?= $type == 'stock-out' ? 'selected' : '' ?>>Stock-Out</label>
            <?php } ?>  
            
        </select>
        <span class="error"><?= $statusErr ?></span>
        
        <div></div>

        <!-- quantity -->
        <label for="quantity">Quantity</label><br>
        <input type="Number" placeholder="Enter Quantity" id="quantity" name="quantity" value="<?= $quantity ?>">
        <span class="error"><?= $quantityErr ?></span>

        <div></div>
        
        <input type="submit">
    </form>
</body>
</html>