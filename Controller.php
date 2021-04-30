<?php

require_once './model/Database.php';
require_once './model/Validator.php';
require_once 'autoload.php';

class Controller {

    private $twig;
    private $action;
    private $db;

    /**
     * Instantiates a new controller
     */
    public function __construct() {
        $this->setupConnection();
        $this->connectToDatabase();
        $loader = new Twig\Loader\FilesystemLoader('./view');
        $this->twig = new Twig\Environment($loader);
        $this->twig->addGlobal('session', $_SESSION);
        $this->action = $this->getAction();

        $this->validate = new Validator();
        $fields = $this->validate->getFields();
        $fields->addField('username');
        $fields->addField('password');
        $fields->addField('first_name');
        $fields->addField('last_name');
        $fields->addField('email');
        $fields->addField('phone');
    }

    /**
     * Initiates the processing of the current action
     */
    public function invoke() {
        switch ($this->action) {
            case 'Show Login':
                $this->processShowLogin();
                break;
            case 'Login':
                $this->processLogin();
                break;
            case 'Register':
                $this->processRegistration();
                break;
            case 'Logout':
                $this->processLogout();
                break;
            case 'Order':
                $this->processAddProduct();
                break;
            case 'Order History':
                $this->processShowOrders();
                break;
            case 'Show Products':
                $this->processShowProducts();
                break;
            case 'FAQ':
                $this->processShowFAQ();
                break;
            case 'Services':
                $this->processShowServices();
                break;
            case 'Build':
                $this->processShowBuild();
                break;
            case 'Home':
                $this->processShowHomePage();
                break;
            default:
                $this->processShowHomePage();
                break;
        }
    }

    /*     * **************************************************************
     * Process Request
     * ************************************************************* */

    private function processShowLogin() {
        $template = $this->twig->load('sign_in.twig');
        echo $template->render(['login_message' => '', 'user' => '']);
    }

    private function processLogin() {
        $username = filter_input(INPUT_POST, 'login_username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'login_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($this->db->isValidUserLogin($username, $password)) {
            $_SESSION['is_valid_user'] = true;
            $_SESSION['username'] = $username;
            header("Location: .?action=Home");
        } else {
            $template = $this->twig->load('sign_in.twig');
            echo $template->render(['login_message' => 'Invalid username or password']);
        }
    }

    private function processRegistration() {
        $username = filter_input(INPUT_POST, 'new_username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $this->validate->checkText('username', $username, true, 1, 30);
        $this->validate->checkText('password', $password, true, 6, 30);
        $this->validate->checkText('first_name', $first_name, true, 1, 30);
        $this->validate->checkText('last_name', $last_name, true, 1, 30);
        $this->validate->checkEmail('email', $email, true);
        $this->validate->checkPhone('phone', $phone);

        if ($this->validate->foundErrors()) {
            $fields = $this->validate->getFields();
            $template = $this->twig->load('sign_in.twig');
            echo $template->render(['error_username' => $fields->getField('username')->getHTML(),
                'error_password' => $fields->getField('password')->getHTML(),
                'error_first_name' => $fields->getField('first_name')->getHTML(),
                'error_last_name' => $fields->getField('last_name')->getHTML(),
                'error_email' => $fields->getField('email')->getHTML(),
                'error_phone' => $fields->getField('phone')->getHTML(),
                'new_username' => $username,
                'new_password' => $password,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone]);
        } else {
            $this->db->addCustomer($username, $password, $first_name, $last_name, $email, $phone);
            $_SESSION['is_valid_user'] = true;
            $_SESSION['username'] = $username;
            header("Location: .?action=Home");
        }
    }

    private function processShowHomePage() {
        if (!isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('home.twig');
            echo $template->render(['user' => '']);
        } else {
            $username = $_SESSION['username'];
            $template = $this->twig->load('home.twig');
            echo $template->render(['user' => 'Welcome ' . $username]);
        }
    }

    private function processShowFAQ() {
        if (!isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('faq.twig');
            echo $template->render(['user' => '']);
        } else {
            $username = $_SESSION['username'];
            $template = $this->twig->load('faq.twig');
            echo $template->render(['user' => 'Welcome ' . $username]);
        }
    }

    private function processShowServices() {
        if (!isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('services.twig');
            echo $template->render(['user' => '']);
        } else {
            $username = $_SESSION['username'];
            $template = $this->twig->load('services.twig');
            echo $template->render(['user' => 'Welcome ' . $username]);
        }
    }

    private function processShowBuild() {
        if (!isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('build.twig');
            echo $template->render(['user' => '']);
        } else {
            $username = $_SESSION['username'];
            $template = $this->twig->load('build.twig');
            echo $template->render(['user' => 'Welcome ' . $username]);
        }
    }

    private function processLogout() {
        $_SESSION = array();   // Clear all session data from memory
        session_destroy();     // Clean up the session ID
        header("Location: .?action=Home");
    }

    private function processShowProducts() {
        $flow = filter_input(INPUT_POST, 'flow', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $cervix = filter_input(INPUT_POST, 'cervix', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $string = filter_input(INPUT_POST, 'string', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        //calculate price here
        //figure out which cup is recommended here and save into variable $new_cup


        if (empty($flow) || $flow == NULL || empty($cervix) || $cervix == NULL || empty($color) || $color == NULL || empty($string) || $string == NULL) {
            $template = $this->twig->load('build.twig');
            echo $template->render(['invalid' => 'Required field']);
        } else if (!isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('sign_in.twig');
            echo $template->render(['login_message' => 'Login to get your cup recommendation']);
        } else {
            $new_cup = "cup1";
            $new_cup_id = $this->db->getCupID($new_cup);
            $_SESSION['newCupID'] = $new_cup_id;
            $username = $_SESSION['username'];
            $template = $this->twig->load('default_product.twig');
            echo $template->render(['user' => 'Welcome ' . $username, 'price' => 40]);
        }
    }

    private function processAddProduct() {
        if (!isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('sign_in.twig');
            echo $template->render(['login_message' => 'Login to get your cup recommendation']);
        } else {
            $username = $_SESSION['username'];
            $customer_id = $this->db->getCustomerID($username);
            $new_cup_id = $_SESSION['newCupID'];
            $date = date("m/d/Y");
            $this->db->addOrder($customer_id, $new_cup_id, $date);
            $this->processShowOrders();
        }
    }

    private function processShowOrders() {
        if (!isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('sign_in.twig');
            echo $template->render(['user' => '']);
        } else {
            $username = $_SESSION['username'];
            $customer_id = $this->db->getCustomerID($username);
            $order_history = $this->db->getOrderHistory($customer_id);
            $template = $this->twig->load('order_history.twig');
            echo $template->render(['user' => 'Welcome ' . $username, 'order_history' => $order_history]);
        }
    }

    /**
     * Gets the action from $_GET or $_POST array
     * 
     * @return string the action to be processed
     */
    private function getAction() {
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($action === NULL) {
            $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($action === NULL) {
                $action = '';
            }
        }
        return $action;
    }

    /**
     * Ensures a secure connection and start session
     */
    private function setupConnection() {
        $https = filter_input(INPUT_SERVER, 'HTTPS');
        if (!$https) {
            $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
            $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
            $url = 'https://' . $host . $uri;
            header("Location: " . $url);
            exit();
        }
        session_start();
    }

    /**
     * Connects to the database
     */
    private function connectToDatabase() {
        $this->db = new ButtercupDB();
        if (!$this->db->isConnected()) {
            $error_message = $this->db->getErrorMessage();
            $template = $this->twig->load('database_error.twig');
            echo $template->render(['error_message' => $error_message]);
            exit();
        }
    }

}
