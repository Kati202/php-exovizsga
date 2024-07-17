<?php
/*namespace App;

use App\Controllers\Auth;
use App\Controllers\Home;
use App\Controllers\Kecso;
use App\Controllers\Halas;
use App\Controllers\Nyerges;
use App\Controllers\Tatab;
use App\Config;

class Router
{
    private $controller;
    private $method;
    private $param;

    public function __construct($controller, $operation, $param)
    {
        session_start();

        // Alapértelmezett kontroller és metódus beállítása
        $this->controller = new Home();
        $this->method = 'index';

        // Bejelentkezés ellenőrzése
        if (!isset($_SESSION['user_id'])) {
            if ($controller === 'home' && $operation === 'login') {
                $this->controller = new Home();
                $this->method = 'login';
            } else {
                // Ha nincs bejelentkezve, alapértelmezett metódus index
                $this->method = 'index';
            }
        } else {
            // Ha be van jelentkezve, kiválasztjuk a megfelelő kontrollert és metódust
            switch ($controller) {
                case 'kecso':
                    $this->controller = new Kecso();
                    $this->method = $operation;
                    break;
                case 'halas':
                    $this->controller = new Halas();
                    $this->method = $operation;
                    break;
                case 'nyerges':
                    $this->controller = new Nyerges();
                    $this->method = $operation;
                    break;
                case 'tatab':
                    $this->controller = new Tatab();
                    $this->method = $operation;
                    break;
                case 'logout':
                    $this->logout();
                    break;
                default:
                    // Ha nem található az oldal, hibakezelés vagy 404-es hiba
                    header("HTTP/1.0 404 Not Found");
                    echo '404 Not Found';
                    exit();
                    break;
            }
        }

        $this->param = $param;
    }

    public function execute()
    {
        // Ellenőrizzük, hogy a controller és a method létezik-e
        if (method_exists($this->controller, $this->method)) {
            // Hívjuk meg a controller method-ot a paraméterrel
            return call_user_func_array([$this->controller, $this->method], [$this->param]);
        } else {
            // Ha nem létezik, 404-es hiba
            header("HTTP/1.0 404 Not Found");
            echo '404 Not Found';
            exit();
        }
    }

    private function logout()
    {
        // Kiléptetés: session törlése és átirányítás a BASE_URL-re
        session_destroy();
        header('Location: ' . Config::BASE_URL);
        exit();
    }
}*/
?>