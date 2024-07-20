<?php
namespace app\Views\HalasView;

use App\Models\HalasModel\CarsModel;
use App\Views\IndexView;
use App\Config;

class HalasCarView
{
    public static function ShowCar()
    {
        
        $html ='';
        $html .= IndexView::OpenSection('Gépjárművek hozzáadása');
        $html .= '<form method="post" action="' . Config::HALAS_URL . '">';
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
                            <form method="post" action="' . Config::HALAS_URL . '" style="display:inline;">
                                <input type="hidden" name="deleteCarId" value="' . htmlspecialchars($car['_id'] ?? '') . '">
                                <button type="submit" name="deleteCar">Törlés</button>
                            </form>
                        </td>
                      </tr>';
        }
        $html .= '</tbody></table>';

        
        $html .= '<div class="buttonss">';
        $html .= '<a href="'.Config::HALAS_URL_CARDATA.'">Gépjárművek adatai</a>';
        $html .= '<a href="'.Config::HALAS_URL_CARCOST.'">Javítási költségek</a>';
        $html .= '</div>';
    } else {
        $html .= '<tr><td colspan="2">Nincsenek elérhető autók</td></tr>';
        $html .= '</tbody></table>';
    }

    return $html;
}
  //Cardata aloldal
public static function CarData($cardata, $editcardata = null)
{
    $html = '';
    $groupedData = self::GroupDataCarData($cardata);

    foreach ($groupedData as $ids => $items) {
        $html .= self::DisplayCarDataGroup($ids, $items, $editcardata);
    }
    
    $html .= self::CreateCarDataForm();
    return $html;
}
private static function GroupDataCarData($cardata)
{
    $groupedData = [];
    
    foreach ($cardata as $car) {
        $ids = $car['ids'];

        if (!isset($groupedData[$ids])) {
            $groupedData[$ids] = [];
        }
        $groupedData[$ids][] = [
            'ids' => $car['ids'],
            'km' => $car['km'],
            'liters' => $car['liters'],
            'date' => $car['date'],
            '_id' => (string) $car['_id'],
        ];
    }

    return $groupedData;
}
private static function DisplayCarDataGroup($ids, $items, $editcardata)
{
    $html = '';

    if (!empty($ids)) {
        $html .= '<h2>' . htmlspecialchars($ids) . '</h2>';
    }

    $html .= '<table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Rendszám</th>
                        <th>Km állás</th>
                        <th>Tankolt liter</th>
                        <th>Időpont</th>
                        <th>Műveletek</th>
                    </tr>
                </thead>
                <tbody>';
    foreach ($items as $item) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($item['ids'] ?? '') . '</td>
                    <td>' . htmlspecialchars($item['km'] ?? '') . '</td>
                    <td>' . htmlspecialchars($item['liters'] ?? '') . '</td>
                    <td>' . htmlspecialchars(date('Y-m-d H:i', strtotime($item['date']->toDateTime()->format('Y-m-d H:i')))) . '</td>
                    <td>
                        <form method="post" action="' . Config::HALAS_URL_CARDATA . '?operation=cardata3&param=' . htmlspecialchars($item['_id'] ?? '') . '" style="display:inline;">
                            <input type="hidden" name="updateCarDataId" value="' . ($item['_id'] ?? '') . '">
                            <button type="submit" name="updateCarData">Szerkesztés</button>
                        </form>
                        <form method="post" action="' . Config::HALAS_URL_CARDATA . '" style="display:inline;">
                            <input type="hidden" name="deleteCarDataId" value="' . ($item['_id'] ?? '') . '">
                            <input type="hidden" name="operation" value="cardata">
                            <input type="hidden" name="param" value="' . ($item['_id'] ?? '') . '">
                            <button type="submit" name="deleteCardata">Törlés</button>
                        </form>
                    </td>
                </tr>';
        
        if ($editcardata && $editcardata['_id'] == $item['_id']) {
            $html .= '<tr><td colspan="10">' . self::CarDataEdit($editcardata) . '</td></tr>';
        }
    }

    $html .= '</tbody></table>';

    return $html;
}

public static function CreateCarDataForm()
{
    $html = '<form method="post" action="' . Config::HALAS_URL_CARDATA . '">';
    $html .= IndexView::CreateInput('Rendszám', 'ids');
    $html .= '<label for="km">Km állás:</label>';
    $html .= '<input type="number" id="km" name="km">';
    $html .= '<label for="liters">Tankolt liter:</label>';
    $html .= '<input type="number" id="liters" name="liters">';
    $html .= '<label for="date">Időpont:</label>';
    $html .= '<input type="datetime-local" id="date" name="date">';
    $html .= '<button type="submit" name="newCarData">Új adat hozzáadása</button>';
    $html .= '</form>';

    return $html;
}

private static function CarDataEdit($editcardata)
{
    
    $html = '<form method="post" action="' . Config::HALAS_URL_CARDATA . '">';
    $html .= '<input type="hidden" name="editCarDataId" value="' . $editcardata['_id'] . '">';
    $html .= IndexView::CreateInputValue('Rendszám', 'ids', $editcardata['ids']);
    $html .= '<label for="km">Km állás:</label>';
    $html .= '<input type="number" id="km" name="km" value="' . htmlspecialchars($editcardata['km']) . '">';
    $html .= '<label for="liters">Tankolt liter:</label>';
    $html .= '<input type="number" id="liters" name="liters" value="' . htmlspecialchars($editcardata['liters']) . '">';
    $html .= '<label for="date">Időpont:</label>';
    $html .= '<input type="datetime-local" id="date" name="date" value="' . date('Y-m-d\TH:i', strtotime($editcardata['date'])) . '">';
    $html .= '<button type="submit" name="saveCarData">Mentés</button>';
    $html .= '</form>';

    return $html;
}
 public static function RenderForm($result = '', $beforeKm = '', $afterKm = '', $totalLiters = '') {
    
    $html = '<form method="post" action="">
                <label for="previousKm">Előző kilométerállás (km):</label>
                <input type="number" id="previousKm" name="previousKm" step="0.01" value="' . htmlspecialchars($beforeKm) . '" required>
                <br><br>
                <label for="currentKm">Jelenlegi kilométerállás (km):</label>
                <input type="number" id="currentKm" name="currentKm" step="0.01" value="' . htmlspecialchars($afterKm) . '" required>
                <br><br>
                <label for="totalLiters">Tankolt liter:</label>
                <input type="number" id="totalLiters" name="totalLiters" step="0.01" value="' . htmlspecialchars($totalLiters) . '" required>
                <br><br>
                <button type="submit" name="calculateConsumption">Számítás</button>
            </form>';

    if ($result) {
        $html .= '<h1>Az átlagfogyasztás:</h1>
                  <h4>' . htmlspecialchars($result) . '</h4>';
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
                            <form method="post" action="' . Config::HALAS_URL_CARCOST . '?operation=carcost3&param=' . htmlspecialchars($item['_id'] ?? '') . '" style="display:inline;">
                             <input type="hidden" name="updateCarCostId" value="' . ($item['_id'] ?? '') . '">
                             <button type="submit" name="updateCarcost">Szerkesztés</button>
                            </form>
                                <form method="post" action="' . Config::HALAS_URL_CARCOST . '" style="display:inline;">
                                <input type="hidden" name="deleteCarcostId" value="' . ($item['_id'] ?? '') . '">
                                <input type="hidden" name="operation" value="carcost">
                                <input type="hidden" name="param" value="' . ($item['_id'] ?? '') . '">
                                <button type="submit" name="deleteCarcost">Törlés</button>
                            </form>
                        </td>
                    </tr>';
    
            if ($editcarcost && $editcarcost['_id'] == $item['_id']) {
                $html .= '<tr><td colspan="10">' . self::CarCostEdit($editcarcost) . '</td></tr>';
            }
        }
    
        $html .= '</tbody></table>';
    
        return $html;
    }
    
    public static function CreateCarCostForm()
    {
        $html = '<form method="post" action="' . Config::HALAS_URL_CARCOST . '">';
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
    $html = '<form method="post" action="' . Config::HALAS_URL_CARCOST . '">';
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
    /*public static function ShowCostByGroup($cars, $startDate, $endDate)
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
        $html .= '<form method="post" action="' . htmlspecialchars(Config::HALAS_URL_CARCOST) . '">';
        $html .= '<label for="startDate">Kezdő dátum:</label>';
        $html .= '<input type="date" id="startDate" name="startDate" value="' . htmlspecialchars($startDate) . '">';
        $html .= '<label for="endDate">Vég dátum:</label>';
        $html .= '<input type="date" id="endDate" name="endDate" value="' . htmlspecialchars($endDate) . '">';
        $html .= '<button type="submit" name="filter">Szűrés</button>';
        $html .= '</form>';
    
        return $html;
    }*/

}
  
