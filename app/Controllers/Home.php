<?php
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use App\Config;
use App\Views\IndexView;

class Home 
{
    public function index(): string
    {
        $view = IndexView::Begin();
        $view .= $this->login();
        $view .= IndexView::Home();
        $view .= IndexView::End();
        
        return $view;
    }
    
    public function login()
    {
      
       var_dump( session_start());
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

           
            $users = Config::USERS; 
            if (array_key_exists($username, $users) && $users[$username] === $password) {
                $_SESSION['user_id'] = $username; // 
                $_SESSION['username'] = $username;
                header('Location: ' . Config::BASE_URL . 'kecso'); 
                exit();
            } else {
                
                $_SESSION['error_message'] = 'Hibás felhasználónév vagy jelszó.';
                header('Location: ' . Config::BASE_URL . 'login.php');
                exit();
            }
        }

        
        ob_start(); 

        if (isset($_SESSION['error_message'])) {
            echo '<p style="color: red;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
            unset($_SESSION['error_message']);
        }

        ?>
        <!DOCTYPE html>
        <html lang="hu">
        <head>
            <meta charset="UTF-8">
            <title>Bejelentkezés</title>
        </head>
        <body>
            <h2>Bejelentkezés</h2>
            <form action="<?= Config::BASE_URL ?>login.php" method="POST">
                <label for="username">Felhasználónév:</label><br>
                <input type="text" id="username" name="username" required><br><br>
                
                <label for="password">Jelszó:</label><br>
                <input type="password" id="password" name="password" required><br><br>
                
                <input type="submit" name="login" value="Bejelentkezés">
            </form>
        </body>
        </html>
        <?php

        $content = ob_get_clean(); 

        return $content;
    }
    public function logout()
    {
        session_start();
        session_unset(); 
        session_destroy(); 
        header('Location: ' . Config::BASE_URL . 'login.php'); 
        exit();
    }
}
?>

