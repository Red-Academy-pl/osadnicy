<?php
session_start(); // Start sesji

// Zmienne niezbedne do polaczenia z baza danych
$servername = "localhost";
$username = "root";
$password = "tajnehaslodb";
$dbname = "osadnicy";

$conn = new mysqli($servername, $username, $password, $dbname, NULL, '/run/mysqld/mysqld.sock');

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Pobranie danych z formularza
$user = $_POST['username'];
$pass = $_POST['password'];

// Zapytanie SQL (bez zabezpieczeń, aby pokazać SQL Injection)
$sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
$result = $conn->query($sql);

// Sprawdzenie czy użytkownik istnieje
if ($result->num_rows > 0) {
    // Użytkownik znaleziony
    $row = $result->fetch_assoc();

    // Zapisanie informacji o użytkowniku w sesji
    $_SESSION['username'] = $row['username'];
    $_SESSION['wood'] = $row['wood'];
    $_SESSION['stone'] = $row['stone'];
    $_SESSION['gold'] = $row['gold'];

    // Przekierowanie do strony dashboard.php
    header("Location: dashboard.php");
    exit();
} else {
    //Bledne dane logowania
    echo "<h1>Błąd logowania</h1>";
}


$conn->close();
?>
