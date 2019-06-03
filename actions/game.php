<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/addition.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>Добавление игры</title>
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
        <br/>
        <br/>
        <?php
        $stmt = $conn->stmt_init();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit'])) {
            $query = $_SESSION['update'] === "Not updated" ?
                'INSERT INTO `competitions`(`team1_id`, `team2_id`, `playground_id`, `game_date`, `absence`) VALUES (?, ?, ?, ?, ?)' :
                'UPDATE `competitions` SET `team1_id` = ?, `team2_id` = ?, `playground_id` = ?, `game_date` = ?, `absence` = ? WHERE `id` = ?';
            if ($_POST['team1'] == $_POST['team2']) {
                $_SESSION['error'] = 1;
                if ($_SESSION['update'] !== "Not updated")
                    header("Location: game.php?edit=$_SESSION[update]", false, 303);
                else
                    header("Location: game.php", true, 303);
            } elseif ($_POST['team1'] == 0 || $_POST['team2'] == 0) {
                $_SESSION['error'] = 2;
                if ($_SESSION['update'] !== "Not updated")
                    header("Location: game.php?edit=$_SESSION[update]", true, 303);
                else
                    header("Location: game.php", true, 303);
            } elseif ($_POST['playground'] == 0) {
                $_SESSION['error'] = 3;
                if ($_SESSION['update'] !== "Not updated")
                    header("Location: game.php?edit=$_SESSION[update]", true, 303);
                else
                    header("Location: game.php", true, 303);
            } else {
                if ($stmt->prepare($query)) {
                    $game_date = $_POST['gameDate'] != "" ? $_POST['gameDate'] : date("Y-m-d");
                    $_SESSION['update'] === "Not updated" ?
                        $stmt->bind_param('iiisi', $_POST['team1'], $_POST['team2'],
                            $_POST['playground'], $game_date, $_POST['absence']) :
                        $stmt->bind_param('iiisii', $_POST['team1'], $_POST['team2'],
                            $_POST['playground'], $game_date, $_POST['absence'], $_SESSION['update']);
                    if ($stmt->execute()) {
                        $_SESSION['update'] === "Not updated" ?
                            $result = "<p><output style=\"color: seagreen;\">Новая игра успешно добавлена</output></p>" :
                            $result = "<p><output style=\"color: seagreen;\">Игра успешно обновлена</output></p>";
                    } else {
                        if ($_SESSION['update'] === "Not updated")
                            $text = '<p><output>При добавлении игры возникла ошибка</output></p><p><output>Попробойте осуществить операцию еще раз</output></p>';
                        else {
                            $text = '<p><output>При обновлении игры возникла ошибка</output></p><p><output>Попробойте осуществить операцию еще раз</output></p>';
                            echo "<input type='text' name='edit' value='$_SESSION[update]' hidden />";
                        }
                        $result = '<form name="errorNewGame" method="GET" action="game.php"><div style="color: indianred;">' .
                            $text .
                            '<div class="submit-btn"><input type="submit" value="Попробовать"/></div></div></form>';
                    }
                } else {
                    $_SESSION['errno'] = $stmt->errno;
                    $_SESSION['error'] = $stmt->error;
                    header("Location: ../helpers/error.php");
                }
            }
            echo $result;
            $stmt->close();
        } else {
            $_SESSION['update'] = "Not updated";
            $team1_id = 0;
            $team1_name = 'Выберите команду 1';
            $team2_id = 0;
            $team2_name = 'Выберите команду 2';
            $playground_id = 0;
            $playground_name = 'Выберите площадку';
            $game_date = date("Y-m-d");
            $composition_value = 0;
            $composition = "Полные составы";
            $teams = '';
            $playgrounds = '';
            $query = 'SELECT id, team_name FROM teams';
            if ($stmt->prepare($query)) {
                if ($stmt->execute()) {
                    $stmt->bind_result($id, $team_name);
                    $stmt->store_result();

                    if ($stmt->num_rows == 0 && !isset($_POST['edit'])) {
                        $stmt->free_result();
                        $stmt->close();
                        ?>
                        <div style="color: indianred;">
                            <p><output>В базе данных отсутствуют команды</output></p>
                            <p><output>Для добавления игры, пожалуйста, добавьте новую команду</output></p>
                            <p><output>Напомним, что у команды всегда должен быть капитан, то есть если Вы хотите добавить
                                    новую команду с капитаном из уже существующей команды, то в первую очередь необходимо
                                    изменить капитана в существующей команде (или удалить её), обнавить статус команды у
                                    будующего капитана на "Не в команде" и только потом добавить новую команду</output></p>
                        </div>
                        <?php
                    } else {
                        while ($stmt->fetch())
                            $teams .= "<option value=$id>$team_name</option>";

                        $stmt->free_result();
                        $stmt->close();
                        $query = 'SELECT id, name FROM playgrounds';
                        if ($stmt->prepare($query)) {
                            if ($stmt->execute()) {
                                $stmt->bind_result($id, $plg_name);
                                $stmt->store_result();

                                if ($stmt->num_rows == 0) {
                                    $stmt->free_result();
                                    $stmt->close();
                                    ?>
                                    <div style="color: indianred;">
                                        <p><output>В базе данных отсутствуют площадки</output></p>
                                        <p><output>Для добавления игры, пожалуйста, добавьте новую площадку</output></p>
                                    </div>
                                    <?php
                                } else {
                                    while ($stmt->fetch())
                                        $playgrounds .= "<option value=$id>$plg_name</option>";

                                    $stmt->free_result();
                                    $stmt->close();

                                    if (isset($_POST['edit']) || isset($_GET['edit'])) {
                                        $_SESSION['update'] = isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'];
                                        $query = 'SELECT team1_id, team2_id, playground_id, game_date, absence FROM competitions WHERE id = ?';
                                        if ($stmt->prepare($query)) {
                                            $stmt->bind_param('i', $_SESSION['update']);
                                            if ($stmt->execute()) {
                                                $stmt->bind_result($team1_id, $team2_id, $playground_id, $game_date, $composition_value);
                                                $stmt->store_result();
                                                $stmt->fetch();
                                                $stmt->free_result();
                                                $stmt->close();
                                                if ($composition_value == 1) $composition = "Первая команда";
                                                elseif ($composition_value == 2) $composition = "Вторая команда";
                                                else $composition = "Полные составы";
                                                $query = 'SELECT team_name FROM teams WHERE id = ?';
                                                if ($stmt->prepare($query)) {
                                                    $stmt->bind_param('i', $team1_id);
                                                    if ($stmt->execute()) {
                                                        $stmt->bind_result($team1_name);
                                                        $stmt->store_result();
                                                        $stmt->fetch();
                                                        $stmt->free_result();
                                                        $stmt->bind_param('i', $team2_id);
                                                        if ($stmt->execute()) {
                                                            $stmt->bind_result($team2_name);
                                                            $stmt->store_result();
                                                            $stmt->fetch();
                                                            $stmt->free_result();
                                                            $query = 'SELECT name FROM playgrounds WHERE id = ?';
                                                            if ($stmt->prepare($query)) {
                                                                $stmt->bind_param('i', $playground_id);
                                                                if ($stmt->execute()) {
                                                                    $stmt->bind_result($playground_name);
                                                                    $stmt->store_result();
                                                                    $stmt->fetch();
                                                                    $stmt->free_result();
                                                                    $stmt->close();
                                                                } else {
                                                                    $_SESSION['errno'] = $stmt->errno;
                                                                    $_SESSION['error'] = $stmt->error;
                                                                    header("Location: ../helpers/error.php");
                                                                }
                                                            } else {
                                                                $_SESSION['errno'] = $stmt->errno;
                                                                $_SESSION['error'] = $stmt->error;
                                                                header("Location: ../helpers/error.php");
                                                            }
                                                        } else {
                                                            $_SESSION['errno'] = $stmt->errno;
                                                            $_SESSION['error'] = $stmt->error;
                                                            header("Location: ../helpers/error.php");
                                                        }
                                                    } else {
                                                        $_SESSION['errno'] = $stmt->errno;
                                                        $_SESSION['error'] = $stmt->error;
                                                        header("Location: ../helpers/error.php");
                                                    }
                                                } else {
                                                    $_SESSION['errno'] = $stmt->errno;
                                                    $_SESSION['error'] = $stmt->error;
                                                    header("Location: ../helpers/error.php");
                                                }
                                            } else {
                                                $_SESSION['errno'] = $stmt->errno;
                                                $_SESSION['error'] = $stmt->error;
                                                header("Location: ../helpers/error.php");
                                            }
                                        } else {
                                            $_SESSION['errno'] = $stmt->errno;
                                            $_SESSION['error'] = $stmt->error;
                                            header("Location: ../helpers/error.php");
                                        }
                                    }
                                    ?>
                                    <form name="insertNewGame" method="POST" action="game.php">
                                        <p><label>Команда 1<select name="team1">
                                                    <option selected
                                                            value="<?php echo $team1_id; ?>"><?php echo $team1_name; ?></option>
                                                    <?php echo $teams; ?></select></label></p>
                                        <p><label>Команда 2<select name="team2">
                                                    <option selected
                                                            value="<?php echo $team2_id; ?>"><?php echo $team2_name; ?></option>
                                                    <?php echo $teams; ?></select></label></p>
                                        <p><label>Дата игры<input type="date" name="gameDate"
                                                                  max="<?php echo date("Y-m-d"); ?>"
                                                                  value="<?php echo $game_date; ?>"/></label></p>
                                        <p><label>Площадка<select name="playground">
                                                    <option selected
                                                            value="<?php echo $playground_id; ?>"><?php echo $playground_name; ?></option>
                                                    <?php echo $playgrounds; ?></select></label></p>
                                        <p><label>Отсутствие<select name="absence">
                                                    <option selected
                                                            value="<?php echo $composition_value; ?>"><?php echo $composition; ?></option>
                                                    <option value="0">Полные составы</option>
                                                    <option value="1">Первая команда</option>
                                                    <option value="2">Вторая команда</option>
                                                </select></label></p>
                                        <div class="submit-btn"><input
                                                    type="submit"
                                                    value="<?php if (!isset($_POST['edit'])) echo 'Добавить'; else echo 'Обновить'; ?>"
                                                    style="margin-left: 23px"/></div>
                                    </form>
                                    <?php
                                    if (strpos($_SERVER['HTTP_REFERER'], "game.php") != 0 && isset($_SESSION['error'])) {
                                        $error = $_SESSION['error'];
                                        unset($_SESSION['error']);
                                        if ($error == 3) $error = 'Необходимо выбрать площадку';
                                        elseif ($error == 2) $error = 'Необходимо заполнить оба поля "Команда"';
                                        else $error = 'Команда не может играть сама с собой, по этой причине поле "Команда 1" должна отличаться от поля "Команда 2"';
                                        ?>
                                        <div style="color: indianred;">
                                            <p><output><?php echo $error; ?></output></p>
                                        </div>
                                        <?php
                                    }
                                }
                            } else {
                                $_SESSION['errno'] = $stmt->errno;
                                $_SESSION['error'] = $stmt->error;
                                header("Location: ../helpers/error.php");
                            }
                        } else {
                            $_SESSION['errno'] = $stmt->errno;
                            $_SESSION['error'] = $stmt->error;
                            header("Location: ../helpers/error.php");
                        }
                        $stmt->close();
                    }
                } else {
                    $_SESSION['errno'] = $stmt->errno;
                    $_SESSION['error'] = $stmt->error;
                    header("Location: ../helpers/error.php");
                }
            } else {
                $_SESSION['errno'] = $stmt->errno;
                $_SESSION['error'] = $stmt->error;
                header("Location: ../helpers/error.php");
            }
        }
        ?>
    </div>

</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
