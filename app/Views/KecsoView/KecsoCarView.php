<?php
namespace App\Views\KecsoView;

use App\Models\KecsoModel\CarsModel;
use App\Views\IndexView;
use App\Config;

class KecsoCarView
{
public static function ShowCar()
    {
        
        $html='';
        $html = IndexView::OpenSection('Gépjárművek');
        $html.= '<form method="post" action="' . Config::KECSO_URL . '">';
        $html.= IndexView::CreateInput('Rendszám', 'license');
        $html.= '<button type="submit" name="newCar">Gépjármű hozzáadása</button>';
        $html.= '</form>';

        $html.= self::DisplayCars();

        $html.=IndexView::CloseSection();
        return $html;

    }
public static function CarData($carId,$uploadedFileName)
    {
       $html= '<form method="post" action="'.Config::KECSO_URL_CARDATA.'" enctype="multipart/form-data">';
       $html .= '<input type="hidden" name="carId" value="' . htmlspecialchars($carId) . '">';
       $html .= Index::CreateInput('Fálj neve', 'data');
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
public static function CarCost($carId)
    { 
        
        $html = '<form method="post" action="' . Config::KECSO_URL_CARCOST . '?param=' . htmlspecialchars($carId) . '">';
        $html .= '<input type="text" name="date" placeholder="Időpont">';
        $html .= '<input type="text" name="part" placeholder="Alkatrész">';
        $html .= '<input type="text" name="price" placeholder="Ár">';
        $html .= '<button type="submit" name="addCarCost">Költség hozzáadása</button>';
        $html .= '</form>';

        // Javítási költségek listázása
        $html .= self::DisplayCarCosts($carId);

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
            $html .= '<ul>';
            $html .= '<li>
                        <p>Fájl neve: ' . htmlspecialchars($uploadedFileName) . '</p>
                        <!-- További adatok megjelenítése, például feltöltés dátuma, stb. -->
                    </li>';
            $html .= '</ul>';
        } else {
            $html .= '<p>Nincsenek feltöltött képek.</p>';
        }
    
        return $html;
    }
    private static function DisplayCarCosts($carId)
    {
      $carCosts = CarsModel::GetCarCosts($carId);

        $html = '<h3>Rögzített javítási költségek:</h3>';

        if (!empty($carCosts)) {
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

            foreach ($carCosts as $cost) {
                $html .= '<tr>
                            <td>' . date('Y-m-d H:i:s', $cost['date']->toDateTime()->getTimestamp()) . '</td>
                            <td>' . $cost['part'] . '</td>
                            <td>' . $cost['price'] . '</td>
                            <td>
                                <form method="post" action="">
                                    <input type="hidden" name="deleteCostId" value="' . $cost['_id'] . '">
                                    <button type="submit" name="deleteCost">Törlés</button>
                                </form>
                            </td>
                        </tr>';
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<p>Nincsenek rögzített javítási költségek.</p>';
        }

        return $html;
    }
  
}