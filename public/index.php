<?php
require_once '../vendor/autoload.php';

use App\Controllers\Home;
use App\Controllers\Kecso;
use App\Controllers\Tatab;
use App\Controllers\Halas;
use App\Controllers\Nyerges;

$response = '';

$page = isset($_GET['page']) ? $_GET['page'] : '';
$operation = isset($_GET['operation']) ? $_GET['operation'] : '';
$param = isset($_GET['param']) ? $_GET['param'] : '';

switch ($page) {
    case 'kecso':
        $kecsoController = new Kecso();
        if ($operation === 'cardata') 
        {
            $response = $kecsoController->cardata($param);
        } 
        elseif ($operation === 'carcost') 
        {
            $response = $kecsoController->carcost($param);
        } 
        elseif ($operation === 'couriordata') 
        {
            $response = $kecsoController->couriordata($param);
        } 
        elseif ($operation === 'courioraddress') 
        {
            $response = $kecsoController->courioraddress($param);
        } 
        elseif ($operation === 'depo') 
        {
            $response = $kecsoController->depo($param);
        } 
        elseif ($operation === 'disp') 
        {
            $response = $kecsoController->disp($param);
        } 
        else 
        {
            $response = $kecsoController->kecso();
        }
        break;
    case 'tatab':
        $tataController = new Tatab();
        if($operation ==='cardata2')
        {
            $response = $tataController->cardata2($param);
        }
        elseif($operation === 'carcost2')
        {
            $response = $tataController->carcost2($param);
        }
        elseif($operation === 'couriordata2')
        {
            $response = $tataController->couriordata2($param);
        }
        elseif($operation === 'courioraddress2')
        {
            $response = $tataController->courioraddress2($param);
        }
        elseif($operation === 'depo2')
        {
            $response = $tataController->depo2($param);
        }
        elseif($operation === 'disp2')
        {
            $response = $tataController->disp2($param);
        }
        else
        {
            $response = $tataController->tatab();
        }
        break;
    case 'halas':
        $halasController = new Halas();
        if($operation ==='cardata3')
        {
            $response = $halasController->cardata3($param);
        }
        elseif($operation === 'carcost3')
        {
            $response = $halasController->carcost3($param);
        }
        elseif($operation === 'couriordata3')
        {
            $response = $halasController->couriordata3($param);
        }
        elseif($operation === 'courioraddress3')
        {
            $response = $halasController->courioraddress3($param);
        }
        elseif($operation === 'depo3')
        {
            $response = $halasController->depo3($param);
        }
        elseif($operation === 'disp3')
        {
            $response = $halasController->disp3($param);
        }
        else
        {
            $response = $halasController->halas();
        }
        break;
    case 'nyerges':
        $nyergesController = new Nyerges();
        if($operation ==='cardata4')
        {
            $response = $nyergesController->cardata4($param);
        }
        elseif($operation === 'carcost4')
        {
            $response = $nyergesController->carcost4($param);
        }
        elseif($operation === 'couriordata4')
        {
            $response = $nyergesController->couriordata4($param);
        }
        elseif($operation === 'courioraddress4')
        {
            $response = $nyergesController->courioraddress4($param);
        }
        elseif($operation === 'depo4')
        {
            $response = $nyergesController->depo4($param);
        }
        elseif($operation === 'disp4')
        {
            $response = $nyergesController->disp4($param);
        }
        else
       {
        $response = $nyergesController->nyerges();
       }
        break;
    default:
        $homeController = new Home();
        $response = $homeController->index();
        break;
}

echo $response;
?>