<?php
namespace App\Views\KecsoView;

use App\Models\KecsoModel\CouriorsModel;
use App\Views\IndexView;
use App\Config;

class KecsoCouriorView
{
public static function ShowCourior()
    {
    $html = '';
    $html .= IndexView::OpenSection('Futárok hozzáadása');
    $html .= '<form method="post" action="' . Config::KECSO_URL . '">';
    $html .= IndexView::CreateInput('Azonosító', 'ids');
    $html .= IndexView::CreateInput('Futár neve', 'name');
    $html .= '<button type="submit" name="newCourior">Futár hozzáadása</button>';
    $html .= '</form>';

    $html .= '<h3>Futárok listája</h3>';
    $html .= self::DisplayCouriors();

    $html .= IndexView::CloseSection();
    return $html;
    }

   public static function CouriorData($couriordata, $editcourior = null, $id = null)
   {

    usort($couriordata, function($a, $b) {
        return $a['ids'] - $b['ids'];
    });
    $html = '';

    // Csoportosítás az 'ids' kulcs alapján
    $groupedData = [];
    foreach ($couriordata as $courior) {
        $ids = htmlspecialchars($courior->ids ?? '');

        if (!isset($groupedData[$ids])) {
            $groupedData[$ids] = [];
        }
        $groupedData[$ids][] = $courior;
    }

    // Csoportosított adatok megjelenítése
    foreach ($groupedData as $ids => $couriors) {
        $html .= '<h3>FutárSz.: ' . htmlspecialchars($ids) . '</h3>';
        $html .= self::DisplayCouriorGroup($couriors, $editcourior);
    }

    // Új elem hozzáadása űrlap
    $html .= '<form method="post" action="' . Config::KECSO_URL_COURIORDATA . '?param=' . $id . '">';
    $html .= IndexView::CreateInput('Azonosító', 'ids');
    $html .= IndexView::CreateInput('Neve', 'name');
    $html .= IndexView::CreateInput('Születési ideje', 'date');
    $html .= IndexView::CreateInput('Születési helye', 'dateaddress');
    $html .= IndexView::CreateInput('Kora', 'age');
    $html .= IndexView::CreateInput('Lakcíme', 'address');
    $html .= IndexView::CreateInput('Anyja neve', 'mothername');
    $html .= '<button type="submit" name="newCouriors">Futár adat hozzáadása</button>';
    $html .= '</form>';

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

     
private static function DisplayCouriorGroup($couriors, $editcourior = null)
{
    $html = '';

    $html .= '<table border="1" cellpadding="10">
                <thead>
                    <tr>
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

    foreach ($couriors as $courior) {
        $couriorId = (string)($courior->_id ?? '');

        $html .= '<tr>
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

    if (!empty($couriors)) {
        foreach ($couriors as $courior) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($courior['ids'] ?? '') . '</td>
                        <td>' . htmlspecialchars($courior['name'] ?? '') . '</td>
                        <td>
                            <form method="post" action="' . Config::KECSO_URL . '" style="display:inline;">
                                <input type="hidden" name="deleteCouriorId" value="' . htmlspecialchars($courior['_id'] ?? '') . '">
                                <button type="submit" name="deleteCourior">Törlés</button>
                            </form>
                         </td>
                    </tr>';
        }
        $html .= '</tbody></table>';
        
        if (isset($courior)) {
            $html .= '<a href="' . Config::KECSO_URL_COURIORDATA  .'">Személyes adatok</a>
                      <a href="' . Config::KECSO_URL_COURIORADDRESS .'">Hónapos szétbontás</a>';
        }
    } else {
        $html .= '<tr><td>Nincsenek elérhető futárok</td></tr>';
        $html .= '</tbody></table>';
    }

    return $html;
}


    public static function CouriorsAddress($addresses, $editaddress = null)
    {
        $html = '';
        usort($addresses, function($a, $b) {
            return $a['ids'] - $b['ids'];
        });
        $groupedData = self::GroupDataAddress($addresses);

        foreach ($groupedData as $ids => $items) 
        {
            $html .= self::DisplayAddressGroup($ids, $items, $editaddress);
        }
    
        $html .= self::CreateAddressForm();
        return $html;
    }
    
    private static function GroupDataAddress($addresses)
    {
        $groupedData = [];
    
        foreach ($addresses as $address) {
            if ($address instanceof \MongoDB\Model\BSONDocument && isset($address['ids'])) {
                $ids = $address['ids'];
                $name = $address['name'];
                $month = $address['month'];
                $day = $address['day'];
                $time = $address['time'];
                $total_addresses = $address['total_addresses'];
                $delivered_addresses = $address['delivered_addresses'];
                $final_return = $address['final_return'];
                $live_return = $address['live_return'];
                $id = (string) $address['_id'];
    
                if (!isset($groupedData[$ids])) {
                    $groupedData[$ids] = [];
                }
                $groupedData[$ids][] = [
                    'ids' => $ids,
                    'name' => $name,
                    'day' => $day,
                    'month' => $month,
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
    
    private static function DisplayAddressGroup($ids, $items, $editaddress)
    {
        $html = '';
    
        if (!empty($ids)) {
            $html .= '<h2>' . htmlspecialchars($ids) . '</h2>';
        }
    
        $html .= '<table border="1" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Név</th>
                            <th>Futár Azonosító</th>
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
                        <td>' . htmlspecialchars($item['name'] ?? '') . '</td>
                        <td>' . htmlspecialchars($item['ids'] ?? '') . '</td>
                        <td>' . htmlspecialchars($item['day'] ?? '') . '</td>
                        <td>' . htmlspecialchars($item['month'] ?? '') . '</td>
                        <td>' . htmlspecialchars(date('Y-m-d H:i', strtotime($item['time']->toDateTime()->format('Y-m-d H:i')))) . '</td>
                        <td>' . htmlspecialchars($item['total_addresses'] ?? '') . '</td>
                        <td>' . htmlspecialchars($item['delivered_addresses'] ?? '') . '</td>
                        <td>' . htmlspecialchars($item['final_return'] ?? '') . '</td>
                        <td>' . htmlspecialchars($item['live_return'] ?? '') . '</td>
                        <td>
                            <form method="post" action="' . Config::KECSO_URL_COURIORADDRESS . '?operation=courioraddress&param=' . htmlspecialchars($item['_id'] ?? '') . '" style="display:inline;">
                             <input type="hidden" name="updateAddressId" value="' . ($item['_id'] ?? '') . '">
                             <button type="submit" name="updateAddress">Szerkesztés</button>
                            </form>
                                <form method="post" action="' . Config::KECSO_URL_COURIORADDRESS . '" style="display:inline;">
                                <input type="hidden" name="deleteAddressId" value="' . ($item['_id'] ?? '') . '">
                                <input type="hidden" name="operation" value="courioraddress">
                                <input type="hidden" name="param" value="' . ($item['_id'] ?? '') . '">
                                <button type="submit" name="deleteAddress">Törlés</button>
                            </form>
                        </td>
                    </tr>';
    
            // Ha a szerkesztési gomb megnyomásra került, jelenjen meg a szerkesztési űrlap
            if ($editaddress && $editaddress['_id'] == $item['_id']) {
                $html .= '<tr><td colspan="10">' . self::AddressEdit($editaddress) . '</td></tr>';
            }
        }
    
        $html .= '</tbody></table>';
    
        return $html;
    }
    
    public static function CreateAddressForm()
    {
        $html = '<form method="post" action="' . Config::KECSO_URL_COURIORADDRESS . '">';
        $html .= IndexView::CreateInput('Név', 'name');
        $html .= IndexView::CreateInput('Azonosító', 'ids');
        $html .= IndexView::CreateInput('Ledolgozott nap száma', 'day');
        $html .= IndexView::CreateInput('Hónap', 'month');
        $html .= '<label for="time">Pontos időpont:</label>';
        $html .= '<input type="datetime-local" id="time" name="time">';
        $html .= IndexView::CreateInput('Össz cím', 'total_addresses');
        $html .= IndexView::CreateInput('Kézbesített címek', 'delivered_addresses');
        $html .= IndexView::CreateInput('Véglegvissza', 'final_return');
        $html .= IndexView::CreateInput('Élővissza', 'live_return');
        $html .= '<button type="submit" name="newAddress">Új cím hozzáadása</button>';
        $html .= '</form>';

        return $html;
    
        
    }

    private static function AddressEdit($editaddress)
    {
        $html = '<form method="post" action="' . Config::KECSO_URL_COURIORADDRESS . '">';
        $html .= '<input type="hidden" name="editAddressId" value="' . $editaddress['_id'] . '">';
        $html .= IndexView::CreateInputValue('Név', 'name', $editaddress['name']);
        $html .= IndexView::CreateInputValue('Azonosító', 'ids', $editaddress['ids']);
        $html .= IndexView::CreateInputValue('Ledolgozott nap száma', 'day', $editaddress['day']);
        $html .= IndexView::CreateInputValue('Hónap', 'month', $editaddress['month']);
        $html .= '<label for="time">Pontos időpont:</label>';
        $html .= '<input type="datetime-local" id="time" name="time" value="';

        if (is_object($editaddress['time']) && method_exists($editaddress['time'], 'toDateTime')){
        $html .= date('Y-m-d\TH:i', strtotime($editaddress['time']->toDateTime()->format('Y-m-d H:i')));
        } else {$html .= date('Y-m-d\TH:i', strtotime($editaddress['time']));}$html .= '">';

        $html .= IndexView::CreateInputValue('Össz cím', 'total_addresses', $editaddress['total_addresses']);
        $html .= IndexView::CreateInputValue('Kézbesített címek', 'delivered_addresses', $editaddress['delivered_addresses']);
        $html .= IndexView::CreateInputValue('Véglegvissza', 'final_return', $editaddress['final_return']);
        $html .= IndexView::CreateInputValue('Élővissza', 'live_return', $editaddress['live_return']);
        $html .= '<button type="submit" name="saveAddress">Mentés</button>';
        $html .= '</form>';
    
        return $html;
    }
    
    public static function ShowDeliveriesByGroup($deliveries, $startDate, $endDate, $selectedIds = [])
{

    $html = '<h2>Kézbesítések összesítése az időszakra (' . $startDate . ' - ' . $endDate . ')</h2>';

    
    $html .= '<form method="post" action="' . Config::KECSO_URL_COURIORADDRESS . '">
                <label for="startDate">Kezdő dátum:</label>
                <input type="date" id="startDate" name="startDate" value="' . htmlspecialchars($startDate) . '">
                <label for="endDate">Vég dátum:</label>
                <input type="date" id="endDate" name="endDate" value="' . htmlspecialchars($endDate) . '">';

    
    if ($deliveries !== null) {
     
        $html .= '<label for="ids">Válassz azonosítókat:</label>
                    <select id="ids" name="ids[]" multiple>';

       
        if (!empty($deliveries)) {
            foreach ($deliveries as $delivery) {
                $html .= '<option value="' . htmlspecialchars($delivery['_id'] ?? '') . '" ' . (in_array($delivery['_id'], $selectedIds) ? 'selected' : '') . '>' . htmlspecialchars($delivery['_id'] ?? '') . '</option>';
            }
        }

        $html .= '</select>';
    }

    
    $html .= '<button type="submit" name="filter">Szűrés</button>
              </form>';

    
    if ($deliveries === null) {
        $html .= '<p>Nincs adat az időszakra.</p>';
    }

    
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
    }
    
    return $html;
}

    

    
   
}