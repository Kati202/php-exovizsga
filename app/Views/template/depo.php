<?php
$data = [
    ['title' => 'Címe', 'content' => 'Kecskemét Matkói út 16'],
    ['title' => 'Területe', 'content' => '300 négyzetméter'],
    ['title' => 'Futárok Száma', 'content' => '16'],
    ['title' => 'Autók száma', 'content' => '18'],
];

$html = '<table border="1" cellpadding="10">';


$html .= '<thead><tr>';
foreach ($data as $item) {
    $html .= '<th>' . htmlspecialchars($item['title']) . '</th>';
}
$html .= '</tr></thead>';


$html .= '<tbody><tr>';
foreach ($data as $item) {
    $html .= '<td>' . htmlspecialchars($item['content']) . '</td>';
}
$html .= '</tr></tbody>';

$html .= '</table>';

return $html;
?>