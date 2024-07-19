<?php
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use App\Views\IndexView;
use App\Views\HalasView\HalasCarView;
use App\Views\HalasView\HalasCouriorView;
use App\Views\HalasView\HalasDepoView;
use App\Views\HalasView\HalasDispView;
use App\Models\HalasModel\CarsModel;
use App\Models\HalasModel\CouriorsModel;
use App\Models\HalasModel\DeposModel;
use App\Models\HalasModel\DispModel;
use App\Requests\KecsoRequest;
use App\Config;
use App\DatabaseManager;

class Halas extends BaseController
{
    public function halas(): string
    {
        
        $view = IndexView::Begin();
        $view .= IndexView::StartTitle('Halasi depó főoldal');

        // Gépjárművek kezelése
        $view .= CarsModel::Init();

        // Üzenetek megjelenítése
        if (isset($_SESSION['success_message'])) {
            $view .= '<p class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            $view .= '<p class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
            unset($_SESSION['error_message']);
        }

        // Új gépjármű hozzáadása
        if (KecsoRequest::CarsInsert()) {
            $ids = $_POST['ids'] ?? '';
            if (!empty($ids)) {
                $car = ['ids' => $ids];
                $result = CarsModel::InsertCar($car);
                if ($result) {
                    $_SESSION['success_message'] = 'A gépjármű rendszáma sikeresen hozzáadva.';
                } else {
                    $_SESSION['error_message'] = 'Már létezik ilyen rendszámmal rendelkező gépjármű.';
                }
            } else {
                $_SESSION['error_message'] = 'Gépjármű rendszámának hozzáadáshoz az űrlap mező kitöltése kötelező!';
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        // Gépjármű törlése
        if (isset($_POST['deleteCar'])) {
            $carId = $_POST['deleteCarId'] ?? '';
            if (!empty($carId)) {
                CarsModel::DeleteCar($carId);
                $_SESSION['success_message'] = 'A gépjűrmű rendszáma sikeresen törölve.';
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        // Gépjárművek megjelenítése
        $view .= HalasCarView::ShowCar();

        // Futárok kezelése
        if (KecsoRequest::CouriorInsert()) {
            $ids = $_POST['ids'] ?? '';
            $name = $_POST['name'] ?? '';

            // Azonosító validálása: 
            if (empty($ids) || empty($name)) {
                $_SESSION['error_message'] = 'Futár név hozzáadáshoz minden mező kitöltése kötelező';
            } else if (ctype_digit($ids) && preg_match('/^[\p{L}\s]+$/u', $name)) {
                $courior = ['ids' => (int) $ids, 'name' => $name];
                $result = CouriorsModel::InsertCouriors($courior);
                if ($result) {
                    $_SESSION['success_message'] = 'A futár neve sikeresen hozzáadva a táblához.';
                } else {
                    $_SESSION['error_message'] = 'Már létezik ilyen azonosítóval vagy névvel futár.';
                }
            } else {
                $_SESSION['error_message'] = 'Az azonosító mező csak számokat tartalmazhat, a név mező pedig csak betűket!';
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        // Futár törlése
        if (isset($_POST['deleteCourior'])) {
            $couriorId = $_POST['deleteCouriorId'] ?? '';
            if (!empty($couriorId)) {
                CouriorsModel::DeleteCouriors($couriorId);
                $_SESSION['success_message'] = 'A futár neve sikeresen törölve a táblából.';
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        // Futárok megjelenítése növekvő sorrendben
        $couriors = CouriorsModel::GetCouriors();
        usort($couriors, function ($a, $b) {
            return $a['ids'] - $b['ids'];
        });
        $view .= HalasCouriorView::ShowCourior($couriors);

        // Gombok megjelenítése
        $view .= HalasDepoView::ShowDepoButton();
        $view .= HalasDispView::ShowDispButton();

        // Oldalzárás
        $view .= IndexView::End();

        return $view;
    }
    public function cardata3($param): string
    {
        $view = IndexView::Begin();
        $view .= IndexView::StartTitle('Kiskunhalasi depó gépjárműveinek tankolással kapcsolatos információi');
        
        $errors = [];
        $result = '';
        $beforeKm = '';
        $afterKm = '';
        $totalLiters = '';
    
        // Új gépjármű adat hozzáadása
        if (isset($_POST['newCarData'])) {
            $cardata = [
                'ids' => trim($_POST['ids']),
                'km' => trim($_POST['km']),
                'liters' => trim($_POST['liters']),
                'date' => date('Y-m-d H:i:s', strtotime($_POST['date'])),
            ];
    
            // Validálás
            if (empty($cardata['ids']) || empty($cardata['date']) || empty($cardata['km']) || empty($cardata['liters'])) {
                $errors[] = 'Minden mező kitöltése kötelező.';
            } elseif (!is_numeric($cardata['km']) || $cardata['km'] < 0) {
                $errors[] = 'A kilométer nem lehet negatív szám vagy betű.';
            } elseif (!is_numeric($cardata['liters']) || $cardata['liters'] < 0) {
                $errors[] = 'A liter nem lehet negatív szám vagy betű.';
            }
    
            if (empty($errors)) {
                // Új adat hozzáadása
                CarsModel::InsertCarData($cardata);
                $_SESSION['success'] = 'A gépjármű tankolásának adata sikeresen hozzáadva.';
                header("Location: " . Config::HALAS_URL_CARDATA);
                exit();
            } else {
                $_SESSION['errors'] = $errors;
                header("Location: " . Config::HALAS_URL_CARDATA);
                exit();
            }
        }
    
        // Gépjármű adat törlése
        if (isset($_POST['deleteCarDataId'])) {
            $carDataId = $_POST['deleteCarDataId'];
            CarsModel::DeleteCarData($carDataId);
            $_SESSION['success'] = 'A gépjármű tankolásának adata sikeresen törölve.';
            header("Location: " . Config::HALAS_URL_CARDATA);
            exit();

        }
    
        // Gépjármű adat szerkesztése
        $editCarData = null;
        if (isset($_POST['updateCarData'])) {
            $editCarData = CarsModel::GetCarDataById($_POST['updateCarDataId']); 
            
        }
    
        // Gépjármű adat szerkesztésének mentése
        if (isset($_POST['saveCarData'])) {
            $cardata = [
                'ids' => trim($_POST['ids']),
                'km' => trim($_POST['km']),
                'liters' => trim($_POST['liters']),
                'date' => date('Y-m-d H:i:s', strtotime($_POST['date'])),
            ];
    
            // Validálás
            if (empty($cardata['ids']) || empty($cardata['date']) || empty($cardata['km']) || empty($cardata['liters'])) {
                $errors[] = 'Minden mező kitöltése kötelező.';
            } elseif (!is_numeric($cardata['km']) || $cardata['km'] < 0) {
                $errors[] = 'A kilométer nem lehet negatív szám vagy betű.';
            } elseif (!is_numeric($cardata['liters']) || $cardata['liters'] < 0) {
                $errors[] = 'A liter nem lehet negatív szám vagy betű.';
            }
    
            if (empty($errors)) {
                // Adat frissítése
                CarsModel::UpdateCarData($_POST['editCarDataId'], $cardata);
                $_SESSION['success'] = 'A gépjármű adat sikeresen frissítve.';
                header("Location: " . Config::HALAS_URL_CARDATA);
                exit();
            } else {
                $_SESSION['errors'] = $errors;
                header("Location: " . Config::HALAS_URL_CARDATA);
                exit();
            }
        }
    
        // Átlagfogyasztás számítás kezelése
        if (isset($_POST['calculateConsumption'])) {
            $beforeKm = isset($_POST['previousKm']) ? (float)$_POST['previousKm'] : 0;
            $afterKm = isset($_POST['currentKm']) ? (float)$_POST['currentKm'] : 0;
            $totalLiters = isset($_POST['totalLiters']) ? (float)$_POST['totalLiters'] : 0;
    
            if ($afterKm > $beforeKm) {
                $distanceTravelled = $afterKm - $beforeKm;
                if ($distanceTravelled > 0) {
                    $averageConsumption = ($totalLiters / $distanceTravelled) * 100;
                    $result = number_format($averageConsumption, 2) . ' liter/100 km';
                } else {
                    $errors[] = 'A jelenlegi kilométerállás nem lehet kisebb, mint az előző kilométerállás.';
                }
            } else {
                $errors[] = 'A jelenlegi kilométerállás nem lehet kisebb, mint az előző kilométerállás.';
            }
    
            if (empty($errors)) {
                $_SESSION['success'] = 'Az átlagfogyasztás sikeresen kiszámítva.';
                $_SESSION['result'] = $result;
            } else {
                $_SESSION['errors'] = $errors;
            }
    
            header("Location: " . Config::HALAS_URL_CARDATA);
            exit();
        }
        
        // Megjelenítés: sikeres üzenetek, hibaüzenetek kezelése
        if (isset($_SESSION['success'])) {
            $view .= '<div class="success-message">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
    
        if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
            foreach ($_SESSION['errors'] as $error) {
                $view .= '<div class="error-message">' . $error . '</div>';
            }
            unset($_SESSION['errors']);
        }
    
        
        if (isset($_SESSION['result'])) {
            $result = $_SESSION['result'];
            unset($_SESSION['result']);
        } else {
            $result = '';
        }
    
        // Gépjármű adatok lekérése és megjelenítése
        $carData = CarsModel::GetCarData();
        
        $view .= HalasCarView::CarData($carData, $editCarData);
        
        $view .= HalasCarView::RenderForm($result, $beforeKm, $afterKm, $totalLiters);
    
        $view .= IndexView::End();
    
        return $view;
    }

public function carcost3($param): string {
    $view = IndexView::Begin();
    $view .= IndexView::StartTitle('Kiskunhalasi depó gépjárműveinek költségei');

    /*if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter'])) {
        $startDate = $_POST['startDate'] ?? date('Y-m-01');
        $endDate = $_POST['endDate'] ?? date('Y-m-t');
    } else {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
    }

    // Autók költségeinek összesítése dátum és csoport szerint
    $cars = CarsModel::SumCostByDateAndGroup($startDate, $endDate);*/

    $errors = [];

    // Autó költség beszúrása
    if (isset($_POST['newCarCost'])) {
        $carcost = [
            'ids' => $_POST['ids'],
            'date' => date('Y-m-d H:i:s', strtotime($_POST['date'])),
            'part' => $_POST['part'],
            'cost' => $_POST['cost'],
        ];
        
        if (empty($carcost['ids']) || empty($carcost['date']) || empty($carcost['part']) || empty($carcost['cost'])) {
            $errors[] = 'Minden mező kitöltése kötelező.';
        }
        if (!is_numeric($carcost['cost']) || $carcost['cost'] < 0) {
            $errors[] = 'A költség nem lehet negatív szám vagy betű.';
        }

        if (empty($errors)) {
            $carcost['cost'] = IndexView::customRound($carcost['cost']);
            CarsModel::InsertCarCost($carcost);
            $_SESSION['success'] = 'A javítás költsége sikeresen hozzáadva.';
            header("Location: " . Config::HALAS_URL_CARCOST);
            exit();
        } else {
            $_SESSION['errors'] = $errors;
            header("Location: " . Config::HALAS_URL_CARCOST);
            exit();
        }
    }

    // Autó költség törlése
    if (isset($_POST['deleteCarcost'])) {
        $carcostId = $_POST['deleteCarcostId'];
        CarsModel::DeleteCarCost($carcostId);
        $_SESSION['success'] = 'A javítás költsége sikeresen törölve.';
        header("Location: " . Config::HALAS_URL_CARCOST);
        exit();
    }

    // Autó költség szerkesztése 
    $editcarcost = null;
    if (isset($_POST['updateCarcost'])) {
        $editcarcost = CarsModel::GetCarCostById($_POST['updateCarCostId']);
    }

    
    if (isset($_POST['saveCarCost'])) {
        $carcost = [
            'ids' => $_POST['ids'],
            'date' => date('Y-m-d H:i:s', strtotime($_POST['date'])),
            'part' => $_POST['part'],
            'cost' => $_POST['cost'],
        ];

        if (empty($carcost['ids']) || empty($carcost['date']) || empty($carcost['part'])) {
            $errors[] = 'Minden mező kitöltése kötelező.';
        }

        if (!is_numeric($carcost['cost']) || $carcost['cost'] < 0) {
            $errors[] = 'A költség nem lehet negatív szám vagy betű.';
        }

        if (empty($errors)) {
            $carcost['cost'] = IndexView::customRound($carcost['cost']);
            CarsModel::UpdateCarCost($_POST['editCarCostId'], $carcost);
            $_SESSION['success'] = 'A javítás költsége sikeresen frissítve.';
            header("Location: " . Config::HALAS_URL_CARCOST);
            exit();
        } else {
            $_SESSION['errors'] = $errors;
            header("Location: " . Config::HALAS_URL_CARCOST);
            exit();
        }
    }

    // Megjelenítés: sikeres üzenetek, hibaüzenetek kezelése
    if (isset($_SESSION['success'])) {
        $view .= '<div class="success-message">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
        foreach ($_SESSION['errors'] as $error) {
            $view .= '<div class="error-message">' . $error . '</div>';
        }
        unset($_SESSION['errors']);
    }

    // Autó költségek lekérése és megjelenítése
    $carcost = CarsModel::GetCarCost();
    //$view .= HalasCarView::ShowCostByGroup($cars, $startDate, $endDate);
    $view .= HalasCarView::CarCost($carcost, $editcarcost);
    $view .= IndexView::End();

    return $view;
}

public function couriorData3($param): string
{
    $view = IndexView::Begin();
   
     
    // Sikeres üzenetek megjelenítése
    if (isset($_SESSION['error_message'])) {
        $html .= '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
    
    if (isset($_SESSION['success_message'])) {
        $view .= '<p class="success-message">' . $_SESSION['success_message'] . '</p>';
        unset($_SESSION['success_message']);
    }

    $error = '';

    // Új futár hozzáadása  
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && KecsoRequest::CouriorsInsert()) {
        $ids = $_POST['ids'] ?? '';
        $name = $_POST['name'] ?? '';
        $date = $_POST['date'] ?? '';
        $dateaddress = $_POST['dateaddress'] ?? '';
        $age = $_POST['age'] ?? '';
        $address = $_POST['address'] ?? '';
        $mothername = $_POST['mothername'] ?? '';

        
        if (empty($ids) || empty($name) || empty($date) || empty($dateaddress) || empty($age) || empty($address) || empty($mothername)) {
            $error = '<p class="error-message">Minden mező kitöltése kötelező!</p>';
        } elseif (!ctype_digit($ids)) {
            $error = '<p class="error-message">Az azonosító mező csak számot tartalmazhat!</p>';
        }

        if (empty($error)) {
            try {
                CouriorsModel::InsertCouriordata([
                    'ids' => (int)$ids,
                    'name' => $name,
                    'date' => $date,
                    'dateaddress' => $dateaddress,
                    'age' => $age,
                    'address' => $address,
                    'mothername' => $mothername
                ]);

                $_SESSION['success_message'] = 'A futár adatai sikeresen hozzáadva.';
                header("Location: " . Config::HALAS_URL_COURIORDATA);
                exit();
            } catch (Exception $e) {
                $error = '<p class="error-message">' . $e->getMessage() . '</p>';
            }
        }
    }

    // Futár törlése  
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && KecsoRequest::CouriorDelete()) {
        $deleteCouriorId = $_POST['deleteCouriorId'] ?? null;
        if ($deleteCouriorId) {
            CouriorsModel::DeleteCouriordata($deleteCouriorId);
            $_SESSION['success_message'] = 'A futár adatai sikeresen törölve.';
            header("Location: " . Config::HALAS_URL_COURIORDATA);
            exit();
        }
    }

    // Futár szerkesztése
    $editCourior = null;
    $id = $_REQUEST['param'] ?? '';
    if ($id && $_SERVER['REQUEST_METHOD'] === 'POST' && KecsoRequest::CouriorUpdate()) {
        $editCouriorId = $_POST['updateCouriorId'] ?? null;
        if ($editCouriorId) {
            $editCourior = CouriorsModel::GetCouriorDataById($editCouriorId);
        }
    }

    // Futár mentése
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && KecsoRequest::CouriorSave()) {
        $editCouriorId = $_POST['editCouriorId'] ?? null;
        $ids = (int)$_POST['ids'] ?? '';
        $name = $_POST['name'] ?? '';
        $date = $_POST['date'] ?? '';
        $dateaddress = $_POST['dateaddress'] ?? '';
        $age = $_POST['age'] ?? '';
        $address = $_POST['address'] ?? '';
        $mothername = $_POST['mothername'] ?? '';

       
        if (empty($ids) || empty($name) || empty($date) || empty($dateaddress) || empty($age) || empty($address) || empty($mothername)) {
            $error = '<p class="error-message">Minden mező kitöltése kötelező!</p>';
        } elseif (!ctype_digit((string)$ids)) {
            $error = '<p class="error-message">Az azonosító mező csak számot tartalmazhat!</p>';
        }

        if (empty($error)) {
            try {
                CouriorsModel::UpdateCouriordata($editCouriorId, [
                    'ids' => (int)$ids,
                    'name' => $name,
                    'date' => $date,
                    'dateaddress' => $dateaddress,
                    'age' => $age,
                    'address' => $address,
                    'mothername' => $mothername
                ]);

                $_SESSION['success_message'] = 'A futáradatai sikeresen frissítve.';
                header("Location: " . Config::HALAS_URL_COURIORDATA);
                exit();
            } catch (Exception $e) {
                $error = '<p class="error-message">' . $e->getMessage() . '</p>';
            }
        }
    }

    
    $couriorData = CouriorsModel::GetCouriorData();
   
    // Futárok megjelenítése
    $view .= IndexView::OpenSection('Futár adatai');
    $view .= HalasCouriorView::CouriorData($couriorData, $editCourior, $id);
    $view .= IndexView::CloseSection();

    if (!empty($error)) {
        $view .= $error;
    }

    $view .= IndexView::End();

    return $view;
}
public function courioraddress3($param): string
{
    
    $view = IndexView::Begin();
    $ids = 'deliveryIds';
    
    if (isset($_SESSION['success_message'])) {
        $view .= '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }

    if (isset($_SESSION['error_message'])) {
        $view .= '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }

    $startDate = date('Y-m-01');
    $endDate = date('Y-m-t');

    //Szűrés kezelése
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter'])) {
        if (isset($_POST['startDate']) && strtotime($_POST['startDate']) !== false) {
            $startDate = date('Y-m-d', strtotime($_POST['startDate']));
        } else {
            $startDate = date('Y-m-d');
        }
        if (isset($_POST['endDate']) && strtotime($_POST['endDate']) !== false) {
            $endDate = date('Y-m-d', strtotime($_POST['endDate']));
        } else {
            $endDate = date('Y-m-d');
        }
    }
    $filterParams = [
        'startDate' => $startDate,
        'endDate' => $endDate,
        'ids' => isset($_POST['ids']) ? $_POST['ids'] : []
    ];

    //Szűrés megjelenítése
    $deliveries = CouriorsModel::SumDeliveredAddressesByDateAndGroup($startDate, $endDate);

    $view .= HalasCouriorView::ShowDeliveriesByGroup($deliveries, $startDate, $endDate, $ids);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newAddress'])) {
       
        if (!ctype_digit($_POST['ids'])) {
            $_SESSION['error_message'] = 'Az azonosító mező csak számot tartalmazhat.';
            header("Location: " . Config::HALAS_URL_COURIORADDRESS);
            exit();
        }

        
        $requiredFields = ['name', 'ids', 'day', 'month', 'time', 'total_addresses', 'delivered_addresses'];
        $error = '';

        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $error = '<p class="error-message">Minden mező kitöltése kötelező!</p>';
                break;
            }
        }

        if (empty($error)) {
            try {
               
                $address = [
                    'name' => $_POST['name'],
                    'ids' => (int)$_POST['ids'],
                    'day' => (int)$_POST['day'],
                    'month' => $_POST['month'],
                    'time' => date('Y-m-d H:i:s', strtotime($_POST['time'])),
                    'total_addresses' => (int)$_POST['total_addresses'],
                    'delivered_addresses' => (int)$_POST['delivered_addresses'],
                    'final_return' => isset($_POST['final_return']) ? (int)$_POST['final_return'] : 0,
                    'live_return' => isset($_POST['live_return']) ? (int)$_POST['live_return'] : 0
                ];
                // Cím hozzáadása
                CouriorsModel::InsertAddress($address);
                $_SESSION['success_message'] = 'Új címadat sikeresen hozzáadva.';
                header("Location: " . Config::HALAS_URL_COURIORADDRESS);
                exit();
            } catch (Exception $e) {
                $_SESSION['error_message'] = 'Hiba történt a cím hozzáadása során: ' . $e->getMessage();
                header("Location: " . Config::HALAS_URL_COURIORADDRESS);
                exit();
            }
        } else {
            $_SESSION['error_message'] = $error;
            header("Location: " . Config::HALAS_URL_COURIORADDRESS);
            exit();
        }
    }

    // Futár törlése
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteAddressId'])) {
        $addressId = $_POST['deleteAddressId'];
        try {
            CouriorsModel::DeleteAddress($addressId);
            $_SESSION['success_message'] = 'Címadat sikeresen törölve.';
            header("Location: " . Config::HALAS_URL_COURIORADDRESS);
            exit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Hiba történt a cím törlése során: ' . $e->getMessage();
            header("Location: " . Config::HALAS_URL_COURIORADDRESS);
            exit();
        }
    }

    // Futár szerkesztése
    $editaddress = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateAddressId'])) {
        $editaddress = CouriorsModel::GetAddressById($_POST['updateAddressId']);
        if ($editaddress) {
            $editaddress['time'] = date('Y-m-d\TH:i', strtotime($editaddress['time']));
        }
    }

    // Futár mentése
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editAddressId'])) {
        $editAddressId = $_POST['editAddressId'];
        
        $requiredFields = ['name', 'ids', 'day', 'month', 'time', 'total_addresses', 'delivered_addresses'];
        $error = '';

        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $error = '<p class="error-message">Minden mező kitöltése kötelező!</p>';
                break;
            }
        }

        if (empty($error)) {
            try {
                $address = [
                    'name' => $_POST['name'],
                    'ids' => (int)$_POST['ids'],
                    'day' => (int)$_POST['day'],
                    'month' => $_POST['month'],
                    'time' => date('Y-m-d H:i:s', strtotime($_POST['time'])),
                    'total_addresses' => (int)$_POST['total_addresses'],
                    'delivered_addresses' => (int)$_POST['delivered_addresses'],
                    'final_return' => isset($_POST['final_return']) ? (int)$_POST['final_return'] : 0,
                    'live_return' => isset($_POST['live_return']) ? (int)$_POST['live_return'] : 0
                ];
                //Szerkesztése
                CouriorsModel::UpdateAddress($editAddressId, $address);
                $_SESSION['success_message'] = 'Cím sikeresen frissítve.';
                header("Location: " . Config::HALAS_URL_COURIORADDRESS);
                exit();
            } catch (Exception $e) {
                $_SESSION['error_message'] = 'Hiba történt a cím frissítése során: ' . $e->getMessage();
                header("Location: " . Config::HALAS_URL_COURIORADDRESS);
                exit();
            }
        } else {
            $_SESSION['error_message'] = $error;
            header("Location: " . Config::HALAS_URL_COURIORADDRESS);
            exit();
        }
    }

    
    $addresses = CouriorsModel::GetAddresses();

    $view .= HalasCouriorView::CouriorsAddress($addresses, $editaddress);
    $view .= IndexView::End();

    return $view;
}
public static function depo3($param): string
{
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Depó adatai');
    
    $editDepo = null;
    $errorMessages = []; 
    
    
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Depó szerkesztése 
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
                $_SESSION['success_message'] = 'Az adat sikeresen frissítve.';
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
    
        // Új depó adat hozzáadása
        if (KecsoRequest::DepoInsert()) {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if (empty($title) || empty($content)) {
                $_SESSION['error_message'] = 'Minden mező kitöltése kötelező!';
            } else {
                DeposModel::InsertDepodata(['title' => $title, 'content' => $content]);
                $_SESSION['success_message'] = 'A depóadat sikeresen hozzáadva.';
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
    
        // Depó törlése
        if (KecsoRequest::DepoDelete()) {
            $deleteDepoId = $_POST['deleteDepoId'] ?? null;
            if (!empty($deleteDepoId)) {
                DeposModel::DeleteDepodata($deleteDepoId);
                $_SESSION['success_message'] = 'Az adat sikeresen törölve.';
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
    }
    
    if (isset($_SESSION['error_message'])) {
        $view .= '<div>';
        $view .= '<p>' . htmlspecialchars($_SESSION['error_message']) . '</p>';
        $view .= '</div>';
        unset($_SESSION['error_message']);
    }
    
    if (isset($_SESSION['success_message'])) {
        $view .= '<div>';
        $view .= '<p>' . htmlspecialchars($_SESSION['success_message']) . '</p>';
        $view .= '</div>';
        unset($_SESSION['success_message']);
    }
    
    $depodata = DeposModel::GetDepoData();
    $view .= HalasDepoView::Depo($depodata, $editDepo);
    $view .= IndexView::CloseSection();
    return $view;
    
}
public function disp3($param): string
{   
    
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Diszpécserek elérhetőségei');
    $dispdata = DispModel::GetDispdata(); 
    $editdisp = null; 

    $errors = [];
    $successMessages = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Új diszpécser hozzáadása
        if (isset($_POST['newDisp'])) {
            $name = $_POST['name'] ?? '';
            $title = $_POST['title'] ?? '';
            $phone = $_POST['phone'] ?? '';

            
            if (empty($name) || empty($title) || empty($phone)) {
                $errors[] = 'Minden mező kitöltése kötelező.';
            } else {
                // Specifikus validáció
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
                    $_SESSION['success'] = 'A diszpécser sikeresen hozzáadva.';
                    header("Location: " . Config::HALAS_URL_DISP);
                    exit();
                }
            }
        }

        // Törlés
        if (isset($_POST['deleteDisp'])) {
            $deleteDispId = $_POST['deleteDispId'];
            DispModel::DeleteDispdata($deleteDispId);
            $_SESSION['success'] = 'A diszpécser sikeresen törölve.';
            header("Location: " . Config::HALAS_URL_DISP);
            exit();
        }

        // Szerkesztés
        if (isset($_POST['updateDisp'])) {
            $editDispId = $_POST['updateDispId'] ?? '';
            
            if (!empty($editDispId)) {
                $editdisp = DispModel::GetDispById($editDispId);
            }
        }

        if (isset($_POST['saveDisp'])) {
            $editDispId = $_POST['editDispId'] ?? '';
            $name = $_POST['name'] ?? '';
            $title = $_POST['title'] ?? '';
            $phone = $_POST['phone'] ?? '';
            
            if (empty($name) || empty($title) || empty($phone)) {
                $errors[] = 'Minden mező kitöltése kötelező.';
            } else {
                
                if (!self::isValidNameFormat($name)) {
                    $errors[] = 'A Név mező csak betűket és megengedett egyéb karaktereket tartalmazhat.';
                }
                if (!self::isValidTitleFormat($title)) {
                    $errors[] = 'A Munkaterület mező csak betűket és megengedett egyéb karaktereket tartalmazhat.';
                }

                if (!empty($phone) && !self::isValidPhoneFormat($phone)) {
                    $errors[] = 'A Telefonszám formátuma nem megfelelő.';
                }

                if (empty($errors)) {
                    DispModel::UpdateDispdata($editDispId, ['name' => $name, 'title' => $title, 'phone' => $phone]);
                    $_SESSION['success'] = 'A diszpécser adatai sikeresen frissítve.';
                    header("Location: " . Config::HALAS_URL_DISP);
                    exit();
                }
            }
        }
    }

    if (isset($_SESSION['success'])) {
        $view .= '<div class="success-message">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }

     if (!empty($errors)) {
        foreach ($errors as $error) {
            $view .= '<div class="error-message">' . $error . '</div>';
        }
    }

    $view .= HalasDispView::Disp($dispdata, $editdisp);
    $view .= IndexView::CloseSection();
    $view .= IndexView::End();

    return $view;
}
private static function isValidPhoneFormat($phone)
{
    
    return preg_match('/^(06)(((20|30|70)[0-9]{7})|((?!(30|20|70))[0-9]{8,9}))$/', $phone) || preg_match('/^(\+?[\d\s-]+)$/', $phone);
}

private static function isValidNameFormat($name) 
{
    
    return preg_match('/^[A-Za-zÁÉÍÓÖŐÚÜŰáéíóöőúüű\s.,-]+$/', $name);
}

private static function isValidTitleFormat($title) 
{
    
    return preg_match('/^[A-Za-zÁÉÍÓÖŐÚÜŰáéíóöőúüű\s.,-]+$/', $title);
}

}

    
