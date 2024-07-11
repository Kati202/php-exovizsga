<?php
namespace app\Views\TatabView;

use App\Models\TatabModel\DepoModel;
use App\Views\IndexView;
use App\Config;

class TatabDepoView
{
    public static function Depo($depodata,$editDepo=null)
    {
      
        $html = '<form method="post" action="' . Config::TATAB_URL_DEPO .'">';
        $html .= IndexView::CreateInput('Kategória', 'title');
        $html .= IndexView::CreateInput('Adat', 'content');
        $html .= '<button type="submit" name="newDepo">Depó adat hozzáadása</button>';
        $html .= '</form>';

        $html .= self::DisplayDepos($depodata, $editDepo);

        return $html;
    }
    public static function DepoEdit($depo)
   {
        $html = '<form method="post" action="' . Config::TATAB_URL_DEPO .'">';
        $html .= '<input type="hidden" name="editDepoId" value="' . $depo['_id'] . '">';
        $html .= IndexView::CreateInputValue('Kategória', 'title', $depo['title']);
        $html .= IndexView::CreateInputValue('Adat', 'content', $depo['content']);
        $html .= '<button type="submit" name="saveDepo">Mentés</button>';
        $html .= '</form>';

    return $html;
    }
    public static function ShowDepoButton()
    {
     $html = '<form method="post" action="' . Config::TATAB_URL_DEPO . '">';
     $html .= '<button type="submit" name="showDepo">Depó adatok megtekintése</button>';
     $html .= '</form>';
 
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
                                             <form method="post" action="' . Config::TATAB_URL_DEPO . '" style="display:inline;">
                                                 <input type="hidden" name="updateDepoId" value="' . $depo['_id'] . '">
                                                 <button type="submit" name="updateDepo">Szerkesztés</button>
                                             </form>
                                             <form method="post" action="' . Config::TATAB_URL_DEPO . '" style="display:inline;">
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
}