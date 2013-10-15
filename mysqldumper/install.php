<?php
if (!@ob_start("ob_gzhandler")) @ob_start();
$install_ftp_server=$install_ftp_user_name=$install_ftp_user_pass=$install_ftp_path="";
$dbhost=$dbuser=$dbpass=$dbport=$dbsocket=$manual_db='';
foreach ($_GET as $getvar=>$getval)
{
	${$getvar}=$getval;
}
foreach ($_POST as $postvar=>$postval)
{
	${$postvar}=$postval;
}
include_once ( './inc/functions.php' );
include_once ( './inc/mysql.php' );
include_once ( './inc/runtime.php' );
if (!isset($language)) $language="en";

$config['language']=$language;
include ( './language/lang_list.php' );
include ( 'language/' . $language . '/lang_install.php' );
include ( 'language/' . $language . '/lang_main.php' );
include ( 'language/' . $language . '/lang_config_overview.php' );

//Übergabe der Parameter über FORM
if (isset($_POST['dbhost']))
{
	$config['dbhost']=$dbhost;
	$config['dbuser']=$dbuser;
	$config['dbpass']=$dbpass;
	$config['dbport']=$dbport;
	$config['dbsocket']=$dbsocket;
	$config['manual_db']=$manual_db;
}
else
{
	// Wenn Connection-String existiert -> Verbindungsdaten aus connstr auslesen
	if (isset($connstr) && !empty($connstr))
	{
		$p=explode("|", $connstr);
		$dbhost=$config['dbhost']=$p[0];
		$dbuser=$config['dbuser']=$p[1];
		$dbpass=$config['dbpass']=$p[2];
		$dbport=$config['dbport']=$p[3];
		$dbsocket=$config['dbsocket']=$p[4];
		$manual_db=$config['manual_db']=$p[5];
	}
	else
		$connstr="";
}

//Variabeln
$phase=( isset($phase) ) ? $phase : 0;
if (isset($_POST['manual_db'])) $manual_db=trim($_POST['manual_db']);
$connstr = "$dbhost|$dbuser|$dbpass|$dbport|$dbsocket|$manual_db";
$connection='';
$delfiles=Array();

$config['files']['iconpath']='./css/msd/icons/';
$img_ok='<img src="' . $config['files']['iconpath'] . 'ok.gif" width="16" height="16" alt="ok">';
$img_failed='<img src="' . $config['files']['iconpath'] . 'notok.gif" width="16" height="16" alt="failed">';
$href="install.php?language=$language&phase=$phase&connstr=$connstr";
header('content-type: text/html; charset=utf-8');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="cache-control" content="must-revalidate">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>MySQLDumper - Installation</title>

<link rel="stylesheet" type="text/css" href="css/msd/style.css">
<script src="js/script.js" type="text/javascript"></script>
<style type="text/css" media="screen">
td {
	border: 1px solid #ddd;
}

td table td {
	border: 0;
}
</style>
</head>
<body class="content">
<script language="JavaScript" type="text/javascript">
function hide_tooldivs() {
	<?php
	foreach ($lang['languages'] as $key)
	{
		echo 'document.getElementById("' . $key . '").style.display = \'none\';' . "\n";
	}
	?>
}

function show_tooldivs(lab) {
	hide_tooldivs();
	switch(lab) {
		<?php
		foreach ($lang['languages'] as $key)
		{
			echo 'case "' . $key . '":' . "\n" . 'document.getElementById("' . $key . '").style.display = \'block\';' . "\n" . 'break;' . "\n";
		}
		?>

	}
}
</script>


<?php
if ($phase < 10)
{
	if ($phase == 0) $Anzeige=$lang['L_INSTALL'] . ' - ' . $lang['L_INSTALLMENU'];
	else $Anzeige=$lang['L_INSTALL'] . ' - ' . $lang['L_STEP'] . ' ' . ( $phase );
}
elseif ($phase > 9 && $phase < 12)
{
	$Anzeige=$lang['L_INSTALL'] . ' - ' . $lang['L_STEP'] . ' ' . ( $phase - 7 );
}
elseif ($phase > 19 && $phase < 100)
{
	$Anzeige=$lang['L_TOOLS'];
}
else
{
	$Anzeige=$lang['L_UNINSTALL'] . ' - ' . $lang['L_STEP'] . ' ' . ( $phase - 99 );
}

echo '<img src="css/msd/pics/h1_logo.gif" alt="' . $lang['L_INSTALL_TOMENU'] . '">';
echo '<div id="pagetitle"><p>
' . $Anzeige . '
</p></div>';

echo '<div id="content" align="center"><p class="small"><strong>Version ' . MSD_VERSION . '</strong><br></p>';

switch ($phase)
{

	case 0: // Anfang - Sprachauswahl
		// da viele ja nicht in die Anleitung schauen -> versuchen die Perldateien automatisch richtig zu chmodden
		@chmod('./msd_cron/crondump.pl',0755);
		@chmod('./msd_cron/perltest.pl',0755);
		@chmod('./msd_cron/simpletest.pl',0755);

		echo '<form action="install.php" method="get"><input type="hidden" name="phase" value="1">';
		echo '<table class="bdr"><tr class="thead"><th>Language</th><th>Tools</th></tr>';
		echo '<tr><td valign="top" width="300"><table>';
		echo GetLanguageCombo("radio","radio","language","<tr><td>","</td></tr>");
		echo '</table></td><td valign="top">';

		foreach ($lang['languages'] as $key)
		{
			echo ( "\n<div id=\"" . $key . '"><a href="install.php?language=' . $key . '&phase=100">' . $lang['L_TOOLS1'][$key] . '</a><br><br>' );
			echo ( "</div>" );
		}

		echo ( "\n</td></tr><tr><td colspan=\"2\" style=\"padding: 4px\"><input type=\"submit\" name=\"submit\" value=\"Installation\" class=\"Formbutton\"></td></tr></table></form>" );
		echo '<script language="JavaScript" type="text/javascript">show_tooldivs("' . $language . '");</script>';
		break;
	case 1: // checken
		@chmod("config.php",0777);
		echo '<h6>' . $lang['L_DBPARAMETER'] . '</h6>';
		if (!is_writable("config.php"))
		{
			echo '<p class="warning">' . $lang['L_CONFIGNOTWRITABLE'] . '</p>';
			echo '<a href="' . $href . '">' . $lang['L_TRYAGAIN'] . '</a>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="install.php">' . $lang['L_INSTALL_TOMENU'] . '</a>';
		}
		else
		{
			$tmp=file("config.php");
			$stored=0;

			if (!isset($_POST['dbconnect']))
			{
				// Erstaufruf - Daten aus config.php auslesen
				for ($i=0; $i < count($tmp); $i++)
				{
					if (substr($tmp[$i],0,17) == '$config[\'dbhost\']')
					{
						$config['dbhost']=extractValue($tmp[$i]);
						$dbhost=$config['dbhost'];
						$stored++;
					}
					if (substr($tmp[$i],0,17) == '$config[\'dbport\']')
					{
						$config['dbport']=extractValue($tmp[$i]);
						$dbport=$config['dbport'];
						$stored++;
					}
					if (substr($tmp[$i],0,19) == '$config[\'dbsocket\']')
					{
						$config['dbsocket']=extractValue($tmp[$i]);
						$dbsocket=$config['dbsocket'];
						$stored++;
					}
					if (substr($tmp[$i],0,17) == '$config[\'dbuser\']')
					{
						$config['dbuser']=extractValue($tmp[$i]);
						$dbuser=$config['dbuser'];
						$stored++;
					}
					if (substr($tmp[$i],0,17) == '$config[\'dbpass\']')
					{
						$config['dbpass']=extractValue($tmp[$i]);
						$dbpass=$config['dbpass'];
						$stored++;
					}
					if (substr($tmp[$i],0,19) == '$config[\'language\']')
					{
						$config['language']=extractValue($tmp[$i]);
						$stored++;
					}
					if ($stored == 6) break;
				}
			}

			if (!isset($config['dbport'])) $config['dbport']="";
			if (!isset($config['dbsocket'])) $config['dbsocket']="";

			echo '<form action="install.php?language=' . $language . '&phase=' . $phase . '" method="post">';
			echo '<table class="bdr" style="width:700px;">';
			echo '<tr><td>' . $lang['L_DB_HOST'] . ':</td><td><input type="text" name="dbhost" value="' . $dbhost . '" size="60" maxlength="100"></td></tr>';
			echo '<tr><td>' . $lang['L_DB_USER'] . ':</td><td><input type="text" name="dbuser" value="' . $dbuser . '" size="60" maxlength="100"></td></tr>';
			echo '<tr><td>' . $lang['L_DB_PASS'] . ':</td><td><input type="password" name="dbpass" value="' . $dbpass . '" size="60" maxlength="100"></td></tr>';
			echo '<tr><td>* ' . $lang['L_DB'] . ':<p class="small">('.$lang['L_ENTER_DB_INFO'].')</p></td><td><input type="text" name="manual_db" value="' . $manual_db . '" size="60" maxlength="100"></td></tr>';
			echo '<tr><td>';
			echo $lang['L_PORT'] . ':</td><td><input type="text" name="dbport" value="' . $dbport . '" size="5" maxlength="5">&nbsp;&nbsp;' . $lang['L_INSTALL_HELP_PORT'] . '</td></tr>';
			echo '<tr><td>' . $lang['L_SOCKET'] . ':</td><td><input type="text" name="dbsocket" value="' . $dbsocket . '" size="30" maxlength="255">&nbsp;&nbsp;' . $lang['L_INSTALL_HELP_SOCKET'] . '</td></tr>';

			echo '<tr><td>' . $lang['L_TESTCONNECTION'] . ':</td><td><input type="submit" name="dbconnect" value="' . $lang['L_CONNECTTOMYSQL'] . '" class="Formbutton"></td></tr>';
			if (isset($_POST['dbconnect']))
			{
				echo '<tr class="thead"><th colspan="2">' . $lang['L_DBCONNECTION'] . '</th></tr>';
				echo '<tr><td colspan="2">';
				$connection=MSD_mysql_connect();

				if ($connection === false)
				{
					echo '<p class="error">' . $lang['L_CONNECTIONERROR'] . '</p><span>&nbsp;';
				}
				else
				{
				    $databases = array();
					echo '<p class="success">' . $lang['L_CONNECTION_OK'] . '</p><span class="ssmall">';
					$connection="ok";
					$connstr="$dbhost|$dbuser|$dbpass|$dbport|$dbsocket|$manual_db";
					echo '<input type="hidden" name="connstr" value="' . $connstr . '">';
					if ($manual_db > '') SearchDatabases(1,$manual_db);
					else SearchDatabases(1);
					if (!isset($databases['Name']) || !in_array($manual_db, $databases['Name'])) {
                        // conect to manual db was not successful
					    $connstr = substr($connstr,0, strlen($connstr)-strlen($manual_db));
					    $manual_db = '';
					}
				}
				echo '</span></td></tr>';
			}
			echo '</table></form><br>';

			if ($connection == "ok")
			{
				if (!isset($databases['Name'][0])) echo '<br>' . $lang['L_NO_DB_FOUND_INFO'];

				echo '<form action="install.php?language=' . $language . '&phase=' . ( $phase + 1 ) . '" method="post">';
				echo '<input type="hidden" name="dbhost" value="' . $config['dbhost'] . '">
			<input type="hidden" name="dbuser" value="' . $config['dbuser'] . '">
			<input type="hidden" name="dbpass" value="' . $config['dbpass'] . '">
			<input type="hidden" name="manual_db" value="' . $manual_db . '">
			<input type="hidden" name="dbport" value="' . $config['dbport'] . '">
			<input type="hidden" name="dbsocket" value="' . $config['dbsocket'] . '">
			<input type="hidden" name="connstr" value="' . $connstr . '">';
				echo '<input type="submit" name="submit" value=" ' . $lang['L_SAVEANDCONTINUE'] . ' " class="Formbutton"></form>';
			}
		}
		break;

	case 2: //
		echo '<h6>MySQLDumper - ' . $lang['L_CONFBASIC'] . '</h6>';
		$tmp=@file("config.php");
		$stored=0;
		for ($i=0; $i < count($tmp); $i++)
		{
			if (substr($tmp[$i],0,17) == '$config[\'dbhost\']')
			{
				$tmp[$i]='$config[\'dbhost\'] = \'' . $dbhost . '\';' . "\n";
				$stored++;
			}
			if (substr($tmp[$i],0,17) == '$config[\'dbport\']')
			{
				$tmp[$i]='$config[\'dbport\'] = \'' . $dbport . '\';' . "\n";
				$stored++;
			}
			if (substr($tmp[$i],0,19) == '$config[\'dbsocket\']')
			{
				$tmp[$i]='$config[\'dbsocket\'] = \'' . $dbsocket . '\';' . "\n";
				$stored++;
			}
			if (substr($tmp[$i],0,17) == '$config[\'dbuser\']')
			{
				$tmp[$i]='$config[\'dbuser\'] = \'' . $dbuser . '\';' . "\n";
				$stored++;
			}
			if (substr($tmp[$i],0,17) == '$config[\'dbpass\']')
			{
				$tmp[$i]='$config[\'dbpass\'] = \'' . $dbpass . '\';' . "\n";
				$stored++;
			}

			if ($stored == 6) break;
		}
		$ret=true;
		if ($fp=fopen("config.php","wb"))
		{
			if (!fwrite($fp,implode($tmp,""))) $ret=false;
			if (!fclose($fp)) $ret=false;
			@chmod("config.php",0644);
		}
		if (!$ret)
		{
			echo '<p class="warnung">' . $lang['L_CONFIG_SAVE_ERROR'] . '</p>';
		}
		else
		{
			if (ini_get('safe_mode') == 1)
			{
				$nextphase=( extension_loaded("ftp") ) ? 10 : 9;
			}
			else
				$nextphase=$phase + 2;
			echo $lang['L_INSTALL_STEP2FINISHED'];
			echo '<p>&nbsp;</p>';
			echo '<form action="install.php?language=' . $language . '&phase=' . $nextphase . '" method="post" name="continue"><input type="hidden" name="connstr" value="' . $connstr . '"><input class="Formbutton" style="width:360px;" type="submit" name="continue2" value=" ' . $lang['L_INSTALL_STEP2_1'] . ' "></form>';
			echo '<script language="javascript">';
			echo 'document.forms["continue"].submit();';
			echo '</script>';
		}

		break;

	case 4: //Verzeichnisse
		if (isset($_POST['submit']))
		{
			$ret=true;
			if ($fp=fopen("config.php","wb"))
			{
				if (!fwrite($fp,stripslashes(stripslashes($_POST['configfile'])))) $ret=false;
				if (!fclose($fp)) $ret=false;
			}
			else
				$ret=false;

			if ($ret == false)
			{
				echo '<br><strong>' . $lang['L_ERRORMAN'] . ' config.php ' . $lang['L_MANUELL'] . '.';
				die();
			}
		}

		echo '<h6>' . $lang['L_CREATEDIRS'] . '</h6>';
		$check_dirs=ARRAY(

							"work/",
							"work/config/",
							"work/log/",
							"work/backup/"
		);
		$msg='';
		foreach ($check_dirs as $d)
		{
			$success=SetFileRechte($d,1,0777);
			if ($success != 1) $msg.=$success . '<br>';
		}

		if ($msg > '') echo '<b>' . $msg . '</b>';

		$iw[0]=IsWritable("work");
		$iw[1]=IsWritable("work/config");
		$iw[2]=IsWritable("work/log");
		$iw[3]=IsWritable("work/backup");
/*
		// save manual_db
		if ($manual_db > '')
		{
			if (file_exists('./' . $config['files']['dbs_manual'])) @unlink('./' . $config['files']['dbs_manual']);
			$file_handle=fopen('./' . $config['files']['dbs_manual'],'a');
			if ($file_handle)
			{
				fwrite($file_handle,$manual_db);
				fclose($file_handle);
				@chmod('./' . $config['files']['dbs_manual'],0777);
			}
		}
*/
		if ($iw[0] && $iw[1] && $iw[2] && $iw[3])
		{
			echo '<script language="javascript">';
			echo 'self.location.href=\'install.php?language=' . $language . '&phase=5&connstr=' . $connstr . '\'';
			echo '</script>';
		}

		echo '<form action="install.php?language=' . $language . '&phase=4" method="post"><table class="bdr"><tr class="thead">';
		echo '<th>' . $lang['L_DIR'] . '</th><th>' . $lang['L_RECHTE'] . '</th><th>' . $lang['L_STATUS'] . '</th></tr>';
		echo '<tr><td><strong>work</strong></td><td>' . Rechte("work") . '</td><td>' . ( ( $iw[0] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td><strong>work/config</strong></td><td>' . Rechte("work/config") . '</td><td>' . ( ( $iw[1] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td><strong>work/log</strong></td><td>' . Rechte("work/log") . '</td><td>' . ( ( $iw[2] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td><strong>work/backup</strong></td><td>' . Rechte("work/backup") . '</td><td>' . ( ( $iw[3] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td colspan="3" align="right"><input type="hidden" name="connstr" value="' . $connstr . '"><input class="Formbutton" type="submit" name="dir_check" value=" ' . $lang['L_CHECK_DIRS'] . ' "></td></tr>';
		if ($iw[0] && $iw[1] && $iw[2] && $iw[3]) echo '<tr><td colspan="2">' . $lang['L_DIRS_CREATED'] . '<br><br><input class="Formbutton" type="Button" value=" ' . $lang['L_INSTALL_CONTINUE'] . ' " onclick="location.href=\'install.php?language=' . $language . '&phase=5&connstr=' . $connstr . '\'"></td></tr>';
		echo '</table></form>';
		break;
	case 5:
		echo '<h6>' . $lang['L_LASTSTEP'] . '</h6>';

		echo '<br><h4>' . $lang['L_INSTALLFINISHED'] . '</h4>';
		SetDefault(1);
		include ( "language/" . $language . "/lang_install.php" );

		// direkt zum Start des Dumeprs
		echo '<script language="javascript">self.location.href=\'index.php\';</script>';
		break;
	case 9:

		clearstatcache();
		$iw[0]=IsWritable("work");
		$iw[1]=IsWritable("work/config");
		$iw[2]=IsWritable("work/log");
		$iw[3]=IsWritable("work/backup");
		echo '<h6>' . $lang['L_FTPMODE'] . '</h6>';
		echo '<p align="left" style="padding-left:100px; padding-right:100px;">' . $lang['L_SAFEMODEDESC'] . '</p>';

		echo '<form action="install.php?language=' . $language . '&phase=9" method="post"><input type="hidden" name="connstr" value="' . $connstr . '"><table>';
		echo '<tr><td class="hd2" colspan="2">' . $lang['L_IDOMANUAL'] . '</td></tr>';
		echo '<tr><td colspan="2">' . $lang['L_DOFROM'] . '<br><div class="small">' . Realpfad('./') . '</div></td></tr>';
		echo '<tr><td><strong>work</strong></td><td>' . ( ( $iw[0] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td><strong>work/config</strong></td><td>' . ( ( $iw[1] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td><strong>work/log</strong></td><td>' . ( ( $iw[2] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td><strong>work/backup</strong></td><td>' . ( ( $iw[3] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td colspan="3" align="right"><input type="submit" class="Formbutton" name="dir_check" value=" ' . $lang['L_CHECK_DIRS'] . ' "></td></tr>';

		// Wenn Verzeichnisse erstellt wurden - direkt weitermachen
		if ($iw[0] && $iw[1] && $iw[2] && $iw[3])
		{
			echo '<script language="javascript">';
			echo 'self.location.href=\'install.php?language=' . $language . '&phase=4&connstr=' . $connstr . '\'';
			echo '</script>';
		}
		echo '</table>';

		break;
	case 10: //safe_mode FTP
		$config['ftp_useSSL']=0;
		clearstatcache();
		$iw[0]=IsWritable("work");
		$iw[1]=IsWritable("work/config");
		$iw[2]=IsWritable("work/log");
		$iw[3]=IsWritable("work/backup");
		if (!isset($install_ftp_port) || $install_ftp_port < 1) $install_ftp_port=21;
		echo '<h6>' . $lang['L_FTPMODE'] . '</h6>';
		echo '<p align="left" style="padding-left:100px; padding-right:100px;">' . $lang['L_SAFEMODEDESC'] . '</p>';

		echo '<form action="install.php?language=' . $language . '&phase=10" method="post"><input type="hidden" name="connstr" value="' . $connstr . '">
		<table width="80%"><tr><td width="50%" valign="top"><table>';
		echo '<tr><td class="hd2" colspan="2">' . $lang['L_IDOMANUAL'] . '</td></tr>';
		echo '<tr><td colspan="2">' . $lang['L_DOFROM'] . '<br><div class="small">' . Realpfad('./') . '</div></td></tr>';
		echo '<tr><td><strong>work</strong></td><td>' . ( ( $iw[0] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td><strong>work/config</strong></td><td>' . ( ( $iw[1] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td><strong>work/log</strong></td><td>' . ( ( $iw[2] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td><strong>work/backup</strong></td><td>' . ( ( $iw[3] ) ? $img_ok : $img_failed ) . '</td></tr>';
		echo '<tr><td colspan="3" align="right"><input type="submit" name="dir_check" value=" ' . $lang['L_CHECK_DIRS'] . ' " class="Formbutton"></td></tr>';
		if ($iw[0] && $iw[1] && $iw[2] && $iw[3]) echo '<tr><td colspan="2">' . $lang['L_DIRS_CREATED'] . '<br><input class="Formbutton" type="Button" value=" ' . $lang['L_INSTALL_CONTINUE'] . ' " onclick="location.href=\'install.php?language=' . $language . '&phase=4&connstr=' . $connstr . '\'"></td></tr>';
		echo '</table></td><td width="50%" valign="top">';
		echo '<table><tr><td class="hd2" colspan="2">' . $lang['L_FTPMODE2'] . '</td></tr>';
		echo '<tr><td>FTP-Server</td><td><input type="text" name="install_ftp_server" value="' . $install_ftp_server . '"></td></tr>';
		echo '<tr><td>FTP-Port</td><td><input type="text" name="install_ftp_port" value="' . $install_ftp_port . '" size="4"></td></tr>';
		echo '<tr><td>FTP-User</td><td><input type="text" name="install_ftp_user_name" value="' . $install_ftp_user_name . '"></td></tr>';
		echo '<tr><td>FTP-' . $lang['L_PASS'] . '</td><td><input type="text" name="install_ftp_user_pass" value="' . $install_ftp_user_pass . '"></td></tr>';
		echo '<tr><td>' . $lang['L_INFO_SCRIPTDIR'] . '</td><td><input type="text" name="install_ftp_path" value="' . $install_ftp_path . '"></td></tr>';
		echo '<tr><td colspan="2" align="right">
		<input type="submit" name="ftp_connect" value="' . $lang['L_CONNECT'] . '" class="Formbutton"></td></tr></table></table></form>';
		if (isset($ftp_connect))
		{
			echo '<table><tr><td class="small">';
			$tftp=TesteFTP($install_ftp_server,$install_ftp_port,$install_ftp_user_name,$install_ftp_user_pass,$install_ftp_path);
			echo $tftp;
			echo '</td><td colspan="2" align="right">&nbsp;';
			if (substr($tftp,-9) == "</strong>")
			{
				echo '<form action="install.php?language=' . $language . '&phase=11" method="post">
				<input type="hidden" name="connstr" value="' . $connstr . '">';
				echo '<input type="hidden" name="install_ftp_server" value="' . $install_ftp_server . '">
				<input type="hidden" name="install_ftp_port" value="' . $install_ftp_port . '">
				<input type="hidden" name="install_ftp_user_name" value="' . $install_ftp_user_name . '">
				<input type="hidden" name="install_ftp_user_pass" value="' . $install_ftp_user_pass . '">
				<input type="hidden" name="install_ftp_path" value="' . $install_ftp_path . '">';
				echo '<input type="submit" name="submit" value=" ' . $lang['L_CREATEDIRS2'] . ' " class="Formbutton"></form>';
			}
			echo '</td></tr></table>';
		}
		//echo '</td></tr>';


		//echo '</table>';


		break;

	case 11: //FTP-Create Dirs
		echo '<h6>' . $lang['L_FTPMODE'] . '</h6>';
		if (CreateDirsFTP() == 1)
		{
			SetDefault(true);
			echo DirectoryWarnings();
			echo '<br>' . $lang['L_INSTALLFINISHED'];
		}
		break;
	case 100: //uninstall
		echo '<h6>' . $lang['L_UI1'] . '</h6>';
		echo '<h6>' . $lang['L_UI2'] . '</h6>';
		echo '<a href="install.php">' . $lang['L_UI3'] . '</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<a href="install.php?language=' . $language . '&phase=101">' . $lang['L_UI4'] . '</a>';
		break;
	case 101:
		echo '<h6>' . $lang['L_UI5'] . '</h6>';
		$paths=Array();
		$w=substr($config['paths']['work'],0,strlen($config['paths']['work']) - 1);
		if (is_dir($w)) $res=rec_rmdir($w);
		else $res=0;
		// wurde das Verzeichnis korrekt gelöscht
		if ($res == 0)
		{
			// das Verzeichnis wurde korrekt gelöscht
			echo '<p>' . $lang['L_UI6'] . '</p>';
			echo $lang['L_UI7'] . "<br>\"" . Realpfad("./") . "\"<br> " . $lang['L_MANUELL'] . ".<br><br>";
			echo '<a href="../">' . $lang['L_UI8'] . '</a>';

		}
		else
		{
			echo '<p class="Warnung">' . $lang['L_UI9'] . '"' . $paths[count($paths) - 1] . '"';

		}
		break;
}

?>

</div>
</body>
</html>


<?php

//eigene Funktionen
// rec_rmdir - loesche ein Verzeichnis rekursiv
// Rueckgabewerte:
//   0  - alles ok
//   -1 - kein Verzeichnis
//   -2 - Fehler beim Loeschen
//   -3 - Ein Eintrag eines Verzeichnisses war keine Datei und kein Verzeichnis und
//        kein Link
function rec_rmdir($path)
{
	global $paths;
	$paths[]=$path;
	// schau' nach, ob das ueberhaupt ein Verzeichnis ist
	if (!is_dir($path))
	{
		return -1;
	}
	// oeffne das Verzeichnis
	$dir=@opendir($path);
	// Fehler?
	if (!$dir)
	{
		return -2;
	}

	// gehe durch das Verzeichnis
	while ($entry=@readdir($dir))
	{
		// wenn der Eintrag das aktuelle Verzeichnis oder das Elternverzeichnis
		// ist, ignoriere es
		if ($entry == '.' || $entry == '..') continue;
		// wenn der Eintrag ein Verzeichnis ist, dann
		if (is_dir($path . '/' . $entry))
		{
			// rufe mich selbst auf
			$res=rec_rmdir($path . '/' . $entry);
			// wenn ein Fehler aufgetreten ist
			if ($res == -1)
			{ // dies duerfte gar nicht passieren
				@closedir($dir); // Verzeichnis schliessen
				return -2; // normalen Fehler melden
			}
			else if ($res == -2)
			{ // Fehler?
				@closedir($dir); // Verzeichnis schliessen
				return -2; // Fehler weitergeben
			}
			else if ($res == -3)
			{ // nicht unterstuetzer Dateityp?
				@closedir($dir); // Verzeichnis schliessen
				return -3; // Fehler weitergeben
			}
			else if ($res != 0)
			{ // das duerfe auch nicht passieren...
				@closedir($dir); // Verzeichnis schliessen
				return -2; // Fehler zurueck
			}
		}
		else if (is_file($path . '/' . $entry) || is_link($path . '/' . $entry))
		{
			// ansonsten loesche diese Datei / diesen Link
			$res=@unlink($path . '/' . $entry);
			// Fehler?
			if (!$res)
			{
				@closedir($dir); // Verzeichnis schliessen
				return -2; // melde ihn
			}
		}
		else
		{
			// ein nicht unterstuetzer Dateityp
			@closedir($dir); // Verzeichnis schliessen
			return -3; // tut mir schrecklich leid...
		}
	}

	// schliesse nun das Verzeichnis
	@closedir($dir);

	// versuche nun, das Verzeichnis zu loeschen
	$res=@rmdir($path);

	// gab's einen Fehler?
	if (!$res)
	{
		return -2; // melde ihn
	}

	// alles ok
	return 0;
}

function Rechte($file)
{
	clearstatcache();
	return @substr(decoct(fileperms($file)),-3);
}

function extractValue($s)
{
	$r=trim(substr($s,strpos($s,"=") + 1));
	$r=substr($r,0,strlen($r) - 1);
	if (substr($r,-1) == "'" || substr($r,-1) == '"') $r=substr($r,0,strlen($r) - 1);
	if (substr($r,0,1) == "'" || substr($r,0,1) == '"') $r=substr($r,1);
	return $r;
}

ob_end_flush();