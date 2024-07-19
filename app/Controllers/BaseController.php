<?php
namespace App\Controllers;

use App\Config;

class BaseController {
    protected $db;

    public function __construct()
    {
        session_start();
        $this->checkLogin();
    }

    protected function checkLogin()
    {
        
        if (!isset($_SESSION['user_id'])) {
            
            header('Location: ' . Config::BASE_URL . 'login.php');
            exit();
        }
    }
}