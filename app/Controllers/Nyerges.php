<?php
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use App\Views\IndexView;
use App\Views\NyergesView\NyergesCarView;
use App\Views\NyergesView\NyergesCouriorView;
use App\Views\NyergesView\NyergesDepoView;
use App\Views\NyergesView\NyergesDispView;
use App\Models\NyergesModel\CarsModel;
use App\Models\NyergesModel\CouriorsModel;
use App\Models\NyergesModel\DeposModel;
use App\Models\NyergesModel\DispModel;
use App\Requests\NyergesRequest;
use App\Config;

class Nyerges
{
public function nyerges(): string
{
        // Kezdeti nézet 
        $view = IndexView::Begin();
        $view .= IndexView::StartTitle('Nyergesújfalui depó főoldal');

      
       /* $view.=CarsModel::Init();
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
       

        $view .=KecsoCouriorView::ShowCourior();*/
        $view .=NyergesDepoView::ShowDepoButton();
        $view .=NyergesDispView::ShowDispButton();

        //Oldalzárás
        $view .= IndexView::End();

        return $view;
}
public function cardata4($param): string
{
 $carId = isset($param) ? $param : null;
 $view = IndexView::Begin();
 $view .= IndexView::StartTitle('Gépjármű adatai');
 $view .= CarsModel::Init();

$uploadedFileName = '';

// Fájlfeltöltés kezelése
$view .= KecsoCarView::HandleFileUpload();

// Űrlap megjelenítése és feltöltött képek listázása
$view .= KecsoCarView::CarData($carId, $uploadedFileName);

// Vége
$view .= IndexView::End();

return $view;}

public function carcost4($param): string
{
    $carId = isset($param) ? $param : null;
    $view = CarsModel::Init();
    //$view = CarsModel::InsertCarCost();
    $view .= IndexView::Begin();
    $view .= IndexView::StartTitle('Javítási költségek');
    $view .= KecsoCarView::CarCost($carId);
   

    $carCosts = CarsModel::GetCarCosts($carId);
    if (!empty($carCosts)) {
        // A költségek feldolgozása és hozzáadása a nézethez
        foreach ($carCosts as $cost) {
            $view .= '<p>Date: ' . $cost['date'] . ', Part: ' . $cost['part'] . ', Price: ' . $cost['price'] . '</p>';
        }
    } else {
        $view .= '<p>Nincsenek költségek.</p>';
    }
    

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        // Javítási költség hozzáadása
        if (KecsoRequest::AddCarCost()) 
        {
            $carCost = [
                'carId' => new \MongoDB\BSON\ObjectId($carId),  // Győződj meg róla, hogy az ObjectId helyesen van beállítva
                'date' => new \MongoDB\BSON\UTCDateTime(strtotime($_POST['date']) * 1000),
                'part' => $_POST['part'],
                'price' => floatval($_POST['price'])
            ];
            

            // Hívás a Model-be, hogy hozzáadjuk az új költséget
            $result = CarsModel::InsertCarCost($carCost);

            if ($result) 
            {
                header("Location: " . Config::KECSO_URL_CARCOST . "?param=" . $carId);
                exit();
            } 
            else 
            {
                $view .= '<p>Hiba történt a költség hozzáadása során.</p>';
            }
        }

        // Javítási költség szerkesztése
        if (KecsoRequest::EditCarCost()) 
        {
            $costId = $_POST['costId'];
            $carCost = [
                'date' => new \MongoDB\BSON\UTCDateTime(strtotime($_POST['date']) * 1000),
                'part' => $_POST['part'],
                'price' => floatval($_POST['price'])
            ];

            $result = CarsModel::UpdateCarCost($costId, $carCost);

            if ($result) 
            {
                header("Location: " . Config::KECSO_URL_CARCOST . "?param=" . $carId);
                exit();
            } 
            else 
            {
                $view .= '<p>Hiba történt a költség frissítése során.</p>';
            }
        }

        // Javítási költség törlése
        if (KecsoRequest::DeleteCarCost()) 
        {
            $costId = $_POST['deleteCostId'];
            $result = CarsModel::DeleteCarCost($costId);

            if ($result) 
            {
                header("Location: " . Config::KECSO_URL_CARCOST . "?param=" . $carId);
                exit();
            } 
            else 
            {
                $view .= '<p>Hiba történt a költség törlése során.</p>';
            }
        }
    }

    $view .= IndexView::End();
    return $view;
}
public function couriorData4($param): string
{
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Futár adatai');
    $view .= CouriorsModel::Init();

    $editCourior = null;
    $error='';
    $id=$_REQUEST['param'] ?? '100';
    

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
            $name = $_POST['name'] ?? '';
            $date = $_POST['date'] ?? '';
            $dateaddress = $_POST['dateaddress'] ?? '';
            $age = $_POST['age'] ?? '';
            $address = $_POST['address'] ?? '';
            $mothername = $_POST['mothername'] ?? '';
    // Ellenőrzés, hogy minden mező ki legyen töltve
            if (!empty($editCouriorId) && !empty($name) && !empty($date) && !empty($dateaddress) && !empty($age) && !empty($address) && !empty($mothername)) 
            {
                CouriorsModel::UpdateCouriordata($editCouriorId, [
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
        if (KecsoRequest::CouriorsInsert()) {
            $name = $_POST['name'] ?? '';
            $date = $_POST['date'] ?? '';
            $dateaddress = $_POST['dateaddress'] ?? '';
            $age = $_POST['age'] ?? '';
            $address = $_POST['address'] ?? '';
            $mothername = $_POST['mothername'] ?? '';
    // Ellenőrzés, hogy minden mező ki legyen töltve
            if (!empty($name) && !empty($date) && !empty($dateaddress) && !empty($age) && !empty($address) && !empty($mothername)) {
                CouriorsModel::InsertCouriordata([
                    'name' => $name,
                    'date' => $date,
                    'dateaddress' => $dateaddress,
                    'age' => $age,
                    'address' => $address,
                    'mothername' => $mothername
                ]);
 
                header("Location: " . $_SERVER['REQUEST_URI']);
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
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
    }
    // Futárok adatainak lekérése az adatbázisból
    
    $couriorData = CouriorsModel::GetCouriorDataById($id);
    $view .= KecsoCouriorView::CouriorData($couriorData, $editCourior);
    $view .= IndexView::CloseSection();
    $view .= IndexView::End();
    
    if (isset($error) && !empty($error)) {
        $view .= $error;
    }
    
    return $view;
}

public function courioraddress4($param): string
{
    // Futár cím kezelése itt
    /*úgy mint a dispet majd a hónap lesz group szempont csak a sorok száma(napok száma),név,időpont hónap,nap össz cím amivel kiment,leadott cím,véglegvissza,élővissza*/
    
    $view = IndexView::Begin();
    $view .= IndexView::StartTitle('Kecskeméti depó főoldal');

    // Futár cím kezelése
    if (KecsoRequest::AddressInsert()) {
        $address = [
            'day' => $_POST['day'],
            'month' => $_POST['month'],
            'time' => $_POST['time'],
            'total_addresses' => $_POST['total_addresses'],
            'delivered_addresses' => $_POST['delivered_addresses'],
            'final_return' => $_POST['final_return'],
            'live_return' => $_POST['live_return']
        ];
        CouriorsModel::InsertAddress($address);
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }

    if (KecsoRequest::AddressDelete()) {
        $addressId = $_POST['deleteAddressId'];
        CouriorsModel::DeleteAddress($addressId);
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }

    if (KecsoRequest::AddressUpdate()) {
        $editaddress = CouriorsModel::GetAddressById($_POST['updateAddressId']);
    }

    if (KecsoRequest::AddressSave()) {
            $address = [
                'day' => $_POST['day'],
                'month' => $_POST['month'],
                'time' => $_POST['time'],
                'total_addresses' => $_POST['total_addresses'],
                'delivered_addresses' => $_POST['delivered_addresses'],
                'final_return' => $_POST['final_return'],
                'live_return' => $_POST['live_return']
            ];
        CouriorsModel::UpdateAddress($_POST['editAddressId'], $address);
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }

    $addresses = CouriorsModel::GetAddresses();
    $view .= KecsoCouriorView::CouriorsAddress($addresses, $editaddress ?? null);
    $view .= IndexView::End();

    return $view;
}

public static function depo4($param):string
{
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Depó adatai');
  
    $editDepo=null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        // Depó szerkesztése
        if (NyergesRequest::DepoUpdate()) 
        {
            $editDepoId = $_POST['updateDepoId'];
            $editDepo = DeposModel::GetDepoById($editDepoId);
        }
       if (NyergesRequest::DepoSave()) 
        {
            $editDepoId = $_POST['editDepoId'];
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if (!empty($editDepoId) && !empty($title) && !empty($content)) 
            {
                DeposModel::UpdateDepodata($editDepoId, ['title' => $title, 'content' => $content]);
                // Mentés után ne legyen szerkesztési állapotban
                $editDepo = null;
                header("Location: " . Config::NYERGES_URL_DEPO);
                exit();
            }
        }
        // Új depó adat hozzáadása
        if (NyergesRequest::DepoInsert()) 
        {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if (!empty($title) && !empty($content)) 
            {
                DeposModel::InsertDepodata(['title' => $title, 'content' => $content]);
                header("Location: " . Config::NYERGES_URL_DEPO);
                exit();
            }
        }
        // Depó adat törlése
        if (NyergesRequest::DepoDelete()) 
        {
            $deleteDepoId = $_POST['deleteDepoId'];
            DeposModel::DeleteDepodata($deleteDepoId);
        }
    }
    $depodata = DeposModel::GetDepoData();
    $view .= NyergesDepoView::Depo($depodata,$editDepo);
    $view .= IndexView::CloseSection();
    return $view;
}  
  public static function disp4($param):string
{
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Diszpécserek elérhetőségei');
    $dispdata = DispModel::GetDispdata(); 
    $editdisp = null; 

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (NyergesRequest::DispInsert()) {
            $name = $_POST['name'] ?? '';
            $title = $_POST['title'] ?? '';
            $phone = $_POST['phone'] ?? '';
            if (!empty($name) && !empty($title) && !empty($phone)) {
                DispModel::InsertDispdata(['name' => $name, 'title' => $title, 'phone' => $phone]);
                header("Location: " . Config::NYERGES_URL_DISP);
                exit();
            }
            
        }

        if (NyergesRequest::DispSave()) {
            $editDispId = $_POST['editDispId'];
            $name = $_POST['name'] ?? '';
            $title = $_POST['title'] ?? '';
            $phone = $_POST['phone'] ?? '';
            if (!empty($editDispId) && !empty($name) && !empty($title) && !empty($phone)) {
                DispModel::UpdateDispdata($editDispId, ['name' => $name, 'title' => $title, 'phone' => $phone]);
                header("Location: " . Config::NYERGES_URL_DISP);
                exit();
            }
        }

        if (NyergesRequest::DispDelete()) {
            $deleteDispId = $_POST['deleteDispId'];
            DispModel::DeleteDispdata($deleteDispId);
            header("Location: " . Config::NYERGES_URL_DISP);
            exit();
        }

        if (NyergesRequest::DispUpdate()) {
            $editDispId = $_POST['updateDispId'] ?? '';
            
            if (!empty($editDispId)) {
                
                $editdisp = DispModel::GetDispById($editDispId);
            }
        }
    }

    $view .= NyergesDispView::Disp($dispdata,$editdisp);
    $view .= IndexView::CloseSection();
    return $view;
} 
}