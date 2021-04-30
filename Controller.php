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
            case 'Orders':
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
        $username = filter_input(INPUT_POST, 'login_username');
        $password = filter_input(INPUT_POST, 'login_password');
        if ($this->db->isValidUserLogin($username, $password)) {
            $_SESSION['is_valid_user'] = true;
            $_SESSION['username'] = $username;
            $template = $this->twig->load('home.twig');
            echo $template->render(['user' => 'Welcome ' . $username]);
        } else {
            $template = $this->twig->load('sign_in.twig');
            echo $template->render(['login_message' => 'Invalid username or password']);
        }
    }

    private function processRegistration() {
        $username = filter_input(INPUT_POST, 'new_username');
        $password = filter_input(INPUT_POST, 'new_password');
        $first_name = filter_input(INPUT_POST, 'first_name');
        $last_name = filter_input(INPUT_POST, 'last_name');
        $email = filter_input(INPUT_POST, 'email');
        $phone = filter_input(INPUT_POST, 'phone');

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
                'new_username'=> $username,
                'new_password'=> $password, 
                'first_name'=> $first_name, 
                'last_name'=> $last_name, 
                'email'=> $email,
                'phone'=> $phone]);
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
        $template = $this->twig->load('home.twig');
        echo $template->render();
    }

    private function processShowProducts() {
        if (!isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('sign_in.twig');
            echo $template->render(['login_message' => 'Login to get your cup recommendation']);
        } else {
            //calculate prices here
            
            $username = $_SESSION['username'];
            $template = $this->twig->load('default_product.twig');
            echo $template->render(['user' => 'Welcome ' . $username, 'price' => 40]);
        }
    }

    private function processShowOrders() {
        if (!isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('sign_in.twig');
            echo $template->render(['user' => '']);
        } else {
            $username = $_SESSION['username'];
            $template = $this->twig->load('order_history.twig');
            echo $template->render(['user' => 'Welcome ' . $username]);
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
