<?php
namespace App\Views\KecsoView;

use App\Models\KecsoModel\CarsModel;
use App\Views\IndexView;
use App\Config;

class KecsoCarView
{
//Fő oldal   
public static function ShowCar()
    {
        
        $html ='';
        $html .= IndexView::OpenSection('Gépjárművek hozzáadása');
        $html .= '<form method="post" action="' . Config::KECSO_URL . '">';
        $html .= IndexView::CreateInput('Rendszám', 'ids');
        $html .= '<button type="submit" name="newCar">Gépjármű hozzáadása</button>';
        $html .= '</form>';

        $html .= '<h3>Gépjárművek listája</h3>';
        $html .= self::DisplayCars();

        $html .= IndexView::CloseSection();
        return $html;

    }
private static function DisplayCars()
 {
    $cars = CarsModel::GetCars();
    $html = '<table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Rendszám</th>
                        <th>Műveletek</th>
                    </tr>
                </thead>
                <tbody>';

    if (!empty($cars)) {
        foreach ($cars as $car) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($car['ids'] ?? '') . '</td>
                        <td>
                            <form method="post" action="' . Config::KECSO_URL . '" style="display:inline;">
                                <input type="hidden" name="deleteCarId" value="' . htmlspecialchars($car['_id'] ?? '') . '">
                                <button type="submit" name="deleteCar">Törlés</button>
                            </form>
                        </td>
                      </tr>';
        }
        $html .= '</tbody></table>';

        
        $html .= '<div class="buttonss">';
        $html .= '<a href="'.Config::KECSO_URL_CARDATA.'">Gépjárművek adatai</a>';
        $html .= '<a href="'.Config::KECSO_URL_CARCOST.'">Javítási költségek</a>';
        $html .= '</div>';
    } else {
        $html .= '<tr><td colspan="2">Nincsenek elérhető autók</td></tr>';
        $html .= '</tbody></table>';
    }

    return $html;
}
//Cardata aloldal
public static function CarData($carId)
{
    $html = '<form method="post" action="'. htmlspecialchars(Config::KECSO_URL_CARDATA) .'" enctype="multipart/form-data">';
    $html .= '<input type="hidden" name="carId" value="'. htmlspecialchars((string)$carId) .'">';
    $html .= IndexView::CreateInput('Fájl neve', 'data');
    $html .= '<div>
                <label for="file">Válassz egy fájlt:</label>
                <input type="file" name="file" id="file">
              </div>';
    $html .= '<button type="submit" name="upload">Fájl/kép hozzáadása</button>';
    $html .= '</form>';

    // Feltöltött képek listázása
    $html .= self::ListUploadedImages($carId);

    return $html;
}
public static function HandleFileUpload(): string
{
    $html = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
        $carId = $_POST['carId'];
        $file = $_FILES['filename'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            $detectedType = mime_content_type($file['tmp_name']);

            if (in_array($detectedType, $allowedTypes)) {
                $uploadDir = 'uploads/';
                $selectedFolder = 'kecso/';

                if (!is_dir($uploadDir . $selectedFolder)) {
                    mkdir($uploadDir . $selectedFolder, 0777, true);
                }

                $uniqueFileName = uniqid() . '_' . basename($file['name']);
                $uploadFile = $uploadDir . $selectedFolder . $uniqueFileName;

                if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                    $imageData = [
                        'filename' => $uniqueFileName  // Csak a fájlnév tárolása
                    ];

                    // Beszúrjuk az adatbázisba csak a fájlnévvel
                    $inserted = CarsModel::InsertCarImage($carId, $imageData);

                    if ($inserted) {
                        // Sikeres beszúrás esetén átirányítás
                        header("Location: " . htmlspecialchars(Config::KECSO_URL_CARDATA) . "?param=" . htmlspecialchars($carId));
                        exit();
                    } else {
                        $html .= "Hiba történt a kép feltöltése során.";
                    }
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
    
private static function ListUploadedImages($carId)
{
    $uploadDir = 'uploads/kecso/';
    $carImages = CarsModel::GetCarImages($carId);

    $html = '<h2>Feltöltött képek:</h2>';

    if (!empty($carImages)) {
        $html .= '<ul>';
        foreach ($carImages as $image) {
            $imageUrl = $uploadDir . $image['filename']; // Felhasználjuk a 'filename' mezőt

            $html .= '<li>';
            $html .= '<img src="' . htmlspecialchars($imageUrl) . '" alt="Feltöltött kép">';
            $html .= '<p>Fájl neve: <a href="' . htmlspecialchars($imageUrl) . '" target="_blank">' . htmlspecialchars($image['data']) . '</a></p>';
            $html .= '<form method="post">';
            $html .= '<input type="hidden" name="carId" value="'. htmlspecialchars($carId) .'">';
            $html .= '<input type="hidden" name="deleteImageId" value="'. htmlspecialchars($image['_id']) .'">'; // '_id' mező az adatbázisban
            $html .= '<button type="submit" name="deleteImage">Kép törlése</button>';
            $html .= '</form>';
            $html .= '</li>';
        }
        $html .= '</ul>';
    } else {
        $html .= '<p>Nincsenek feltöltött képek.</p>';
    }

    return $html;
}

//Carcost aloldal 
  public static function CarCost($carcost, $editcarcost = null)
    {
        $html = '';
        $groupedData = self::GroupDataCarCost($carcost);
       
        foreach ($groupedData as $ids => $items) 
        {
            $html .= self::DisplayCarCostGroup($ids, $items, $editcarcost);
        }
    
        $html .= self::CreateCarCostForm();
        return $html;
    }
    
    private static function GroupDataCarCost($carcost)
    {
        $groupedData = [];

        foreach ($carcost as $car) {
           
            $ids = $car['ids'];
            
            if (!isset($groupedData[$ids])) {
                $groupedData[$ids] = [];
            }
            $groupedData[$ids][] = [
                'ids' => $car['ids'],
                'date' => $car['date'],
                'part' => $car['part'],
                'cost' => $car['cost'],
               '_id' => (string) $car['_id'], 
            ];
        }

        return $groupedData;
    }
    
    private static function DisplayCarCostGroup($ids, $items, $editcarcost)
    {
        $html = '';
    
        if (!empty($ids)) {
            $html .= '<h2>' . htmlspecialchars($ids) . '</h2>';
        }
    
        $html .= '<table border="1" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Rendszám</th>
                            <th>Időpont</th>
                            <th>Alkatrész</th>
                            <th>Költségek/Ft-ben</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>';
    
        foreach ($items as $item) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($item['ids'] ?? '') . '</td>
                        <td>' . htmlspecialchars(date('Y-m-d H:i', strtotime($item['date']->toDateTime()->format('Y-m-d H:i')))) . '</td>
                        <td>' . htmlspecialchars($item['part'] ?? '') . '</td>
                        <td>' . htmlspecialchars($item['cost'] ?? '') . '</td>
                        <td>
                           <form method="post" action="' . Config::KECSO_URL_CARCOST . '?operation=carcost&param=' . htmlspecialchars($item['_id'] ?? '') . '" style="display:inline;">
                             <input type="hidden" name="updateCarCostId" value="' . ($item['_id'] ?? '') . '">
                             <button type="submit" name="updateCarcost">Szerkesztés</button>
                            </form>
                                <form method="post" action="' . Config::KECSO_URL_CARCOST . '" style="display:inline;">
                                <input type="hidden" name="deleteCarcostId" value="' . ($item['_id'] ?? '') . '">
                                <input type="hidden" name="operation" value="carcost">
                                <input type="hidden" name="param" value="' . ($item['_id'] ?? '') . '">
                                <button type="submit" name="deleteCarcost">Törlés</button>
                            </form>
                        
                        </td>
                    </tr>';
    
            // Ha a szerkesztési gomb megnyomásra került, jelenjen meg a szerkesztési űrlap
            if ($editcarcost && $editcarcost['_id'] == $item['_id']) {
                $html .= '<tr><td colspan="10">' . self::CarCostEdit($editcarcost) . '</td></tr>';
            }
        }
    
        $html .= '</tbody></table>';
    
        return $html;
    }
    public static function ShowConfirmDeleteScript()
   {
    return '<script src="app/public/src/confirmDelete.js"></script>';
   }
    public static function CreateCarCostForm()
    {
        $html = '<form method="post" action="' . Config::KECSO_URL_CARCOST . '">';
        $html .= IndexView::CreateInput('Rendszám', 'ids');
        $html .= '<label for="date">Időpont:</label>';
        $html .= '<input type="datetime-local" id="date" name="date">';
        $html .= IndexView::CreateInput('Alkatrész', 'part');
        $html .= IndexView::CreateInput('Ára/Ft-ben', 'cost');
        $html .= '<button type="submit" name="newCarCost">Új költség hozzáadása</button>';
        $html .= '</form>';
    
        return $html;
    }

    private static function CarCostEdit($editcarcost)
   {
    $html = '<form method="post" action="' . Config::KECSO_URL_CARCOST . '">';
    $html .= '<input type="hidden" name="editCarCostId" value="' . $editcarcost['_id'] . '">';
    $html .= IndexView::CreateInputValue('Rendszám', 'ids', $editcarcost['ids']);
    $html .= '<label for="date">Időpont:</label>';
    $html .= '<input type="datetime-local" id="date" name="date" value="' . date('Y-m-d\TH:i', strtotime($editcarcost['date'])) . '">';
    $html .= IndexView::CreateInputValue('Alkatrész', 'part', $editcarcost['part']);
    $html .= IndexView::CreateInputValue('Ára', 'cost', $editcarcost['cost']);
    $html .= '<button type="submit" name="saveCarCost">Mentés</button>';
    $html .= '</form>';

    return $html;
    }
   

    public static function ShowCostByGroup($cars, $startDate, $endDate)
{
    $html = '<h2>Rendszám szerint alkatrész árak összesítése (' . htmlspecialchars($startDate) . ' - ' . htmlspecialchars($endDate) . ')</h2>';

    if (!empty($cars)) {
        $html .= '<table border="1" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Rendszám</th>
                            <th>Költségek</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($cars as $car) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($car['ids'] ?? '') . '</td>
                        <td>' . htmlspecialchars($car['cost'] ?? '') . '</td>
                      </tr>';
        }

        $html .= '</tbody></table>';
    } else {
        $html .= '<p>Nincs adat az időszakra.</p>';
    }

    // Időszak kiválasztó űrlap megjelenítése
    $html .= '<form method="post" action="' . htmlspecialchars(Config::KECSO_URL_CARCOST) . '">';
    $html .= '<label for="startDate">Kezdő dátum:</label>';
    $html .= '<input type="date" id="startDate" name="startDate" value="' . htmlspecialchars($startDate) . '">';
    $html .= '<label for="endDate">Vég dátum:</label>';
    $html .= '<input type="date" id="endDate" name="endDate" value="' . htmlspecialchars($endDate) . '">';
    $html .= '<button type="submit" name="filter">Szűrés</button>';
    $html .= '</form>';

    return $html;
}

   

}
  
