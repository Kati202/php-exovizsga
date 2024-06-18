<?php
$data = [
    ['title' => 'Címe', 'content' => 'Egy példa cím'],
    ['title' => 'Területe', 'content' => '100 négyzetméter'],
    ['title' => 'Futárok Száma', 'content' => '5'],
    ['title' => 'Autók száma', 'content' => '3'],
    
];

echo '<ul>';

foreach ($data as $item) {
    echo '<li><strong>' . $item['title'] . '</strong>:' . $item['content'] . '</li>';
}

echo '</ul>';
?>