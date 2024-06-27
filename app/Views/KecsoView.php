<?php
namespace App\Views;

use App\Models\CarsModel;
use App\Models\CouriorsModel;
use App\Views\IndexView;
use App\Config;

class KecsoView
{   //Kecso home
    public static function ShowCar()
    {
        
        $html='';
        $html = IndexView::OpenSection('Gépjárművek');
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
    public static function ShowCourior()
    {
       
        $html ='';
        $html = IndexView::OpenSection('Futárok');
        $html.= '<form method="post" action="' . Config::KECSO_URL . '">';
        $html.= self::CreateInput('Azonosító', 'ids');
        $html.= self::CreateInput('Futár neve', 'name');
        $html.= '<button type="submit" name="newCourior">Futár hozzáadása</button>';
        $html.= '</form>';

        $html.= self::DisplayCouriors();

        $html.=IndexView::CloseSection();
        return $html;
    }

    //Kecso page
    public static function CarData($carId,$uploadedFileName)
     {
        $html= '<form method="post" action="'.Config::KECSO_URL_CARDATA.'" enctype="multipart/form-data">';
        $html .= '<input type="hidden" name="carId" value="' . htmlspecialchars($carId) . '">';
        $html .= self::CreateInput('Fálj neve', 'data');
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
    public static function Depo($depodata,$editDepo=null)
    {
      
        $html = '<form method="post" action="' . Config::KECSO_URL_DEPO .'">';
        $html .= self::CreateInput('Kategória', 'title');
        $html .= self::CreateInput('Adat', 'content');
        $html .= '<button type="submit" name="newDepo">Depó adat hozzáadása</button>';
        $html .= '</form>';

        $html .= self::DisplayDepos($depodata, $editDepo);

        return $html;
    }
    public static function Disp($dispdata,$editdisp=null)
    {
    $html = '';
     
    
    $groupedData = self::GroupDataDisp($dispdata);
   
    foreach ($groupedData as $title => $items) {
        $html .= self::DisplayDispGroup($title, $items, $editdisp);
    }

    $html .= self::CreateDispForm();
    return $html;
    }

    public static function ShowDepoButton()
   {
    $html = '<form method="post" action="' . Config::KECSO_URL_DEPO . '">';
    $html .= '<button type="submit" name="showDepo">Depó adatok megtekintése</button>';
    $html .= '</form>';

    return $html;
   } 
   public static function ShowDispButton()
   {
    $html = '<form method="post" action="' . Config::KECSO_URL_DISP. '">';
    $html .= '<button type="submit" name="showDisp">Diszpécser elérhetőségek megtekintése</button>';
    $html .= '</form>';

    return $html;
   }
   




    //Kecso home,page
    private static function CreateInput($text, $name)
    {
        return '<div>
                    <label for="'. $name .'">'. $text .'</label>
                    <input type="text" name="'. $name .'" id="'. $name .'">
                </div>';
    }
    private static function CreateInputValue($label, $name, $value = '')
    {
        return '<div>
                    <label for="' . $name . '">' . $label . '</label>
                    <input type="text" name="' . $name . '" id="' . $name . '" value="' . htmlspecialchars($value) . '">
                </div>';
    }
    //Kecso home
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
    
    private static function DisplayCouriors()
    {
        $couriors = CouriorsModel::GetCouriors();
        $html = '<table border="1" cellpadding="10" >
                    <thead>
                        <tr><th>Futár azonosító</th>
                            <th>Futár neve</th>
                            <th>Futár adatai</th>
                            <th>Futár címmenyisége</th></tr>
                    </thead>
                    <tbody>';
        foreach ($couriors as $courior) 
        {
            $html .= '<tr>
                        <td>' . $courior['ids'] . '</td>
                        <td>' . $courior['name'] .'</td>
                        <td><a href="'.Config::KECSO_URL_COURIORDATA.'?operation=couriordata&param=' . $courior['_id'] .'">Személyes adatok</a></td>
                        <td><a href="'.Config::KECSO_URL_COURIORADDRESS.'?operation=courioraddress&param=' . $courior['_id'] .'">Hónapos szétbontásban</a></td>
                        <td>
                            <form method="post" action="' . Config::KECSO_URL . '" style="display:inline;">
                                <input type="hidden" name="deleteCouriorId" value="' . $courior['_id'] . '">
                                <button type="submit" name="deleteCourior">Törlés</button>
                            </form>
                        </td>
                      </tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
    //Kecso page
   
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
    private static function DisplayDepos($depodata,$editDepo=null)
    {
        $html = '';

        if (!empty($depodata)) {
            $html .= '<table border="1" cellpadding="10">
                        <thead>
                            <tr><th>Kategória</th>
                                <th>Adat</th>
                                <th>Műveletek</th></tr>
                        </thead>
                        <tbody>';

                        foreach ($depodata as $depo) {
                            $html .= '<tr>
                                        <td>' . htmlspecialchars($depo['title']) . '</td>
                                        <td>' . htmlspecialchars($depo['content']) . '</td>
                                        <td>
                                             <form method="post" action="' . Config::KECSO_URL_DEPO . '" style="display:inline;">
                                                 <input type="hidden" name="updateDepoId" value="' . $depo['_id'] . '">
                                                 <button type="submit" name="updateDepo">Szerkesztés</button>
                                             </form>
                                             <form method="post" action="' . Config::KECSO_URL_DEPO . '" style="display:inline;">
                                                <input type="hidden" name="deleteDepoId" value="' . $depo['_id'] . '">
                                                <button type="submit" name="deleteDepo">Törlés</button>
                                             </form>
                                        </td>
                                      </tr>';

                // Ha a szerkesztési gomb megnyomásra került, jelenjen meg a szerkesztési űrlap
                if ($editDepo && $editDepo['_id'] == $depo['_id']) {
                    $html .= '<tr><td colspan="3">' . self::DepoEdit($editDepo) . '</td></tr>';
                }
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<p>Nincsenek depó adatok.</p>';
        }

        return $html;
   }
    

    private static function GroupDataDisp($dispdata)
{
    $groupedData = [];

    foreach ($dispdata as $item) {
        // Ellenőrizze, hogy a $item egy BSONDocument objektum
        if ($item instanceof \MongoDB\Model\BSONDocument) 
        {
            $title = $item['title'] ;
            $name = $item['name'] ;
            $phone = $item['phone'] ;
            $id = (string) $item['_id']; // Az ObjectId objektumot stringgé alakítjuk


            // Csoportosítás feladatkör alapján
            if (!isset($groupedData[$title])) 
            {
                $groupedData[$title] = [];
            }
            $groupedData[$title][] = 
            [
                'title' => $title,
                'name'=>$name['name'] ?? 'Nincs megadva név',
                'phone' => $item['phone'] ,
                '_id' => $item['_id'], 
            ];
        }
    }

    return $groupedData;
}
    
  
    private static function DisplayDispGroup($title, $items, $editdisp)
{
    $html = '<h2>' . htmlspecialchars($title) . '</h2>';
   

    // Táblázat létrehozása az adatok megjelenítéséhez
    $html .= '<table border="1" cellpadding="10">
                <thead>
                <tr>
                    <th>Név</th>
                    <th>Munkaterület</th>
                    <th>Telefonszám</th>
                    <th>Műveletek</th>
                </tr>
                       
                </thead>
                <tbody>';

    // Adatok megjelenítése a táblázatban
    foreach ($items as $item) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($item['name']) . '</td>
                    <td>' . htmlspecialchars($item['title']) . '</td>
                    <td>' . htmlspecialchars($item['phone']) . '</td>
                    <td>
                        <form method="post" action="' . Config::KECSO_URL_DISP . '" style="display:inline;">
                            <input type="hidden" name="updateDispId" value="' . $item['_id'] . '">
                            <button type="submit" name="updateDisp">Szerkesztés</button>
                        </form>
                        <form method="post" action="' . Config::KECSO_URL_DISP . '" style="display:inline;">
                            <input type="hidden" name="deleteDispId" value="' . $item['_id'] . '">
                            <button type="submit" name="deleteDisp">Törlés</button>
                        </form>
                    </td>
                </tr>';

        // Ha szerkesztés alatt van az adott elem, jelenítsük meg a szerkesztési űrlapot
        if ($editdisp && $editdisp['_id'] == $item['_id']) {
            $html .= '<tr><td colspan="3">' . self::DispEdit($editdisp) . '</td></tr>';
        }
    }

    $html .= '</tbody></table>';

    return $html;
}

// Űrlap létrehozása új depó adat hozzáadásához
private static function CreateDispForm()
{
    $html = '<form method="post" action="' . Config::KECSO_URL_DISP . '">';
    $html .= self::CreateInput('Név','name');
    $html .= self::CreateInput('Munkaterület', 'title');
    $html .= self::CreateInput('Telefonszám', 'phone');
    $html .= '<button type="submit" name="newDisp">Diszpécser adat hozzáadása</button>';
    $html .= '</form>';

    return $html;
}
public static function DispEdit($editdisp)
{
    $html = '<form method="post" action="' . Config::KECSO_URL_DISP .'">';
    $html .= '<input type="hidden" name="editDispId" value="' . $editdisp['_id'] . '">';
    $html .= self::CreateInputValue('Név', 'name', $editdisp['name']);
    $html .= self::CreateInputValue('Munkaterület', 'title', $editdisp['title']);
    $html .= self::CreateInputValue('Telefonszám', 'phone', $editdisp['phone']);
    $html .= '<button type="submit" name="saveDisp">Mentés</button>';
    $html .= '</form>';

    return $html;
}



   
}



