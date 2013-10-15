<?php
if (!function_exists('get_page_parameter')) include ( './inc/functions_global.php' );

if (!function_exists('str_ireplace')) // borrowed from http://www.dscripts.net
{

	function str_ireplace($find, $replace, $string)
	{
		if (!is_array($find)) $find=array(

											$find
		);
		if (!is_array($replace))
		{
			if (!is_array($find)) $replace=array(

													$replace
			);
			else
			{
				// this will duplicate the string into an array the size of $find
				$c=count($find);
				$rString=$replace;
				unset($replace);
				for ($i=0; $i < $c; $i++)
				{
					$replace[$i]=$rString;
				}
			}
		}
		foreach ($find as $fKey=>$fItem)
		{
			$between=explode(strtolower($fItem),strtolower($string));
			$pos=0;
			foreach ($between as $bKey=>$bItem)
			{
				$between[$bKey]=substr($string,$pos,strlen($bItem));
				$pos+=strlen($bItem) + strlen($fItem);
			}
			$string=implode($replace[$fKey],$between);
		}
		return ( $string );
	}
}

if (!function_exists('stripos')) // borrowed from php.net comments
{

	function stripos($haystack, $needle)
	{
		return strpos($haystack,stristr($haystack,$needle));
	}
}

function Help($ToolTip, $Anker, $imgsize=12)
{/*
	global $config;
	if($Anker!=""){
	return '<a href="language/'.$config['language'].'/help.php#'.$Anker.'" title="'.$ToolTip.'"><img src="'.$config['files']['iconpath'].'help16.gif" width="'.$imgsize.'" height="'.$imgsize.'" hspace="'.(round($imgsize/4,0)).'" vspace="0" border="0" alt="Help"></a>';
	} else {
	return '<img src="'.$config['files']['iconpath'].'help16.gif" width="'.$imgsize.'" height="'.$imgsize.'" alt="Help" title="'.$ToolTip.'" border="0" hspace="'.(round($imgsize/4,0)).'" vspace="0" >';
	}
*/
}

function DeleteFilesM($dir, $pattern="*.*")
{
	$deleted=array();
	$pattern=str_replace(array(

								"\*",
								"\?"
	),array(

			".*",
			"."
	),preg_quote($pattern));
	if (substr($dir,-1) != "/") $dir.="/";
	if (is_dir($dir))
	{
		$d=opendir($dir);
		while ($file=readdir($d))
		{
			if (is_file($dir . $file) && ereg("^" . $pattern . "$",$file))
			{
				if (unlink($dir . $file)) $deleted[$file]=true;
				else $deleted[$file]=false;
			}
		}
		closedir($d);
		return $deleted;
	}
}

function SetDefault($load_default=false)
{
	global $config,$databases,$nl,$out,$lang,$preConfig;

	if ($load_default == true)
	{
		if (file_exists($config['files']['parameter']) && ( is_readable($config['files']['parameter']) )) include ( $config['files']['parameter'] ); // alte Config lesen
	}
	$restore_values=array();
	$restore_values['cron_dbindex']=isset($config['cron_dbindex']) ? $config['cron_dbindex'] : -3;
	$restore_values['cron_dbpraefix_array']=isset($config['cron_dbpraefix_array']) ? $config['cron_dbpraefix_array'] : '';
	if ($restore_values['cron_dbindex'] >= 0 && isset($databases['Name'][$config['cron_dbindex']])) // eine bestimmte Db gewaehlt?
	{
		// Ja, Namen merken, um spaeter den Index wieder herzustellen
		$restore_values['db_actual_cron']=$databases['Name'][$config['cron_dbindex']];
	}
	$restore_values['db_actual']=isset($databases['db_actual']) ? $databases['db_actual'] : '';

	$old_lang=isset($config['language']) && in_array($config['language'],$lang['languages']) ? $config['language'] : '';
	if ($load_default == true)
	{
		if (file_exists($config['files']['parameter'])) @unlink($config['files']['parameter']);
		include ( "./config.php" );
		if (is_array($preConfig))
		{
			foreach ($preConfig as $key=>$val)
			{
				$config[$key]=$val;
			}
		}

		if ($old_lang != '') $config['language']=$old_lang;
		include ( "./language/" . $config['language'] . "/lang.php" );
	}

	$oldVals = array();
	// Zuordnung nach Namen der Db zwischenspeichern, um Eingaben nicht zu verlieren
	if (isset($databases) && isset($databases['Name'])) {
    	foreach ($databases['Name'] as $k=>$v) {
    	    if (!isset($oldVals[$k])) $oldVals[$v] = array();
            $oldVals[$v]['praefix'] = $databases['praefix'][$k];
            $oldVals[$v]['command_before_dump'] = $databases['command_before_dump'][$k];
            $oldVals[$v]['command_after_dump'] = $databases['command_after_dump'][$k];
    	}
	}
	$oldDbArray = array();
	if (isset($databases['Name'])) {
	    $oldDbArray = $databases['Name'];
	}
	$databases['Name']=array();
	$found_dbs = array();
	//DB-Liste holen
	MSD_mysql_connect();

	$create_statement='CREATE TABLE `mysqldumper_test_abcxyvfgh` (`test` varchar(200) default NULL, `id` bigint(20) unsigned NOT NULL auto_increment,' . 'PRIMARY KEY  (`id`)) TYPE=MyISAM;';

	$res=mysql_query("SHOW DATABASES",$config['dbconnection']);
	WHILE ($row=mysql_fetch_row($res)) {
	    $found_dbs[] = $row[0];
	}
	$found_dbs = array_merge($oldDbArray, $found_dbs);
    $found_dbs = array_unique($found_dbs);
    sort($found_dbs);
    // now check each db
	$a=0;
	for ($i=0; $i<count($found_dbs);$i++)
	{
	    $found_db = $found_dbs[$i];
		// Testverbindung - Tabelle erstellen, nachschauen, ob es geklappt hat und dann wieder löschen
		$use=@mysql_select_db($found_db);
		if ($use)
		{
			if (isset($old_db) && $found_db == $old_db) $databases['db_selected_index']=$a;
			$databases['Name'][$a]=$found_db;
			$databases['praefix'][$a]="";
			$databases['command_before_dump'][$a]="";
			$databases['command_after_dump'][$a]="";
			if (isset($oldVals[$found_db])) {
                $databases['praefix'][$a]= $oldVals[$found_db]['praefix'];
                $databases['command_before_dump'][$a]=$oldVals[$found_db]['command_before_dump'];
                $databases['command_after_dump'][$a]=$oldVals[$found_db]['command_after_dump'];
			}
			    $out.=$lang['L_SAVING_DB_FORM'] . " " . $found_db . " " . $lang['L_ADDED'] . "$nl";
			$a++;
		}
	}
	if (!isset($databases['db_selected_index']))
	{
		$databases['db_selected_index']=0;
		$databases['db_actual']=$databases['Name'][0];
	}
	WriteParams(1,$restore_values);
	if ($load_default === true) WriteLog("default settings loaded.");

	return $out;
}

function WriteParams($as=0, $restore_values=false)
{
	// wenn $as=1 wird versucht den aktuellen Index der Datenbank nach dem Einlesen wieder zu ermitteln
	// auch wenn sich die Indexnummer durch Loeschaktionen geaendert hat
	global $config,$databases,$config_dontsave;
	$nl="\n";
	// alte Werte retten
	if ($as)
	{
		if (is_array($restore_values))
		{
			if ($restore_values['cron_dbindex'] < 0)
			{
				// Multidump oder "alle Datenbanken" war gewaehlt
				$config['cron_dbindex']=$restore_values['cron_dbindex'];
			}
			else
			{
				//den Index der konkreten Datenbank aus der alten Konfiguration ermitteln
				$db_names=array();
				$db_names=array_flip($databases['Name']);
				if (isset($db_names[$restore_values['db_actual']]))
				{
					// alte Db existiert noch -> Index uebernehmen
					$databases['db_actual']=$restore_values['db_actual'];
				}
				else
					$databases['db_actual']=$databases['Name'][0];

				//Cron-Index wiederfinden
				if (isset($db_names[$restore_values['cron_dbindex']]))
				{
					$config['cron_dbindex']=$db_names[$restore_values['cron_dbindex']];
				}
				else
				{
					// DB wurde zwischenzeitlich geloescht - sicherheitshalber alle DBs sichern
					$databases['cron_dbindex']=-3;
				}
			}
		}
	}
	FillMultiDBArrays();

	//Parameter zusammensetzen
	$config['multipart_groesse']=$config['multipartgroesse1'] * ( ( $config['multipartgroesse2'] == 1 ) ? 1024 : 1024 * 1024 );
	$param=$pars_all='<?php ' . $nl;
	if (!isset($config['email_maxsize'])) $config['email_maxsize']=$config['email_maxsize1'] * ( ( $config['email_maxsize2'] == 1 ) ? 1024 : 1024 * 1024 );
	if (!isset($config['cron_execution_path'])) $config['cron_execution_path']="msd_cron/";
	if ($as == 0) $config['paths']['root']=addslashes(Realpfad("./"));
	$config['files']['parameter']=$config['paths']['config'] . $config['config_file'] . '.php';
	$config['files']['iconpath']='./css/' . $config['theme'] . '/icons/';

	foreach ($config as $var=>$val)
	{
		if (!in_array($var,$config_dontsave))
		{
			if (is_array($val))
			{
				$pars_all.='$config[\'' . $var . '\']=array();' . $nl;
				foreach ($val as $var2=>$val2)
				{
					if ($config['magic_quotes_gpc'] == 1)
					{
						$val2=stripslashes($val2);
					}
					$pars_all.='$config[\'' . $var . '\'][' . ( ( is_int($var2) ) ? $var2 : "'" . $var2 . "'" ) . '] = \'' . my_addslashes($val2) . "';$nl";
				}
			}
			else
			{
				if ($config['magic_quotes_gpc'] == 1)
				{
					$val=stripslashes($val);
				}
				if (!in_array($var,$config_dontsave)) $pars_all.='$config[\'' . $var . '\'] = \'' . my_addslashes($val) . "';$nl";
			}
		}
	}
	foreach ($databases as $var=>$val)
	{
		if (is_array($val))
		{
			$pars_all.='$databases[\'' . $var . '\']=array();' . $nl;
			foreach ($val as $var2=>$val2)
			{
				if ($config['magic_quotes_gpc'] == 1 || $as == 1)
				{
					$pars_all.='$databases[\'' . $var . '\'][' . ( ( is_int($var2) ) ? $var2 : "'" . $var2 . "'" ) . '] = \'' . my_addslashes(stripslashes($val2)) . "';$nl";
				}
				else
				{
					$pars_all.='$databases[\'' . $var . '\'][' . ( ( is_int($var2) ) ? $var2 : "'" . $var2 . "'" ) . '] = \'' . my_addslashes($val2) . "';$nl";
				}
			}
		}
		else
		{
			if ($config['magic_quotes_gpc'] == 0 || $as == 1)
			{
				$pars_all.='$databases[\'' . $var . '\'] = \'' . addslashes($val) . "';$nl";
			}
			else
			{
				$pars_all.='$databases[\'' . $var . '\'] = \'' . $val . "';$nl";
			}
		}
	}

	$param.='?>';
	$pars_all.='?>';

	//Datei öffnen und schreiben
	$ret=true;
	$file=$config['paths']['config'] . $config['config_file'] . '.php';
	if ($fp=fopen($file,"wb"))
	{
		if (!fwrite($fp,$pars_all)) $ret=false;
		if (!fclose($fp)) $ret=false;
		@chmod($file,0777);
	}
	else
		$ret=false;

	$ret=WriteCronScript($restore_values);
	return $ret;
}

function escape_specialchars($text)
{
	$suchen=ARRAY(

				'@',
				'$',
				'\\\\',
				'"'
	);
	$ersetzen=ARRAY(

					'\@',
					'\$',
					'\\',
					'\"'
	);
	$text=str_replace($suchen,$ersetzen,$text);
	return $text;
}

// definiert einen SAtring, der ein Array nach Perlsyntax aufbaut
function my_implode($arr, $mode=0) // 0=String, 1=intval
{
	global $nl;
	if (!is_array($arr)) return false;
	foreach ($arr as $key=>$val)
	{
		if ($mode == 0) $arr[$key]=escape_specialchars($val);
		else $arr[$key]=intval($val);
	}
	if ($mode == 0) $ret='("' . implode('","',$arr) . '");' . $nl;
	else $ret='(' . implode(',',$arr) . ');' . $nl;
	return $ret;
}

function WriteCronScript($restore_values=false)
{
	global $nl,$config,$databases,$cron_db_array,$cron_dbpraefix_array,$cron_db_cbd_array,$cron_db_cad_array, $dontBackupDatabases;

	if (!isset($databases['db_selected_index'])) $databases['db_selected_index']=0;
	if (!isset($databases['praefix'][$databases['db_selected_index']])) $databases['praefix'][$databases['db_selected_index']]="";
	if (!isset($databases['db_actual_cronindex'])) $databases['db_actual_cronindex']=$databases['db_selected_index'];
	if (!isset($config['email_maxsize'])) $config['email_maxsize']=$config['email_maxsize1'] * ( ( $config['email_maxsize2'] == 1 ) ? 1024 : 1024 * 1024 );
	$cron_dbname=$databases['db_actual'];

	// -2 = Multidump configuration
	// -3 = all databases - nothing to do
	// get standard values for all databases
	$cron_db_array = $databases['Name'];
	$cron_dbpraefix_array=$databases['praefix'];
	$cron_command_before_dump=$databases['command_before_dump'];
	$cron_command_after_dump=$databases['command_after_dump'];
	if (!isset($config['cron_dbindex'])) $config['cron_dbindex']=-3;
    if (intval($config['cron_dbindex']) == -2)
    {
        // get selected dbs from multidump-settings
        $cron_db_array=$databases['multi'];
        $cron_dbpraefix_array=$databases['multi_praefix'];
        $cron_command_before_dump=$databases['multi_commandbeforedump'];
        $cron_command_after_dump=$databases['multi_commandafterdump'];
    }
    // we need to correct the index of the selected database after we cleaned
    // the db-array from information_schema and mysql if it points to a db-name
    if ($config['cron_dbindex'] >= 0) {
        $cronDbIndexDbName = $databases['Name'][$config['cron_dbindex']];
    } else {
        $cronDbIndex = $config['cron_dbindex'];
    }

    $newDbNames = $databases['Name'];
    //remove database we don't want to backup
    foreach ($databases['Name'] as $k=>$v) {
        if (in_array($v, $dontBackupDatabases)) {
            unset($cron_db_array[$k],
                   $cron_dbpraefix_array[$k],
                   $cron_command_before_dump[$k],
                   $cron_command_after_dump[$k],
                   $newDbNames[$k]);
        }
    }

    // find new index
    if ($config['cron_dbindex'] >= 0) {
        sort($newDbNames);
        $dbNames = array_flip($newDbNames);
        if (isset($dbNames[$cronDbIndexDbName])) {
            $cronDbIndex = $dbNames[$cronDbIndexDbName];
        } else {
            $cronDbIndex = 0;
        }
    }
	$r=str_replace("\\\\","/",$config['paths']['root']);
	$r=str_replace("@","\@",$r);
	$p1=$r . $config['paths']['backup'];
	$p2=$r . $config['files']['perllog'] . ( ( $config['logcompression'] == 1 ) ? '.gz' : '' );
	$p3=$r . $config['files']['perllogcomplete'] . ( ( $config['logcompression'] == 1 ) ? '.gz' : '' );

	// auf manchen Server wird statt 0 ein leerer String gespeichert -> fuehrt zu einem Syntax-Fehler
	// hier die entsprechenden Ja/Nein-Variablen sicherheitshalber in intvalues aendern
	$int_array=array(
					'dbport',
					'cron_compression',
					'cron_printout',
					'multi_part',
					'multipart_groesse',
					'email_maxsize',
					'auto_delete',
					'max_backup_files',
					'perlspeed',
					'optimize_tables_beforedump',
					'logcompression',
					'log_maxsize',
					'cron_completelog',
					'cron_use_sendmail',
					'cron_smtp_port'
	);
	foreach ($int_array as $i)
	{
		if (is_array($i))
		{
			foreach ($i as $key=>$val)
			{
				$int_array[$key]=intval($val);
			}
		}
		else
			$config[$i]=intval($config[$i]);
	}
	if ($config['dbport'] == 0) $config['dbport']=3306;

	$cronscript="<?php\n#Vars - written at " . date("Y-m-d") . $nl;
	$cronscript.='$dbhost="' . $config['dbhost'] . '";' . $nl;
	$cronscript.='$dbname="' . $cron_dbname . '";' . $nl;
	$cronscript.='$dbuser="' . escape_specialchars($config['dbuser']) . '";' . $nl;
	$cronscript.='$dbpass="' . escape_specialchars($config['dbpass']) . '";' . $nl;
	$cronscript.='$dbport=' . $config['dbport'] . ';' . $nl;
	$cronscript.='$dbsocket="' . escape_specialchars($config['dbsocket']) . '";' . $nl;
	$cronscript.='$compression=' . $config['cron_compression'] . ';' . $nl;
	$cronscript.='$backup_path="' . $p1 . '";' . $nl;
	$cronscript.='$logdatei="' . $p2 . '";' . $nl;
	$cronscript.='$completelogdatei="' . $p3 . '";' . $nl;
	$cronscript.='$sendmail_call="' . escape_specialchars($config['cron_sendmail']) . '";' . $nl;
	$cronscript.='$nl="\n";' . $nl;
	$cronscript.='$cron_dbindex=' . $cronDbIndex . ';' . $nl;
	$cronscript.='$cron_printout=' . $config['cron_printout'] . ';' . $nl;
	$cronscript.='$cronmail=' . $config['send_mail'] . ';' . $nl;
	$cronscript.='$cronmail_dump=' . $config['send_mail_dump'] . ';' . $nl;
	$cronscript.='$cronmailto="' . escape_specialchars($config['email_recipient']) . '";' . $nl;
	$cronscript.='$cronmailto_cc="' . escape_specialchars($config['email_recipient_cc']) . '";' . $nl;
	$cronscript.='$cronmailfrom="' . escape_specialchars($config['email_sender']) . '";' . $nl;
	$cronscript.='$cron_use_sendmail=' . $config['cron_use_sendmail'] . ';' . $nl;
	$cronscript.='$cron_smtp="' . escape_specialchars($config['cron_smtp']) . '";' . $nl;
	$cronscript.='$cron_smtp_port="' . $config['cron_smtp_port'] . '";' . $nl;

	$cronscript.='@cron_db_array=' . my_implode($cron_db_array);
	$cronscript.='@cron_dbpraefix_array=' . my_implode($cron_dbpraefix_array);
	$cronscript.='@cron_command_before_dump=' . my_implode($cron_command_before_dump);
	$cronscript.='@cron_command_after_dump=' . my_implode($cron_command_after_dump);

	$cronscript.='@ftp_server=' . my_implode($config['ftp_server']);
	$cronscript.='@ftp_port=' . my_implode($config['ftp_port'],1);
	$cronscript.='@ftp_mode=' . my_implode($config['ftp_mode'],1);
	$cronscript.='@ftp_user=' . my_implode($config['ftp_user']);
	$cronscript.='@ftp_pass=' . my_implode($config['ftp_pass']);
	$cronscript.='@ftp_dir=' . my_implode($config['ftp_dir']);
	$cronscript.='@ftp_timeout=' . my_implode($config['ftp_timeout'],1);
	$cronscript.='@ftp_useSSL=' . my_implode($config['ftp_useSSL'],1);
	$cronscript.='@ftp_transfer=' . my_implode($config['ftp_transfer'],1);
	$cronscript.='$mp=' . $config['multi_part'] . ';' . $nl;
	$cronscript.='$multipart_groesse=' . $config['multipart_groesse'] . ';' . $nl;
	$cronscript.='$email_maxsize=' . $config['email_maxsize'] . ';' . $nl;
	$cronscript.='$auto_delete=' . $config['auto_delete'] . ';' . $nl;
	$cronscript.='$max_backup_files=' . $config['max_backup_files'] . ';' . $nl;
	$cronscript.='$perlspeed=' . $config['perlspeed'] . ';' . $nl;
	$cronscript.='$optimize_tables_beforedump=' . $config['optimize_tables_beforedump'] . ';' . $nl;
	$cronscript.='$logcompression=' . $config['logcompression'] . ';' . $nl;
	$cronscript.='$log_maxsize=' . $config['log_maxsize'] . ';' . $nl;
	$cronscript.='$complete_log=' . $config['cron_completelog'] . ';' . $nl;
	$cronscript.='$my_comment="' . escape_specialchars(stripslashes($config['cron_comment'])) . '";' . $nl;
	$cronscript.="?>";

	// Save config
	$ret=true;
	$sfile=$config['paths']['config'] . $config['config_file'] . '.conf.php';
	if (file_exists($sfile)) @unlink($sfile);

	if ($fp=fopen($sfile,"wb"))
	{
		if (!fwrite($fp,$cronscript)) $ret=false;
		if (!fclose($fp)) $ret=false;
		@chmod("$sfile",0777);
	}
	else
		$ret=false;

	// if standard config was deleted -> restore it with the actual values
	if (!file_exists($config['paths']['config'] . "mysqldumper.conf.php"))
	{
		$sfile=$config['paths']['config'] . 'mysqldumper.conf.php';
		if ($fp=fopen($sfile,"wb"))
		{
			if (!fwrite($fp,$cronscript)) $ret=false;
			if (!fclose($fp)) $ret=false;
			@chmod("$sfile",0777);
		}
		else
			$ret=false;
	}
	return $ret;

}

function LogFileInfo($logcompression)
{
	global $config;

	$l=Array();
	$sum=$s=$l['log_size']=$l['perllog_size']=$l['perllogcomplete_size']=$l['errorlog_size']=$l['log_totalsize']=0;
	if ($logcompression == 1)
	{
		$l['log']=$config['files']['log'] . ".gz";
		$l['perllog']=$config['files']['perllog'] . ".gz";
		$l['perllogcomplete']=$config['files']['perllogcomplete'] . ".gz";
		$l['errorlog']=$config['paths']['log'] . "error.log.gz";
	}
	else
	{
		$l['log']=$config['files']['log'];
		$l['perllog']=$config['files']['perllog'];
		$l['perllogcomplete']=$config['files']['perllogcomplete'];
		$l['errorlog']=$config['paths']['log'] . "error.log";
	}
	$l['log_size']+=@filesize($l['log']);
	$sum+=$l['log_size'];
	$l['perllog_size']+=@filesize($l['perllog']);
	$sum+=$l['perllog_size'];
	$l['perllogcomplete_size']+=@filesize($l['perllogcomplete']);
	$sum+=$l['perllogcomplete_size'];
	$l['errorlog_size']+=@filesize($l['errorlog']);
	$sum+=$l['errorlog_size'];
	$l['log_totalsize']+=$sum;

	return $l;
}

function DeleteLog()
{
	global $config;
	//Datei öffnen und schreiben
	$log=date('d.m.Y H:i:s') . " Log created.\n";
	if (file_exists($config['files']['log'] . '.gz')) @unlink($config['files']['log'] . '.gz');
	if (file_exists($config['files']['log'] . '.gz')) @unlink($config['files']['log']);
	if ($config['logcompression'] == 1)
	{
		$fp=@gzopen($config['files']['log'] . '.gz',"wb");
		@gzwrite($fp,$log);
		@gzclose($fp);
		@chmod($config['files']['log'] . '.gz',0777);
	}
	else
	{
		$fp=@fopen($config['files']['log'],"wb");
		@fwrite($fp,$log);
		@fclose($fp);
		@chmod($config['files']['log'],0777);
	}
}

function CreateDirsFTP()
{

	global $config,$lang,$install_ftp_server,$install_ftp_port,$install_ftp_user_name,$install_ftp_user_pass,$install_ftp_path;
	// Herstellen der Basis-Verbindung
	echo '<hr>' . $lang['L_CONNECT_TO'] . ' `' . $install_ftp_server . '` Port ' . $install_ftp_port . ' ...<br>';
	$conn_id=ftp_connect($install_ftp_server);
	// Einloggen mit Benutzername und Kennwort
	$login_result=ftp_login($conn_id,$install_ftp_user_name,$install_ftp_user_pass);
	// Verbindung überprüfen
	if (( !$conn_id ) || ( !$login_result ))
	{
		echo $lang['L_FTP_NOTCONNECTED'];
		echo $lang['L_CONNWITH'] . " $tinstall_ftp_server " . $lang['L_ASUSER'] . " $install_ftp_user_name " . $lang['L_NOTPOSSIBLE'];
		return 0;
	}
	else
	{
		if ($config['ftp_mode'] == 1) ftp_pasv($conn_id,true);
		//Wechsel in betroffenes Verzeichnis
		echo $lang['L_CHANGEDIR'] . ' `' . $install_ftp_path . '` ...<br>';
		ftp_chdir($conn_id,$install_ftp_path);
		// Erstellen der Verzeichnisse
		echo $lang['L_DIRCR1'] . ' ...<br>';
		ftp_mkdir($conn_id,"work");
		ftp_site($conn_id,"CHMOD 0777 work");
		echo $lang['L_CHANGEDIR'] . ' `work` ...<br>';
		ftp_chdir($conn_id,"work");
		echo $lang['L_INDIR'] . ' `' . ftp_pwd($conn_id) . '`<br>';
		echo $lang['L_DIRCR5'] . ' ...<br>';
		ftp_mkdir($conn_id,"config");
		ftp_site($conn_id,"CHMOD 0777 config");
		echo $lang['L_DIRCR2'] . ' ...<br>';
		ftp_mkdir($conn_id,"backup");
		ftp_site($conn_id,"CHMOD 0777 backup");
		echo $lang['L_DIRCR4'] . ' ...<br>';
		ftp_mkdir($conn_id,"log");
		ftp_site($conn_id,"CHMOD 0777 log");

		// Schließen des FTP-Streams
		ftp_quit($conn_id);
		return 1;
	}
}

function ftp_mkdirs($config, $dirname)
{
	$dir=split("/",$dirname);
	for ($i=0; $i < count($dir) - 1; $i++)
	{
		$path.=$dir[$i] . "/";
		@ftp_mkdir($config['dbconnection'],$path);
	}
	if (@ftp_mkdir($config['dbconnection'],$dirname)) return 1;
}

function IsWritable($dir)
{
	$testfile=$dir . "/.writetest";
	if ($writable=@fopen($testfile,'w'))
	{
		@fclose($writable);
		@unlink($testfile);
	}
	return $writable;
}

function SearchDatabases($printout, $db='')
{
	global $databases,$config,$lang;

	if (!isset($config['dbconnection'])) MSD_mysql_connect();
	$db_list=array();
	if ($db > '') {
	    $db_list[]=$db; // DB wurde manuell angegeben
	}
	// Datenbanken automatisch erkennen
	$show_dbs=mysql_query("SHOW DATABASES",$config['dbconnection']);
	if (!$show_dbs === false)
	{
		WHILE ($row=mysql_fetch_row($show_dbs))
		{
			if (trim($row[0]) > '') $db_list[]=$row[0];
		}
	}
    $db_list = array_unique($db_list);
    sort($db_list);
	if (sizeof($db_list) > 0)
	{
		$databases['db_selected_index']=0;
		for ($i=0; $i < sizeof($db_list); $i++)
		{
			// Test-Select um zu sehen, ob Berechtigungen existieren
			if (!@mysql_query("SHOW TABLES FROM `" . $db_list[$i] . "`",$config['dbconnection']) === false)
			{
				$databases['Name'][$i]=$db_list[$i];
				$databases['praefix'][$i]='';
				$databases['command_before_dump'][$i]='';
				$databases['command_after_dump'][$i]='';
				if ($printout == 1) echo $lang['L_FOUND_DB'] . ' `' . $db_list[$i] . '`<br />';
			} else {
    			if ($printout == 1) echo '<span class="error">'.sprintf($lang['L_DB_MANUAL_ERROR'], $db_list[$i]) . '</span><br />';
			}
		}
	}
	if (isset($databases['Name'][0])) $databases['db_actual']=$databases['Name'][0];
}

// removes tags from inputs recursivly
function my_strip_tags($value)
{
	global $dont_strip;
	if (is_array($value))
	{
		foreach ($value as $key=>$val)
		{
			if (!in_array($key,$dont_strip)) $ret[$key]=my_strip_tags($val);
			else $ret[$key]=$val;
		}
	}
	else
		$ret=trim(strip_tags($value));
	return $ret;
}

/**
 * Add a slashes only before '
 *
 * Used for escaping strings in JS-alerts and config-files
 *
 * @param $string
 * @return string
 */
function my_addslashes($string)
{
	return str_replace("'","\'",$string);
}

/**
 * Replaces quotes for outputting value in HTML-attributes
 *
 * Replaces quotes for outputing value in HTML-attributes without breaking HTML
 *
 * @param string $value value to output
 * @return string
 */
function my_quotes($value)
{
	return str_replace('"','&quot;',$value);
}


// prepares a string for executing it as query
function db_escape($string)
{
	global $config;
	if (function_exists('mysql_real_escape_string'))
	{
		$string=mysql_real_escape_string($string,$config['dbconnection']);
	}
	else if (function_exists('mysql_escape_string'))
	{
		$string=mysql_escape_string($string,$config['dbconnection']);
	}
	else $string=addslashes($string);
	return $string;
}

?>