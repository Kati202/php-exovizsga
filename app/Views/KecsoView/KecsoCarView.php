<?php
namespace App\Views\KecsoView;

use App\Models\KecsoModel\CarsModel;
use App\Views\IndexView;
use App\Config;

class KecsoCarView
{
public static function ShowCar()
    {
        
        $html ='';
        $html .= IndexView::OpenSection('Gépjárművek');
        $html .= '<form method="post" action="' . Config::KECSO_URL . '">';
        $html .= IndexView::CreateInput('Rendszám', 'license');
        $html .= '<button type="submit" name="newCar">Gépjármű hozzáadása</button>';
        $html .= '</form>';

        $html .= self::DisplayCars();

        $html .= IndexView::CloseSection();
        return $html;

    }
public static function CarData($carId,$uploadedFileName)
    {
        $html = '<form method="post" action="'. Config::KECSO_URL_CARDATA .'" enctype="multipart/form-data">';
        $html .= '<input type="hidden" name="carId" value="' . htmlspecialchars($carId) . '">';
        $html .= IndexView::CreateInput('Fálj neve', 'data');
        $html .= '<div>
                    <label for="file">Válassz egy fájlt:</label>
                    <input type="file" name="file" id="file">
                  </div>';
        $html .= '<button type="submit" name="upload">Fájl/kép hozzáadása</button>';
        $html .= '</form>';

        // Feltöltött képek listázása
        $html .= self::ListUploadedImages($uploadedFileName);

        return $html;
    }
    public static function HandleFileUpload(): string
   {
    $html = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
        $carId = $_POST['carId'];
        $file = $_FILES['file'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            $detectedType = mime_content_type($file['tmp_name']);

            if (in_array($detectedType, $allowedTypes)) {
                $uploadDir = 'uploads/';
                $selectedFolder = 'kecso';

                if (!is_dir($uploadDir . $selectedFolder)) {
                    mkdir($uploadDir . $selectedFolder, 0777, true);
                }

                $uniqueFileName = uniqid() . '_' . basename($file['name']);
                $uploadFile = $uploadDir . $selectedFolder . '/' . $uniqueFileName;

                if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                    $imageData = [
                        'data' => $_POST['data'],
                        'file' => $uniqueFileName
                    ];
                    CarsModel::InsertCarImage($carId, $imageData);

                    // Sikeres feltöltés esetén átirányítás
                    header("Location: " . Config::KECSO_URL_CARDATA);
                    exit();
                } else {
                    $html .= "Hiba történt a kép feltöltése során.";
                }
            } else {
                $html .= "Csak JPG, JPEG, PNG és GIF típusú képek feltöltése engedélyezett.";
            }
        } else {
            $html .= "Hiba történt a fájl feltöltése során: " . $_FILES['file']['error'];
        }
    }

    return $html;
   }
    public static function CarCost($carId)
    { 
        $html = '<form method="post" action="' . Config::KECSO_URL_CARCOST . '?param=' . htmlspecialchars($carId) . '">';
        $html .= IndexView::CreateInput('Időpont', 'date');
        $html .= IndexView::CreateInput('Alkatrész', 'part');
        $html .= IndexView::CreateInput('Ár', 'price');
        $html .= '<button type="submit" name="addCarCost">Költség hozzáadása</button>';
        $html .= '</form>';

        // Javítási költségek listázása
        $html .= self::DisplayCarCosts($carId);

        return $html;
    }
    public static function CarCostEdit($carCost)
    { 
        $carId = null;
        
        $html = '<form method="post" action="' . Config::KECSO_URL_CARCOST . '">';
        $html .= '<input type="hidden" name="editCostId" value="' . htmlspecialchars($carCost['_id']) . '">';
        $html .= IndexView::CreateInputValue('Időpont', 'date', date('Y-m-d H:i:s', $carCost['date']->toDateTime()->getTimestamp()));
        $html .= IndexView::CreateInputValue('Alkatrész', 'part', htmlspecialchars($carCost['part']));
        $html .= IndexView::CreateInputValue('Ár', 'price', htmlspecialchars($carCost['price']));
        $html .= '<button type="submit" name="saveCarCost">Mentés</button>';
        $html .= '</form>';
    
        return $html;
    }
    
   
    private static function DisplayCars()
    {
        $cars = CarsModel::GetCars();
        $html = '<table border="1" cellpadding="10" >
                    <thead>
                        <tr><th>Rendszám</th>
                            <th>Adatok</th>
                            <th>Költségek</th>
                            <th>Műveletek</th></tr>
                    </thead>
                    <tbody>';
        foreach ($cars as $car) 
        {
            $html .= '<tr>
                        <td>' . $car['license'] . '</td>
                        <td><a href="'.Config::KECSO_URL_CARDATA.'?operation=cardata&param=' . $car['_id'] .'">Gépjármű adatai</a></td>
                        <td><a href="'.Config::KECSO_URL_CARCOST.'?operation=carcost&param=' . $car['_id'] .'">Javítási költségek</a></td>
                        <td>
                            <form method="post" action="' . Config::KECSO_URL . '" style="display:inline;">
                                <input type="hidden" name="deleteCarId" value="' . $car['_id'] . '">
                                <button type="submit" name="deleteCar">Törlés</button>
                            </form>
                        </td>
                      </tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
    private static function ListUploadedImages($uploadedFileName) 
{
    $html = '<h2>Feltöltött képek:</h2>';

    if (!empty($uploadedFileName)) {
        // Elérési útvonal generálása a feltöltött fájlhoz
        $imageUrl = 'uploads/kecso/' . $uploadedFileName;

        $html .= '<ul>';
        $html .= '<li>';
        // Kép megjelenítése az <img> elem használatával
        $html .= '<img src="' . htmlspecialchars($imageUrl) . '" alt="Feltöltött kép">';
        // Fájl nevének kattintható linkként való megjelenítése
        $html .= '<p>Fájl neve: <a href="' . htmlspecialchars($imageUrl) . '" target="_blank">' . htmlspecialchars($uploadedFileName) . '</a></p>';
        // További adatok megjelenítése (opcionális)
        // pl. feltöltés dátuma, stb.
        $html .= '</li>';
        $html .= '</ul>';
    } else {
        $html .= '<p>Nincsenek feltöltött képek.</p>';
    }

    return $html;
}
    
    private static function DisplayCarCosts($carId, $editCarCost = null)
    {
        $carCosts = CarsModel::GetCarCosts($carId);
    
        usort($carCosts, function($a, $b) {
            return strcmp($a['part'], $b['part']);
        });
    
        $html = '<h3>Rögzített javítási költségek:</h3>';
    
        if (!empty($carCosts)) 
        {
            $html .= '<table border="1" cellpadding="10">
                        <thead>
                            <tr>
                                <th>Időpont</th>
                                <th>Alkatrész</th>
                                <th>Ár</th>
                                <th>Műveletek</th>
                            </tr>
                        </thead>
                        <tbody>';
    
            foreach ($carCosts as $cost) 
            {
                $html .= '<tr>
                            <td>' . date('Y-m-d H:i:s', $cost['date']->toDateTime()->getTimestamp()) . '</td>
                            <td>' . htmlspecialchars($cost['part']) . '</td>
                            <td>' . htmlspecialchars($cost['price']) . '</td>
                            <td>
                                <form method="post" action="' . Config::KECSO_URL_CARCOST . '?param=' . htmlspecialchars($carId) . '" style="display:inline;">
                                    <input type="hidden" name="costId" value="' . htmlspecialchars($cost['_id']) . '">
                                    <button type="submit" name="updateCarCost">Szerkesztés</button>
                                </form>
                                <form method="post" action="' . Config::KECSO_URL_CARCOST . '?param=' . htmlspecialchars($carId) . '" style="display:inline;">
                                    <input type="hidden" name="deleteCostId" value="' . htmlspecialchars($cost['_id']) . '">
                                    <button type="submit" name="deleteCost">Törlés</button>
                                </form>
                            </td>
                        </tr>';
    
                // Ha a szerkesztési gomb megnyomásra került, jelenjen meg a szerkesztési űrlap
                if ($editCarCost && $editCarCost['_id'] == $cost['_id']) 
                {
                    $html .= '<tr><td colspan="4">' . self::CarCostEdit($editCarCost) . '</td></tr>';
                }
            }
    
            $html .= '</tbody></table>';
        } 
        else 
        {
            $html .= '<p>Nincsenek rögzített javítási költségek.</p>';
        }
    
        return $html;
    }
}
  
