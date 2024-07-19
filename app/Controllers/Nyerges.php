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
use App\Requests\KecsoRequest;
use App\Config;
use App\DatabaseManager;

class Nyerges extends BaseController
{
public function nyerges(): string
{
       
        
       

        // Bejelentkezés kezelése
        /*if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            // MongoDB kapcsolat inicializálása
            $databaseManager = new DatabaseManager();
            $usersCollection = $databaseManager->connectToMongoDB()->users;

            $username = $_POST['kecso'];
            $password = $_POST['kecso12345'];

            // Keresés a felhasználók között
            $user = $usersCollection->findOne(['username' => $username, 'password' => $password]);

            if ($user) {
                $_SESSION['user_id'] = (string) $user['_id'];
                $_SESSION['username'] = $user['username'];
                header('Location: ' . Config::BASE_URL . 'kecso'); // Vagy a megfelelő oldal, ahova irányítani szeretnéd
                exit();
            } else {
                // Sikertelen bejelentkezés
                $_SESSION['error_message'] = 'Hibás felhasználónév vagy jelszó.';
                header('Location: ' . Config::BASE_URL . 'login.php');
                exit();
            }
        }*/

        // Oldal tartalmának összeállítása
        $view = IndexView::Begin();
        $view .= IndexView::StartTitle('Nyergesújfalui depó főoldal');

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
                $_SESSION['success_message'] = 'A gépjármű rendszáma sikeresen törölve.';
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        // Gépjárművek megjelenítése
        $view .= NyergesCarView::ShowCar();

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
                $_SESSION['success_message'] = 'A futár neve sikeresen törölve a táblából .';
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        // Futárok megjelenítése növekvő sorrendben
        $couriors = CouriorsModel::GetCouriors();
        usort($couriors, function ($a, $b) {
            return $a['ids'] - $b['ids'];
        });
        $view .= NyergesCouriorView::ShowCourior($couriors);

        // Gombok megjelenítése
        $view .= NyergesDepoView::ShowDepoButton();
        $view .= NyergesDispView::ShowDispButton();

        // Oldalzárás
        $view .= IndexView::End();

        return $view;
    }
    


    
public function cardata4($param): string
 {


  $carId = isset($param) ? $param : null;
  $view = IndexView::Begin();
  $view .= IndexView::StartTitle('Gépjármű adatai');
  $view .= CarsModel::Init();



   // Fájlfeltöltés kezelése
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $view .= NyergesCarView::HandleFileUpload();
   }

   // Kép törlésének kezelése
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteImage'])) {
    $carId = $_POST['carId'];
    $imageId = $_POST['deleteImageId'];

    $deleted = CarsModel::DeleteCarImage($carId, $imageId);
    if ($deleted) {
        $_SESSION['success'] = 'A kép sikeresen törölve.';
    } else {
        $_SESSION['errors'] = ['Hiba történt a kép törlése során.'];
    }
 }


    $view .= NyergesCarView::CarData($carId);

    $view .= IndexView::End();

   return $view;
}

public function carcost4($param): string {
    $view = IndexView::Begin();
    $view .= IndexView::StartTitle('Nyergesújfalui depó gépjárműveinek költségei');

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
            header("Location: " . Config::NYERGES_URL_CARCOST);
            exit();
        } else {
            $_SESSION['errors'] = $errors;
            header("Location: " . Config::NYERGES_URL_CARCOST);
            exit();
        }
    }

    // Autó költség törlése
    if (isset($_POST['deleteCarcost'])) {
        $carcostId = $_POST['deleteCarcostId'];
        CarsModel::DeleteCarCost($carcostId);
        $_SESSION['success'] = 'A javítás költsége sikeresen törölve.';
        header("Location: " . Config::NYERGES_URL_CARCOST);
        exit();
    }

    // Autó költség szerkesztése 
    $editcarcost = null;
    if (isset($_POST['updateCarcost'])) {
        $editcarcost = CarsModel::GetCarCostById($_POST['updateCarCostId']);
    }

    // Autó költség szerkesztésének mentése
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
            header("Location: " . Config::NYERGES_URL_CARCOST);
            exit();
        } else {
            $_SESSION['errors'] = $errors;
            header("Location: " . Config::NYERGES_URL_CARCOST);
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
    //$view .= NyergesCarView::ShowCostByGroup($cars, $startDate, $endDate);
    $view .= NyergesCarView::CarCost($carcost, $editcarcost);
    $view .= IndexView::End();

    return $view;
}

public function couriorData4($param): string
{
    $view = IndexView::Begin();
    
     
    // Sikeres üzenetek megjelenítéseű
    if (isset($_SESSION['error_message'])) {
        $html .= '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
    
    if (isset($_SESSION['success_message'])) {
        $view .= '<p class="success-message">' . $_SESSION['success_message'] . '</p>';
        unset($_SESSION['success_message']);
    }

    

    // Hibaüzenetek kezelése
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

        // Ellenőrizzük, hogy minden mező ki legyen töltve
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
                header("Location: " . Config::NYERGES_URL_COURIORDATA);
                exit();
            } catch (Exception $e) {
                $error = '<p class="error-message">' . $e->getMessage() . '</p>';
            }
        }
    }

    // Egyéb POST kérések kezelése

    // Futár törlése  
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && KecsoRequest::CouriorDelete()) {
        $deleteCouriorId = $_POST['deleteCouriorId'] ?? null;
        if ($deleteCouriorId) {
            CouriorsModel::DeleteCouriordata($deleteCouriorId);
            $_SESSION['success_message'] = 'A futár adtai sikeresen törölve.';
            header("Location: " . Config::NYERGES_URL_COURIORDATA);
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

                $_SESSION['success_message'] = 'A futár adatai sikeresen frissítve.';
                header("Location: " . Config::NYERGES_URL_COURIORDATA);
                exit();
            } catch (Exception $e) {
                $error = '<p class="error-message">' . $e->getMessage() . '</p>';
            }
        }
    }

    // Futárok adatainak lekérése az adatbázisból
    $couriorData = CouriorsModel::GetCouriorData();
   
    // Futárok megjelenítése
    $view .= IndexView::OpenSection('Futár adatai');
    $view .= NyergesCouriorView::CouriorData($couriorData, $editCourior, $id);
    $view .= IndexView::CloseSection();

    // Hibaüzenet megjelenítése
    if (!empty($error)) {
        $view .= $error;
    }

    $view .= IndexView::End();

    return $view;
}
public function courioraddress4($param): string
{
    // Oldal kezdete és session kezelése
    $view = IndexView::Begin();
    $ids = 'deliveryIds';
   

    // Sikeres és hibaüzenetek kezelése
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

    
    $deliveries = CouriorsModel::SumDeliveredAddressesByDateAndGroup($startDate, $endDate);

    $view .= NyergesCouriorView::ShowDeliveriesByGroup($deliveries, $startDate, $endDate, $ids);

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newAddress'])) {
       
        if (!ctype_digit($_POST['ids'])) {
            $_SESSION['error_message'] = 'Az azonosító mező csak számot tartalmazhat.';
            header("Location: " . Config::NYERGES_URL_COURIORADDRESS);
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
                $_SESSION['success_message'] = 'Új cím adat sikeresen hozzáadva.';
                header("Location: " . Config::NYERGES_URL_COURIORADDRESS);
                exit();
            } catch (Exception $e) {
                $_SESSION['error_message'] = 'Hiba történt a cím hozzáadása során: ' . $e->getMessage();
                header("Location: " . Config::NYERGES_URL_COURIORADDRESS);
                exit();
            }
        } else {
            $_SESSION['error_message'] = $error;
            header("Location: " . Config::NYERGES_URL_COURIORADDRESS);
            exit();
        }
    }

    // Egyéb POST kérések kezelése (törlés, szerkesztés, mentés)

    // Futár törlése
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteAddressId'])) {
        $addressId = $_POST['deleteAddressId'];
        try {
            CouriorsModel::DeleteAddress($addressId);
            $_SESSION['success_message'] = 'Cím adat sikeresen törölve.';
            header("Location: " . Config::NYERGES_URL_COURIORADDRESS);
            exit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Hiba történt a cím törlése során: ' . $e->getMessage();
            header("Location: " . Config::NYERGES_URL_COURIORADDRESS);
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
        // Szükséges adatok ellenőrzése
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
                // Adatok frissítése az adatbázisban
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

                CouriorsModel::UpdateAddress($editAddressId, $address);
                $_SESSION['success_message'] = 'Cím adat sikeresen frissítve.';
                header("Location: " . Config::NYERGES_URL_COURIORADDRESS);
                exit();
            } catch (Exception $e) {
                $_SESSION['error_message'] = 'Hiba történt a cím frissítése során: ' . $e->getMessage();
                header("Location: " . Config::NYERGES_URL_COURIORADDRESS);
                exit();
            }
        } else {
            $_SESSION['error_message'] = $error;
            header("Location: " . Config::NYERGES_URL_COURIORADDRESS);
            exit();
        }
    }

    // Futárcímek lekérése az adatbázisból
    $addresses = CouriorsModel::GetAddresses();

    $view .= NyergesCouriorView::CouriorsAddress($addresses, $editaddress);
    $view .= IndexView::End();

    return $view;
}
public static function depo4($param): string
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
                $_SESSION['success_message'] = 'Az adat sikeresen frissítve.';
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
    $view .= NyergesDepoView::Depo($depodata, $editDepo);
    $view .= IndexView::CloseSection();
    return $view;
    
}
public function disp4($param): string
{   
    session_start();
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Diszpécserek elérhetőségei');
    $dispdata = DispModel::GetDispdata(); 
    $editdisp = null; 

    // Hibaüzenetek tömbje
    $errors = [];

    // Sikeres üzenetek tömbje
    $successMessages = [];

    // Űrlap beküldésének és validációinak kezelése
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Új diszpécser hozzáadása
        if (isset($_POST['newDisp'])) {
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
                    $_SESSION['success'] = 'A diszpécser sikeresen hozzáadva.';
                    header("Location: " . Config::NYERGES_URL_DISP);
                    exit();
                }
            }
        }

        // Törlés
        if (isset($_POST['deleteDisp'])) {
            $deleteDispId = $_POST['deleteDispId'];
            DispModel::DeleteDispdata($deleteDispId);
            $_SESSION['success'] = 'A diszpécser sikeresen törölve.';
            header("Location: " . Config::NYERGES_URL_DISP);
            exit();
        }

        // Frissítés előkészítése
        if (isset($_POST['updateDisp'])) {
            $editDispId = $_POST['updateDispId'] ?? '';
            
            if (!empty($editDispId)) {
                $editdisp = DispModel::GetDispById($editDispId);
            }
        }

        // Frissítés mentése
        if (isset($_POST['saveDisp'])) {
            $editDispId = $_POST['editDispId'] ?? '';
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
                    DispModel::UpdateDispdata($editDispId, ['name' => $name, 'title' => $title, 'phone' => $phone]);
                    $_SESSION['success'] = 'A diszpécser adatai sikeresen frissítve.';
                    header("Location: " . Config::NYERGES_URL_DISP);
                    exit();
                }
            }
        }
    }

    // Sikerüzenet megjelenítése
    if (isset($_SESSION['success'])) {
        $view .= '<div class="success-message">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }

    // Hibák megjelenítése
    if (!empty($errors)) {
        foreach ($errors as $error) {
            $view .= '<div class="error-message">' . $error . '</div>';
        }
    }

    $view .= NyergesDispView::Disp($dispdata, $editdisp);
    $view .= IndexView::CloseSection();
    $view .= IndexView::End();

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