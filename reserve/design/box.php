<?php

function tab_go($width, $height, $align, $title) {
	if (substr($title, 0, 4) !== '<div') {
		$title = $title;
	}
	echo '<div class="headline">' . $title . '</div>';
}

function tab_end() {
	echo '&nbsp;';
}

function tab_box($width, $height, $align, $title, $text) {
	tab_go($width, $height, $align, $title);
	echo $text;
	tab_end();
}

function htmlhead($title, $add_header, $nobody) {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="de">
            <head>
                <title>' . $title . '</title>
                <link href="design/template.css" rel="stylesheet" type="text/css" />
                <meta http-equiv="content-type" content="text/html;charset=utf-8" />
                <meta http-equiv="Content-Style-Type" content="text/css" />
                ' . $add_header . '
            </head>';
	if ($nobody !== '1') {
		echo '<body>';
	}
}

function sub_tab_box($width, $height, $align, $title, $text, $sub) {
	sub_tab_go($width, $height, $align, $title, $sub);
	echo $text;
	sub_tab_end($sub);
}

function sub_tab_go($width, $height, $align, $title, $sub) {
	if (substr($title, 0, 4) !== '<div') {
		$title = ' &nbsp; &nbsp; ' . $title;
	}
	echo '<table width="' . $width . '" style="height: ' . $height . '" align="' . $align . '" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="background-image:url(' . $sub . '/design/corner_ul.png)" width="24" height="24"></td>
            <td style="background-image:url(' . $sub . '/design/top.png)" colspan="2"><b>' . $title . '</b></td>
        </tr>
        <tr>
            <td style="background-image:url(' . $sub . '/design/left.png)"></td>
            <td>';
}

function sub_tab_end($sub) {
	echo '</td>
        <td></td>
    </tr>
    <tr>
        <td height="10" style="background-image:url(' . $sub . '/design/left.png)"></td>
        <td></td>
        <td></td>
    </tr>
</table>';
}

function sub_htmlhead($title, $add_header, $nobody, $sub) {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="de">
            <head>
                <title>' . $title . '</title>
                <link href="' . $sub . '/design/template.css" rel="stylesheet" type="text/css" />
                <meta http-equiv="content-type" content="text/html;charset=utf-8" />
                <meta http-equiv="Content-Style-Type" content="text/css" />
                ' . $add_header . '
            </head>';
	if ($nobody !== '1') {
		echo '<body>';
	}
}
?>
