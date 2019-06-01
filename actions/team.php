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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $stmt = $conn->stmt_init();
            $query = 'INSERT INTO `teams`(`team_name`, `cap_id`) VALUES (?, ?)';
            if ($stmt->prepare($query)) {
                $stmt->bind_param('si', $_POST['team_name'], $_POST['cap']);
                if ($stmt->execute()) {
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
                    $stmt->close();
                } else {
                    $stmt->close();
                    ?>
                    <form name="errorNewTeam" method="GET" action="team.php">
                        <div style="color: indianred;">
                            <p><output>При добавлении команды возникла ошибка</output></p>
                            <p><output>Попробойте осуществить операцию еще раз</output></p>
                            <div class="submit-btn"><input type="submit" value="Попробовать"/></div>
                        </div>
                    </form>
                    <?php
                }
            }
        } else {
            $players = '';
            $query = 'SELECT id, CONCAT(name, " ", surname) FROM players WHERE team_id IS NULL';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($id, $cap_name);
                $stmt->store_result();

                if ($stmt->num_rows == 0) {
                    $stmt->free_result();
                    $stmt->close();
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
                    $stmt->close();

                    ?>
                    <form name="insertNewPlayground" method="POST" action="team.php">
                        <p><label>Название команды<input type="text" name="team_name" maxlength="50"
                                                         pattern="[0-9A-ZА-Я^ЪЬ]{1}" placeholder="Team Smith"/></label></p>
                        <p><label>Капитан<select name="cap"><?php echo $players; ?></select></label></p>
                        <div class="submit-btn"><input type="submit" value="Добавить" style="margin-left: 30px"/></div>
                    </form>
                    <?php
                }
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
