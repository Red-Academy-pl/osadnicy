<?php
session_start();
session_destroy(); // Usunięcie wszystkich danych z sesji
header("Location: /"); // Przekierowanie na stronę logowania
exit();
?>

