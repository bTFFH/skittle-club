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
        $loss = 0;  // fstTeam = 1, sndTeam = -1, loss == not all players were playing
        $competitionId = $_POST['competitionId'];

        $lossUpd1 = false;  // отвечает за факт наличия отсутствующего игрока в первой команде
        foreach ($playerst1Array as $playerId => $playerName) {
            $skittles = $_POST[$playerId];

            if ($skittles == '') {
                if (!$lossUpd1) {
                    $loss = 1;
                    $lossUpd1 = true;
                }
                echo "<p><output style=\"color: seagreen\">Статистика для игрока $playerName обновлена</output></p>";
                continue;
            }

            $skittles = (int)$skittles;

            $query = "SELECT `effectiveness` FROM `players_stats` WHERE `player_id` = $playerId";
            $query2 = "INSERT INTO `competitions_info`(`competition_id`, `player_id`, `skittles_amount`) VALUES ($competitionId, $playerId, $skittles)";

            if ($result = $conn->query($query)) {
                $row = $result->fetch_row();
                $handicap = $row[0];  // массив, содержащий ассоциативный массив
                $handicap = ceil((200 - (double)$handicap) * 0.75);  // "Явное лучше неявного" (c) Zen of Python

                // поскольку гандикап меньше 0 не учитывается, нужно это проверить и подставить иное значение,
                // которое бы ничего не изменило
                //поскольку гандикап в диапазоне [0; 1) лишь уменьшает количество набраанных очков, игроку нет
                // смысла играть вообще, так как он будет лишь вредить команде, сответственно такой гандикап тоже
                // не учитывается
                if ($handicap < 1) $handicap = 1;
                $sumt1 += $skittles * $handicap;
                if ($conn->query($query2))
                    echo "<p><output style=\"color: seagreen\">Статистика для игрока $playerName обновлена</output></p>";
                else {
                    echo "<p><output style=\"color: indianred\">Статистика для игрока $playerName не была обновлена</output></p>";
                }
            } else {
                echo "<p><output style=\"color: indianred\">Статистика для игрока $playerName не была обновлена</output></p>";
            }
            $result->free();
        }

        $lossUpd2 = false;  // отвечает за факт наличия отсутствующего игрока во второй команде
        foreach ($playerst2Array as $playerId => $playerName) {
            $skittles = $_POST[$playerId];

            if ($skittles == '') {
                if (!$lossUpd2) {
                    $loss = $loss == 1 ? 0 : -1;
                    $lossUpd2 = true;
                }
                echo "<p><output style=\"color: seagreen\">Статистика для игрока $playerName обновлена</output></p>";
                continue;
            }

            $skittles = (int)$skittles;

            $query = "SELECT `effectiveness` FROM `players_stats` WHERE `player_id` = $playerId";
            $query2 = "INSERT INTO `competitions_info`(`competition_id`, `player_id`, `skittles_amount`) VALUES ($competitionId, $playerId, $skittles)";

            if ($result = $conn->query($query)) {
                $row = $result->fetch_row();
                $handicap = $row[0];  // массив, содержащий ассоциативный массив
                $handicap = ceil((200 - (double)$handicap) * 0.75);

                if ($handicap < 1) $handicap = 1;
                $sumt2 += $skittles * $handicap;

                if ($conn->query($query2))
                    echo "<p><output style=\"color: seagreen\">Статистика для игрока $playerName обновлена</output></p>";
                else {
                    echo "<p><output style=\"color: indianred\">Статистика для игрока $playerName не была обновлена</output></p>";
                }
            } else {
                echo "<p><output style=\"color: indianred\">Статистика для игрока $playerName не была обновлена</output></p>";
            }
            $result->free();
        }

        // calculate winner
        if ($sumt1 > $sumt2) $winner = 1;
        elseif ($sumt1 < $sumt2) $winner = -1;
        else $winner = 0;

        $weekStart = date("Y-m-d", time() - 60 * 60 * 24 * 7);
        $weekEnd = date("Y-m-d", time());
        $query = "SELECT COUNT(`winner`), SUM(`winner`) FROM `competitions` WHERE (`team1_id` = $_SESSION[team1] AND `team2_id` = $_SESSION[team2] AND `game_date` > '$weekStart' AND `game_date` < '$weekEnd')";

        if ($result = $conn->query($query)) {
            $row = $result->fetch_row();
            $result->free();

            $gamesAmount = $row[0];  // amount of games between two particular team in last week
            $winnerSum = $row[1];

            if ($gamesAmount == 2) {
                if ($winnerSum == 2 && $winner = 1) $wins = 3;  // 3 wins in a row
                elseif ($winnerSum == 0 && $winner = -1) $wins = -3;
                else $wins = 0;  // no series wins

                if ($wins == 3) {
                    $points1 = 2;
                    $points2 = -2;
                } elseif ($wins == -3) {
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

            // так как команда не может победить, если присутствовали не все игроки, то если обе команды были
            // не в полном составе, то должна быть ничья не смотря на победу одной команды по очкам
            if ($lossUpd1) $points1 = 0;
            if ($lossUpd2) $points2 = 0;

            $query = 'UPDATE `teams_stats` SET `points_amount` = `points_amount` + ?, `last_week_points` = `last_week_points` + ?, `games_amount` = `games_amount` + 1, `effectiveness` = `points_amount` / IF(`games_amount` = 1, 1, 2) WHERE `team_id` = ?';

            if ($stmt->prepare($query)
                && $stmt->bind_param('iii', $points1, $points1, $_SESSION['team1'])
                && $stmt->execute()
            ) {
                echo "<p><output style=\"color: seagreen\">Статистика команды 1 обновлена</output></p>";
                if ($stmt->bind_param('iii', $points2, $points2, $_SESSION['team2'])
                    && $stmt->execute()) {
                    echo "<p><output style=\"color: seagreen\">Статистика команды 2 обновлена</output></p>";
                    $query = 'UPDATE `competitions` SET `winner` = ? WHERE `id` = ?';
                    if ($stmt->prepare($query)
                        && $stmt->bind_param('ii', $winner, $competitionId)
                        && $stmt->execute()
                    ) {
                        echo "<p><output style=\"color: seagreen\">Статистика игры обновлена</output></p>";
                        unset($_SESSION['team1']);
                        unset($_SESSION['team2']);
                    } else {
                        unset($_SESSION['team1']);
                        unset($_SESSION['team2']);
                        echo "$stmt->errno --- $stmt->error";
                        echo "<p><output style=\"color: indianred\">Статистика игры не была обновлена</output></p>";
                    }
                } else {
                    unset($_SESSION['team1']);
                    unset($_SESSION['team2']);
                    echo "$stmt->errno --- $stmt->error";
                    echo "<p><output style=\"color: indianred\">Статистика команды 2 не была обновлена</output></p>";
                    echo "<p><output style=\"color: indianred\">Статистика игры не была обновлена</output></p>";
                }
            } else {
                unset($_SESSION['team1']);
                unset($_SESSION['team2']);
                echo "<p><output style=\"color: indianred\">Статистика команды 1 не была обновлена</output></p>";
                echo "<p><output style=\"color: indianred\">Статистика команды 2 не была обновлена</output></p>";
                echo "<p><output style=\"color: indianred\">Статистика игры не была обновлена</output></p>";
            }
        } else {
            unset($_SESSION['team1']);
            unset($_SESSION['team2']);
            $_SESSION['errno'] = $stmt->errno;
            $_SESSION['error'] = $stmt->error;
            echo "<p><output style=\"color: indianred\">Невозможно обновить статистику игры</output></p>";
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
                        $playerst1 .= "<p><label style='color: navy'>$playerName [$playerId]<input name=$playerId placeholder='Очки игрока' pattern='(^$|[0-9]+)' maxlength='10' /></label></p>";
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
                    $playerst2 .= "<p><label style='color: maroon'>$playerName [$playerId]<input name=$playerId placeholder='Очки игрока' pattern='(^$|[0-9]+)' maxlength='10' /></label></p>";
                }
                $stmt->free_result();
            } else {
                echo "<p><output style='color: indianred'>Невозможно получить список игроков для второй команды</output></p>";
            }
            $playerst1Str = serialize($playerst1Array);
            $playerst2Str = serialize($playerst2Array);
            ?>
            <form name="addCompetitionInfo" method="POST" action="competition.php">
                <?php
                echo $playerst1 . $playerst2;
                ?>
                <textarea hidden name="playerst1Array"><?php echo $playerst1Str; ?></textarea>
                <textarea hidden name="playerst2Array"><?php echo $playerst2Str; ?></textarea>
                <input type="hidden" name="competitionId" value='<?php echo $_POST['competition']; ?>'/>
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
                    <p><output>В базе данных отсутствуют команды</output></p>
                    <p><output>Для добавления статистики игры, пожалуйста, добавьте новую команду</output></p>
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
    $stmt->close();
    ?>
</div>

</div> <!-- дополнительный </div> нужен для закрытия блока, который открывается внутри if через echo -->
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
