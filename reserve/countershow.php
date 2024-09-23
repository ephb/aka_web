<?php
session_start();
$time = time();
$time2 = time() - 86400;
$mode = $_GET['mode'];

tab_go("100%", 200, "center", "Counter");

if ($mode == 'heute') {
    echo '<strong>Heute bisher:</strong><br>
    <div style="text-align: center;">
        <img src="img/counter_img.php?time=' . $time . '" alt="Counter Image">
    </div>
    <br><hr><br>
    <strong>Gestern gesamt:</strong><br>
    <div style="text-align: center;">
        <img src="img/counter_img.php?time=' . $time2 . '" alt="Counter Image">
    </div><br>';
} elseif ($mode == 'gestern') {
    echo '<strong>Gestern gesamt:</strong><br>
    <div style="text-align: center;">
        <img src="img/counter_img.php?time=' . $time2 . '" alt="Counter Image">
    </div>
    <br><hr><br>
    <strong>Heute bisher:</strong><br>
    <div style="text-align: center;">
        <img src="img/counter_img.php?time=' . $time . '" alt="Counter Image">
    </div><br>';
}

echo '<div style="text-align: center;"><a href="index.php?' . SID . '">Zurück</a></div><br>';

tab_end();
?>
