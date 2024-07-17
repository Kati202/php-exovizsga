<?php
session_start();



// Ellenőrzés, hogy be van-e jelentkezve
if (isset($_SESSION['user_id'])) {
    header('Location: ' . \App\Config::BASE_URL); // Ha be van jelentkezve, átirányítjuk a főoldalra
    exit();
}

// Ellenőrzés, hogy POST kérést küldtek-e (bejelentkezési adatok elküldése)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ellenőrzés a konfigurációs fájlban tárolt felhasználók között
    $users = \App\Config::USERS1; // Itt a konkrét felhasználók adatai kellenek majd
    if (array_key_exists($username, $users) && $users[$username] === $password) {
        $_SESSION['kecso'] = $username; // Felhasználónév tárolása a session-ben
        $_SESSION['kecso12345'] = $username;
        header('Location: ' . \App\Config::BASE_URL); // Sikeres bejelentkezés esetén átirányítás a főoldalra
        exit();
    } else {
        // Sikertelen bejelentkezés
        $_SESSION['error_message'] = 'Hibás felhasználónév vagy jelszó.';
        header('Location: ' . \App\Config::AUTH_URL); // Visszairányítás a bejelentkezési oldalra
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Bejelentkezés</title>
</head>
<body>
    <h2>Bejelentkezés</h2>
    
    <?php
    // Hibaüzenet megjelenítése, ha van
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
        unset($_SESSION['error_message']); // Hibaüzenet törlése a session-ből
    }
    ?>

    <form action="<?php echo \App\Config::AUTH_URL; ?>" method="POST">
        <label for="username">Felhasználónév:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Jelszó:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" name="login" value="Bejelentkezés">
    </form>
</body>
</html>


