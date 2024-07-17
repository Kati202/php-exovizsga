<?php
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use App\Config;
use App\Views\IndexView;

class Home extends BaseController
{
    public function index(): string
    {
        $view = IndexView::Begin();
        $view .= IndexView::Home();
        $view .= IndexView::End();

        return $view;
    }
    
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Ellenőrzés a depó felhasználók között
            $users = Config::USERS; // Konfigurációs fájlban tárolt felhasználók
            if (array_key_exists($username, $users) && $users[$username] === $password) {
                $_SESSION['user_id'] = $username; // Felhasználónév tárolása
                $_SESSION['username'] = $username;
                header('Location: ' . Config::BASE_URL . 'kecso'); // Vagy a megfelelő oldal, ahova irányítani szeretnéd
                exit();
            } else {
                // Sikertelen bejelentkezés
                $_SESSION['error_message'] = 'Hibás felhasználónév vagy jelszó.';
                header('Location: ' . Config::BASE_URL . 'login.php');
                exit();
            }
        }

        // Bejelentkezési űrlap megjelenítése
        ob_start(); // Bufferelés indítása

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

        $content = ob_get_clean(); // Buffer tartalmának lezárása és lementése változóba

        return $content;
    }
}
?>

