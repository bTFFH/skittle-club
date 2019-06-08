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
$dbName = "skittle_club2";
$port= 3306;
/* Создать соединение */
$conn = new mysqli($hostname, $username, $password, $dbName, $port);
if (mysqli_connect_errno()) exit("Unable to connect");
$conn->set_charset('utf8');
/* Дальше идет код основного файла */
?>
