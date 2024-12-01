<?php
session_start(); // Start sesji

// Sprawdzenie, czy użytkownik jest zalogowany
if (isset($_SESSION['username'])) {
    // Jeśli użytkownik jest zalogowany, przekierowanie na dashboard
    header("Location: dashboard.php");
    exit();
}

// Połączenie z bazą danych
$servername = "localhost";
$username = "root";
$password = "tajnehaslodb";
$dbname = "osadnicy";

$conn = new mysqli($servername, $username, $password, $dbname, NULL, '/run/mysqld/mysqld.sock');

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

/// Sprawdzanie, czy formularz wyszukiwarki został wysłany
$monsterName = "";
$searchResults = ""; // Zmienna przechowująca wyniki
if (isset($_GET['monster_name'])) {
    $monsterName = $_GET['monster_name'];

    // Zapytanie podatne na SQL Injection
    $sql = "SELECT name, type, attack, def FROM monsters WHERE name = '$monsterName'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Tworzenie tabeli z wynikami
        $searchResults .= "<table border='1'>
                            <tr>
                                <th>Nazwa</th>
                                <th>Typ</th>
                                <th>Atak</th>
                                <th>Obrona</th>
                            </tr>";
        while ($row = $result->fetch_assoc()) {
            $searchResults .= "<tr>
                                <td>" . $row['name'] . "</td>
                                <td>" . $row['type'] . "</td>
                                <td>" . $row['attack'] . "</td>
                                <td>" . $row['def'] . "</td>
                              </tr>";
        }
        $searchResults .= "</table>";
    } else {
        $searchResults = "Nie znaleziono takiego potwora.";
    }
}




$searchResults2 = "";
// Obsługa wyszukiwania graczy
if (isset($_GET['player'])) {
    $player = $_GET['player'];

    // Zapytanie SQL podatne na SQL Injection
    $sql = "SELECT username FROM users WHERE username = '$player'";

    // Wykonanie zapytania
    $result = $conn->query($sql);

    // Sprawdzenie, czy użytkownik istnieje
    if ($result->num_rows > 0) {
        $searchResults2 = "<p><em>Wyniki wyszukiwania są tymczasowo wyłączone.</em></p>";
    } else {
        $searchResults2 =  "<p><em>Gracz <strong>" . htmlspecialchars($player) . "</strong> nie istnieje.</em></p>";
    }
}

// Zamknięcie połączenia z bazą

$conn->close();



?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Osadnicy - Logowanie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Logo umieszczone nad formularzem -->
	<img src="img/redacademy.png" alt="Red Academy Logo" class="logo">

        <h1>Osadnicy - Logowanie</h1>
        <form action="login.php" method="POST">
            <input type="text" id="username" name="username" placeholder="Nazwa użytkownika"><br>
            <input type="password" id="password" name="password" placeholder="Hasło"><br>
            <input type="submit" value="Zaloguj">
        </form>
        
               <!-- Wyszukiwarka potworów -->
        <h2>Wyszukaj potwora</h2>
        <form action="index.php" method="GET">
            <label for="monster_name">Nazwa potwora:</label>
            <input type="text" id="monster_name" name="monster_name" placeholder="Wprowadź nazwę potwora">
            <input type="submit" value="Szukaj">
        </form>

        <!-- Wyświetlanie wyników wyszukiwania potworów poniżej wyszukiwarki -->
        <div>
            <?php echo $searchResults; ?>
        </div>

       </br>
        </br>


        <!-- Formularz wyszukiwania graczy -->
        <form method="GET" action="">
        <label for="player_search">Wyszukaj gracza:</label>
        <input type="text" name="player" id="player_search" placeholder="Podaj login gracza">
        <input type="submit" value="Szukaj gracza">
        </form>

         <div>
            <?php echo $searchResults2; ?>
        </div>

        
        
    </div>
</body>
</html>

