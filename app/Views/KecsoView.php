<?php
namespace App\Views;

use App\Models\Model;
use App\Views\IndexView;
use App\Config;

class KecsoView
{   //Kecso home
    public static function Show()
    {
        IndexView::OpenSection('Gépjárművek');
        $html='';

        $html.= '<form method="post" action="' . Config::KECSO_URL . '">';
        $html.= self::CreateInput('Rendszám', 'license');
        $html.= '<button type="submit" name="newCar">Gépjármű hozzáadása</button>';
        $html.= '</form>';

        $html.= self::DisplayCars();

        $html.=IndexView::CloseSection();
        return $html;

    }
    //Kecso home
    public static function renderDeleteConfirmationForm($ids)
    {
       $html.=IndexView::renderDeleteConfirmationForm(Config::KECSO_URL, $ids);
       return $html;
    }
    //Kecso page
    public static function CarData()
     {
        $html= '<form method="post" action="'.Config::KECSO_URL_CARDATA.'" enctype="multipart/form-data">';
        $view .= '<input type="hidden" name="carId" value="' . $carId . '">';
        $html .= self::CreateInput('Fálj neve', 'data');
        $html .= '<div>
                    <label for="file">Válassz egy fájlt:</label>
                    <input type="file" name="file" id="file">
                  </div>';
        $html .= '<button type="submit" name="upload">Fájl/kép hozzáadása</button>';
        $html .= '</form>';

        // Feltöltött képek listázása
        $html .= self::ListUploadedImages();

        return $html;
     }
    
     public static function CarCost()
    { 
        $html= '<form method="post" action="' . Config::KECSO_URL . '">';
        $html .= self::CreateInput('Rendszám', 'license');
        $html .= self::CreateInput('Időpont', 'date');
        $html .= self::CreateInput('Alkatrész', 'part');
        $html .= self::CreateInput('Ár', 'price');
        $html .= '<button type="submit" name="addCarCost">Költség hozzáadása</button>';
        $html.= '</form>';

        return $html;
    }






    //Kecso home,page
    private static function CreateInput($text, $car)
    {
        return '<div>
                    <label for="'. $car .'">'. $text .'</label>
                    <input type="text" name="'. $car .'" id="'. $car .'">
                </div>';
    }
    //Kecso home
    private static function DisplayCars()
    {
        $cars = Model::GetCars();
        $html = '<table border="1" cellpadding="10" >
                    <thead>
                        <tr><th>Rendszám</th>
                            <th>Adatok</th>
                            <th>Költségek</th>
                            <th>Műveletek</th></tr>
                    </thead>
                    <tbody>';
        foreach ($cars as $car) {
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
    //Kecso page
   
    private static function ListUploadedImages() 
    {
        $images = Model::GetImages();
        $html = '<h2>Feltöltött képek:</h2>';
    
        if (!empty($images)) {
            $html .= '<ul>';
            foreach ($images as $image) {
                $fileName = $image['file'];
                $license = $image['data'];  // Itt változtatva "license"-ról "data"-ra
                $html .= '<li>
                            <p>Rendszám: ' . htmlspecialchars($license) . '</p>
                            <img src="uploads/kecso/' . htmlspecialchars($fileName) . '" width="200" height="auto">
                          </li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>Nincsenek feltöltött képek.</p>';
        }
    
        return $html;
    }
private static function DisplayCarCosts()
{
    $carCosts = Model::GetCarCosts(); // Módosítani a model függvény nevét és paramétereit

    $html = '<h2>Javítási költségek:</h2>';

    if (!empty($carCosts)) {
        $html .= '<table border="1" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Rendszám</th>
                            <th>Időpont</th>
                            <th>Alkatrész</th>
                            <th>Ár</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($carCosts as $cost) {
            $html .= '<tr>
                        <td>' . $cost['license'] . '</td>
                        <td>' . $cost['date'] . '</td>
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

// ...

}
