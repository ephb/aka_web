<?php
// Variables and Image Setup
$width = 600;
$height = 200;
$image = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($image, 255, 255, 255);
imagefilledrectangle($image, 0, 0, $width, $height, $white);

$time = $_GET['time'];

include('../scripts/config.php');
$dbTable = "aka_counter";

// Retrieve data from the database
$query = "SELECT time, typ FROM `$dbTable` WHERE typ <> '9'";
$result = $mysqli->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [$row['time'], $row['typ']];
}

// Sorting data into hours
$countData = [];
for ($hour = 0; $hour <= 23; $hour++) {
    $minTime = mktime($hour, 0, 0, date("n", $time), date("j", $time), date("Y", $time));
    $maxTime = mktime($hour + 1, 0, 0, date("n", $time), date("j", $time), date("Y", $time));

    $countData[$hour] = array_filter($data, function($entry) use ($minTime, $maxTime) {
        return $entry[0] > $minTime && $entry[0] < $maxTime;
    });
}

// Scale data
$maxCount = max(array_map('count', $countData));

if ($maxCount > 0) {
    $scaledData = [];
    for ($hour = 0; $hour <= 23; $hour++) {
        $scaledData[$hour] = round((count($countData[$hour]) / $maxCount) * 100);
    }
}

// Drawing the graph
$gapBetweenBars = 5;
$gapWidth = $gapBetweenBars * 30;
$barWidth = round(($width - 30 - $gapWidth) / 23);

for ($hour = 0; $hour <= 23; $hour++) {
    $xStart = ($gapBetweenBars * $hour) + ($barWidth * $hour);
    $xEnd = $xStart + $barWidth;

    imageline($image, $xStart - 3, 0, $xStart - 3, $height, imagecolorallocate($image, 234, 234, 234));

    if (!empty($countData[$hour])) {
        $scaledHeight = round($scaledData[$hour] * ($height - 30) / 100);
        $yStart = $height - $scaledHeight;

        $points = [
            $xStart, $yStart,
            $xStart + 5, $yStart - 5,
            $xStart + 5 + $barWidth, $yStart - 5,
            $xStart + 5 + $barWidth, $height - 6,
            $xStart + $barWidth, $height - 1,
            $xStart + $barWidth, $yStart
        ];

        imagerectangle($image, $xStart, $yStart, $xEnd, $height - 1, 0);
        imagefilledpolygon($image, $points, 6, imagecolorallocate($image, 222, 222, 222));
        imagepolygon($image, $points, 6, 0);
        imageline($image, $xEnd, $yStart, $xEnd + 5, $yStart - 5, 0);
        imagestring($image, 5, $xStart + round($barWidth / 6), $height - $scaledHeight + 5, count($countData[$hour]), 0);
    }

    imagestring($image, 5, $xStart + 3, $height - 10, $hour . '.', 0);
}

// Add labels
imagestring($image, 5, $xEnd + 15, $height - 35, 'Accesses', 0);
imagestring($image, 5, $xEnd + 15, $height - 10, 'Time', 0);

// Output the image
imagejpeg($image);
imagedestroy($image);
?>
