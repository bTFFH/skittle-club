<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/addition.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>Добавление игрока</title>
    <?php
    session_start();
    if (!isset($_SESSION['username']))
        header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnOpen.php');
    ?>
</head>
<body>
<div style="display: flex">
    <div style="flex-wrap: nowrap">
        <?php
        include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
        ?>
    </div>
    <div class="add-form">
        <br />
        <br />
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {

        }
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $query = $_POST['team'] == 0 ?
                'INSERT INTO `players`(`name`, `surname`, `phone`, `street`, `house`) VALUES (?, ?, ?, ?, ?)' :
                'INSERT INTO `players`(`name`, `surname`, `phone`, `street`, `house`, `team_id`) VALUES (?, ?, ?, ?, ?, ?)';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                $_POST['team'] == 0 ?
                    $stmt->bind_param('ssisi', $_POST['name'], $_POST['surname'],
                        $_POST['phone'], $_POST['street'], $_POST['house']) :
                    $stmt->bind_param('ssisii', $_POST['name'], $_POST['surname'],
                        $_POST['phone'], $_POST['street'], $_POST['house'], $_POST['team']);
                if ($stmt->execute()) {
                    echo "<p><output style=\"color: seagreen;\">Новый игрок успешно добавлен</output></p>";

                    $playerId = $stmt->insert_id;
                    $query = 'INSERT INTO `players_stats`(`player_id`) VALUES (?)';
                    $stmt->prepare($query);
                    $stmt->bind_param('i', $playerId);

                    if ($stmt->execute()) {
                        echo "<p><output style=\"color: seagreen;\">Статистика игрока обновлена</output></p>";
                    } else {
                        echo "<p><output style=\"color: indianred;\">Статистика игрока не была обновлена</output></p>";
                    }

                    $stmt->close();
                } else {
                    $stmt->close();
                    ?>
                    <form name="errorNewPlayer" method="GET" action="player.php" style="border: none">
                        <div style="color: indianred;">
                            <p><output>При добавлении игрока возникла ошибка</output></p>
                            <p><output>Попробуйте осуществить операцию еще раз</output></p>
                            <div class="submit-btn"><input type="submit" value="Попробовать"/></div>
                        </div>
                    </form>
                    <?php
                }
            }
        } else {
            $teams = '';
            $query = 'SELECT * FROM teams';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($id, $team_name, $cap);
                $stmt->store_result();

                while ($stmt->fetch())
                    $teams .= "<option value=$id>$team_name</option>";

                $stmt->free_result();
                $stmt->close();
            }
            ?>
            <form name="insertNewPlayer" method="POST" action="player.php">
                <p><label>Имя игрока<input type="text" name="name" maxlength="50"
                                           pattern="[A-ZА-Я^ЪЬ]{1}[A-ZА-ЯЁa-zа-яё -]{1,49}" placeholder="Adam"
                                           required/></label></p>
                <p><label>Фамилия игрока<input type="text" name="surname" maxlength="100"
                                               pattern="[A-ZА-Я^ЪЬ]{1}[A-ZА-ЯЁa-zа-яё -]{1,99}" placeholder="Smith"
                                               required/></label></p>
                <p><label>Телефон<input type="text" name="phone" maxlength="11" pattern="[0-9]{11}"
                                        placeholder="12345678901" required/></label></p>
                <p><label>Улица<input type="text" name="street" maxlength="50" placeholder="Wall Street"
                                      required/></label></p>
                <p><label>Дом<input type="text" name="house" maxlength="3" pattern="[0-9]{1,3}" placeholder="17"
                                    required/></label></p>
                <p><label>Команда<select name="team">
                            <option selected value="0">Не в команде</option>
                            <?php echo $teams; ?></select></label></p>
                <div class="submit-btn" style="margin-left: 295px"><input type="submit" value="Добавить"/></div>
            </form>
            <?php
        }
        ?>
    </div>

</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
