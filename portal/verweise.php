<?php
session_start();
require_once('scripts/fkt_jkw.php');
require_once('design/box.php');
htmlhead('Aka Portal','',0);

echo'
<table border=0 width="100%" class="head"><tr><td width="1%">&nbsp;</td><td width="98%" >
<div style="float:left;" class="head">
<a target="Daten" href="../reserve/" class="head">Reservierungssystem</a> &nbsp; | &nbsp;
<a target="Daten" href="../drinks/" class="head">Getr&auml;nkeabrechnung</a> &nbsp; | &nbsp;
<a target="_blank" href="https://my.hidrive.com/share/zf11qosqq0" class="head">Protokolle/Dateien</a> &nbsp; | &nbsp;
<!-- <a target="Daten" href="../liste_des_tyrannen/" class="head">Liste des Tyrannen</a> &nbsp; | &nbsp; -->
<a target="Daten" href="startseite.php" class="head">Start</a>
</div><div style="float:right;"></div><br>
<hr style="height:0;  border-bottom:1px dotted #000000; border-top: 0px;">
</td><td width="1%">&nbsp;</td></tr></table>';



echo'</html>';

?>
