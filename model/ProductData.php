<?php
class ProductData {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
        
    function get_products_by_category($category_id) {
        $query = 'SELECT * FROM products
                  WHERE products.categoryID = :category_id
                  ORDER BY productID';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':category_id', $category_id);
        $statement->execute();
        $products = $statement->fetchAll();
        $statement->closeCursor();
        return $products;
    }
    
    function get_product($product_id) {
        $query = 'SELECT * FROM products
                  WHERE productID = :product_id';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':product_id', $product_id);
        $statement->execute();
        $product = $statement->fetch();
        $statement->closeCursor();
        return $product;
    }
    
    function delete_product($product_id) {
        $query = 'DELETE FROM products
                  WHERE productID = :product_id';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':product_id', $product_id);
        $statement->execute();
        $statement->closeCursor();
    }
    
    function add_product($category_id, $code, $name, $price) {
        $query = 'INSERT INTO products
                     (categoryID, productCode, productName, listPrice)
                  VALUES
                     (:category_id, :code, :name, :price)';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':category_id', $category_id);
        $statement->bindValue(':code', $code);
        $statement->bindValue(':name', $name);
        $statement->bindValue(':price', $price);
        $statement->execute();
        $statement->closeCursor();
    }
}
?>