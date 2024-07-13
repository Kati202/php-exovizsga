<?php
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use App\Views\IndexView;
use App\Views\KecsoView\KecsoCarView;
use App\Views\KecsoView\KecsoCouriorView;
use App\Views\KecsoView\KecsoDepoView;
use App\Views\KecsoView\KecsoDispView;
use App\Models\KecsoModel\CarsModel;
use App\Models\KecsoModel\CouriorsModel;
use App\Models\KecsoModel\DeposModel;
use App\Models\KecsoModel\DispModel;
use App\Requests\KecsoRequest;
use App\Config;


class Kecso
{
public function kecso(): string
{
        // Kezdeti nézet 
        $view = IndexView::Begin();
        $view .= IndexView::StartTitle('Kecskeméti depó főoldal');

      
        $view.=CarsModel::Init();
        // Új gépjármű hozzáadása
        if (KecsoRequest::CarsInsert()) {
            $car = [
                'license' => $_POST['license']
            ];
            CarsModel::InsertCar($car);
            header("Location: " . $_SERVER['REQUEST_URI']); 
            exit();
        }
        //Gépjármű törlése
        if (isset($_POST['deleteCar'])) {
            $carId = $_POST['deleteCarId'];
            CarsModel::DeleteCar($carId);
            header("Location: " . $_SERVER['REQUEST_URI']); 
            exit();
        }
        $view.=KecsoCarView::ShowCar();
       
        
        $view.=CouriorsModel::Init();
        //Új futár hozzáadása
        if (KecsoRequest::CouriorInsert()) {
            $courior = 
            [
                'ids' => $_POST['ids'],
                'name' => $_POST['name']
            ];
            CouriorsModel::InsertCouriors($courior);
            header("Location: " . $_SERVER['REQUEST_URI']); 
            exit();
        }
        //Futár törlése
        if (isset($_POST['deleteCourior'])) {
            $couriorId = $_POST['deleteCouriorId'];
            CouriorsModel::DeleteCouriors($couriorId);
            header("Location: " . $_SERVER['REQUEST_URI']); 
            exit();
        }
       

        $view .=KecsoCouriorView::ShowCourior();
        $view .=KecsoDepoView::ShowDepoButton();
        $view .=KecsoDispView::ShowDispButton();

        //Oldalzárás
        $view .= IndexView::End();

        return $view;
}
public function cardata($param): string
{
$carId = isset($param) ? $param : null;
$view = IndexView::Begin();
$view .= IndexView::StartTitle('Gépjármű adatai');
$view .= CarsModel::Init();



// Fájlfeltöltés kezelése
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $view .= KecsoCarView::HandleFileUpload();
}


$view .= KecsoCarView::CarData($carId);


$view .= IndexView::End();

return $view;}

public function carcost($param): string
{   
   
    $view = IndexView::Begin();
    $view .= IndexView::StartTitle('Kecskeméti depó főoldal');

    /*if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter'])) {
        $startDate = $_POST['startDate'] ?? date('Y-m-01');
        $endDate = $_POST['endDate'] ?? date('Y-m-t');
    } else {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
    }*/

   /* $deliveries = CarsModel::SumDeliveredAddressesByDateAndGroup($startDate, $endDate);
    var_dump($deliveries);*/

    // Autóbeszúrás 
    if (KecsoRequest::CarCostInsert()) {
        $carcost = [
            'ids' => $_POST['ids'],
            'date' => $_POST['date'], 
            'part' => $_POST['part'],
            'cost' => $_POST['cost'],
        ];
        CarsModel::InsertCarCost($carcost);
        header("Location: " . Config::KECSO_URL_CARCOST);
        exit();
    }

    if (KecsoRequest::CarCostDelete()) {
        $carcostId = $_POST['deleteCarcostId'];
        CarsModel::DeleteCarCost($carcostId);
        header("Location: " . Config::KECSO_URL_CARCOST);
        exit();
    }

    if (KecsoRequest::CarCostUpdate()) {
        $editcarcost = CarsModel::GetCarCostById($_POST['updateCarCostId']);
    }

    if (KecsoRequest::CarCostSave()) {
        $carcost = [
            'ids' => $_POST['ids'],
            'date' => $_POST['date'], 
            'part' => $_POST['oart'],
            'cost' => $_POST['cost'],
        ];
        CouriorsModel::UpdateCarCost($_POST['editCarcostId'], $carcost);
        header("Location: " . Config::KECSO_URL_CARCOST);
        exit();
    }

    $carcost = CarsModel::GetCarCost();
    //$view .= KecsoCouriorView::ShowDeliveriesByGroup($deliveries, $startDate, $endDate);
    $view .= KecsoCarView::CarCost($carcost, $editcarcost = null);
    $view .= IndexView::End();

    return $view;
}
public function couriorData($param): string
{
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Futár adatai');
    $view .= CouriorsModel::Init();
    
    $editCourior = null;
    $id = $_REQUEST['param'] ?? '';
    if ($id) {
        $couriordata = CouriorsModel::GetCouriorDataById($id);
    } else {
        $couriordata = [];
    }

    

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Futár szerkesztése    
        if (KecsoRequest::CouriorUpdate()) {
            $editCouriorId = $_POST['updateCouriorId'] ?? null;
            if ($editCouriorId) {
                $editCourior = CouriorsModel::GetCouriorDataById($editCouriorId);
            }
        }

    // Futár mentése
        if (KecsoRequest::CouriorSave()) {
            $editCouriorId = $_POST['editCouriorId'] ?? null;
            $ids=$_POST['ids'] ?? '';
            $name = $_POST['name'] ?? '';
            $date = $_POST['date'] ?? '';
            $dateaddress = $_POST['dateaddress'] ?? '';
            $age = $_POST['age'] ?? '';
            $address = $_POST['address'] ?? '';
            $mothername = $_POST['mothername'] ?? '';
    // Ellenőrzés, hogy minden mező ki legyen töltve
            if (!empty($editCouriorId) && !empty($ids) && !empty($name) && !empty($date) && !empty($dateaddress) && !empty($age) && !empty($address) && !empty($mothername)) 
            {
                CouriorsModel::UpdateCouriordata($editCouriorId, [
                    'ids' =>$ids,
                    'name' => $name,
                    'date' => $date,
                    'dateaddress' => $dateaddress,
                    'age' => $age,
                    'address' => $address,
                    'mothername' => $mothername
                ]);
            
                $editCourior = null;
                header("Location: " . Config::KECSO_URL_COURIORDATA);
                exit();
            }
            
        }

    // Új futár hozzáadása  
        if (KecsoRequest::CouriorsInsert()) 
        {
            $ids = $_POST['ids'] ?? '';
            $name = $_POST['name'] ?? '';
            $date = $_POST['date'] ?? '';
            $dateaddress = $_POST['dateaddress'] ?? '';
            $age = $_POST['age'] ?? '';
            $address = $_POST['address'] ?? '';
            $mothername = $_POST['mothername'] ?? '';
    // Ellenőrzés, hogy minden mező ki legyen töltve
            if  (!empty($ids) &&!empty($name) && !empty($date) && !empty($dateaddress) && !empty($age) && !empty($address) && !empty($mothername)) {
                CouriorsModel::InsertCouriordata([
                    'ids' => $ids,
                    'name' => $name,
                    'date' => $date,
                    'dateaddress' => $dateaddress,
                    'age' => $age,
                    'address' => $address,
                    'mothername' => $mothername
                ]);
 
                header("Location: " . Config::KECSO_URL_COURIORDATA);
                exit();
            } 
           
        }

    // Futár törlése  
        if (KecsoRequest::CouriorDelete()) {
            $deleteCouriorId = $_POST['deleteCouriorId'] ?? null;
            if ($deleteCouriorId) 
            {
    // Futár törlése az adatbázisból
                CouriorsModel::DeleteCouriordata($deleteCouriorId);
                header("Location: " . Config::KECSO_URL_COURIORDATA);
                exit();
            }
        }
    }
    // Futárok adatainak lekérése az adatbázisból
    
    $couriorData = CouriorsModel::GetCouriorData();
   
    $view .= KecsoCouriorView::CouriorData($couriorData, $editCourior,$id);
    $view .= IndexView::CloseSection();
    $view .= IndexView::End();
    
    if (isset($error) && !empty($error)) {
        $view .= $error;
    }
    
    return $view;
}
public function courioraddress($param): string
{
    $view = IndexView::Begin();
    $view .= IndexView::StartTitle('Kecskeméti depó főoldal');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter'])) {
        $startDate = $_POST['startDate'] ?? date('Y-m-01');
        $endDate = $_POST['endDate'] ?? date('Y-m-t');
    } else {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
    }

    $deliveries = CouriorsModel::SumDeliveredAddressesByDateAndGroup($startDate, $endDate);
    var_dump($deliveries);
    // Futár cím kezelése
    if (KecsoRequest::AddressInsert()) {
        $address = [
            'name' => $_POST['name'],
            'ids' => (int)$_POST['ids'], // ids számmá konvertálása
            'day' => (int)$_POST['day'],
            'month' => $_POST['month'],
            'time' => $_POST['time'],
            'total_addresses' => (int)$_POST['total_addresses'],
            'delivered_addresses' => (int)$_POST['delivered_addresses'],
            'final_return' => (int)$_POST['final_return'],
            'live_return' => (int)$_POST['live_return']
        ];
        CouriorsModel::InsertAddress($address);
        header("Location: " . Config::KECSO_URL_COURIORADDRESS);
        exit();
    }

    if (KecsoRequest::AddressDelete()) {
        $addressId = $_POST['deleteAddressId'];
        CouriorsModel::DeleteAddress($addressId);
        header("Location: " . Config::KECSO_URL_COURIORADDRESS);
        exit();
    }

    if (KecsoRequest::AddressUpdate()) {
        $editaddress = CouriorsModel::GetAddressById($_POST['updateAddressId']);
    }

    if (KecsoRequest::AddressSave()) {
        $address = [
            'name' => $_POST['name'],
            'ids' => (int)$_POST['ids'], 
            'day' => (int)$_POST['day'],
            'month' => $_POST['month'],
            'time' => $_POST['time'],
            'total_addresses' => (int)$_POST['total_addresses'],
            'delivered_addresses' => (int)$_POST['delivered_addresses'],
            'final_return' => (int)$_POST['final_return'],
            'live_return' => (int)$_POST['live_return']
        ];
        CouriorsModel::UpdateAddress($_POST['editAddressId'], $address);
        header("Location: " . Config::KECSO_URL_COURIORADDRESS);
        exit();
    }

    $addresses = CouriorsModel::GetAddresses();
    $view .= KecsoCouriorView::ShowDeliveriesByGroup($deliveries, $startDate, $endDate);
    $view .= KecsoCouriorView::CouriorsAddress($addresses, $editaddress ?? null);
    $view .= IndexView::End();

    return $view;
}





public static function depo($param):string
{
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Depó adatai');
  
    $editDepo=null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        // Depó szerkesztése
        if (KecsoRequest::DepoUpdate()) 
        {
            $editDepoId = $_POST['updateDepoId'];
            $editDepo = DeposModel::GetDepoById($editDepoId);
        }
       if (KecsoRequest::DepoSave()) 
        {
            $editDepoId = $_POST['editDepoId'];
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if (!empty($editDepoId) && !empty($title) && !empty($content)) 
            {
                DeposModel::UpdateDepodata($editDepoId, ['title' => $title, 'content' => $content]);
                // Mentés után ne legyen szerkesztési állapotban
                $editDepo = null;
                header("Location: " . Config::KECSO_URL_DEPO);
                exit();
            }
        }
        // Új depó adat hozzáadása
        if (KecsoRequest::DepoInsert()) 
        {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if (!empty($title) && !empty($content)) 
            {
                DeposModel::InsertDepodata(['title' => $title, 'content' => $content]);
        
            }
        }
        // Depó adat törlése
        if (KecsoRequest::DepoDelete()) 
        {
            $deleteDepoId = $_POST['deleteDepoId'];
            DeposModel::DeleteDepodata($deleteDepoId);
        }
    }
    $depodata = DeposModel::GetDepoData();
    $view .= KecsoDepoView::Depo($depodata,$editDepo);
    $view .= IndexView::CloseSection();
    return $view;
}  
  public static function disp($param):string
{
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Diszpécserek elérhetőségei');
    $dispdata = DispModel::GetDispdata(); 
    $editdisp = null; 

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (KecsoRequest::DispInsert()) {
            $name = $_POST['name'] ?? '';
            $title = $_POST['title'] ?? '';
            $phone = $_POST['phone'] ?? '';
            if (!empty($name) && !empty($title) && !empty($phone)) {
                DispModel::InsertDispdata(['name' => $name, 'title' => $title, 'phone' => $phone]);
                header("Location: " . Config::KECSO_URL_DISP);
                exit();
            }
            
        }

        if (KecsoRequest::DispSave()) {
            $editDispId = $_POST['editDispId'];
            $name = $_POST['name'] ?? '';
            $title = $_POST['title'] ?? '';
            $phone = $_POST['phone'] ?? '';
            if (!empty($editDispId) && !empty($name) && !empty($title) && !empty($phone)) {
                DispModel::UpdateDispdata($editDispId, ['name' => $name, 'title' => $title, 'phone' => $phone]);
                header("Location: " . Config::KECSO_URL_DISP);
                exit();
            }
        }

        if (KecsoRequest::DispDelete()) {
            $deleteDispId = $_POST['deleteDispId'];
            DispModel::DeleteDispdata($deleteDispId);
            header("Location: " . Config::KECSO_URL_DISP);
            exit();
        }

        if (KecsoRequest::DispUpdate()) {
            $editDispId = $_POST['updateDispId'] ?? '';
            
            if (!empty($editDispId)) {
                
                $editdisp = DispModel::GetDispById($editDispId);
            }
        }
    }

    $view .= KecsoDispView::Disp($dispdata,$editdisp);
    $view .= IndexView::CloseSection();
    return $view;
} 
}