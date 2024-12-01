<?php
session_start(); // Start sesji

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['username'])) {
    header("Location: /");
    exit();
}

// Zmienne do połączenia z bazą danych
$servername = "localhost";
$username = "root";
$password = "tajnehaslodb";
$dbname = "osadnicy";

$conn = new mysqli($servername, $username, $password, $dbname, NULL, '/run/mysqld/mysqld.sock');

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Pobranie aktualnych danych o surowcach użytkownika
$sql = "SELECT wood, stone, gold FROM users WHERE username = '{$_SESSION['username']}'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['wood'] = $row['wood'];
    $_SESSION['stone'] = $row['stone'];
    $_SESSION['gold'] = $row['gold'];
} else {
    echo "Błąd: Nie znaleziono użytkownika.";
    exit();
}

// Przechowywanie zasobów w tablicy
$resources = [
    'drewno' => $_SESSION['wood'],
    'kamień' => $_SESSION['stone'],
    'złoto' => $_SESSION['gold']
];
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Osadnicy - Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Logo -->
        <img src="img/redacademy.png" alt="Red Academy Logo" class="logo">

        <h1>Witaj, <?php echo $_SESSION['username']; ?>!</h1>
        <p>Twoje zasoby:</p>
        <ul>
            <li><img src="img/wood.png" alt="Drewno"><span class="resource-title">Drewno: <?php echo $_SESSION['wood']; ?></span></li>
            <li><img src="img/stone.png" alt="Kamień"><span class="resource-title">Kamień: <?php echo $_SESSION['stone']; ?></span></li>
            <li><img src="img/gold.png" alt="Złoto"><span class="resource-title">Złoto: <?php echo $_SESSION['gold']; ?></span></li>
        </ul>

        <!-- Formularz wyszukiwania surowców -->
        <h2>Wyszukaj surowiec</h2>
        <form class="search-form" action="dashboard.php" method="GET">
            <input type="text" id="resource" name="resource" placeholder="Wprowadź nazwę surowca">
            <input type="submit" value="Szukaj">
        </form>

        <?php
        // Sprawdzanie, czy formularz został przesłany
        if (isset($_GET['resource'])) {
            $searchedResource = $_GET['resource'];

            // Sprawdzanie, czy szukany surowiec istnieje w zasobach użytkownika
            if (array_key_exists($searchedResource, $resources)) {
                echo "<p>Szukany surowiec: <strong>" . htmlspecialchars($searchedResource) . "</strong>, ilość: " . $resources[$searchedResource] . "</p>";
            } else {
                echo "<p>Szukany surowiec: <strong>" . htmlspecialchars($searchedResource) . "</strong> nie został znaleziony.</p>";
            }
        }
        ?>

        <!-- Formularz wysyłania surowców -->
        <h2>Wyślij surowiec do gracza</h2>
        <form class="send-resource-form" action="wyslij.php" method="POST">
            <label for="recipient">Wybierz gracza:</label>
            <select id="recipient" name="recipient">
                <option value="Maciej">Maciej</option>
                <option value="Piotr">Piotr</option>
                <option value="Agata">Agata</option>
            </select>
            <br><br>

            <label for="resource">Wybierz surowiec:</label>
            <select id="resource" name="resource">
                <option value="wood">Drewno</option>
                <option value="stone">Kamień</option>
                <option value="gold">Złoto</option>
            </select>
            <br><br>

            <label for="amount">Podaj ilość:</label>
            <input type="number" id="amount" name="amount" min="1">
            <br><br>

            <input type="submit" value="Wyślij">
        </form>

        <!-- Link do wylogowania -->
        <a class="logout-link" href="logout.php">Wyloguj się</a>
    </div>
</body>
</html>
