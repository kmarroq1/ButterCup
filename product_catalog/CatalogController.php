<?php
require_once '../model/GuitarShopDB.php';
require_once '../model/CategoryData.php';
require_once '../model/ProductData.php';
require_once '../util/Util.php';

class CatalogController {
    private $action;
    
    public function __construct() {
        $this->action = '';
        $this->db = new GuitarShopDB();
        if ($this->db->isConnected()) {
            $this->category_data = new CategoryData($this->db);
            $this->product_data = new ProductData($this->db);
        } else {
            $error_message = $this->db->getErrorMessage();
            include '../view/errors/database_error.php';
            exit();
        }
    }
    
    public function invoke() {
        // get the action to be processed
        $this->action = Util::getAction($this->action);
        
        switch ($this->action) {
            case 'list_products':
                $this->processListProducts();
                break;
            case 'view_product':
                $this->processViewProduct();
                break;
            case 'add_to_cart':
                $this->processAddToCart();
                break;
            default:
                $this->processListProducts();
                break;
        }
    }
    
    /****************************************************************
     * Process Request
     ***************************************************************/
    private function processListProducts() {
        $category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
        if ($category_id == NULL || $category_id == FALSE) {
            $category_id = 1;
        }
        $categories = $this->category_data->get_categories();
        $category_name = $this->category_data->get_category_name($category_id);
        $products = $this->product_data->get_products_by_category($category_id);
        include '../view/product_catalog/product_list.php';
    }
    
    private function processViewProduct() {
        $product_id = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);
        if ($product_id == NULL || $product_id == FALSE) {
            $error = 'Missing or incorrect product id.';
            include '../view/errors/error.php';
        } else {
            $categories = $this->category_data->get_categories();
            $product = $this->product_data->get_product($product_id);
            
            // Get product data
            $code = $product['productCode'];
            $name = $product['productName'];
            $list_price = $product['listPrice'];
            
            // Calculate discounts
            $discount_percent = 30;  // 30% off for all web orders
            $discount_amount = round($list_price * ($discount_percent/100.0), 2);
            $unit_price = $list_price - $discount_amount;
            
            // Format the calculations
            $discount_amount_f = number_format($discount_amount, 2);
            $unit_price_f = number_format($unit_price, 2);
            
            // Get image URL and alternate text
            $image_filename = '../model/images/' . $code . '.png';
            $image_alt = 'Image: ' . $code . '.png';
            
            include '../view/product_catalog/product_view.php';
        }
    }
    
    private function processAddToCart() {
        header("Location: ../cart");
    }
}

?>