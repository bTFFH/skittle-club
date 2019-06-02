<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel="icon" href="/IndZ/images/logo.png">
    <link rel='stylesheet' href='/IndZ/styles/table.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>Игроки клуба</title>
    <?php
    session_start();
    if (!isset($_SESSION['username']))
        header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnOpen.php');
    ?>
</head>
<body>
<div style="display: flex; flex-wrap: nowrap; flex-direction: row;">
    <div>
        <?php
        include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
        ?>
    </div>
    <div class="general-table">
        <br />
        <br />
        <table cellpadding="3 5">
            <caption>Игроки клуба</caption>
            <tr>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Телефон</th>
                <th>Адрес</th>
                <th>Команда</th>
                <th></th>
                <th></th>
            </tr>
            <?php
            $query = 'SELECT * FROM PlayersV';
            $stmt = $conn->stmt_init();
            /**
             * поскольку используется класс mysqli_stmt
             * результат выполнения запроса всегда будет буферизованным
             */
            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($id, $name, $surname, $phone, $street, $house, $team);
                $stmt->store_result();  // вернет буферизованный запрос
                /*$result = $stmt->get_result();  // получен ответ как класс mysqli_result

                while ($row = $result->fetch_row())  // $result->fetch_assoc() для получения асоциативного массива
                    echo $row;*/

                while ($stmt->fetch())
                    echo "<tr><td>$name</td><td>$surname</td><td>$phone</td><td>$street, $house</td><td>$team</td><td class='edit-btn'><form method='POST' action='/IndZ/actions/player.php'><button type='submit' name='edit' value=$id><img src='/IndZ/images/settings.svg' alt='Изменить'/></button></form></td><td class='edit-btn'><form method='POST' action='/IndZ/helpers/delete.php'><button type='submit' name='delete' value=\"" . $id . ' players"' . "><img src='/IndZ/images/delete.svg' alt='Удалить'/></button></form></td></tr>";

                $stmt->free_result();
                $stmt->close();
            }
            ?>
        </table>
    </div>
</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>