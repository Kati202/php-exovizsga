<?php
namespace App\Views\KecsoView;

use App\Models\KecsoModel\DispModel;
use App\Views\IndexView;
use App\Config;

class KecsoDispView
{
public static function Disp($dispdata,$editdisp=null)
  {
    $html = '';
     
    
    $groupedData = self::GroupDataDisp($dispdata);
   
    foreach ($groupedData as $title => $items) 
    {
        $html .= self::DisplayDispGroup($title, $items, $editdisp);
    }

    $html .= self::CreateDispForm();
    return $html;
  }
 private static function GroupDataDisp($dispdata)
    {
        $groupedData = [];
    
        foreach ($dispdata as $item) {
            
            if ($item instanceof \MongoDB\Model\BSONDocument) 
            {
                $title = $item['title'] ;
                $name = $item['name'] ;
                $phone = $item['phone'] ;
                $id = (string) $item['_id'];
    
    
               
                if (!isset($groupedData[$title])) 
                {
                    $groupedData[$title] = [];
                }
                $groupedData[$title][] = 
                [
                    'title' => $title,
                    'name'=>$item['name'] ,
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
    
          
            if ($editdisp && $editdisp['_id'] == $item['_id']) {
                $html .= '<tr><td colspan="3">' . self::DispEdit($editdisp) . '</td></tr>';
            }
        }
    
        $html .= '</tbody></table>';
    
        return $html;
    }
    private static function CreateDispForm()
{
    $html = '<form method="post" action="' . Config::KECSO_URL_DISP . '">';
    $html .= IndexView::CreateInput('Név','name');
    $html .= IndexView::CreateInput('Munkaterület', 'title');
    $html .= IndexView::CreateInput('Telefonszám', 'phone');
    $html .= '<button type="submit" name="newDisp">Diszpécser adat hozzáadása</button>';
    $html .= '</form>';

    return $html;
}
public static function DispEdit($editdisp)
{
    $html = '<form method="post" action="' . Config::KECSO_URL_DISP .'">';
    $html .= '<input type="hidden" name="editDispId" value="' . $editdisp['_id'] . '">';
    $html .= IndexView::CreateInputValue('Név', 'name', $editdisp['name']);
    $html .= IndexView::CreateInputValue('Munkaterület', 'title', $editdisp['title']);
    $html .= IndexView::CreateInputValue('Telefonszám', 'phone', $editdisp['phone']);
    $html .= '<button type="submit" name="saveDisp">Mentés</button>';
    $html .= '</form>';

    return $html;
}
        

}