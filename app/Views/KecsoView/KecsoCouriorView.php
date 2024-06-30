<?php
namespace App\Views\KecsoView;

use App\Models\KecsoModel\CouriorsModel;
use App\Views\IndexView;
use App\Config;

class KecsoCouriorView
{
    public static function ShowCourior()
    {
       
        $html ='';
        $html = IndexView::OpenSection('Futárok');
        $html.= '<form method="post" action="' . Config::KECSO_URL . '">';
        $html.= IndexView::CreateInput('Azonosító', 'ids');
        $html.= IndexView::CreateInput('Futár neve', 'name');
        $html.= '<button type="submit" name="newCourior">Futár hozzáadása</button>';
        $html.= '</form>';

        $html.= self::DisplayCouriors();

        $html.=IndexView::CloseSection();
        return $html;
    }
    public static function CouriorData($couriordata,$editcourior=null)
    {
        $html = '<form method="post" action="' . Config::KECSO_URL_COURIORDATA  .'">';
        $html .= IndexView::CreateInput('Neve', 'name');
        $html .= IndexView::CreateInput('Születési ideje', 'date');
        $html .= IndexView::CreateInput('Születési helye', 'dateaddress');
        $html .= IndexView::CreateInput('Kor', 'age');
        $html .= IndexView::CreateInput('Lakcíme', 'address');
        $html .= IndexView::CreateInput('Anyja neve', 'mothername');
        $html .= '<button type="submit" name="newDepo">Depó adat hozzáadása</button>';
        $html .= '</form>';

        $html .= self::DisplayCouriorData($couriordata, $editcourior);

        return $html;
    }
    public static function CouriorEdit($courior)
    {
    $html = '<form method="post" action="' . Config::KECSO_URL_COURIORDATA .'">';
    $html .= '<input type="hidden" name="editCouriordataId" value="' . $courior['_id'] . '">';
    $html .= IndexView::CreateInputValue('Neve', 'name', $courior['name']);
    $html .= IndexView::CreateInputValue('Születési ideje', 'date', $courior['date']);
    $html .= IndexView::CreateInputValue('Születési helye', 'dateadress', $courior['dateaddress']);
    $html .= IndexView::CreateInputValue('Kor', 'age', $courior['age']);
    $html .= IndexView::CreateInputValue('Lakcíme', 'address', $courior['address']);
    $html .= IndexView::CreateInputValue('Anyja neve', 'mothername', $courior['mothername']);
    $html .= '<button type="submit" name="saveCouriordata">Mentés</button>';
    $html .= '</form>';

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
}