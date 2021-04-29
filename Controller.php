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
            case 'Show Registration':
                $this->processShowRegistration();
                break;
            case 'Register':
                $this->processRegistration();
                break;
            case 'Logout':
                $this->processLogout();
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
            $template = $this->twig->load('build.twig');
            echo $template->render(['user' => 'Welcome ' . $username]);
        } else {
            $template = $this->twig->load('sign_in.twig');
            echo $template->render(['login_message' => 'Invalid username or password']);
        }
    }

    private function processShowRegistration() {
        $template = $this->twig->load('registration.twig');
        echo $template->render(['error_username' => '', 'error_password' => '']);
    }

    private function processRegistration() {
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');

        $validator = new Validator($this->db);
        $error_username = $validator->validateUsername($username);
        $error_password = $validator->validatePassword($password);

        if (!empty($error_username) || !empty($error_password)) {
            $template = $this->twig->load('registration.twig');
            echo $template->render(['error_username' => $error_username, 'error_password' => $error_password]);
        } else {
            $this->db->addUser($username, $password);
            $_SESSION['is_valid_user'] = true;
            $_SESSION['username'] = $username;
            header("Location: .?action=Show Tasks");
        }
    }

    private function processShowHomePage() {
        $template = $this->twig->load('home.twig');
        echo $template->render();
    }

    private function processShowFAQ() {
        $template = $this->twig->load('faq.twig');
        echo $template->render();
    }

    private function processShowServices() {
        $template = $this->twig->load('services.twig');
        echo $template->render();
    }

    private function processShowBuild() {
        if (!isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('sign_in.twig');
            echo $template->render(['login_message' => 'Log in to build your cup']);
        } else {
            $template = $this->twig->load('build.twig');
            echo $template->render();
        }
    }

    private function processLogout() {
        $_SESSION = array();   // Clear all session data from memory
        session_destroy();     // Clean up the session ID
        $template = $this->twig->load('login.twig');
        echo $template->render(['login_message' => 'You have been logged out.']);
    }

    private function processShowProducts() {
        if (!isset($_SESSION['is_valid_user'])) {
            $template = $this->twig->load('login.twig');
            echo $template->render(['login_message' => 'Log in to manage your tasks.']);
        } else {
            $errors = array();
            $tasks = $this->db->getTasksForUser($_SESSION['username']);
            $template = $this->twig->load('task_list.twig');
            echo $template->render(['errors' => $errors, 'tasks' => $tasks]);
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
