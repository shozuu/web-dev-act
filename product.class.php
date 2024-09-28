<!-- product.class.php -->

<?php
require_once 'database.php';

class Product {
    public $id = '';
    public $name = '';
    public $category = '';
    public $price = '';

    protected $db;

    function __construct() {
        $this->db = new Database();
    }

    function add() {
        $sql = "INSERT INTO product (
            name,
            category,
            price 
        ) VALUES (
            :name,
            :category,
            :price
        );";

        $query = $this->db->connect()->prepare($sql);

        $query->bindParam(':name', $this->name);
        $query->bindParam(':category', $this->category);
        $query->bindParam(':price', $this->price);

        if ($query->execute()) { // query executes successfully
            return true;
        }
        else {
            return false;
        }
    }

    function edit($id) {
        $sql = "UPDATE product 
        SET
            name = :name,
            category = :category,
            price = :price
        WHERE id = :id;";

        $query = $this->db->connect()->prepare($sql);

        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->bindParam(':name', $this->name);
        $query->bindParam(':category', $this->category);
        $query->bindParam(':price', $this->price);

        if ($query->execute()) { 
            return true;
        }
        else {
            return false;
        }
    }

    function delete($id) {
        $sql = "UPDATE product SET delete_status = 1 WHERE id = :id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        if ($query->execute()) { 
            return true;
        }
        else {
            return false;
        }
    }

    function showAll($search = '', $category = '') {
        $sql = 
        "SELECT * FROM (
            SELECT 
                p.id,
                p.name,
                c.category_name,
                p.price,
                CASE
                    WHEN (
                        COALESCE((SELECT SUM(quantity) FROM stock WHERE product_id = p.id AND status = 'stock-in'), 0) - 
                        COALESCE((SELECT SUM(quantity) FROM stock WHERE product_id = p.id AND status = 'stock-out'), 0)
                    ) <= 0
                    THEN 'Not-Available'
                    ELSE 'Available'
                END AS availability
            FROM 
                product AS p 
            JOIN 
                category AS c ON p.category = c.id
            WHERE 
                delete_status = 0
        ) AS initial_query 
        -- wrapped in another select which can be used for filtering/searching keywords in WHERE since we cant search from computed queries like availability
        WHERE 
            (
                name LIKE '%' :search '%' 
                OR category_name LIKE '%' :search '%' 
                OR availability LIKE '%' :search '%'
            )
        AND 
            category_name LIKE '%' :category '%'
        ORDER BY 
            id;";

        // if search == '', it will still select * from product because (name LIKE '%' :search '%') is the same as (name LIKE '%%'), which selects all the name that matches any string

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':search', $search);
        $query->bindParam(':category', $category);

        $data = null;

        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            // returns an array that contains both associative and numerically indexed array
        }

        return $data;
    }

    function getProductById($id) {
        $sql = "SELECT * FROM product WHERE id = :id;";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $data = null;

        if ($query->execute()) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
            // returns associative array that has column names as keys
            // like $data['column_name] shows the value under that colName
        }

        return $data;
    }

    function getCategory() {
        $sql = "SELECT * FROM category ORDER BY category_name;";

        $query = $this->db->connect()->prepare($sql);

        $data = null;

        if ($query->execute()) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            // returns associative array that has column names as keys
            // like $data['column_name] shows the value under that colName
        }

        return $data;
    }

    function transact($id, $quantity, $status) {
        $sql = "INSERT INTO stock (
            product_id, 
            quantity,
            status
        ) VALUES (
            :id,
            :quantity,
            :status
        );";

        $query = $this->db->connect()->prepare($sql);

        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->bindParam(':quantity', $quantity);
        $query->bindParam(':status', $status);

        if ($query->execute()) { 
            return true;
        }
        else {
            return false;
        }
    }
}
?>