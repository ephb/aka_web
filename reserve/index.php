<?php
// Preparations
if (!isset($_SESSION)) {
    session_start();
}

include('../a_common_scripts/config.php');
include('../a_common_scripts/fkt_jkw.php');
include('design/box.php');

htmlhead('Aka Reservation System', '<script type="text/javascript" src="javascript/jquery-1.4.3.min.js"></script>', 0);
include("../a_common_scripts/jsc.php");
include('../a_common_scripts/sec.php');

// Menu with modern table and CSS styling
echo '<table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 5%;">&nbsp;</td>
            <td style="width: 90%;" class="head">
                <div style="text-align:right;">
                    <a href="index.php?' . SID . '" class="head">Übersicht</a> &nbsp; | &nbsp;
                    <a href="index.php?' . SID . '&mod=rules" class="head">Regeln</a> &nbsp; | &nbsp;
                    <a href="index.php?logout=1&' . SID . '" class="head">Logout</a> &nbsp;
                </div>
            </td>
            <td style="width: 5%;">&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>';

// Load content based on "mod" parameter
if (isset($_GET['mod'])) {
    if ($_GET['mod'] == 'rules') {
        include('rules.php');
    }
}

// Include the main content
include('tab.php');

echo '</td><td>&nbsp;</td></tr></table>';

// Include counter and footer
echo '<div style="text-align:center;">';
include("counter.php");
echo '</div>';

echo '<div style="text-align:center;">' . impressum() . '</div></body></html>';
?>
