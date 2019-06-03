<?php
/* Данный файл предназначен только для импорта
 * Открытие соединенния с БД
 * Переменные для соединения с базой данных
 */
if (!isset($_SESSION['username']))
    header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
$hostname = "localhost";
$username = "root";
$password = "";
$dbName = "skittle_club";
/* Создать соединение */
$conn = new mysqli($hostname, $username, $password, $dbName);
if (mysqli_connect_errno()) exit("Unable to connect");
$conn->set_charset('utf8');
/* Дальше идет код основного файла */
?>
