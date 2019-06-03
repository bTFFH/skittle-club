<!DOCTYPE html>
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/actionsHead.php'); ?>
    <title>Добавление игры</title>
</head>
<body>
<div style="display: flex; flex-wrap: nowrap; flex-direction: row;">
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
    ?>
    <?php
    $stmt = $conn->stmt_init();
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['competitionId'])) {
        echo "<div class=\"add-form\"><br /><br />";
        $playersArray = unserialize($_POST['playersArray']);
        $query = 'INSERT INTO `competitions_info`(`competition_id`, `player_id`, `skittles_amount`) VALUES (?, ?, ?)';
        if ($stmt->prepare($query)) {
            foreach ($playersArray as $playerId => $playerName) {
                $skittles = $_POST[$playerId] == '' ? 0 : $_POST[$playerId];
                $stmt->bind_param('iii', $_POST['competitionId'], $playerId, $skittles);
                if ($stmt->execute()) {
                    echo "<p><output style=\"color: seagreen\">Статистика для игрока $playerName обновлена</output></p>";
                } else {
                    echo "<p><output style=\"color: indianred\">Статистика для игрока $playerName не была обновлена</output></p>";
                }
            }
        } else {
            $_SESSION['errno'] = $stmt->errno;
            $_SESSION['error'] = $stmt->error;
//            header("Location: ../helpers/error.php");
            echo "5";
        }
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['competition'])) {
        echo "<div class=\"add-form\"  style='flex-basis: auto; min-width: 450px; max-width: 900px'><br /><br />";
        $players = '';
        $query = "SELECT team1_id, team2_id FROM competitions WHERE id = $_POST[competition]";
        if ($stmt->prepare($query) && $stmt->execute()) {
            $stmt->bind_result($team1, $team2);
            $stmt->store_result();
            $stmt->fetch();
            $stmt->free_result();
            $query = "SELECT id, CONCAT(name, ' ', surname) FROM players WHERE team_id = ?";
            if ($stmt->prepare($query)) {
                $stmt->bind_param('i', $team1);
                $playersArray = [];
                if ($stmt->execute()) {
                    $stmt->bind_result($playerId, $playerName);
                    $stmt->store_result();
                    while ($stmt->fetch()) {
                        $playersArray[$playerId] = $playerName;
                        $players .= "<p><label style='color: navy'>$playerName [$playerId]<input name=$playerId placeholder='Очки игрока' pattern='{0-9]+' maxlength='10' required /></label></p>";
                    }
                    $stmt->free_result();
                } else {
                    echo "<p><output style='color: indianred'>Невозможно получить список игроков для первой команды</output></p>";
                }
            } else {
                $_SESSION['errno'] = $stmt->errno;
                $_SESSION['error'] = $stmt->error;
//                    header("Location: ../helpers/error.php");
                echo "6";
            }
            $stmt->bind_param('i', $team2);
            if ($stmt->execute()) {
                $stmt->bind_result($playerId, $playerName);
                $stmt->store_result();
                while ($stmt->fetch()) {
                    $playersArray[$playerId] = $playerName;
                    $players .= "<p><label style='color: maroon'>$playerName [$playerId]<input name=$playerId placeholder='Очки игрока' pattern='{0-9]+' maxlength='10' required /></label></p>";
                }
                $stmt->free_result();
            } else {
                echo "<p><output style='color: indianred'>Невозможно получить список игроков для второй команды</output></p>";
            }
            $playersStr = serialize($playersArray);
//            $stmt->close();
            ?>
            <form name="addCompetitionInfo" method="POST" action="competition.php">
                <?php
                echo $players;
                ?>
                <textarea hidden name="playersArray"><?php echo $playersStr; ?></textarea>
                <input type="hidden" name="competitionId" value=<?php echo $_POST['competition']; ?>/>
                <div class="submit-btn"><input type="submit" value="Добавить"/></div>
            </form>
            <?php
        } else {
            $_SESSION['errno'] = $stmt->errno;
            $_SESSION['error'] = $stmt->error;
//            header("Location: ../helpers/error.php");
            echo "8";
        }
    } else {
        echo "<div class=\"competition-form\"><br /><br />";
        $teamsArray = [];
        $query = 'SELECT id, team_name FROM teams';
        if ($stmt->prepare($query) && $stmt->execute()) {
            $stmt->bind_result($teamId, $team);
            $stmt->store_result();
            if ($stmt->num_rows == 0) {
                $stmt->free_result();
                ?>
                <div style="color: indianred;">
                    <p>
                        <output>В базе данных отсутствуют команды</output>
                    </p>
                    <p>
                        <output>Для добавления статистики игры, пожалуйста, добавьте новую команду</output>
                    </p>
                </div>
                <?php
            } else {
                while ($stmt->fetch())
                    $teamsArray[$teamId] = $team;

                $stmt->free_result();
                $competitions = '';
                $query = 'SELECT id, team1_id, team2_id, game_date FROM competitions';
                if ($stmt->prepare($query) && $stmt->execute()) {
                    $stmt->bind_result($id, $team1, $team2, $gameDate);
                    $stmt->store_result();

                    if ($stmt->num_rows == 0) {
                        $stmt->free_result();
//                                $stmt->close();
                        ?>
                        <div style="color: indianred;">
                            <p><output>В базе данных отсутствуют игры</output></p>
                            <p><output>Для добавления статистики игры, пожалуйста, добавьте новую игру</output></p>
                        </div>
                        <?php
                    } else {
                        while ($stmt->fetch()) {
                            $gameDate = substr($gameDate, 8, 2) . '.' . substr($gameDate, 5, 2) . '.' . substr($gameDate, 0, 4);
                            $competitions .= "<option value=$id>$teamsArray[$team1] vs $teamsArray[$team2] [$gameDate]</option>";
                        }

                        $stmt->free_result();
//                                $stmt->close();
                        ?>
                        <form name="insertNewGame" method="POST" action="competition.php">
                            <p><label>Выберите команду для добавления статистики</label></p>
                            <p><select name="competition"
                                       style="width: 400px"><?php echo $competitions; ?></select></p>
                            <div class="submit-btn"><input type="submit" value="Выбрать"
                                                           style="margin-left: auto; margin-right: auto"/></div>
                        </form>
                        <?php
                    }

                } else {
                    $_SESSION['errno'] = $stmt->errno;
                    $_SESSION['error'] = $stmt->error;
//            header("Location: ../helpers/error.php");
                    echo "4";
                }
            }
        } else {
            $_SESSION['errno'] = $stmt->errno;
            $_SESSION['error'] = $stmt->error;
//                        header("Location: ../helpers/error.php");
            echo "2";
        }
    }
    ?>
</div>

</div> <!-- дополнительный </div> нужен для закрытия блока, который открывается внутри if через echo -->
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
