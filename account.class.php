<?php

require_once 'database.php';

class Account {
    // role:staff == is_staff:true && is_admin:false
    // role:admin == is_staff:true && is_admin:true
    // role:customer == is_staff:false && is_admin:false

    // public $id = '';
    // public $first_name = 'Staff';
    // public $last_name = '01';
    // public $username = 'staff';
    // public $password = 'staff';
    // public $role = 'staff';
    // public $is_staff = true;
    // public $is_admin = false;

    // public $id = '';
    // public $first_name = 'Admin';
    // public $last_name = '02';
    // public $username = 'admin';
    // public $password = 'admin';
    // public $role = 'admin';
    // public $is_staff = true;
    // public $is_admin = true;

    // public $id = '';
    // public $first_name = 'Customer';
    // public $last_name = '03';
    // public $username = 'customer';
    // public $password = 'customer';
    // public $role = 'customer';
    // public $is_staff = false;
    // public $is_admin = false;

    public $id = '';
    public $first_name = '';
    public $last_name = '';
    public $username = '';
    public $password = '';
    public $role = '';
    public $is_staff = '';
    public $is_admin = '';

    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    function add() {
        $sql = "INSERT INTO account (
            first_name,
            last_name,
            username,
            password,
            role,
            is_staff,
            is_admin
        ) VALUES (
            :first_name,
            :last_name,
            :username,
            :password,
            :role,
            :is_staff,
            :is_admin
        );";

        $query = $this->db->connect()->prepare($sql);

        $query->bindParam(':first_name', $this->first_name);
        $query->bindParam(':last_name', $this->last_name);
        $query->bindParam(':username', $this->username);

        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        //using password default will always keep it up to date
        $query->bindParam(':password', $hashedPassword);

        $query->bindParam(':role', $this->role);
        $query->bindParam(':is_staff', $this->is_staff);
        $query->bindParam(':is_admin', $this->is_admin);

        if ($query->execute()) { // query executes successfully
            return true;
        }
        else {
            return false;
        }
    }

    function usernameExists($username) { //currently only applicable for signup
        $sql = "SELECT * FROM account WHERE username = :username;";
        //select all from account that has the same username

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':username', $username);

        $data = null;

        if ($query->execute()) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
        }

        $result = empty($data) ? false : true;
        //false if empty; user with same username does not exist
        return $result;
    }

    function login($username, $password) {
        //this function makes sure that the given username exist in the db and matches the password

        //by default, the query should always return 1 row since the username is unique
        //we use limit 1 to prevent selecting unwanted bypassed data (like an unexpected duplicate)
        $sql = "SELECT * FROM account WHERE username = :username LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam('username', $username);

        if($query->execute()){
            $data = $query->fetch(PDO::FETCH_ASSOC);

            if($data && password_verify($password, $data['password'])){
                return true;
            }
        }
        return false;
    }

    function fetch($username){
        //this function may not be needed if we were to return the value(data) directly from the login function
        $sql = "SELECT * FROM account WHERE username = :username LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);

        $query->bindParam('username', $username);
        $data = null;
        if($query->execute()){
            $data = $query->fetch(PDO::FETCH_ASSOC);
        }

        return $data;
    }
}

// $obj = new Account();
// $obj->add();
// $obj->usernameExists('1', 'staff');
// fix dashboard, login, logout, class