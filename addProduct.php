<!-- addProduct.php -->

<?php
    require_once('functions.php');
    require_once('product.class.php');

    $name = $category = $price = '';
    $nameErr = $categoryErr = $priceErr = '';

    $productObj = new Product();
    $categoryArr = $productObj->getCategory();


    if ($_SERVER['REQUEST_METHOD'] =='POST') {
        $name = clean_input(($_POST['name']));
        $category = clean_input(($_POST['category']));
        $price = clean_input(($_POST['price']));

        if (empty($name)) {
            $nameErr = 'name is required';
        }

        if (empty($category)) {
            $categoryErr = 'category is required';
        }
        
        if (empty($price)) {
            $priceErr = 'price is required';
        }
        
        
        if (empty($nameErr) && empty($categoryErr) && empty($priceErr)){
            foreach ($categoryArr as $cat) {
                if ($category === $cat['category_name']) {
                    $category = $cat['id'];
                    break;
                }
            }

            $productObj->name = $name;
            $productObj->category = $category;
            $productObj->price = $price;
        
            if ($productObj->add()) {
                header('location: products.php');
            } else {
                echo 'Something went wrong when adding new product';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
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
    <h1>Add a New Product to the Inventory</h1>
    <a href="products.php">Show Products</a>
    <form action="" method="post">
        <!-- name -->
        <label for="name">Product Name</label><br>
        <input type="text" placeholder="Enter Product Name" id="name" name="name" value="<?= $name ?>">
        <span class="error"><?= $nameErr ?></span>

        <div></div>  

        <!-- category -->
        <label for="category">Category</label><br>
        <select name="category" id="category">
            <option value="" disabled hidden selected>Select a Category</option>
            <?php
                foreach($categoryArr as $cat) {
            ?>
                <option value = "<?= $cat['category_name'] ?>"><?= $cat['category_name'] ?></option>
            <?php
                }
            ?>
        </select>
        <span class="error"><?= $categoryErr ?></span>
        
        <div></div>

        <!-- price -->
        <label for="price">Price</label><br>
        <input type="Number" placeholder="Enter Product's Price" id="price" name="price" value="<?= $price ?>">
        <span class="error"><?= $priceErr ?></span>

        <div></div>
        
        <input type="submit">
    </form>
</body>
</html>


