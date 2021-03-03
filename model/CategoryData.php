<?php
class CategoryData {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    function get_categories() {
        $query = 'SELECT * FROM categories
                  ORDER BY categoryID';
        $statement = $this->db->getDB()->prepare($query);
        $statement->execute();
        return $statement;    
    }

    function get_category_name($category_id) {
        $query = 'SELECT * FROM categories
                  WHERE categoryID = :category_id';    
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':category_id', $category_id);
        $statement->execute();    
        $category = $statement->fetch();
        $statement->closeCursor();    
        $category_name = $category['categoryName'];
        return $category_name;
    }
}
?>