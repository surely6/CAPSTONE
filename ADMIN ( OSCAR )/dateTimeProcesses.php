<?php
function formatDateTime($datetime)
{
    $date = new DateTime($datetime);

    $day = (int) $date->format('j');

    if ($day % 10 == 1 && $day != 11) {
        $suffix = 'st';
    } elseif ($day % 10 == 2 && $day != 12) {
        $suffix = 'nd';
    } elseif ($day % 10 == 3 && $day != 13) {
        $suffix = 'rd';
    } else {
        $suffix = 'th';
    }

    $formatted = $day . $suffix . ' ' . $date->format('F Y H:i:s');
    return $formatted;
}

function formatDate($datetime)
{
    $date = new DateTime($datetime);

    $day = (int) $date->format('j');

    if ($day % 10 == 1 && $day != 11) {
        $suffix = 'st';
    } elseif ($day % 10 == 2 && $day != 12) {
        $suffix = 'nd';
    } elseif ($day % 10 == 3 && $day != 13) {
        $suffix = 'rd';
    } else {
        $suffix = 'th';
    }

    $formatted = $day . $suffix . ' ' . $date->format('F Y');
    return $formatted;
}

function getHoursWithinRange($timeLog, DateTime $firstDate, DateTime $secondDate): float
{
    $totalSeconds = 0;
    $countLogin = 0;
    $countLogout = 0;
    $login = [];
    $logout = [];

    foreach ($timeLog as $log) {
        if ($log['isLogin'] == 1) {
            $login[$countLogin] = new DateTime($log['timeOfLog']);
            $countLogin++;
        }
    }
    foreach ($timeLog as $log) {
        if ($log['isLogin'] == 0) {
            $logout[$countLogout] = new DateTime($log['timeOfLog']);
            $countLogout++;
        }
    }

    $sessionCount = min(count($login), count($logout));

    for ($index = 0; $index < $sessionCount; $index++) {
        $logInTime = $login[$index];
        $logOutTime = $logout[$index];

        $startOfSession = ($logInTime > $firstDate) ? $logInTime : $firstDate;
        $endOfSession = ($logOutTime < $secondDate) ? $logOutTime : $secondDate;

        if ($startOfSession <= $endOfSession) {
            $difference = $endOfSession->getTimestamp() - $startOfSession->getTimestamp();
            $totalSeconds += $difference;
        }
    }

    return round($totalSeconds / 3600, 0);
}

function getWeekHours($timeLog): float
{
    $today = new DateTime();
    $startOfWeek = (clone $today)->modify('monday this week')->setTime(0, 0);
    $endOfWeek = (clone $startOfWeek)->modify('+7 days');

    return getHoursWithinRange($timeLog, $startOfWeek, $endOfWeek);
}

function getMonthHours($timeLog): float
{
    $today = new DateTime();
    $startOfMonth = (clone $today)->modify('first day of this month')->setTime(0, 0);
    $endOfMonth = (clone $startOfMonth)->modify('first day of next month');

    return getHoursWithinRange($timeLog, $startOfMonth, $endOfMonth);
}

function getAllTimeHours($timeLog): float
{
    return getHoursWithinRange($timeLog, new DateTime('1970-01-01'), new DateTime('+1day'));
}
?>