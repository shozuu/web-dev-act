<!-- products.php -->

<?php
    session_start();

    if (isset($_SESSION['account'])) {
        if(!$_SESSION['account']['is_staff']){
            // check if user is not staff/admin, redirect to login page
            header('location: login.php');
        }
    } else {
        header('location: login.php');
    }

    require_once 'product.class.php';
    require_once 'functions.php';

    $productObj = new Product();
    $array = $productObj->showAll();
    $categoryArr = $productObj->getCategory();

    $search = $category = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
        // isset always return true as long as its not null 
        $search = htmlentities($_POST['search']);
        $category = htmlentities($_POST['category']);
        $array = $productObj->showAll($search, $category);
    }

    // echo '<pre>'; //format var_dump for easy viewing
    // var_dump($array);
    // echo '</pre>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .info {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        a {
            text-decoration: none;
            color: #007bff;
            margin-bottom: 20px;
            display: inline-block;
        }

        .def {
            margin: 0px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        .column {
            display: flex;
            flex-direction: column;
            gap: 5px;
            justify-content: center;
            align-items: center;
        }

        .column a {
            margin-bottom: 0px;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        td {
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th:first-child, td:first-child {
            width: 50px;
        }

        th:last-child, td:last-child {
            width: 300px;
        }

        .container {
            margin: auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script src="product.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="info">
            <h1>Product Information</h1>
            <a href="addProduct.php">Add Product</a>
            <form action="" method="post">
                <!-- select -->
                <select name="category" id="category">
                    <option value = "">All</option>
                    
                    <?php
                        foreach ($categoryArr as $cat) {
                    ?>
                        <option value = "<?= $cat['category_name'] ?>" <?= (isset($category) && $category == $cat['category_name']) ? 'selected' : '' ?>><?= $cat['category_name'] ?></option>
                    <?php
                        }
                    ?>
                </select>

                <!-- searchbar -->
                <input type="text" id="search" name="search" placeholder="Search anything" value = <?= $search?>>
                <input type="submit" value="search">
            </form>
        </div>

        <table>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
            
            <?php
            foreach ($array as $arr) {
            ?>
            <tr>
                <td><?= $arr['id'] ?></td>
                <td><?= $arr['name'] ?></td>
                <td><?= $arr['category_name'] ?></td>
                <td><?= $arr['price'] ?></td>
                <td><?= $arr['availability'] ?></td>

                <td class="column">
                    <a href="editProduct.php?id=<?= $arr['id'] ?>" class="def">Edit</a>

                    <!-- the if statement ensures that delete and stock-out links wont appear in the DOM at all if the user isn't an admin to prevent user manipulation (if we were just to hide it) -->
                     
                    <?php if ($_SESSION['account']['is_admin']) { ?>
                        <a href="#" data-id="<?= $arr['id'] ?>" data-name="<?= $arr['name'] ?>" class="def delete-button">Delete</a>
                    <?php } ?>

                    <!-- stock-in is always present for both admin and staff -->
                    <a href="stock.php?id=<?= $arr['id'] ?>&type=stock-in">Stock in</a>

                    <?php if ($_SESSION['account']['is_admin']) { ?>
                        <a href="stock.php?id=<?= $arr['id'] ?>&type=stock-out">Stock Out</a>
                    <?php } ?>  
                </td>
            </tr>
            <?php
            }
            ?>
        </table>
    </div>
</body>
</html>
