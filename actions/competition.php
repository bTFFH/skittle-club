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
        $playerst1Array = unserialize($_POST['playerst1Array']);
        $playerst2Array = unserialize($_POST['playerst2Array']);
        $sumt1 = 0;
        $sumt2 = 0;
        $loss = 1;
        $query1 = 'SELECT effectiveness FROM players_stats WHERE player_id = ?';
        $query2 = 'INSERT INTO `competitions_info`(`competition_id`, `player_id`, `skittles_amount`) VALUES (?, ?, ?)';
            foreach ($playerst1Array as $playerId => $playerName) {
                $stmt->prepare($query1);
                $stmt->bind_param('i', $playerId);
                $stmt->execute();
                $stmt->bind_result($handicap);
                $stmt->store_result();
                $stmt->fetch();

                $handicap = ceil((200 - $handicap) * 0.75);
                if ($handicap < 0) $handicap = 1;
                $skittles = $_POST[$playerId] == '' ? 0 : $_POST[$playerId];
                $sumt1 += $skittles * $handicap;
                if ($skittles == 0) $loss = 1;
                $stmt->free_result();

                $stmt->prepare($query2);
                $stmt->bind_param('iii', $_POST['competitionId'], $playerId, $skittles);
                if ($stmt->execute()) {
                    echo "<p><output style=\"color: seagreen\">Статистика для игрока $playerName обновлена</output></p>";
                } else {
                    echo "<p><output style=\"color: indianred\">Статистика для игрока $playerName не была обновлена</output></p>";
                }
            }
            foreach ($playerst2Array as $playerId => $playerName) {
                $stmt->prepare($query1);
                $stmt->bind_param('i', $playerId);
                $stmt->execute();
                $stmt->bind_result($handicap);
                $stmt->store_result();
                $stmt->fetch();

                $handicap = ceil((200 - $handicap) * 0.75);
                if ($handicap < 0) $handicap = -1;
                $skittles = $_POST[$playerId] == '' ? 0 : $_POST[$playerId];
                $sumt2 += $skittles * $handicap;
                if ($skittles == 0) $loss = 1;
                $stmt->free_result();

                $stmt->prepare($query2);
                $stmt->bind_param('iii', $_POST['competitionId'], $playerId, $skittles);
                if ($stmt->execute()) {
                    echo "<p><output style=\"color: seagreen\">Статистика для игрока $playerName обновлена</output></p>";
                } else {
                    echo "<p><output style=\"color: indianred\">Статистика для игрока $playerName не была обновлена</output></p>";
                }
            }
            if ($sumt1 > $sumt2) $winner = 1;
            elseif ($sumt1 < $sumt2) $winner = -1;
            else $winner = 0;
            $query = 'SELECT COUNT(winner), SUM(winner) FROM competitions WHERE (team1 = ? AND team2 = ? AND game_date > ? AND game_date < ?)';
            if ($stmt->prepare($query)
                && $stmt->bind_param('iis', $_SESSION['team1'], $_SESSION['team2'], date("Y-m-d") - 60 * 60 * 24 * 7, date("Y-m-d"))
                && $stmt->execute()
            ) {
                $stmt->bind_result($gamesAmount, $winnerSum);
                $stmt->store_result();
                $stmt->fetch();
                $stmt->free_result();
                $query = 'UPDATE `teams_stats` SET `points_amount` = `points_amount` + ?, `last_week_points` = `last_week_points` + ?, `games_amount` = `games_amount` + 1, `effectiveness` = (`points_amount` + ?) / 2 WHERE `team_id` = ?';
                if ($gamesAmount == 2) {
                    if ($winnerSum == 2 && $winner = 1) $winner1 = 3;
                    elseif ($winnerSum == 0 && $winner = -1) $winner1 = -3;
                    elseif ($winner == 0) $winner1 = 0;
                    if ($winner1 == 3) {
                        $points1 = 2;
                        $points2 = -2;
                    } elseif ($winner1 == -3) {
                        $points1 = -2;
                        $points2 = 2;
                    } else {
                        $points1 = 0.5;
                        $points2 = 0.5;
                    }
                } else {
                    if ($winner == 1) {
                        $points1 = 1;
                        $points2 = -1;
                    } elseif ($winner == -1) {
                        $points1 = -1;
                        $points2 = 1;
                    } else {
                        $points1 = 0.5;
                        $points2 = 0.5;
                    }
                }
                if ($loss == 1 && $points1 > 0) $points1 = 0;
                elseif ($loss == -1 && $points2 > 0) $points2 = 0;
                if ($stmt->prepare($query)
                    && $stmt->bind_param('iiii', $points1, $points1, $points1, $_SESSION['team1'])
                    && $stmt->execute()
                ) {
                    echo "<p><output style=\"color: seagreen\">Статистика команды 1 обновлена</output></p>";
                    if ($stmt->bind_param('iiii', $points2, $points2, $points2, $_SESSION['team2'])
                        && $stmt->execute()) {
                        echo "<p><output style=\"color: seagreen\">Статистика команды 2 обновлена</output></p>";
                        $query = 'UPDATE `competitions` SET `winner` = ? WHERE `id` = ?';
                        if ($stmt->prepare($query)
                            && $stmt->bind_param('ii', $winner, $_POST['competitionId'])
                            && $stmt->execute()
                        ) echo "<p><output style=\"color: seagreen\">Статистика игры обновлена</output></p>";
                        else {
                            $_SESSION['errno'] = $stmt->errno;
                            $_SESSION['error'] = $stmt->error;
                            header("Location: ../helpers/error.php", true);
                        }
                    } else {
                        $_SESSION['errno'] = $stmt->errno;
                        $_SESSION['error'] = $stmt->error;
                        header("Location: ../helpers/error.php", true);
                    }
                } else {
                    $_SESSION['errno'] = $stmt->errno;
                    $_SESSION['error'] = $stmt->error;
                    header("Location: ../helpers/error.php", true);
                }
            }

    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['competition'])) {
        echo "<div class=\"add-form\"  style='flex-basis: auto; min-width: 450px; max-width: 900px'><br /><br />";
        $playerst1 = '';
        $playerst2 = '';
        $playerst1Array = [];
        $playerst2Array = [];
        $query = "SELECT team1_id, team2_id FROM competitions WHERE id = $_POST[competition]";
        if ($stmt->prepare($query) && $stmt->execute()) {
            $stmt->bind_result($team1, $team2);
            $stmt->store_result();
            $stmt->fetch();
            $stmt->free_result();
            $_SESSION['team1'] = $team1;
            $_SESSION['team2'] = $team2;
            $query = "SELECT id, CONCAT(name, ' ', surname) FROM players WHERE team_id = ?";
            if ($stmt->prepare($query)) {
                $stmt->bind_param('i', $team1);
                if ($stmt->execute()) {
                    $stmt->bind_result($playerId, $playerName);
                    $stmt->store_result();
                    while ($stmt->fetch()) {
                        $playerst1Array[$playerId] = $playerName;
                        $playerst1 .= "<p><label style='color: navy'>$playerName [$playerId]<input name=$playerId placeholder='Очки игрока' pattern='{0-9]+' maxlength='10' required /></label></p>";
                    }
                    $stmt->free_result();
                } else {
                    echo "<p><output style='color: indianred'>Невозможно получить список игроков для первой команды</output></p>";
                }
            } else {
                $_SESSION['errno'] = $stmt->errno;
                $_SESSION['error'] = $stmt->error;
                    header("Location: ../helpers/error.php", true);
            }
            $stmt->bind_param('i', $team2);
            if ($stmt->execute()) {
                $stmt->bind_result($playerId, $playerName);
                $stmt->store_result();
                while ($stmt->fetch()) {
                    $playerst2Array[$playerId] = $playerName;
                    $playerst2 .= "<p><label style='color: maroon'>$playerName [$playerId]<input name=$playerId placeholder='Очки игрока' pattern='{0-9]+' maxlength='10' required /></label></p>";
                }
                $stmt->free_result();
            } else {
                echo "<p><output style='color: indianred'>Невозможно получить список игроков для второй команды</output></p>";
            }
            $playerst1Str = serialize($playerst1Array);
            $playerst2Str = serialize($playerst2Array);
            $stmt->close();
            ?>
            <form name="addCompetitionInfo" method="POST" action="competition.php">
                <?php
                echo $playerst1 . $playerst2;
                ?>
                <textarea hidden name="playerst1Array"><?php echo $playerst1Str; ?></textarea>
                <textarea hidden name="playerst2Array"><?php echo $playerst2Str; ?></textarea>
                <input type="hidden" name="competitionId" value=<?php echo $_POST['competition']; ?>/>
                <div class="submit-btn"><input type="submit" value="Добавить"/></div>
            </form>
            <?php
        } else {
            $_SESSION['errno'] = $stmt->errno;
            $_SESSION['error'] = $stmt->error;
            header("Location: ../helpers/error.php", true);
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
                                $stmt->close();
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
                                $stmt->close();
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
            header("Location: ../helpers/error.php", true);
                }
            }
        } else {
            $_SESSION['errno'] = $stmt->errno;
            $_SESSION['error'] = $stmt->error;
                        header("Location: ../helpers/error.php", true);
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
