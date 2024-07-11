<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo \App\Config::BASE_URL?>style.css">
    
    <title>Exo-vizsga</title>
</head>
<body>
  <header>
    <nav>
    <a href="<?php echo \App\Config::KECSO_URL; ?>">Kecskeméti depó</a>
    <a href="<?php echo \App\Config::TATAB_URL; ?>">Tatabánya depó</a>
    <a href="<?php echo \App\Config::NYERGES_URL; ?>">Nyergesújfalui depó</a>
    <a href="<?php echo \App\Config::HALAS_URL; ?>">Kiskunhalasi depó</a>
    <a href="<?php echo \App\Config::HOME_URL; ?>">Vissza a bejelentkezési felülethez</a>
    </nav>
  </header>
</body>
</html>
