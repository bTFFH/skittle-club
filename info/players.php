<!DOCTYPE html>
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/infoHead.php'); ?>
    <title>Игроки клуба</title>
</head>
<body>
<div style="display: flex; flex-wrap: nowrap; flex-direction: row;">
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
    ?>
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
            if ($stmt->prepare($query) && $stmt->execute()) {

                // привязка переменных к подготовленнуму запросу
                $stmt->bind_result($id, $name, $surname, $phone, $street, $house, $team);

                // вернет буферизованный запрос (передает результат запроса на клиента)
                $stmt->store_result();

                // вернет ответ как класс mysqli_result (вернет результат в виде массива)
                /*$result = $stmt->get_result();

                while ($row = $result->fetch_row())  // $result->fetch_assoc() для получения асоциативного массива
                    echo $row;*/

                while ($stmt->fetch())
                    echo "<tr><td>$name</td><td>$surname</td><td>$phone</td><td>$street, $house</td><td>$team</td><td class='edit-btn'><form method='POST' action='/IndZ/actions/player.php'><button type='submit' name='edit' value=$id><img src='/IndZ/images/settings.svg' alt='Изменить'/></button></form></td><td class='edit-btn'><form method='POST' action='/IndZ/helpers/delete.php'><button type='submit' name='delete' value=\"" . $id . ' players"' . "><img src='/IndZ/images/delete.svg' alt='Удалить'/></button></form></td></tr>";

                $stmt->free_result();  // освобождает буфер от результата запроса
                $stmt->close();  // закрывает только подготовленный запрос
            } else {
                $_SESSION['errno'] = $stmt->errno;
                $_SESSION['error'] = $stmt->error;
                header("Location: ../helpers/error.php");
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