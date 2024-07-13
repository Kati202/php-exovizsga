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
public static function CarData($carId)
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
        $html .= self::ListUploadedImages($carId);

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
                    header("Location: " . Config::KECSO_URL_CARDATA . "?param=" . htmlspecialchars($carId));
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
  
    private static function ListUploadedImages($carId)
{
    $uploadDir = 'uploads/kecso/';
    $carImages = CarsModel::GetCarImages($carId); 

    $html = '<h2>Feltöltött képek:</h2>';

    if (!empty($carImages)) {
        $html .= '<ul>';
        foreach ($carImages as $image) {
            $imageUrl = $uploadDir . $image['file'];

            $html .= '<li>';
            $html .= '<img src="' . htmlspecialchars($imageUrl) . '" alt="Feltöltött kép">';
            $html .= '<p>Fájl neve: <a href="' . htmlspecialchars($imageUrl) . '" target="_blank">' . htmlspecialchars($image['data']) . '</a></p>';
            $html .= '</li>';
        }
        $html .= '</ul>';
    } else {
        $html .= '<p>Nincsenek feltöltött képek.</p>';
    }

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
            // Csoportosítás logikája
            $ids = $car['ids'];
            // További adatok csoportosítása
            if (!isset($groupedData[$ids])) {
                $groupedData[$ids] = [];
            }
            $groupedData[$ids][] = [
                'ids' => $car['ids'],
                'date' => $car['date'],
                'part' => $car['part'],
                'cost' => $car['cost'],
               '_id' => (string) $car['_id'], // MongoDB ObjectId stringgé alakítása
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
                            <th>Költségek</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>';
    
        foreach ($items as $item) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($item['ids'] ?? '') . '</td>
                        <td>' . htmlspecialchars($item['date'] ?? '') . '</td>
                        <td>' . htmlspecialchars($item['part'] ?? '') . '</td>
                        <td>' . htmlspecialchars($item['cost'] ?? '') . '</td>
                        <td>
                            <form method="post" action="' . Config::KECSO_URL_CARCOST . '?operation=couriorcarcost&param=' . htmlspecialchars($item['_id'] ?? '') . '" style="display:inline;">
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
    
    public static function CreateCarCostForm()
    {
        $html = '<form method="post" action="' . Config::KECSO_URL_CARCOST . '">';
        $html .= IndexView::CreateInput('Rendszám', 'ids');
        $html .= '<label for="date">Időpont:</label>';
        $html .= '<input type="datetime-local" id="date" name="date">';
        $html .= IndexView::CreateInput('Alkatrész', 'part');
        $html .= IndexView::CreateInput('Ára', 'cost');
        $html .= '<button type="submit" name="newCost">Új költség hozzáadása</button>';
        $html .= '</form>';
    
        return $html;
    }

    private static function CarCostEdit($editcarcost)
    {
        $html = '<form method="post" action="' . Config::KECSO_URL_CARCOST . '">';
        $html .= '<input type="hidden" name="editCarcostId" value="' . $editcarcost['_id'] . '">';
        $html .= IndexView::CreateInputValue('Rendszám', 'ids', $editcarcost['ids']);
        $html .= '<label for="date">Időpont:</label>';
        $html .= '<input type="datetime-local" id="date" name="date" value="' . date('Y-m-d\TH:i', strtotime($editcarcost['date'])) . '">';
        $html .= IndexView::CreateInputValue('Alkatrész', 'part', $editcarcost['part']);
        $html .= IndexView::CreateInputValue('Ára', 'cost', $editcarcost['cost']);
        $html .= '<button type="submit" name="saveCarCost">Mentés</button>';
        $html .= '</form>';
    
        return $html;
    }
  /*public static function ShowDeliveriesByGroup($deliveries, $startDate, $endDate)
    {
        $html = '<h2>Kézbesítések összesítése az időszakra (' . $startDate . ' - ' . $endDate . ')</h2>';

        if (!empty($deliveries)) {
            $html .= '<table border="1" cellpadding="10">
                        <thead>
                            <tr>
                                <th>Azonosító</th>
                                <th>Összesen kézbesített címek száma</th>
                            </tr>
                        </thead>
                        <tbody>';

            foreach ($deliveries as $delivery) {
                $html .= '<tr>
                            <td>' . htmlspecialchars($delivery['_id'] ?? '') . '</td>
                            <td>' . htmlspecialchars($delivery['totalDeliveredAddresses'] ?? '') . '</td>
                          </tr>';
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<p>Nincs adat az időszakra.</p>';
        }

        // Időszak kiválasztó űrlap megjelenítése
        $html .= '<form method="post" action="' . Config::KECSO_URL_CARCOST . '">
                    <label for="startDate">Kezdő dátum:</label>
                    <input type="date" id="startDate" name="startDate" value="' . htmlspecialchars($startDate) . '">
                    <label for="endDate">Vég dátum:</label>
                    <input type="date" id="endDate" name="endDate" value="' . htmlspecialchars($endDate) . '">
                    <button type="selectTime">Szűrés</button>
                  </form>';

        return $html;
    }*/

}
  
