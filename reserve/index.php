<?php
########### vorbereitungen ###############
if (!isset($_SESSION)) {
    $SID = session_start();
}
include('../a_common_scripts//config.php');
include('../a_common_scripts//fkt_jkw.php');
include('design/box.php');
htmlhead('Aka Reservierungssystem', '<script type="text/javascript" src="javascript/jquery-1.4.3.min.js"></script>', 0);
include("../a_common_scripts//jsc.php");
include('../a_common_scripts//sec.php');
########### vorbereitungen ###############
########### menü ###############
echo '<table border="0" width="100%"><tr><td width="5%">&nbsp;</td><td width="90%"  class="head">';
#tab_go("100%",250,'center','');
echo
    '<div style="float:right;">
<a href="index.php?logout=1&' . $SID . '" class="head">Logout</a> &nbsp;</div></td><td width="5%">&nbsp;</td></tr><tr><td>&nbsp;</td><td>';
########### menü ###############
########### reinladen ###############
include('tab.php');
########### reinladen ###############
echo '</td><td>&nbsp;</td></tr></table><center>';
include("counter.php");
echo impressum() . '</center></body></html>';
?>
