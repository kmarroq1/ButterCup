<?php

class ButtercupDB {

    private $db;
    private $error_message;

    /**
     * connect to the database
     */
    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=buttercup';
        $username = 'mgs_user';
        $password = 'pa55word';
        $this->error_message = '';
        try {
            $this->db = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            $this->error_message = $e->getMessage();
        }
    }

    /**
     * check the connection to the database
     *
     * @return boolean - true if a connection to the database has been established
     */
    public function isConnected() {
        return ($this->db != Null);
    }

    public function getErrorMessage() {
        return $this->error_message;
    }

    public function getDB() {
        return $this->db;
    }

    /**
     * Checks if the specified username is in this database
     * 
     * @param string $username
     * @return boolean - true if username is in this database
     */
    public function isValidUser($username) {
        $query = 'SELECT * FROM customers
              WHERE username = :username';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return !($row === false);
    }

    /**
     * Adds the specified user to the table users
     * 
     * @param type $username
     * @param type $password
     */
    public function addCustomer($username, $password, $first_name, $last_name, $email, $phone) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = 'INSERT INTO customers (username, password, firstName, lastName, email, phone)
              VALUES (:username, :password, :firstName, :lastName, :email, :phone)';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $hash);
        $statement->bindValue(':firstName', $first_name);
        $statement->bindValue(':lastName', $last_name);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':phone', $phone);
        $statement->execute();
        $statement->closeCursor();
    }

    /**
     * Checks the login credentials
     * 
     * @param type $username
     * @param type $password
     * @return boolen - true if the specified password is valid for the 
     *              specified username
     */
    public function isValidUserLogin($username, $password) {
        $query = 'SELECT password FROM customers
              WHERE username = :username';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        $hash = $row['password'];
        return password_verify($password, $hash);
    }

    /**
     * Retrieves the customerID for the specified user
     * 
     * @param string $username
     * @return username
     */
    public function getCustomerID($username) {
        $query = 'SELECT customerID FROM customers
                  WHERE username = :username';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $user_id = $statement->fetch();
        $statement->closeCursor();
        return $user_id;
    }

    /**
     * Retrieves the order history for the specified user
     * 
     * @param string $user_id
     * @return array - array of order history for the specified username
     */
    public function getOrderHistory($user_id) {
        $query = 'SELECT * FROM order_history
                  WHERE customerID = :user_id';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $orders = $statement->fetchAll();
        $statement->closeCursor();
        return $orders;
    }

    /**
     * Retrieves the customerID for the specified user
     * 
     * @param string $cup
     * @return username
     */
    public function getCupID($cup) {
        $query = 'SELECT cupID FROM products
                  WHERE cup_option = :cup_option';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':cup_option', $cup);
        $statement->execute();
        $cup_id = $statement->fetch();
        $statement->closeCursor();
        return $cup_id;
    }

    /**
     * Adds the specified user to the table users
     * 
     * @param type $customer_id
     * @param type $cup_id
     * @param type $date
     */
    public function addOrder($customer_id, $cup_id, $date) {
        $query = 'INSERT INTO order_history (customerId, cupId, orderedDate)
              VALUES (:customer_id, :cup_id, :date)';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->bindValue(':cup_id', $cup_id);
        $statement->bindValue(':date', $date);
        $statement->execute();
        $statement->closeCursor();
    }

}

?>