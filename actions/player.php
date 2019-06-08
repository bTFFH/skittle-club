<!DOCTYPE html>
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/actionsHead.php'); ?>
    <title>Добавление игрока</title>
</head>
<body>
<div style="display: flex">
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
    ?>
    <div class="add-form">
        <br />
        <br />
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit'])) {
            $stmt = $conn->stmt_init();
            $query = 'SELECT id, name FROM streets';
            $streets = [];
            if ($stmt->prepare($query) && $stmt->execute()) {
                $stmt->bind_result($id, $street);

                while ($stmt->fetch())
                    $streets[$street] = $id;

                $stmt->free_result();
            } else {
                $_SESSION['errno'] = $stmt->errno;
                $_SESSION['error'] = $stmt->error;
                header("Location: ../helpers/error.php", true, 303);
            }

            if (in_array($_POST['street'], array_keys($streets))) {
                $streetId = $streets[$_POST['street']];
            } else {
                $query = 'INSERT INTO `streets`(`name`) VALUES (?)';
                if ($stmt->prepare($query)
                    && $stmt->bind_param('s', $_POST['street'])
                    && $stmt->execute()
                ) {
                    $streetId = $stmt->insert_id;
                    $stmt->free_result();
                }
                else {
                    $_SESSION['errno'] = $conn->errno;
                    $_SESSION['error'] = $conn->error;
                    header("Location: ../helpers/error.php", true, 303);
                }
            }

            if ($_SESSION['update'] !== "Not updated")
                $query = $_POST['team'] == 0 ?
                    "UPDATE `players` SET `name` = ?, `surname` = ?, `phone` = ?, `street_id` = ?, `house` = ?, `team_id` = NULL WHERE id = $_SESSION[update]" :
                    "UPDATE `players` SET `name` = ?, `surname` = ?, `phone` = ?, `street_id` = ?, `house` = ?, `team_id` = ? WHERE id = $_SESSION[update]";
            else
                $query = $_POST['team'] == 0 ?
                    'INSERT INTO `players`(`name`, `surname`, `phone`, `street_id`, `house`) VALUES (?, ?, ?, ?, ?)' :
                    'INSERT INTO `players`(`name`, `surname`, `phone`, `street_id`, `house`, `team_id`) VALUES (?, ?, ?, ?, ?, ?)';

            if ($stmt->prepare($query)) {
                $_POST['team'] == 0 ?
                    $stmt->bind_param('ssiii', $_POST['name'], $_POST['surname'],
                        $_POST['phone'], $streetId, $_POST['house']) :
                    $stmt->bind_param('ssiiii', $_POST['name'], $_POST['surname'],
                        $_POST['phone'], $streetId, $_POST['house'], $_POST['team']);
                if ($stmt->execute()) {
                    if ($_SESSION['update'] !== "Not updated")
                        echo "<p><output style=\"color: seagreen;\">Игрок успешно обновлен</output></p>";
                    else {
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
                    }

                    unset($_SESSION['update']);
                } else {
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
            } else {
                $_SESSION['errno'] = $conn->errno;
                $_SESSION['error'] = $conn->error;
                header("Location: ../helpers/error.php", true, 303);
            }
        } else {
            $_SESSION['update'] = "Not updated";
            $id = 0;
            $name = '';
            $surname = '';
            $phone = '';
            $street = '';
            $house = '';
            $team_id = 0;
            $teams = '';
            $query = 'SELECT id, team_name FROM teams';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query) && $stmt->execute()) {
                $stmt->bind_result($id, $team_name);
                $stmt->store_result();

                while ($stmt->fetch())
                    $teams .= "<option value=$id>$team_name</option>";

                $stmt->free_result();
            } else {
                $_SESSION['errno'] = $stmt->errno;
                $_SESSION['error'] = $stmt->error;
                header("Location: ../helpers/error.php", true);
            }

            if (isset($_POST['edit'])) {
                $_SESSION['update'] = $_POST['edit'];
                $query = 'SELECT name, surname, phone, street_id, house, team_id FROM players WHERE id = ?';
                if ($stmt->prepare($query)
                    && $stmt->bind_param('i', $_POST['edit'])
                    && $stmt->execute()
                ) {
                    $stmt->bind_result($name, $surname, $phone, $streetId, $house, $team_id);
                    $stmt->store_result();
                    $stmt->fetch();
                    $stmt->free_result();
                    if ($team_id !== "NULL") {
                        $query = "SELECT `team_name` FROM `teams` WHERE `id` = $team_id";
                        if ($result = $conn->query($query)) {
                            $row = $result->fetch_row();
                            $result->free();
                            $team_name = $row[0];
                        } else {
                            $_SESSION['qerr'] = $query;
                            $_SESSION['errno'] = $conn->errno;
                            $_SESSION['error'] = $conn->error;
                            header("Location: ../helpers/error.php", true);
                        }
                    }

                    $query = "SELECT `name` FROM `streets` WHERE `id` = $streetId";
                    if ($result = $conn->query($query)) {
                        $row = $result->fetch_row();
                        $result->free();
                        $street = $row[0];
                    } else {
                        $_SESSION['qerr'] = $query." --- $name, $surname, $phone, $streetId, $house, $team_id, $_POST[edit]";
                        $_SESSION['errno'] = $conn->errno;
                        $_SESSION['error'] = $conn->error;
                        header("Location: ../helpers/error.php", true);
                    }
                } else {
                    $_SESSION['qerr'] = $query;
                    $_SESSION['errno'] = $stmt->errno;
                    $_SESSION['error'] = $stmt->error;
                    header("Location: ../helpers/error.php", true);
                }
            }

            if ($team_id == 0 || $team_id === "NULL") {
                $team_id = 0;
                $team_name = "Не в команде";
            }

            ?>
            <form name="insertNewPlayer" method="POST" action="player.php">
                <p><label>Имя игрока<input type="text" name="name" maxlength="50" value="<?php echo $name; ?>"
                                           pattern="[A-ZА-Я^ЪЬ]{1}[A-ZА-ЯЁa-zа-яё -]{1,49}" placeholder="Adam"
                                           required/></label></p>
                <p><label>Фамилия игрока<input type="text" name="surname" maxlength="100" value="<?php echo $surname; ?>"
                                               pattern="[A-ZА-Я^ЪЬ]{1}[A-ZА-ЯЁa-zа-яё -]{1,99}" placeholder="Smith"
                                               required/></label></p>
                <p><label>Телефон<input type="text" name="phone" maxlength="11" pattern="[0-9]{11}"
                                        placeholder="12345678901" value="<?php echo $phone; ?>" required/></label></p>
                <p><label>Улица<input type="text" name="street" maxlength="50" placeholder="Wall Street"
                                      value="<?php echo $street; ?>" required/></label></p>
                <p><label>Дом<input type="text" name="house" maxlength="3" pattern="[0-9]{1,3}" placeholder="17"
                                    value="<?php echo $house; ?>" required/></label></p>
                <p><label>Команда<select name="team">
                            <option selected value="<?php echo $team_id; ?>"><?php echo $team_name; ?></option>
                            <option value="0">Не в команде</option>
                            <?php echo $teams; ?></select></label></p>
                <div class="submit-btn" style="padding-left: 230px"><input type="submit"
                value="<?php if (!isset($_POST['edit'])) echo 'Добавить'; else echo 'Обновить'; ?>"/></div>
            </form>
            <?php
        }
        $stmt->close();
        ?>
    </div>

</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
