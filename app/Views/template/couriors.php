<?php
echo '<table>
<thead>
<tr>
    <th>Név</th>
    <th>Személyes adatai</th>
    <th>Címei</th>
</tr>
</thead>
<tbody>';

foreach ($couriors as $courior) {
echo '<tr>';
echo '<td>' . $courior['name'] . '</td>';
echo '<td><a href="#">' . $courior['data'] . '</a></td>';
echo '<td><a href="#">' . $courior['adress'] . '</a></td>';
echo '</tr>';
}

echo '</tbody>
</table>';
?>