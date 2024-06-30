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
        $view.=KecsoCarView::ShowCar();
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
       

        $view .=KecsoCouriorView::ShowCourior();
        $view .=IndexView::ShowDepoButton();
        $view .=IndexView::ShowDispButton();

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
        $view .= KecsoCarView::CarData($carId,$uploadedFileName);
        $view .= IndexView::End();

       return $view;
    }
 public function carcost($param): string
   {
     $carId = isset($param) ? $param : null;
      $view = CarsModel::Init();
      $view .= IndexView::Begin();
      $view .= IndexView::StartTitle('Javítási költségek');
      $view .= KecsoCarView::CarCost($carId);
    

    
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
/*public function couriordata($param): string
{
    // Futár adatok kezelése itt
    $view = IndexView::Begin();
    $view .= IndexView::StartTitle('Futár adatai');
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Depó adatai');
  
    $editcourior=null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        // Depó szerkesztése
        if (Request::UpdateCouriordata()) 
        {
            $editcouriorId = $_POST['updatecouriorId'];
            $editDepo = DeposModel::GetDepoById($editDepoId);
        }
       if (Request::DepoSave()) 
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
        if (Request::DepoInsert()) 
        {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if (!empty($title) && !empty($content)) 
            {
                DeposModel::InsertDepodata(['title' => $title, 'content' => $content]);
        
            }
        }
        // Depó adat törlése
        if (Request::DepoDelete()) 
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
    $view .= IndexView::End();

    return $view;
}*/

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
    $view = IndexView::Begin();
    $view .= IndexView::OpenSection('Depó adatai');
  
    $editDepo=null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        // Depó szerkesztése
        if (Request::DepoUpdate()) 
        {
            $editDepoId = $_POST['updateDepoId'];
            $editDepo = DeposModel::GetDepoById($editDepoId);
        }
       if (Request::DepoSave()) 
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
        if (Request::DepoInsert()) 
        {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            if (!empty($title) && !empty($content)) 
            {
                DeposModel::InsertDepodata(['title' => $title, 'content' => $content]);
        
            }
        }
        // Depó adat törlése
        if (Request::DepoDelete()) 
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
        if (Request::DispInsert()) {
            $name = $_POST['name'] ?? '';
            $title = $_POST['title'] ?? '';
            $phone = $_POST['phone'] ?? '';
            if (!empty($name) && !empty($title) && !empty($phone)) {
                DispModel::InsertDispdata(['name' => $name, 'title' => $title, 'phone' => $phone]);
                header("Location: " . Config::KECSO_URL_DISP);
                exit();
            }
            
        }

        if (Request::DispSave()) {
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

        if (Request::DispDelete()) {
            $deleteDispId = $_POST['deleteDispId'];
            DispModel::DeleteDispdata($deleteDispId);
            header("Location: " . Config::KECSO_URL_DISP);
            exit();
        }

        if (Request::DispUpdate()) {
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

