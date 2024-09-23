<?php
$startOfToday = mktime(0, 0, 0, date("m", time()), date("d", time()), date("Y"));
$startOfYesterday = $startOfToday - 86400; // 86400 seconds = 1 day
$currentTime = time();
$dbTable = "aka_counter";

// Initialize session counter if not set
if (!isset($_SESSION['counter']) || $_SESSION['counter'] == 0) {
    $mysqli->query("INSERT INTO `$dbTable` (time, typ) VALUES ($currentTime, '0')");
    $_SESSION['counter'] = $currentTime;
}

// Check if there are more than 200 entries from before yesterday
$oldEntryCount = mysqli_num_rows($mysqli->query("SELECT time, typ FROM `$dbTable` WHERE typ <> '9' AND time < $startOfYesterday"));
if ($oldEntryCount > 200) {
    // Count old counter entries (typ = '0') before yesterday
    $oldCounterEntries = mysqli_num_rows($mysqli->query("SELECT time, typ FROM `$dbTable` WHERE time < '$startOfYesterday' AND typ = '0'"));

    // Get the existing total counter (typ = '9')
    list($totalOldCount) = mysqli_fetch_row($mysqli->query("SELECT time FROM `$dbTable` WHERE typ = '9'"));

    // Update the total counter with the new counts
    $newTotalCount = $totalOldCount + $oldCounterEntries;
    $mysqli->query("UPDATE `$dbTable` SET time = '$newTotalCount' WHERE typ = '9'");

    // Delete old individual counter entries (typ = '0')
    $mysqli->query("DELETE FROM `$dbTable` WHERE time < '$startOfYesterday' AND typ = '0'");
}

// Get the total count of non-total counter entries
$totalNonTotalCount = mysqli_num_rows($mysqli->query("SELECT time, typ FROM `$dbTable` WHERE typ <> '9'"));

// Get the current total counter (typ = '9')
list($totalCounter) = mysqli_fetch_row($mysqli->query("SELECT time FROM `$dbTable` WHERE typ = '9'"));

// Sum of total and non-total counter entries
$totalEntries = $totalNonTotalCount + $totalCounter;

// Backup: insert a total counter if it does not exist
if (empty($totalCounter)) {
    $mysqli->query("INSERT INTO `$dbTable` (time, typ) VALUES ('0', '9')");
}

// Get the counts for different time periods
$lastTwoHours = $currentTime - 3600 * 2; // Last 2 hours
$todayCount = mysqli_num_rows($mysqli->query("SELECT * FROM `$dbTable` WHERE time > '$startOfToday' AND typ = '0'"));
$yesterdayCount = mysqli_num_rows($mysqli->query("SELECT * FROM `$dbTable` WHERE time > '$startOfYesterday' AND time < '$startOfToday' AND typ <> '9'"));
$lastTwoHoursCount = mysqli_num_rows($mysqli->query("SELECT * FROM `$dbTable` WHERE time > '$lastTwoHours' AND typ <> '9'"));

// Display the results
echo '<div class="little">
        <a href="index.php?mod=counter&mode=heute&'.SID.'">Heute</a> gesamt: ' . $todayCount . ' | 
        In den letzten 2 Std: ' . $lastTwoHoursCount . ' | 
        <a href="index.php?mod=counter&mode=gestern&'.SID.'">Gestern</a> gesamt: ' . $yesterdayCount . ' | 
        Gesamt: ' . $totalEntries . '
      </div>';
?>
