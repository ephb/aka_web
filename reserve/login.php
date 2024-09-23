<?php
// Start the session if not already started
if (!isset($_SESSION)) {
	session_start();
}

// Clean up old session data (older than 10 hours)
$timeLimit = time() - 10 * 60 * 60; // 10 hours
$mysqli->query("DELETE FROM `sec` WHERE `date` < $timeLimit;");

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
	$mysqli->query("DELETE FROM `sec` WHERE `key` = " . $_SESSION['session_key'] . ";");
	unset($_SESSION['session_key']);
	session_unset();
	$msg = 'Erfolgreich ausgeloggt.';
}

// Handle login
if (!empty($_POST['pw'])) {
	$msg = '<br /><span style="color: green; ">Login, bitte warten...</span>';

	// Set user type based on password input
	if ($_POST['pw'] == $aka_pw) {
		$_SESSION['session_user_typ'] = 1;
	} elseif ($_POST['pw'] == $aka_reserve_pw) {
		$_SESSION['session_user_typ'] = $aka_reserve_admin_state;
	} elseif ($_POST['pw'] == $aka_reserve_watcher_pw) {
		$_SESSION['session_user_typ'] = $aka_reserve_watcher_state;
	} elseif ($_POST['pw'] == $aka_super_admin_pw) {
		$_SESSION['session_user_typ'] = $aka_super_admin_state;
	}

	// If a valid password is provided, create a session key and insert it into the database
	if (in_array($_POST['pw'], [$aka_pw, $aka_reserve_pw, $aka_super_admin_pw, $aka_reserve_watcher_pw])) {
		$_SESSION['session_key'] = rand(0, 99999);
		$mysqli->query("INSERT INTO `sec` (`id`, `date`, `ip`, `key`) VALUES (NULL, '" . time() . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $_SESSION['session_key'] . "');");
	} else {
		$msg = 'Falsches Passwort.';
	}
}

// GUI for login screen
if (empty($_SESSION['session_key'])) {
	$msg = $msg ?? "";
	require_once('design/box.php');

	htmlhead('AkAKraft Drinks Login', '', '');
	echo '<table style="height: 100%; width: 100%" border="0">
            <tr>
                <td valign="middle" align="center">
                    <img src="../portal/img/logo_256px_transparent.png"><br /><br /><br /><br />
                    <form method="post" action="index.php?' . $_SERVER['QUERY_STRING'] . '" name="login">';

	tab_go(600, '', 'center', 'Login');
	echo '      <table align="center" width="400" style="height: 100%">
                    <tr>
                        <td width="40%">Password:</td>
                        <td align="right" width="40%">
                            <input type="password" name="pw" tabindex="2" class="inputbox" />
                        </td>
                        <td>
                            <input type="submit" name="submit" value="Login" tabindex="3" class="inputbox" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="3">&nbsp;' . $msg . '</td>
                    </tr>
                </table>';

	tab_end();

	echo '  </form>
                </td>
            </tr>
            <tr>
                <td align="right" height="20">
                    <a href="mailto:admin@akakraft.de">Admin</a>
                </td>
            </tr>
        </table>
        <script type="text/javascript">document.login.pw.focus();</script>
        </body></html>';
	exit;
}
?>
