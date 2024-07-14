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
    public static function kecso()
    {
        session_start();

        $view = IndexView::Begin();
        $view .= IndexView::StartTitle('Kecskeméti depó főoldal');
        
        // Gépjárművek kezelése
        $view .= CarsModel::Init();

        // Üzenetek megjelenítése
        if (isset($_SESSION['success_message'])) {
            $view .= '<p class="success-message">' . $_SESSION['success_message'] . '</p>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            $view .= '<p class="error-message">' . $_SESSION['error_message'] . '</p>';
            unset($_SESSION['error_message']);
        }
        
        // Új gépjármű hozzáadása
        if (KecsoRequest::CarsInsert()) {
            $ids = $_POST['ids'] ?? '';
            if (!empty($ids)) {
                $car = ['ids' => $ids];
                $result = CarsModel::InsertCar($car);
                if ($result) {
                    $_SESSION['success_message'] = 'Az autó sikeresen hozzá lett adva.';
                } else {
                    $_SESSION['error_message'] = 'Már létezik ilyen azonosítóval autó.';
                }
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            } else {
                $_SESSION['error_message'] = 'Gépjármű hozzáadásához az űrlap mező kitöltése kötelező!';
            }
        }
        
        // Gépjármű törlése
        if (isset($_POST['deleteCar'])) {
            $carId = $_POST['deleteCarId'] ?? '';
            if (!empty($carId)) {
                CarsModel::DeleteCar($carId);
                $_SESSION['success_message'] = 'Az autó sikeresen törölve lett.';
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
        
        // Gépjárművek megjelenítése
        $view .= KecsoCarView::ShowCar();
        
        // Futárok kezelése
        if (KecsoRequest::CouriorInsert()) {
            $ids = $_POST['ids'] ?? '';
            $name = $_POST['name'] ?? '';
        
            // Azonosító validálása: csak számok lehetnek
            if (empty($ids) || empty($name)) {
                $_SESSION['error_message'] = 'Futár hozzáadáshoz minden mező kitöltése kötelező!';
            } else if (ctype_digit($ids) && preg_match('/^[\p{L}\s]+$/u', $name)) {
                // Az $name változóban csak betűk és szóközök lehetnek
                $courior = ['ids' => (int)$ids, 'name' => $name];
                $result = CouriorsModel::InsertCouriors($courior);
                if ($result) {
                    $_SESSION['success_message'] = 'A futár sikeresen hozzá lett adva.';
                } else {
                    $_SESSION['error_message'] = 'Már létezik ilyen azonosítóval vagy névvel futár.';
                }
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            } else {
                $_SESSION['error_message'] = 'Az azonosító mező csak számokat tartalmazhat, a név mező pedig csak betűket!';
            }
        }
        
        // Futár törlése
        if (isset($_POST['deleteCourior'])) {
            $couriorId = $_POST['deleteCouriorId'] ?? '';
            if (!empty($couriorId)) {
                CouriorsModel::DeleteCouriors($couriorId);
                $_SESSION['success_message'] = 'A futár sikeresen törölve lett.';
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
        
        // Futárok megjelenítése növekvő sorrendben 
        $couriors = CouriorsModel::GetCouriors();
        usort($couriors, function($a, $b) {
            return $a['ids'] - $b['ids'];
        });
        $view .= KecsoCouriorView::ShowCourior($couriors);
    
        // Gombok megjelenítése
        $view .= KecsoDepoView::ShowDepoButton();
        $view .= KecsoDispView::ShowDispButton();
    
        // Oldalzárás
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter'])) {
        $startDate = $_POST['startDate'] ?? date('Y-m-01');
        $endDate = $_POST['endDate'] ?? date('Y-m-t');
    } else {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
    }

    $cars = CarsModel::SumCostByDateAndGroup($startDate, $endDate);
   

    // Autó beszúrás 
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
    //Autó törlése
    if (KecsoRequest::CarCostDelete()) {
        $carcostId = $_POST['deleteCarcostId'];
        CarsModel::DeleteCarCost($carcostId);
        header("Location: " . Config::KECSO_URL_CARCOST);
        exit();
    }
    //Autó szerkesztése
    $editcarcost = null;
    if (KecsoRequest::CarCostUpdate()) {
        $editcarcost = CarsModel::GetCarCostById($_POST['updateCarCostId']);
    }
    //Szerkesztés mentése
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
    $view .= KecsoCarView::ShowCostByGroup($cars, $startDate, $endDate);
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
public static function depo($param): string
{
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Depó adatai');
    
    $editDepo = null;
    $errorMessages = []; // Hibaüzenetek tárolására
    
    // Session kezelés inicializálása
    session_start();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Depó szerkesztése előkészítése
        if (KecsoRequest::DepoUpdate()) {
            $editDepoId = $_POST['updateDepoId'] ?? null;
            if (!empty($editDepoId)) {
                $editDepo = DeposModel::GetDepoById($editDepoId);
            }
        }
    
        // Depó mentése
        if (KecsoRequest::DepoSave()) {
            $editDepoId = $_POST['editDepoId'] ?? null;
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if (empty($editDepoId) || empty($title) || empty($content)) {
                $_SESSION['error_message'] = 'Minden mező kitöltése kötelező!';
            } else {
                DeposModel::UpdateDepodata($editDepoId, ['title' => $title, 'content' => $content]);
                $_SESSION['success_message'] = 'Az adat sikeresen frissítve lett.';
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
    
        // Új depó hozzáadása
        if (KecsoRequest::DepoInsert()) {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if (empty($title) || empty($content)) {
                $_SESSION['error_message'] = 'Minden mező kitöltése kötelező!';
            } else {
                DeposModel::InsertDepodata(['title' => $title, 'content' => $content]);
                $_SESSION['success_message'] = 'A depóadat sikeresen hozzá lett adva.';
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
    
        // Depó törlése
        if (KecsoRequest::DepoDelete()) {
            $deleteDepoId = $_POST['deleteDepoId'] ?? null;
            if (!empty($deleteDepoId)) {
                DeposModel::DeleteDepodata($deleteDepoId);
                $_SESSION['success_message'] = 'Az adat sikeresen törölve lett.';
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
    }
    
    // Megjelenítjük a hibaüzenetet, ha van
    if (isset($_SESSION['error_message'])) {
        $view .= '<div>';
        $view .= '<p>' . htmlspecialchars($_SESSION['error_message']) . '</p>';
        $view .= '</div>';
        unset($_SESSION['error_message']);
    }
    
    // Megjelenítjük a sikeres üzenetet, ha van
    if (isset($_SESSION['success_message'])) {
        $view .= '<div>';
        $view .= '<p>' . htmlspecialchars($_SESSION['success_message']) . '</p>';
        $view .= '</div>';
        unset($_SESSION['success_message']);
    }
    
    $depodata = DeposModel::GetDepoData();
    $view .= KecsoDepoView::Depo($depodata, $editDepo);
    $view .= IndexView::CloseSection();
    return $view;
    
}


public static function disp($param): string
{
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Diszpécserek elérhetőségei');
    $dispdata = DispModel::GetDispdata(); 
    $editdisp = null; 

    // Hibaüzenetek tömbje
    $errors = [];

    // Űrlap beküldésének és validációinak kezelése
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Új diszpécser hozzáadása
        if (KecsoRequest::DispInsert()) {
            $name = $_POST['name'] ?? '';
            $title = $_POST['title'] ?? '';
            $phone = $_POST['phone'] ?? '';

            // Alap validáció: üres mezők ellenőrzése
            if (empty($name) || empty($title) || empty($phone)) {
                $errors[] = 'Minden mező kitöltése kötelező.';
            } else {
                // Specifikus validáció: név és munkaterület mező betű formátum ellenőrzése
                if (!self::isValidNameFormat($name)) {
                    $errors[] = 'A Név mező csak betűket és megengedett egyéb karaktereket tartalmazhat.';
                }
                if (!self::isValidTitleFormat($title)) {
                    $errors[] = 'A Munkaterület mező csak betűket és megengedett egyéb karaktereket tartalmazhat.';
                }

                // Telefonszám validáció
                if (!empty($phone) && !self::isValidPhoneFormat($phone)) {
                    $errors[] = 'A Telefonszám formátuma nem megfelelő.';
                }

                if (empty($errors)) {
                    DispModel::InsertDispdata(['name' => $name, 'title' => $title, 'phone' => $phone]);
                    header("Location: " . Config::KECSO_URL_DISP);
                    exit();
                }
            }
        }

        // Törlés
        if (KecsoRequest::DispDelete()) {
            $deleteDispId = $_POST['deleteDispId'];
            DispModel::DeleteDispdata($deleteDispId);
            header("Location: " . Config::KECSO_URL_DISP);
            exit();
        }

        // Frissítés
        if (KecsoRequest::DispUpdate()) {
            $editDispId = $_POST['updateDispId'] ?? '';
            
            if (!empty($editDispId)) {
                $editdisp = DispModel::GetDispById($editDispId);
            }
        }
    }

    // Ha van validációs hiba, megjelenítjük
    if (!empty($errors)) {
        foreach ($errors as $error) {
            $view .= '<p style="color: red;">' . htmlspecialchars($error) . '</p>';
        }
    }

    // Megjelenítjük a fő nézetet és az űrlapot
    $view .= KecsoDispView::Disp($dispdata, $editdisp);
    $view .= IndexView::CloseSection();
    return $view;
}

private static function isValidPhoneFormat($phone)
{
    // Elfogadott karakterek: számok, szóköz, +, és a következő speciális karakterek: ()/-.
    return preg_match('/^(06)(((20|30|70)[0-9]{7})|((?!(30|20|70))[0-9]{8,9}))$/', $phone) || preg_match('/^(\+?[\d\s-]+)$/', $phone);
}

private static function isValidNameFormat($name) 
{
    // Elfogadott karakterek: betűk, szóköz, és a következő speciális karakterek: .,-
    return preg_match('/^[A-Za-zÁÉÍÓÖŐÚÜŰáéíóöőúüű\s.,-]+$/', $name);
}

private static function isValidTitleFormat($title) 
{
    // Elfogadott karakterek: betűk, szóköz, és a következő speciális karakterek: .,-
    return preg_match('/^[A-Za-zÁÉÍÓÖŐÚÜŰáéíóöőúüű\s.,-]+$/', $title);
}




}