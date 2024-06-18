<?php
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use App\Views\IndexView;
use App\Views\KecsoView;
use App\Models\Model;
use App\Requests\Request;

class Kecso
{
    public function kecso(): string
    {
        // Kezdeti nézet 
        $view = IndexView::Begin();
        $view .= IndexView::StartTitle('Kecskeméti depó főoldal');

        // Adatbázis inicializálása
        $view.=Model::Init();
        // Új gépjármű hozzáadása
         if (Request::CarsInsert()) {
            $car = [
                'license' => $_POST['license']
            ];
            Model::InsertCar($car);
            header("Location: " . $_SERVER['REQUEST_URI']); 
            exit();
        }

        // Gépjármű törlése
        if (isset($_POST['deleteCar'])) {
            $carId = $_POST['deleteCarId'];
            Model::DeleteCar($carId);
            header("Location: " . $_SERVER['REQUEST_URI']); 
            exit();
        }

        // Főoldal megjelenítése
        $view.=KecsoView::Show();

        // Oldalzárás
        $view .= IndexView::End();

        return $view;
    }

    public function cardata($param): string
    {
        $view = IndexView::Begin();
        $view .= IndexView::StartTitle('Gépjármű adatai');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
            $file = $_FILES['file'];
            
            if ($file['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                $detectedType = mime_content_type($file['tmp_name']);
        
                if (in_array($detectedType, $allowedTypes)) {
                    $uploadDir = 'uploads/';
        
                    // Mappa kiválasztása az id alapján
                    $selectedFolder = 'kecso'; // Például a 'kecso' mappa
        
                    // Ellenőrzés, hogy a mappa létezik-e, ha nem, létrehozzuk
                    if (!is_dir($uploadDir . $selectedFolder)) {
                        mkdir($uploadDir . $selectedFolder, 0777, true);
                    }
        
                    $uploadFile = $uploadDir . $selectedFolder . '/' . basename($file['name']);
        
                    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                        $view .= "A kép sikeresen feltöltve: " . htmlspecialchars($file['name']);
                    } else {
                        $view .= "Hiba történt a kép feltöltése során.";
                    }
                } else {
                    $view .= "Csak JPG, JPEG, PNG és GIF típusú képek feltöltése engedélyezett.";
                }
            } else {
                $view .= "Hiba történt a fájl feltöltése során: " . $_FILES['file']['error'];
            }
        }
        

        // Kép feltöltés űrlap megjelenítése
        $view .= KecsoView::CarData();
        $view .= IndexView::End();

       return $view;
    }
    public function carcost($param): string
{
    $view = IndexView::Begin();
    $view .= IndexView::StartTitle('Javítási költségek');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCarCost'])) {
        $carCost = [
            'carId' => $_POST['carId'], // Az autó _id-je
            'date' => new \MongoDB\BSON\UTCDateTime(strtotime($_POST['date']) * 1000),
            'part' => $_POST['part'],
            'price' => floatval($_POST['price'])
        ];

        // Hívás a Model-be, hogy hozzáadjuk az új költséget
        $result = Model::InsertCarCost($carCost);

        if ($result) {
            $view .= '<p>A költség sikeresen hozzáadva.</p>';
        } else {
            $view .= '<p>Hiba történt a költség hozzáadása során.</p>';
        }
    }

    // Űrlap megjelenítése
    $view .= KecsoView::CarCost();
    $view .= IndexView::End();

    return $view;
}

    
}
