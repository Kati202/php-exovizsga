<?php
require_once '../vendor/autoload.php';

use App\Controllers\Home;
use App\Controllers\Kecso;
use App\Controllers\Tata;
use App\Controllers\Halas;
use App\Controllers\Nyerges;

$response = '';

$page = isset($_GET['page']) ? $_GET['page'] : '';
$operation = isset($_GET['operation']) ? $_GET['operation'] : '';
$param = isset($_GET['param']) ? $_GET['param'] : '';

switch ($page) {
    case 'kecso':
        $kecsoController = new Kecso();
        if ($operation === 'cardata') {
            $response = $kecsoController->cardata($param);
        } elseif ($operation === 'carcost') {
            $response = $kecsoController->carcost($param);
        } else {
            $response = $kecsoController->kecso();
        }
        break;
    case 'tata':
        $tataController = new Tata();
        $response = $tataController->tata();
        break;
    case 'halas':
        $halasController = new Halas();
        $response = $halasController->halas();
        break;
    case 'nyerges':
        $nyergesController = new Nyerges();
        $response = $nyergesController->nyerges();
        break;
    default:
        $homeController = new Home();
        $response = $homeController->index();
        break;
}

echo $response;
?>