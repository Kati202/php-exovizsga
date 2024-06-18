<?php
$depoName=['Kecskemét','Tatabánya,','Kiskunhalas','Nyerges'];
if (isset($_GET['depo'])) {
    // Ellenőrizzük a depó nevet, például adatbázisból vagy egyéb forrásból
    $depoName = $_GET['depo'];
    
    // Példaként itt lehetne egy switch vagy egyéb logika, hogy kiválasztjuk a depóhoz tartozó tartalmat
    switch ($depoName) {
        case 'Kecskemét':
            $content = "Üdvözöljük a Kecskeméti depó oldalán!";
            break;
        case 'Tatabánya':
            $content = "Üdvözöljük a Tatabányai depó oldalán!";
            break;
        // Egyéb depók kezelése...
        default:
            $content = "Nincs ilyen depó.";
            break;
    }

    // Visszaküldjük a tartalmat JSON formátumban az AJAX kérésre
    echo json_encode(['content' => $content]);
} else {
    // Ha nincs érvényes depó név a kérésben, visszaküldünk egy hibaüzenetet
    echo json_encode(['error' => 'Nem megfelelő kérés.']);
}
?>