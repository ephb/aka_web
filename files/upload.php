<?php
if(empty($_SESSION['session_user_typ'])) { exit('falsches passwort'); };
##################### Tabelle #############################################
tab_go("100%",250,'left','Upload');
print_r($_POST);
$beschr_post='';
#################### upload verarbeiten #########################
if(!empty($_POST['upload'])) {
	#$uploadDir = './files/';
	$uploadDir =  //var/www/aka_web/files/files/;
	$uploadFile = $uploadDir.$_FILES['userfile']['name'];
	$uploadFile = strtolower($uploadFile);
	$extension = pathinfo($uploadFile);
	$date=mktime(0,0,0,$_POST['monat'],intval($_POST['tag']),$_POST['jahr']);
	//if($_FILES['userfile']['type'] == 'application/pdf'){
	if($extension['extension']=="pdf"){
	//if(pathinfo($filename, PATHINFO_EXTENSION); $_FILES['userfile']['type'] == 'application/pdf'){
	//if (1==1) { // filetyp klappt zuoft nicht
		$extension = ".".$extension['extension'];
		$uploadFile = str_replace($extension,'_'.$date.$extension,strtolower($uploadFile));
		
		echo "<pre>";
		#echo 'von:'.$_FILES['userfile']['tmp_name'].' nach '.$uploadFile.' <br><br>';
		print_r($_FILES);
		if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)){ 
			list($id)=mysqli_fetch_row($mysqli->query("SELECT `ID` FROM `aka_file_list` ORDER BY `ID` DESC LIMIT 0,1"));
			$id++;
			$uploadFile='files/'.basename($uploadFile);
			if($mysqli->query("INSERT INTO `aka_file_list` (`ID` ,`date` ,`Filename` ,`Bes`) VALUES ('".$id."', '".$date."', '".$uploadFile."', '".$_POST['beschr']."') ")){
				echo '<font color="green"><b>Datei ist in Ordnung und Sie wurde erfolgreich hochgeladen.</b></font>';
				#echo "Hier sind die Fehler informationen:\n";
				#print_r($_FILES);
				########### email ##############
				include_once('mailer/class.phpmailer.php');
				$mail    = new PHPMailer();
				$mail->From = "KKoolljjaa@googlemail.com";
				$mail->FromName = "Kolja Windeler";
				$mail->AddAddress("akakraft-l@listserv.uni-hannover.de");
				#$mail->AddAddress("KKoolljjaa@gmail.com");
				$mail->Subject = "Aka Fileupload Update";
				### umlaute rauswerfen
				$umlaute = array('ä', 'ö', 'ü','Ä','Ö','Ü','ß');
				$htmlcode = array(chr(228), chr(246),chr(252),chr(196),chr(214),chr(220),chr(223));
				$punkte = str_replace($umlaute, $htmlcode, $_POST['beschr']);
				### umlaute rauswerfen
				$text="Hallo Leute, \r\nes wurde soeben eine neue Datei hochgeladen.\r\n\r\nBeschreibung: \r\n -".str_replace('//',' -',$_POST['beschr']);
				$text.="\r\n\r\nEinzusehen unter:\r\nhttp://akakraft.de/portal/ | PW: akapw  \r\nBeste Gr".chr(252).chr(223)."e, der Fileuploadomat.\r\n\r\n Diese Mail wurde automatisch erzeugt und hat nur den Absender wegen dem Verteiler.";
				$mail->Body  = $text;

				if(!$mail->Send())
					{	echo '<br><font color="red"><b>Die Mail konnte nicht verschickt werden.</b></font>';	} 
				else 	{	echo '<br><font color="green"><b>Die Mail wurde erfolgreich verschickt.</b></font>';  };	
				
				########### email ##############
				};
			}
		else{
			echo "Es wurde ein Fehler gemeldet!\nHier sind die Fehler informationen:\n";
			print_r($_FILES);
			}
		echo "</pre>";
		}
	else {
		echo 'Der Typ der Datei ist :<b>'.$_FILES['userfile']['type'].'</b> und nur <b>application/pdf</b> dateien sind erlaubt! <br>Upload abgelehnt!';
		$beschr_post=$_POST['beschr'];
		};
	};
#################### upload verarbeiten #########################
#################### upload tabelle #########################
$options_tag=[];	$options_monat=[];		$options_jahr=[];
for($a=0;$a<=30;$a++) { $b=$a+1; $options_tag[$a]=$b; };
for($a=0;$a<=11;$a++) { $b=$a+1; $options_monat[$a]=$b; };
for($a=date('Y',time())-5;$a<=date('Y',time())+5;$a++) { $b=$a-(date('Y',time())-5); $options_jahr[$b]=$a; };
$values_tag=$options_tag;
$values_monat=$options_monat;
$values_jahr=$options_jahr;
echo	'<input type="hidden" name="MAX_FILE_SIZE" value="100000">
			<table width="100%" class="singletable">
				<tr><th width="250"><font color="#ffffff" ><b>Datei ausw&auml;hlen:</b></th>
						<td><form enctype="multipart/form-data" action="index.php?mod=upload&'.SID.'" method="post">
								<input name="userfile" accept=".pdf" type="file" size="60"></td></tr>
				<tr><th><font color="#ffffff"><b>Beschreibung eingeben:<b><br><i>"//" trennt die Punkte</i></th>
						<td><textarea name="beschr" cols="60" >'.$beschr_post.'</textarea></td></tr>
				<tr><th><font color="#ffffff"><b>Datum manipulieren:<b></th>
						<td><select name="tag">'.select($values_tag,$options_tag,date('d',time())).'</select>
								<select name="monat">'.select($values_monat,$options_monat,date('m',time())).'</select>
								<select name="jahr">'.select($values_jahr,$options_jahr,date('Y',time())).'</select>
								</td></tr>
				<tr><th><font color="#ffffff"><b>Upload:<b><br><i>Maximale Filesize: 100 MB</i></th>
						<td><input type="submit" name="upload" value="Upload starten"></td></tr>
			</table></form>';

tab_end();
?>
