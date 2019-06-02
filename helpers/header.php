<!DOCTYPE html>
<html>
<head>
    <meta charset="utf8">
    <link rel='stylesheet' href='/IndZ/styles/header.css'>
</head>
<body>
<div style="display: flex; flex-wrap: wrap; flex-direction: column">
    <div class="logout-btn">
        <form name="logout" method="GET" action="/IndZ/helpers/logout.php">
            <input type="submit" value="Выйти" />
        </form>
    </div>
    <div>
        <nav class="menu">
            <ul class="main-menu">
                <li><a href="">Информация</a>
                    <ul class="sub-menu">
                        <li><a href="/IndZ/info/teams.php">Команды</a></li>
                        <li><a href="/IndZ/info/players.php">Игроки</a></li>
                        <li><a href="/IndZ/info/competitions.php">Игры</a></li>
                        <li><a href="/IndZ/info/playgrounds.php">Площадки</a></li>
                    </ul>
                </li>
                <li><a href="">Статистика</a>
                    <ul class="sub-menu">
                        <li><a href="/IndZ/stats/teams_stats.php">Команды</a></li>
                        <li><a href="/IndZ/stats/players_stats.php">Игроки</a></li>
                        <li><a href="/IndZ/stats/competitions_stats.php">Игры</a></li>
                        <li><a href="/IndZ/stats/playgrounds.php">Игры по площадкам</a></li><!-- задание 3 -->
                        <li><a href="/IndZ/stats/playgrounds_efficiency.php">Результативность по площадкам</a></li><!-- задание 6 -->
                    </ul>
                </li>
                <li><a href="">Отчет</a>
                    <ul class="sub-menu">
                        <li><a href="/IndZ/reports/teams_participation.php">Отчет об участии команд</a></li><!-- задание 1 -->
                        <li><a href="/IndZ/reports/playgrounds_usage.php">Отчет об использовании площадок</a></li><!-- задание 4 -->
                    </ul>
                </li>
                <li><a href="">Добавить</a>
                    <ul class="sub-menu">
                        <li><a href="/IndZ/actions/player.php">Игрок</a></li><!-- добавление игрока -->
                        <li><a href="/IndZ/actions/team.php">Команда</a></li><!-- добавление команды -->
                        <li><a href="/IndZ/actions/game.php">Игра</a></li><!-- добавление игры -->
                        <li><a href="/IndZ/actions/competition.php">Игровая статистика</a></li><!-- добавление статистики по игрокам -->
                        <li><a href="/IndZ/actions/playground.php">Площадка</a></li><!-- добавление полщадки -->
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
</body>
</html>
