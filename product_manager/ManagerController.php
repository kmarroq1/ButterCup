<?php

require_once '../model/GuitarShopDB.php';
require_once '../model/CategoryData.php';
require_once '../model/ProductData.php';
require_once '../model/Validator.php';
require_once '../util/Util.php';

class ManagerController {

    private $action;

    public function __construct() {
        $this->action = '';
        $this->db = new GuitarShopDB();

        // Instantiate validator and add fields
        $this->validate = new Validator();
        $fields = $this->validate->getFields();
        $fields->addField('code');
        $fields->addField('name');
        $fields->addField('price', 'Must be a valid number.');

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
            case 'delete_product':
                $this->processDeleteProduct();
                break;
            case 'show_add_form':
                $this->processShowAddForm();
                break;
            case 'add_product':
                $this->processAddProduct();
                break;
            default:
                $this->processListProducts();
                break;
        }
    }

    /*     * **************************************************************
     * Process Request
     * ************************************************************* */

    private function processListProducts() {
        $category_id = filter_input(INPUT_GET, 'category_id',
                FILTER_VALIDATE_INT);
        if ($category_id == NULL || $category_id == FALSE) {
            $category_id = 1;
        }
        $category_name = $this->category_data->get_category_name($category_id);
        $categories = $this->category_data->get_categories();
        $products = $this->product_data->get_products_by_category($category_id);
        include '../view/product_manager/product_list.php';
    }

    private function processDeleteProduct() {
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        if ($category_id == NULL || $category_id == FALSE ||
                $product_id == NULL || $product_id == FALSE) {
            $error = "Missing or incorrect product id or category id.";
            include '../view/errors/error.php';
        } else {
            $this->product_data->delete_product($product_id);
            header("Location: .?category_id=$category_id");
        }
    }

    private function processShowAddForm() {
        $code = '';
        $name = '';
        $price = '';
        $categories = $this->category_data->get_categories();
        $fields = $this->validate->getFields();
        include '../view/product_manager/product_add.php';
    }

    private function processAddProduct() {
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $code = filter_input(INPUT_POST, 'code');
        $name = filter_input(INPUT_POST, 'name');
        $price = filter_input(INPUT_POST, 'price');

        // Validate form data
        $this->validate->checkCode('code', $code, true, 1, 10);
        $this->validate->checkText('name', $name, true, 3, 50);
        $this->validate->checkPrice('price', $price);

        if ($category_id == NULL || $category_id == FALSE) {
            $error = "Invalid product data. Check all fields and try again.";
            include '../view/errors/error.php';
        } else if ($this->validate->foundErrors()) {
            $fields = $this->validate->getFields();
            $this->processShowAddForm();
        } else {
            $this->product_data->add_product($category_id, $code, $name, $price);
            header("Location: .?category_id=$category_id");
        }
    }

}

?>