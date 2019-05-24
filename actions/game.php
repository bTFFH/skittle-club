<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/addition.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>Добавление игры</title>
    <?php
    session_start();
    if ( !isset($_SESSION['username']) )
        header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
    include_once($_SERVER['DOCUMENT_ROOT'].'/IndZ/helpers/dbConnOpen.php');
    ?>
</head>
<body>
<div style="display: flex">
    <div style="flex-wrap: nowrap">
        <?php
        include_once($_SERVER['DOCUMENT_ROOT'].'/IndZ/helpers/header.php');
        ?>
    </div>
    <div class="add-form">
        <br />
        <br />
        <?php
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $stmt = $conn->stmt_init();
            $query = 'INSERT INTO `competitions`(`team1_id`, `team2_id`, `playground_id`, `game_date`, `absence`) VALUES (?, ?, ?, ?, ?)';
            if ( $_POST['team1'] == $_POST['team2'] ) {
                $_SESSION['tms'] = '1';
                header("Location: game.php", true, 303);
            }
            else {
                if ($stmt->prepare($query)) {
                    $stmt->bind_param('iiisi', $_POST['team1'], $_POST['team2'],
                        $_POST['playground'], $_POST['gameDate'], $_POST['absence']);
                    if ($stmt->execute()) {
                        echo "<p><output style=\"color: seagreen;\">Новая игра успешно добавлена</output></p>";
                    } else {
                        $stmt->close();
                        ?>
                        <form name="errorNewGame" method="GET" action="game.php">
                            <div style="color: indianred;">
                                <p>
                                    <output>При добавлении игры возникла ошибка</output>
                                </p>
                                <p>
                                    <output>Попробойте осуществить операцию еще раз</output>
                                </p>
                                <div class="submit-btn"><input type="submit" value="Попробовать"/></div>
                            </div>
                        </form>
                        <?php
                    }
                }
            }
        }
        else {
            $teams = '';
            $playgrounds = '';
            $query = 'SELECT id, team_name FROM teams';
            $stmt = $conn->stmt_init();
            if ( $stmt->prepare($query) ) {
                $stmt->execute();
                $stmt->bind_result($id, $team_name);
                $stmt->store_result();

                if ( $stmt->num_rows == 0 ) {
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
                    while ( $stmt->fetch() )
                        $teams .= "<option value=$id>$team_name</option>";

                    $stmt->free_result();
                    $query = 'SELECT id, name FROM playgrounds';
                    if ( $stmt->prepare($query) ) {
                        $stmt->execute();
                        $stmt->bind_result($id, $plg_name);
                        $stmt->store_result();

                        if ( $stmt->num_rows == 0 ) {
                            $stmt->free_result();
                            $stmt->close();
                            ?>
                            <div style="color: indianred;">
                                <p><output>В базе данных отсутствуют площадки</output></p>
                                <p><output>Для добавления игры, пожалуйста, добавьте новую площадку</output></p>
                            </div>
                            <?php
                        } else {
                            while ( $stmt->fetch() )
                                $playgrounds .= "<option value=$id>$plg_name</option>";

                            $stmt->free_result();
                            $stmt->close();

                            ?>
                            <form name="insertNewGame" method="POST" action="game.php">
                                <p><label>Команда 1<select name="team1"><?php echo $teams; ?></select></label></p>
                                <p><label>Команда 2<select name="team2"><?php echo $teams; ?></select></label></p>
                                <p><label>Дата игры<input type="date" name="gameDate" max=<?php echo date("Y-m-d")?> value=<?php echo date("Y-m-d")?> /></label></p>
                                <p><label>Площадка<select name="playground"><?php echo $playgrounds; ?></select></label></p>
                                <p><label>Отсутствие<select name="absence">
                                            <option value="0">Полные составы</option>
                                            <option value="1">Первая команда</option>
                                            <option value="2">Вторая команда</option>
                                        </select></label></p>
                                <div class="submit-btn"><input type="submit" value="Добавить" style="margin-left: 23px" /></div>
                            </form>
                            <?php
                            if ( strpos($_SERVER['HTTP_REFERER'], "game.php") != 0 && isset($_SESSION['tms']) ) {
                                if ( $_SESSION['tms'] == '1' ) {
                                    unset($_SESSION['tms']);
                                    ?>
                                    <div style="color: indianred;">
                                        <p>
                                            <output>Команда не может играть сама с собой, по этой причине "Команда 1" должна отличаться
                                                от "Команда 2"
                                            </output>
                                        </p>
                                    </div>
                                    <?php
                                }
                                $_POST['tms'] = '0';
                            }
                        }
                    }
                }
            }
            else {
                exit($stmt->errno.' '.$stmt->error);
            }
        }
        ?>
    </div>

</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
