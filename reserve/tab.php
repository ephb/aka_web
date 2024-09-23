<?php
require_once('../reserve/design/box.php');

// Define locations
$options = array(
	'Grube rechts',
	'Grube links',
	'Mehrzweckarbeitsplatz',
	'linke Bühne',
	'rechte Bühne',
	'Empore 1',
	'Empore 2',
	'Empore 3'
);

// Maximum reservation times (in seconds)
$max_time = array();
$one_day = 86400;
$max_time[0] = $one_day * 7 * 2;
$max_time[1] = $one_day * 30 * 6;
$max_time[2] = $one_day;
$max_time[3] = $one_day * 30 * 6;
$max_time[4] = $one_day * 3;
$max_time[5] = $one_day * 7 * 4;
$max_time[6] = $one_day * 7 * 4;
$max_time[7] = $one_day * 7 * 4;

$recover = 0;
$sql_done = 0;

// Process request
if (!empty($_POST['save'])) {
	// Convert time input to timestamps
	$temp = explode('-', $_POST['j_from_time-date']);
	$_POST['j_from_time-ts'] = mktime($_POST['j_from_time-hh'], $_POST['j_from_time-mi'], 0, $temp[1], $temp[2], $temp[0]);

	$temp = explode('-', $_POST['j_to_time-date']);
	$_POST['j_to_time-ts'] = mktime($_POST['j_to_time-hh'], $_POST['j_to_time-mi'], 0, $temp[1], $temp[2], $temp[0]);

	$stop = 0;
	$recover = 0;
	$reason = 0;

	// Check if reservation duration exceeds the maximum allowed time
	if ($_POST['j_to_time-ts'] - $_POST['j_from_time-ts'] > $max_time[$_POST['wo']]) {
		$stop = 1;
		$recover = 1;
		$reason = 1;
	} else {
		$query = "SELECT grund FROM aka_reserve WHERE ort = '" . $options[$_POST['wo']] . "' AND 
                 ((bis > " . $_POST['j_from_time-ts'] . " AND von < " . $_POST['j_to_time-ts'] . ") 
                 OR (von < " . $_POST['j_to_time-ts'] . " AND bis > " . $_POST['j_to_time-ts'] . ")) AND active = 1";
		$sql = $mysqli->query($query);
		$count = mysqli_num_rows($sql);
		if ($count > 0) {
			$stop = 1;
			$recover = 1;
			$reason = 4;
		}
	}

	// Insert reservation if no errors
	if (!$stop) {
		$sql = "INSERT INTO `aka_reserve` (`von`, `bis`, `ort`, `person`, `grund`, `time_create`, `ip_create`, `time_delete`, `ip_delete`, `active`) 
                        VALUES (" . $_POST['j_from_time-ts'] . ", " . $_POST['j_to_time-ts'] . ", '" . $options[$_POST['wo']] . "', 
                        '" . $_POST['name'] . "', '" . $_POST['warum'] . "', " . time() . ", '" . $_SERVER['REMOTE_ADDR'] . "', 0, '', 1)";
		if (!$mysqli->query($sql)) {
			echo 'Error occurred';
			$recover = 1;
			$reason = 3;
		} else {
			$sql_done = 1;
		}
	}
} elseif (!empty($_GET['delete'])) {
	$sql = "UPDATE `aka_reserve` SET `active` = 0, `ip_delete` = '" . $_SERVER['REMOTE_ADDR'] . "', `time_delete` = " . time() . " WHERE `id` = " . $_GET['delete'] . " LIMIT 1;";
	if (!$mysqli->query($sql)) {
		echo 'Error occurred';
	} else {
		$info = '<span style="color:red;"><b>Reservierung erfolgreich gelöscht!</b></span>';
	}
}

// Collect and display data
tab_go("100%", 250, 'left', 'Neue Reservierung');

if ($recover == 1) {
	$result = java_cal2('', $_POST['j_from_time-ts'], $_POST['j_to_time-ts'], time(), mktime(0, 0, 0, 1, 1, 2030), '_time', 'h,i', false);
	$wer = $_POST['name'];
	$warum = $_POST['warum'];
	$wo = $_POST['wo'];
	if ($reason == 1) {
		$info = '<span style="color:red;"><b>Maximale Dauer für "' . $options[$wo] . '" beträgt ' . ($max_time[$wo] / $one_day) . ' Tage!</b></span>';
	} elseif ($reason == 3) {
		$info = '<span style="color:red;"><b>Fehler in der Datenbank, bitte später erneut versuchen oder den Admin informieren.</b></span>';
	} elseif ($reason == 4) {
		$info = '<span style="color:red;"><b>Dieser Platz ist im angegebenen Zeitraum bereits reserviert!</b></span>';
	}
} else {
	$result = java_cal2('', floor(time() / 3600) * 3600, floor(time() / 3600) * 3600 + 3600 * 3, time(), mktime(0, 0, 0, 1, 1, 2030), '_time', 'h,i', false);
	$wer = '';
	$wo = '';
	$warum = '';
	$info = '';
	if ($sql_done == 1) {
		$info = '<span style="color:red;"><b>Reservierung erfolgreich eingetragen!</b></span>';
	}
}

echo $result['css'];
echo '<form action="index.php" method="POST">
<table style="width:100%;" class="singletable" cellpadding="0">
    <tr><th>Von</th><th>Bis</th><th>Wo</th><th>Wer</th><th>Warum</th></tr>
    <tr>
        <td>' . $result['from'] . '</td>
        <td>' . $result['to'] . '</td>
        <td><select name="wo">' . select('', $options, $wo, '') . '</select></td>
        <td><input type="text" size="20" name="name" value="' . $wer . '"></td>
        <td><input type="text" size="20" name="warum" value="' . $warum . '"></td>
    </tr>
    <tr><td colspan="5" style="text-align:right;"><input type="submit" value="Speichern" name="save"> &nbsp; &nbsp; ' . $info . '</td></tr>
</table></form>';
echo $result['java'];
tab_end();

// Display reservation table

tab_go("100%", 250, 'left', '');
tab_end();

// Fetch and display reservation data
echo '</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>';

// Table header
echo '<table style="width:100%;" class="singletable" cellpadding="0">
        <tr><th>Von</th><th>Bis</th><th>Wo</th><th>Wer</th>';
if ($_SESSION['session_user_typ'] != $aka_reserve_watcher_state) {
    echo '<th>Warum</th>';
}
echo '<th>Angemeldet</th>';
if ($_SESSION['session_user_typ'] != $aka_reserve_watcher_state) {
    echo '<th>Entfernen</th>';
}
echo '</tr>';

// Fetch data from database
$reservationQuery = "SELECT id, von, bis, ort, person, grund, time_create FROM aka_reserve WHERE active = 1 AND bis >= " . time() . " ORDER BY ort, von";
$reservations = $mysqli->query($reservationQuery);
$reservationData = [];
while ($row = $reservations->fetch_assoc()) {
    $reservationData[] = $row;
}

// Display data
if (count($reservationData) > 0) {
    $b = 0;  // Counter to alternate row colors
    foreach ($reservationData as $index => $reservation) {
        $isCurrent = ($reservation['von'] < time() && $reservation['bis'] > time());
        $endsToday = (floor($reservation['bis'] / $one_day) == floor(time() / $one_day));

        // Alternate row colors: gray for odd rows
        $bg_color = ($b % 2 == 1) ? ' class="gray"' : '';

        echo '<tr' . $bg_color . '>
            <td>' . ($isCurrent ? '<b>' : '') . ($endsToday ? '<span style="color: #880000;">' : '') . date("d.m.y H:i", $reservation['von']) . ($isCurrent ? '</b>' : '') . ($endsToday ? '</span>' : '') . '</td>
            <td>' . ($isCurrent ? '<b>' : '') . ($endsToday ? '<span style="color: #880000;">' : '') . date("d.m.y H:i", $reservation['bis']) . ($isCurrent ? '</b>' : '') . ($endsToday ? '</span>' : '') . '</td>
            <td>' . ($isCurrent ? '<b>' : '') . ($endsToday ? '<span style="color: #880000;">' : '') . $reservation['ort'] . ($isCurrent ? '</b>' : '') . ($endsToday ? '</span>' : '') . '</td>
            <td>' . ($isCurrent ? '<b>' : '') . ($endsToday ? '<span style="color: #880000;">' : '') . $reservation['person'] . ($isCurrent ? '</b>' : '') . ($endsToday ? '</span>' : '') . '</td>';

        if ($_SESSION['session_user_typ'] != $aka_reserve_watcher_state) {
            echo '<td>' . ($isCurrent ? '<b>' : '') . ($endsToday ? '<span style="color: #880000;">' : '') . $reservation['grund'] . ($isCurrent ? '</b>' : '') . ($endsToday ? '</span>' : '') . '</td>';
        }

        echo '<td>' . date("H:i d.m.y", $reservation['time_create']) . '</td>';

        if ($_SESSION['session_user_typ'] != $aka_reserve_watcher_state) {
            echo '<td><a href="index.php?delete=' . $reservation['id'] . '" onclick="return confirmLink(this, \'Bitte nur eigene Reservierungen entfernen. Deine ID wird geloggt!\')">Entfernen</a></td>';
        }

        echo '</tr>';

        $b++;  // Increment counter for alternating rows
    }
} else {
    echo '<tr><td colspan="8" style="height:30px;">Keine Reservierungen mehr vorhanden</td></tr>';
}

echo '<tr><td colspan="8" style="height:30px;"><b>Fett</b>=Aktuelle Belegung, <span style="color:#880000;">Rot</span>=Reservierung endet heute</td></tr>';
echo '</table>';
tab_end();
?>
