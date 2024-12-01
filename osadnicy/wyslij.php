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

// Inicjalizacja zmiennej $message
$message = "Nieznany błąd."; // Domyślna wiadomość na wypadek problemów

// Pobranie danych z formularza
if (isset($_POST['recipient'], $_POST['resource'], $_POST['amount'])) {
    $recipient = $_POST['recipient'];
    $resource = $_POST['resource'];
    $amount = (int)$_POST['amount'];
    $sender = $_SESSION['username'];

    // Sprawdzanie poprawności danych wejściowych
    if ($amount <= 0) {
        $message = "Nieprawidłowa ilość surowca.";
    } else {
        // Pobranie zasobów wysyłającego
        $sqlSender = "SELECT $resource FROM users WHERE username = '$sender'";
        $resultSender = $conn->query($sqlSender);

        if ($resultSender->num_rows === 0) {
            $message = "Nie znaleziono wysyłającego użytkownika.";
        } else {
            $rowSender = $resultSender->fetch_assoc();
            if ($rowSender[$resource] < $amount) {
                $message = "Nie masz wystarczającej ilości surowca.";
            } else {
                // Pobranie danych odbiorcy
                $sqlRecipient = "SELECT $resource FROM users WHERE username = '$recipient'";
                $resultRecipient = $conn->query($sqlRecipient);

                if ($resultRecipient->num_rows === 0) {
                    $message = "Nie znaleziono odbiorcy.";
                } else {
                    // Transakcja: aktualizacja danych
                    $conn->begin_transaction();

                    try {
                        // Odejmowanie surowca od wysyłającego
                        $sqlUpdateSender = "UPDATE users SET $resource = $resource - $amount WHERE username = '$sender'";
                        $sqlUpdateRecipient = "UPDATE users SET $resource = $resource + $amount WHERE username = '$recipient'";
                        $conn->query($sqlUpdateSender);
                        $conn->query($sqlUpdateRecipient);

                        $conn->commit();
                        $message = "Wysłano $amount jednostek $resource do gracza $recipient.";
                    } catch (Exception $e) {
                        $conn->rollback();
                        $message = "Wystąpił błąd podczas wysyłania.";
                    }
                }
            }
        }
    }
} else {
    $message = "Brak danych w formularzu.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wysyłanie surowców</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Automatyczne przekierowanie na dashboard.php po 3 sekundach
        setTimeout(() => {
            window.location.href = "dashboard.php";
        }, 3000);
    </script>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($message); ?></h1>
        <p>Za chwilę nastąpi przekierowanie na dashboard...</p>
    </div>
</body>
</html>

