<?php
$datas = [
    ['feladatkor' => 'Feladatkör1', 'telefonszam' => '0620 2964 378'],
    ['feladatkor' => 'Feladatkör2', 'telefonszam' => '0630 1234 567'],
    ['feladatkor' => 'Feladatkör3', 'telefonszam' => '0670 9876 543'],
    // További adatok...
];

echo '<ul>';

foreach ($datas as $data) {
    echo '<li><strong>' . $data['feladatkor'] . '</strong></li>';
    echo '<div><h3>Telefonszám</h3>: ' . $data['telefonszam'] . '</div>';
}

echo '</ul>';
?>