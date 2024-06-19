<?php
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use App\Views\IndexView;
use App\Views\KecsoView;
use App\Models\CarsModel;
use App\Models\CouriorsModel;
use App\Models\DeposModel;
use App\Requests\Request;
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
        if (Request::CarsInsert()) {
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
        $view.=KecsoView::ShowCar();
        //
        $view.=CouriorsModel::Init();
        //Új futár hozzáadása
        if (Request::CouriorsInsert()) {
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
       

        $view.=KecsoView::ShowCourior();
        $view .= DeposModel::Init();
        $view .= KecsoView::Depo();

        //Oldalzárás
        $view .= IndexView::End();

        return $view;
    }

    public function cardata($param): string
   {
        $carId = isset($param) ? $param : null;
        $view = IndexView::Begin();
        $view .= IndexView::StartTitle('Gépjármű adatai');
        $view.=CarsModel::Init();
       
        $uploadedFileName = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) 
    {
          
            $carId = $_POST['carId'];
            $file = $_FILES['file'];
    
    
        {
          if ($file['error'] === UPLOAD_ERR_OK) 
            {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                $detectedType = mime_content_type($file['tmp_name']);

                if (in_array($detectedType, $allowedTypes))
                 {
                    $uploadDir = 'uploads/';
                    $selectedFolder = 'kecso';

                    if (!is_dir($uploadDir . $selectedFolder)) 
                    {
                        mkdir($uploadDir . $selectedFolder, 0777, true);
                    }

                    $uploadFile = $uploadDir . $selectedFolder . '/' . basename($file['name']);

                    if (move_uploaded_file($file['tmp_name'], $uploadFile)) 
                    {
                        $uploadedFileName = basename($file['name']);
                        $imageData = [
                            'data' => $_POST['data'],
                            'file' => $uploadedFileName
                        ];
                        CarsModel::InsertImage($carId,$imageData);

                        $view .= "A kép sikeresen feltöltve: " . htmlspecialchars($file['name']);
                    } 
                    else 
                    {
                        $view .= "Hiba történt a kép feltöltése során.";
                    }
                } 
                else 
                {
                    $view .= "Csak JPG, JPEG, PNG és GIF típusú képek feltöltése engedélyezett.";
                }
            } 
            else 
            {
                $view .= "Hiba történt a fájl feltöltése során: " . $_FILES['file']['error'];
            }
        } 
       
    }
    $imageData = 
    [
        'fileName' => $uploadedFileName,
        'uploadDate' => new \MongoDB\BSON\UTCDateTime(strtotime('now') * 1000)
    ];

    CarsModel::InsertImage($carId, $imageData);


        // Kép feltöltés űrlap megjelenítése
        $carId = isset($_GET['param']) ? $_GET['param'] : null;
        $view .= KecsoView::CarData($carId,$uploadedFileName);
        $view .= IndexView::End();

       return $view;
    }
 public function carcost($param): string
   {
     $carId = isset($param) ? $param : null;
      $view = CarsModel::Init();
      $view .= IndexView::Begin();
      $view .= IndexView::StartTitle('Javítási költségek');
      $view .= KecsoView::CarCost($carId);
    

    
    // Űrlap megjelenítése
   
      $view .= IndexView::End();
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCarCost'])) 
      {
        $carCost = 
        [
            'carId' =>$carId,  // Az autó _id-je
            'date' => new \MongoDB\BSON\UTCDateTime(strtotime($_POST['date']) * 1000),
            'part' => $_POST['part'],
            'price' => floatval($_POST['price'])
        ];

        // Hívás a Model-be, hogy hozzáadjuk az új költséget
        $result = CarsModel::InsertCarCost($carCost);

        if ($result) 
        {
            $view = '<p>A költség sikeresen hozzáadva.</p>';
        } 
        else 
        {
            $view = '<p>Hiba történt a költség hozzáadása során.</p>';
        }
    }
    return $view;
}
public function couriordata($param): string
{
    // Futár adatok kezelése itt
    $view = IndexView::Begin();
    $view .= IndexView::StartTitle('Futár adatai');
    // Logika a futár adatok betöltéséhez
    $view .= IndexView::End();

    return $view;
}

public function courioraddress($param): string
{
    // Futár cím kezelése itt
    $view = IndexView::Begin();
    $view .= IndexView::StartTitle('Futár címmennyisége');
    // Logika a futár cím betöltéséhez
    $view .= IndexView::End();

    return $view;
}
public static function depo($param):string
{
        $depodata = DeposModel::GetDepoData();

        $view = IndexView::OpenSection('Depó adatai');

        foreach ($depodata as $data) {
            $view .= '<p><strong>' . htmlspecialchars($data['title']) . '</strong>: ' . htmlspecialchars($data['content']) . '</p>';
        }

        $view .= IndexView::CloseSection();

        return $view;
}  
}
