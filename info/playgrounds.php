<!DOCTYPE html>
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/infoHead.php'); ?>
    <title>Игральные площадки</title>
</head>
<body>
<div style="display: flex">
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/header.php");
    ?>
    <div class="general-table">
        <br/>
        <br/>
        <table cellpadding="3 5">
            <caption>Игральные площадки</caption>
            <tr>
                <th>Название</th>
                <th style='width: 75%'>Описание, особенности</th>
                <th></th>
                <th></th>
            </tr>
            <?php
            $query = 'SELECT * FROM PlaygroundsV';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query) && $stmt->execute()) {
                $stmt->bind_result($id, $name, $features);

                while ($stmt->fetch()) {
                    if ($features == '') $features = 'Нет данных';
                    echo "<tr><td>$name</td><td >$features</td><td class='edit-btn'><form method='POST' action='/IndZ/actions/playground.php'><button type='submit' name='edit' value=$id><img src='/IndZ/images/settings.svg' alt='Изменить'/></button></form></td><td class='edit-btn'><form method='POST' action='/IndZ/helpers/delete.php'><button type='submit' name='delete' value=\"" . $id . ' playgrounds"' . "><img src='/IndZ/images/delete.svg' alt='Удалить'/></button></form></td></tr>";
                }

                $stmt->free_result();
                $stmt->close();
            } else {
                $_SESSION['errno'] = $stmt->errno;
                $_SESSION['error'] = $stmt->error;
                header("Location: ../helpers/error.php", true);
            }
            ?>
        </table>
    </div>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnClose.php");
    ?>
</body>
</html>
