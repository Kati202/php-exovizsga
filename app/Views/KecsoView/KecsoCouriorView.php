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
    public static function CouriorData($couriordata, $editcourior = null, $id = null)
    {
        $html = '<form method="post" action="' . Config::KECSO_URL_COURIORDATA . '?param=' . $id . '">';
        $html .= IndexView::CreateInput('Azonosító','ids');
        $html .= IndexView::CreateInput('Neve', 'name');
        $html .= IndexView::CreateInput('Születési ideje', 'date');
        $html .= IndexView::CreateInput('Születési helye', 'dateaddress');
        $html .= IndexView::CreateInput('Kora', 'age');
        $html .= IndexView::CreateInput('Lakcíme', 'address');
        $html .= IndexView::CreateInput('Anyja neve', 'mothername');
        $html .= '<button type="submit" name="newCouriors">Futár adat hozzáadása</button>';
        $html .= '</form>';
    
        $html .= self::DisplayCouriorData($couriordata, $editcourior,$id );
    
        return $html;
    }
    
    public static function CouriorEdit($courior)
{
    $couriorId = (string)($courior['_id'] ?? '');

    $html = '<form method="post" action="' . Config::KECSO_URL_COURIORDATA . '">';
    $html .= '<input type="hidden" name="editCouriorId" value="' . htmlspecialchars($couriorId) . '">';
    $html .= IndexView::CreateInputValue('Azonosító', 'ids', $courior['ids']);
    $html .= IndexView::CreateInputValue('Neve', 'name', $courior['name']);
    $html .= IndexView::CreateInputValue('Születési ideje', 'date', $courior['date']);
    $html .= IndexView::CreateInputValue('Születési helye', 'dateaddress', $courior['dateaddress']);
    $html .= IndexView::CreateInputValue('Kora', 'age', $courior['age']);
    $html .= IndexView::CreateInputValue('Lakcíme', 'address', $courior['address']);
    $html .= IndexView::CreateInputValue('Anyja neve', 'mothername', $courior['mothername']);
    $html .= '<button type="submit" name="saveCouriordata">Mentés</button>';
    $html .= '</form>';

    return $html;
}

     
private static function DisplayCouriorData($couriordata, $editcourior = null,$id = null)
{
    $html = '';

    if (!empty($couriordata)) {
        $html .= '<table border="1" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Azonosító</th>
                            <th>Neve</th>
                            <th>Születési ideje</th>
                            <th>Születési helye</th>
                            <th>Kora</th>
                            <th>Lakcíme</th>
                            <th>Anyja Neve</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($couriordata as $courior) {
            $couriorId =(string)($courior->_id ?? '');

            $html .= '<tr>
                        <td>' . htmlspecialchars($courior->ids ?? '') . '</td>
                        <td>' . htmlspecialchars($courior->name ?? '') . '</td>
                        <td>' . htmlspecialchars($courior->date ?? '') . '</td>
                        <td>' . htmlspecialchars($courior->dateaddress ?? '') . '</td>
                        <td>' . htmlspecialchars($courior->age ?? '') . '</td>
                        <td>' . htmlspecialchars($courior->address ?? '') . '</td>
                        <td>' . htmlspecialchars($courior->mothername ?? '') . '</td>
                        <td>
                           <form method="post" action="' . Config::KECSO_URL_COURIORDATA . '?param=' . $couriorId . '" style="display:inline;">
                                <input type="hidden" name="updateCouriorId" value="' . htmlspecialchars($couriorId) . '">
                                <button type="submit" name="updateCourior">Szerkesztés</button>
                            </form>
                            <form method="post" action="' . Config::KECSO_URL_COURIORDATA . '?param=' . $couriorId . '" style="display:inline;">
                                <input type="hidden" name="deleteCouriorId" value="' . htmlspecialchars($couriorId) . '">
                                <button type="submit" name="deleteCourior">Törlés</button>
                            </form>
                        </td>
                      </tr>';

            // Ha a szerkesztési gomb megnyomásra került, jelenjen meg a szerkesztési űrlap
            if ($editcourior && $editcourior['_id'] == $couriorId) {
                $html .= '<tr><td colspan="7">' . self::CouriorEdit($editcourior) . '</td></tr>';
            }
        }

        $html .= '</tbody></table>';
    } else {
        $html .= '<p>Nincsenek futár adatok.</p>';
    }

    return $html;
}

private static function DisplayCouriors()
{
    $couriors = CouriorsModel::GetCouriors();
    $html = '<table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Futár azonosító</th>
                        <th>Futár neve</th>
                        <th>Műveletek</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($couriors as $courior) {
        $html .= '<tr>
                    <td>' . $courior['ids'] . '</td>
                    <td>' . $courior['name'] . '</td>
                    <td>
                        <form method="post" action="' . Config::KECSO_URL . '" style="display:inline;">
                            <input type="hidden" name="deleteCouriorId" value="' . $courior['_id'] . '">
                            <button type="submit" name="deleteCourior">Törlés</button>
                     </td>
                </tr>';
    }
    $html .= '</tbody></table>';
    $html .='</form>
    <a href="' . Config::KECSO_URL_COURIORDATA . '?operation=couriordata&param=' . $courior['_id'] . '">Személyes adatok</a>
    <a href="' . Config::KECSO_URL_COURIORADDRESS . '?operation=courioraddress&param=' . $courior['_id'] . '">Hónapos szétbontás</a>';
    return $html;
}
    public static function CouriorsAddress($addresses, $editaddress = null)
    {
        $html = '';
        $groupedData = self::GroupDataAddress($addresses);
       
        foreach ($groupedData as $month => $items) 
        {
            $html .= self::DisplayAddressGroup($month, $items, $editaddress);
        }
    
        $html .= self::CreateAddressForm();
        return $html;
    }
    
    private static function GroupDataAddress($addresses)
    {
        $groupedData = [];

        foreach ($addresses as $address) {
            if ($address instanceof \MongoDB\Model\BSONDocument) 
            {
                $month = $address['month'];
                $day = $address['day'];
                $time = $address['time'];
                $total_addresses = $address['total_addresses'];
                $delivered_addresses = $address['delivered_addresses'];
                $final_return = $address['final_return'];
                $live_return = $address['live_return'];
                $id = (string) $address['_id'];
    
                if (!isset($groupedData[$month])) 
                {
                    $groupedData[$month] = [];
                }
                $groupedData[$month][] = 
                [
                    'day' => $day,
                    'month'=>$month,
                    'time' => $time,
                    'total_addresses' => $total_addresses,
                    'delivered_addresses' => $delivered_addresses,
                    'final_return' => $final_return,
                    'live_return' => $live_return,
                    '_id' => $id, 
                ];
            }
        }
    
        return $groupedData;
    }
    
    private static function DisplayAddressGroup($month, $items, $editaddress)
    {
        $html = '<h2>' . htmlspecialchars($month ?? '') . '</h2>';
       
        $html .= '<table border="1" cellpadding="10">
                    <thead>
                    <tr>
                        <th>Ledolgozott nap</th>
                        <th>Hónap</th>
                        <th>Pontos időpont</th>
                        <th>Össz cím</th>
                        <th>Kézbesített címek </th>
                        <th>Véglegvissza</th>
                        <th>Élővissza</th>
                        <th>Műveletek</th>
                    </tr>
                    </thead>
                    <tbody>';
    
        foreach ($items as $item) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($item['day']) . '</td>
                        <td>' . htmlspecialchars($item['month']) . '</td>
                        <td>' . htmlspecialchars($item['time']) . '</td>
                        <td>' . htmlspecialchars($item['total_addresses']) . '</td>
                        <td>' . htmlspecialchars($item['delivered_addresses']) . '</td>
                        <td>' . htmlspecialchars($item['final_return']) . '</td>
                        <td>' . htmlspecialchars($item['live_return']) . '</td>
                        <td>
                            <form method="post" action="' . Config::KECSO_URL_COURIORADDRESS . '" style="display:inline;">
                                <input type="hidden" name="updateAddressId" value="' . $item['_id'] . '">
                                <button type="submit" name="updateAddress">Szerkesztés</button>
                            </form>
                            <form method="post" action="' . Config::KECSO_URL_COURIORADDRESS . '" style="display:inline;">
                                <input type="hidden" name="deleteAddressId" value="' . $item['_id'] . '">
                                <button type="submit" name="deleteAddress">Törlés</button>
                            </form>
                        </td>
                    </tr>';
    
            if ($editaddress && $editaddress['_id'] == $item['_id']) {
                $html .= '<tr><td colspan="8">' . self::AddressEdit($editaddress) . '</td></tr>';
            }
        }
    
        $html .= '</tbody></table>';
    
        return $html;
    }
    
    private static function CreateAddressForm()
    {
        $html = '<form method="post" action="' . Config::KECSO_URL_COURIORADDRESS . '">';
        $html .= IndexView::CreateInput('Ledolgozott nap száma', 'day');
        $html .= IndexView::CreateInput('Időpont', 'time');
        $html .= IndexView::CreateInput('Hónap', 'month');
        $html .= IndexView::CreateInput('Össz cím', 'total_addresses');
        $html .= IndexView::CreateInput('Kézbesített címek', 'delivered_addresses');
        $html .= IndexView::CreateInput('Véglegvissza', 'final_return');
        $html .= IndexView::CreateInput('Élővissza', 'live_return');
        $html .= '<button type="submit" name="newAddress">Új cím hozzáadása</button>';
        $html .= '</form>';

        return $html;
    }
    
    public static function AddressEdit($editaddress)
    {
        $html = '<form method="post" action="' . Config::KECSO_URL_COURIORADDRESS . '">';
        $html .= '<input type="hidden" name="editAddressId" value="' . $editaddress['_id'] . '">';
        $html .= IndexView::CreateInputValue('Napok száma', 'day', $editaddress['day']);
        $html .= IndexView::CreateInputValue('Hónap', 'month', $editaddress['month']);
        $html .= IndexView::CreateInputValue('Időpont', 'time', $editaddress['time']);
        $html .= IndexView::CreateInputValue('Össz cím', 'total_addresses', $editaddress['total_addresses']);
        $html .= IndexView::CreateInputValue('Leadott cím', 'delivered_addresses', $editaddress['delivered_addresses']);
        $html .= IndexView::CreateInputValue('Véglegvissza', 'final_return', $editaddress['final_return']);
        $html .= IndexView::CreateInputValue('Élővissza', 'live_return', $editaddress['live_return']);
        $html .= '<button type="submit" name="saveAddress">Mentés</button>';
        $html .= '</form>';

        return $html;
    }
}