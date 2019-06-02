<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/addition.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>Добавление команды</title>
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
        $stmt = $conn->stmt_init();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit'])) {
            $query = $_SESSION['update'] === "Not updated" ?
                'INSERT INTO `teams`(`team_name`, `cap_id`) VALUES (?, ?)' :
                'UPDATE `teams` SET `team_name` = ?, `cap_id` = ? WHERE `id` = ?';
            if ($_POST['team_name'] == '') {
                $_SESSION['error'] = 1;
                if ($_SESSION['update'] !== "Not updated")
                    header("Location: team.php?edit=$_SESSION[update]", true, 303);
                else
                    header("Location: team.php", true, 303);
            } elseif ($_POST['cap'] == 0) {
                $_SESSION['error'] = 2;
                if ($_SESSION['update'] !== "Not updated")
                    header("Location: team.php?edit=$_SESSION[update]", true, 303);
                else
                    header("Location: team.php", true, 303);
            } else {
                if ($stmt->prepare($query)) {
                    $query = $_SESSION['update'] === "Not updated" ?
                        $stmt->bind_param('si', $_POST['team_name'], $_POST['cap']) :
                        $stmt->bind_param('sii', $_POST['team_name'], $_POST['cap'], $_SESSION['update']);
                    if ($stmt->execute()) {
                        if ($_SESSION['update'] === "Not updated") {
                            echo "<p><output style=\"color: seagreen;\">Новая команда успешно добавлена</output></p>";

                            $insertedTeam = $stmt->insert_id;
                            $query = "INSERT INTO `teams_stats`(`team_id`) VALUES ($insertedTeam)";
                            $stmt->prepare($query);
                            if ($stmt->execute()) {
                                echo "<p><output style=\"color: seagreen;\">Статистика команды обновлена</output></p>";
                            } else {
                                echo "<p><output style=\"color: indianred;\">Статистика команды не была обновлена</output></p>";
                            }

                            $query = "UPDATE `players` SET `team_id` = $insertedTeam WHERE id = $_POST[cap]";
                            $stmt->prepare($query);
                            if ($stmt->execute()) {
                                echo "<p><output style=\"color: seagreen;\">Данные игрока о команде были успешно обновлены</output></p>";
                            } else {
                                ?>
                                <div style="color: indianred;">
                                    <p><output>Данные игрока о команде не были обновлены</output></p>
                                    <p><output>Убедительная просьба обновить их вручную во вкладке "Информация" -> "Игроки"</output></p>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p><output style=\"color: seagreen;\">Команда успешно обновлена</output></p>";
                        }
                        $stmt->close();
                    } else {
                        if ($_SESSION['update'] === "Not updated") $text = 'добавлении';
                        else {
                            $text = 'обновлении';
                            echo "<input type='text' name='edit' value='$_SESSION[update]' hidden />";
                        }
                        ?>
                        <form name="errorTeam" method="GET" action="team.php">
                            <div style="color: indianred;">
                                <p><output>При <?php echo $text; ?> команды возникла ошибка</output></p>
                                <p><output>Попробойте осуществить операцию еще раз</output></p>
                                <div class="submit-btn"><input type="submit" value="Попробовать"/></div>
                            </div>
                        </form>
                        <?php
                    }
                }
            }
        } else {
            $_SESSION['update'] = "Not updated";
            $players = '';
            $query = 'SELECT id, CONCAT(name, " ", surname) FROM players WHERE team_id IS NULL';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($id, $cap_name);
                $stmt->store_result();

                if ($stmt->num_rows == 0 && !isset($_POST['edit'])) {
                    $stmt->free_result();
                    ?>
                    <div style="color: indianred;">
                        <p><output>Нет игроков, которые могли бы стать капитанами</output></p>
                        <p><output>Для добавления команды, пожалуйста, добавьте нового игрока или обновите старого так,
                                чтобы он не состоял в какой-либо команде</output></p>
                        <p><output>Напомним, что у команды всегда должен быть капитан, то есть если Вы хотите добавить
                                новую команду с капитаном из уже существующей команды, то в первую очередь необходимо
                                изменить капитана в существующей команде (или удалить её), обнавить статус команды у
                                будующего капитана на "Не в команде" и только потом добавить новую команду</output></p>
                    </div>
                    <?php
                } else {
                    while ($stmt->fetch())
                        $players .= "<option value=$id>$cap_name [$id]</option>";

                    $stmt->free_result();

                    if (isset($_POST['edit']) || isset($_GET['edit'])) {
                        $_SESSION['update'] = isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'];
                        $query = 'SELECT team_name, cap_id FROM teams WHERE id = ?';
                        if ($stmt->prepare($query)) {
                            $stmt->bind_param('i', $_SESSION['update']);
                            if ($stmt->execute()) {
                                $stmt->bind_result($team_name, $cap_id);
                                $stmt->store_result();
                                $stmt->fetch();
                                $stmt->free_result();
                                $query = 'SELECT CONCAT(name, " ", surname) FROM players WHERE id = ?';
                                if ($stmt->prepare($query)) {
                                    $stmt->bind_param('i', $cap_id);
                                    if ($stmt->execute()) {
                                        $stmt->bind_result($cap_name);
                                        $stmt->store_result();
                                        $stmt->fetch();
                                        $stmt->free_result();
                                    }
                                }
                            }
                        }
                    } else {
                        $team_name = '';
                        $cap_id = 0;
                        $cap_name = "Выберите капитана";
                    }
                    ?>
                    <form name="insertNewPlayground" method="POST" action="team.php">
                        <p><label>Название команды<input type="text" name="team_name" maxlength="50"
                                                         value="<?php echo $team_name; ?>" pattern="[0-9A-ZА-Я^ЪЬ]{1}.*"
                                                         placeholder="Team Smith"/></label></p>
                        <p><label>Капитан<select name="cap">
                                    <option selected value="<?php echo $cap_id; ?>"><?php echo "$cap_name [$cap_id]"; ?></option>
                                    <?php echo $players; ?></select></label></p>
                        <div class="submit-btn"><input
                                    type="submit" style="margin-left: 30px"
                                    value="<?php if (!isset($_POST['edit'])) echo 'Добавить'; else echo 'Обновить'; ?>" /></div>
                    </form>
                    <?php
                    if (strpos($_SERVER['HTTP_REFERER'], "team.php") != 0 && isset($_SESSION['error'])) {
                        $error = $_SESSION['error'];
                        unset($_SESSION['error']);
                        if ($error == 1) $error = "Необходимо ввести название команды";
                        else $error = "Необходимо указать капитана";
                        ?>
                        <div style="color: indianred;">
                            <p><output><?php echo $error; ?></output></p>
                        </div>
                        <?php
                    }
                }
                $stmt->close();
            } else {
                exit($stmt->errno . ' ' . $stmt->error);
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
