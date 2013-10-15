<?php
include ('./inc/functions_global.php');

//Buffer fuer Multipart-Filesizepruefung
$buffer=10*1024;

function new_file($last_groesse=0)
{
	global $dump,$databases,$config,$out,$lang,$nl,$mysql_commentstring;
	
	// Dateiname aus Datum und Uhrzeit bilden
	if ($dump['part']-$dump['part_offset']==1) $dump['filename_stamp']=date("Y_m_d_H_i",time());
	if ($config['multi_part']==1)
	{
		$dateiname=$databases['Name'][$dump['dbindex']].'_'.$dump['filename_stamp'].'_part_'.($dump['part']-$dump['part_offset']);
	}
	else
		$dateiname=$databases['Name'][$dump['dbindex']].'_'.date("Y_m_d_H_i",time());
	$endung=($config['compression']) ? '.sql.gz':'.sql';
	$dump['backupdatei']=$dateiname.$endung;
	
	if (file_exists($config['paths']['backup'].$dump['backupdatei'])) unlink($config['paths']['backup'].$dump['backupdatei']);
	$cur_time=date("Y-m-d H:i");
	$statuszeile=GetStatusLine().$nl.$mysql_commentstring.' Dump by MySQLDumper '.MSD_VERSION.' ('.$config['homepage'].')'.$nl;
	$statuszeile.='/*!40101 SET NAMES \''.$dump['dump_encoding'].'\' */;'.$nl;
	$statuszeile.='SET FOREIGN_KEY_CHECKS=0;'.$nl;
	
	if ($dump['part']-$dump['part_offset']==1)
	{
		if ($config['multi_part']==0)
		{
			if ($config['multi_dump']==1 && $dump['dbindex']==0) WriteLog('starting Multidump with '.count($databases['multi']).' Datenbases.');
			WriteLog('Start Dump \''.$dump['backupdatei'].'\'');
		}
		else
			WriteLog('Start Multipart-Dump \''.$dateiname.'\'');
		
		$out.='<strong>'.$lang['L_STARTDUMP'].' `'.$databases['Name'][$dump['dbindex']].'`</strong>'.(($databases['praefix'][$dump['dbindex']]!="") ? ' ('.$lang['L_WITHPRAEFIX'].' <span style="color:blue">'.$databases['praefix'][$dump['dbindex']].'</span>)':'').'...   ';
		if ($dump['part']==1)
		{
			$dump['table_offset']=0;
			$dump['countdata']=0;
		}
		// Seitenerstaufruf -> Backupdatei anlegen
		$dump['data']=$statuszeile.$mysql_commentstring.' Dump created: '.$cur_time;
	}
	else
	{
		if ($config['multi_part']!=0)
		{
			WriteLog('Continue Multipart-Dump with File '.($dump['part']-$dump['part_offset']).' (last file was '.$last_groesse.' Bytes)');
			$dump['data']=$statuszeile.$mysql_commentstring.' This is part '.($dump['part']-$dump['part_offset']).' of the backup.'.$nl.$nl.$dump['data'];
		
		}
	}
	WriteToDumpFile();
	$dump['part']++;
}

function GetStatusLine($kind="php")
{
	/*AUFBAU der Statuszeile:
		-- Status:tabellenzahl:datensätze:Multipart:Datenbankname:script:scriptversion:Kommentar:MySQLVersion:Backupflags:SQLBefore:SQLAfter:Charset:CharsetEXTINFO
		Aufbau Backupflags (1 Zeichen pro Flag, 0 oder 1, 2=unbekannt)
		(complete inserts)(extended inserts)(ignore inserts)(delayed inserts)(downgrade)(lock tables)(optimize tables)
	*/
	
	global $databases,$config,$lang,$dump,$mysql_commentstring;
	
	$t_array=explode("|",$databases['db_actual_tableselected']);
	$t=0;
	$r=0;
	$t_zeile="$mysql_commentstring\n$mysql_commentstring TABLE-INFO\r\n";
	MSD_mysql_connect();
	$res=mysql_query("SHOW TABLE STATUS FROM `".$databases['Name'][$dump['dbindex']]."`");
	$numrows=intval(@mysql_num_rows($res));
	for($i=0;$i<$numrows;$i++)
	{
		$erg=mysql_fetch_array($res);
		// Get nr of records -> need to do it this way because of incorrect returns when using InnoDBs
		$sql_2="SELECT count(*) as `count_records` FROM `".$databases['Name'][$dump['dbindex']]."`.`".$erg['Name']."`";
		$res2=@mysql_query($sql_2);
		if ($res2===false)
		{
			// error reading table definition
			$read_create_error=sprintf($lang['L_FATAL_ERROR_DUMP'],$databases['Name'][$dump['dbindex']],$erg['Name']).': '.mysql_error($config['dbconnection']);
			Errorlog("DUMP",$databases['Name'][$dump['dbindex']],'',$read_create_error,0);
			WriteLog($read_create_error);
			if ($config['stop_with_error']>0)
			{
				die($read_create_error);
			}
			$dump['errors']++;
			//$i++; // skip corrupted table
		}
		else
		{
			$row2=mysql_fetch_array($res2);
			$erg['Rows']=$row2['count_records'];
			
			if (($databases['db_actual_tableselected']==''||($databases['db_actual_tableselected']!=''&&(in_array($erg[0],$t_array))))&&(substr($erg[0],0,strlen($databases['praefix'][$dump['dbindex']]))==$databases['praefix'][$dump['dbindex']]))
			{
				$t++;
				$r+=$erg['Rows'];
				if (isset($erg['Type'])) $erg['Engine']=$erg['Type'];
				$t_zeile.="$mysql_commentstring TABLE|".$erg['Name'].'|'.$erg['Rows'].'|'.($erg['Data_length']+$erg['Index_length']).'|'.$erg['Update_time'].'|'.$erg['Engine']."\n";
			}
		}
	}
	//$dump['totalrecords']=$r;
	$flags=1;
	
	$mp=($config['multi_part']==1) ? $mp="MP_".($dump['part']-$dump['part_offset']):'MP_0';
	$statusline="$mysql_commentstring Status:$t:$r:$mp:".$databases['Name'][$dump['dbindex']].":$kind:".MSD_VERSION.":".$dump['kommentar'].":";
	$statusline.=MSD_MYSQL_VERSION.":$flags:::".$dump['dump_encoding'].":EXTINFO\n".$t_zeile."$mysql_commentstring"." EOF TABLE-INFO\n$mysql_commentstring";
	return $statusline;
}

// Liest die Eigenschaften der Tabelle aus der DB und baut die CREATE-Anweisung zusammen
function get_def($db,$table,$withdata=1)
{
	global $config,$nl,$mysql_commentstring,$dump;
	
	$def="\n\n$mysql_commentstring\n$mysql_commentstring Create Table `$table`\n$mysql_commentstring\n\n";
	if ($dump['table_types'][getDBIndex($db,$table)]=='VIEW')
	{
		$def.="DROP VIEW IF EXISTS `$table`;\n";
		$withdata=0;
	}
	else
		$def.="DROP TABLE IF EXISTS `$table`;\n";
	mysql_select_db($db);
	$result=mysql_query('SHOW CREATE TABLE `'.$table.'`',$config['dbconnection']);
	$row=@mysql_fetch_row($result);
	if ($row===false) return false;
	$def.=$row[1].';'."\n\n";
	if ($withdata==1)
	{
		$def.="$mysql_commentstring\n$mysql_commentstring Data for Table `$table`\n$mysql_commentstring\n\n";
		$def.="/*!40000 ALTER TABLE `$table` DISABLE KEYS */;".$nl;
	}
	return $def;
}

// Liest die Daten aus der DB aus und baut die INSERT-Anweisung zusammen
function get_content($db,$table)
{
	global $config,$nl,$dump,$buffer;
	
	$content='';
	$complete=Fieldlist($db,$table).' ';
	
	$table_ready=0;
	$query='SELECT * FROM `'.$table.'` LIMIT '.$dump['zeilen_offset'].','.($dump['restzeilen']+1);
	mysql_select_db($db);
	$result=mysql_query($query,$config['dbconnection']);
	$ergebnisse=@mysql_num_rows($result);
	if ($ergebnisse!==false)
	{
		$num_felder=mysql_num_fields($result);
		$first=1;
		
		if ($ergebnisse>$dump['restzeilen'])
		{
			$dump['zeilen_offset']+=$dump['restzeilen'];
			$ergebnisse--;
			$dump['restzeilen']=0;
		}
		else
		{
			$dump['table_offset']++;
			$dump['zeilen_offset']=0;
			$dump['restzeilen']=$dump['restzeilen']-$ergebnisse;
			$table_ready=1;
		}
		$ax=0;
		for($x=0;$x<$ergebnisse;$x++)
		{
			$row=mysql_fetch_row($result);
			$ax++;
			
			$insert='INSERT INTO `'.$table.'` '.$complete.'VALUES (';
			
			for($j=0;$j<$num_felder;$j++)
			{
				if (!isset($row[$j])) $insert.='NULL,';
				else 
					if ($row[$j]!='') $insert.='\''.mysql_escape_string($row[$j]).'\',';
					else
						$insert.='\'\',';
			}
			$insert=substr($insert,0,-1).');'.$nl;
			$dump['data'].=$insert;
			$dump['countdata']++;
			if (strlen($dump['data'])>$config['memory_limit']||($config['multi_part']==1&&strlen($dump['data'])+$buffer>$config['multipart_groesse']))
			{
				WriteToDumpFile();
			}
		}
		if ($table_ready==1&&$dump['table_types'][getDBIndex($db,$table)]!='VIEW') $dump['data'].="/*!40000 ALTER TABLE `$table` ENABLE KEYS */;\n";
	}
	else
	{
		// table corrupt -> skip it
		$dump['table_offset']++;
		$dump['zeilen_offset']=0;
		$dump['restzeilen']=$dump['restzeilen']-$ergebnisse;
		$dump['data'].="/*!40000 ALTER TABLE `$table` ENABLE KEYS */;\n";
		if (strlen($dump['data'])>$config['memory_limit']||($config['multi_part']==1&&strlen($dump['data'])+$buffer>$config['multipart_groesse']))
		{
			WriteToDumpFile();
		}
	}
	@mysql_free_result($result);
}

function WriteToDumpFile()
{
	global $config,$dump,$buffer;
	$dump['filesize']=0;
	
	$df=$config['paths']['backup'].$dump['backupdatei'];
	
	if ($config['compression']==1)
	{
		if ($dump['data']!='')
		{
			$fp=gzopen($df,'ab');
			gzwrite($fp,$dump['data']);
			gzclose($fp);
		}
	}
	else
	{
		if ($dump['data']!='')
		{
			$fp=fopen($df,'ab');
			fwrite($fp,$dump['data']);
			fclose($fp);
		}
	}
	$dump['data']='';
	if (!isset($dump['fileoperations'])) $dump['fileoperations']=0;
	$dump['fileoperations']++;
	
	if ($config['multi_part']==1) clearstatcache();
	$dump['filesize']=filesize($df);
	if ($config['multi_part']==1&&$dump['filesize']+$buffer>$config['multipart_groesse'])
	{
		@chmod($df,0777);
		new_file($dump['filesize']); // Wenn maximale Dateigroesse erreicht -> neues File starten
	}
}

function ExecuteCommand($when)
{
	global $config,$databases,$dump,$out,$lang;
	$lf='<br>';
	if (!isset($dump['dbindex'])) return;
	if ($when=='b')
	{ // before dump
		$cd=$databases['command_before_dump'][$dump['dbindex']];
		//WriteLog('DbIndex: '.$dump['dbindex'].' Before: '.$databases['command_before_dump'][$dump['dbindex']]);
	}
	else
	{
		
		$cd=$databases['command_after_dump'][$dump['dbindex']];
		//WriteLog('DbIndex: '.$dump['dbindex'].' After: '.$databases['command_after_dump'][$dump['dbindex']]);
	}
	
	if ($cd!='')
	{
		//jetzt ausführen
		if (substr(strtolower($cd),0,7)!='system:')
		{
			$cad=array();
			@mysql_select_db($databases['Name'][$dump['dbindex']]);
			if (strpos($cd,';'))
			{
				$cad=explode(';',$cd);
			}
			else
				$cad[0]=$cd;
			
			for($i=0;$i<sizeof($cad);$i++)
			{
				if (trim($cad[$i])>'')
				{
					$result=@mysql_query($cad[$i],$config['dbconnection']);
					
					if ($result===false)
					{
						WriteLog("Error executing Query '$cad[$i]'! MySQL returns: ".trim(mysql_error()));
						ErrorLog("Error executing Query '$cad[$i]'!",$databases['Name'][$dump['dbindex']],$cad[$i],mysql_error(),0);
						$dump['errors']++;
						$out.='<span class="error">Error executing Query '.$cad[$i].'</span>'.$lf;
					}
					else
					{
						WriteLog("Successfully executed Query: '$cad[$i]'");
						$out.='<span class="success">Successfully executed Query: \''.$cad[$i].'\'</span>'.$lf;
					}
				}
			}
		}
		elseif (substr(strtolower($cd),0,7)=="system:")
		{
			$command=substr($cd,7);
			$result=@system($command,$returnval);
			if (!$result)
			{
				WriteLog("Error while executing System Command '$command'");
				$dump['errors']++;
				$out.=$lf.'<span class="error">ERROR executing System Command \''.$ommand.'\'</span><br>';
			}
			else
			{
				WriteLog("Successfully executed System Command '$command'. [$returnval]");
				$out.=$lf.'<span class="success">Successfully executed System Command \''.$ommand.'.</span><br>';
			}
		}
	}

}

function DoEmail()
{
	global $config,$dump,$databases,$email,$lang,$out,$REMOTE_ADDR;
	
	$header="";
	if ($config['cron_use_sendmail']==1)
	{
		//sendmail
		if (ini_get("sendmail_path")!=$config['cron_sendmail']) @ini_set("SMTP",$config['cron_sendmail']);
		if (ini_get("sendmail_from")!=$config['email_sender']) @ini_set("SMTP",$config['email_sender']);
	}
	else
	{
		//SMTP
	}
	if (ini_get("SMTP")!=$config['cron_smtp']) @ini_set("SMTP",$config['cron_smtp']);
	if (ini_get("smtp_port")!=25) @ini_set("smtp_port",25);
	
	if ($config['multi_part']==0)
	{
		$file=$dump['backupdatei'];
		$file_name=(strpos("/",$file)) ? substr($file,strrpos("/",$file)):$file;
		$file_type=filetype($config['paths']['backup'].$file);
		$file_size=filesize($config['paths']['backup'].$file);
		if (($config['email_maxsize']>0&&$file_size>$config['email_maxsize'])||$config['send_mail_dump']==0)
		{
			//anhang zu gross
			$subject="Backup '".$databases['Name'][$dump['dbindex']]."' - ".date("d\.m\.Y H:i",time());
			$header.="FROM:".$config['email_sender']."\n";
			if (isset($config['email_recipient_cc'])&&trim($config['email_recipient_cc'])>'') $header.="Cc:     ".$config['email_recipient_cc']."\r\n";
			$header.="MIME-version: 1.0\n";
			$header.="X-Mailer: PHP/".phpversion()."\n";
			$header.="X-Sender-IP: $REMOTE_ADDR\n";
			$header.="Content-Type: text/html; charset=utf-8\n";
			if ($config['send_mail_dump']!=0)
			{
				$msg_body=sprintf(addslashes($lang['L_EMAILBODY_TOOBIG']),byte_output($config['email_maxsize']),$databases['Name'][$dump['dbindex']],"$file (".byte_output(filesize($config['paths']['backup'].$file)).")<br>");
			}
			else
			{
				$msg_body=sprintf(addslashes($lang['L_EMAILBODY_NOATTACH']),$databases['Name'][$dump['dbindex']],"$file (".byte_output(filesize($config['paths']['backup'].$file)).")");
			}
			include_once ('./inc/functions.php');
			$msg_body.='<a href="'.getServerProtocol().$_SERVER['HTTP_HOST'].substr($_SERVER["PHP_SELF"],0,strrpos($_SERVER["PHP_SELF"],"/")).'/'.$config['paths']['backup'].$file.'">'.$file.'</a>';
			$email_log="Email sent to '".$config['email_recipient']."'";
			$email_out=$lang['L_EMAIL_WAS_SEND']."`".$config['email_recipient']."`<br>";
		}
		else
		{
			//alles ok, anhang generieren
			$msg_body=sprintf(addslashes($lang['L_EMAILBODY_ATTACH']),$databases['Name'][$dump['dbindex']],"$file (".byte_output(filesize($config['paths']['backup'].$file)).")");
			$subject="Backup '".$databases['Name'][$dump['dbindex']]."' - ".date("d\.m\.Y",time());
			$fp=fopen($config['paths']['backup'].$file,"r");
			$contents=fread($fp,$file_size);
			$encoded_file=chunk_split(base64_encode($contents));
			fclose($fp);
			$header.="FROM:".$config['email_sender']."\n";
			if (isset($config['email_recipient_cc'])&&trim($config['email_recipient_cc'])>'') $header.="Cc:     ".$config['email_recipient_cc']."\r\n";
			$header.="MIME-version: 1.0\n";
			$header.="Content-type: multipart/mixed; ";
			$header.="boundary=\"Message-Boundary\"\n";
			$header.="Content-transfer-encoding: 7BIT\n";
			$header.="X-attachments: $file_name";
			$body_top="--Message-Boundary\n";
			$body_top.="Content-type: text/html; charset=utf-8\n";
			$body_top.="Content-transfer-encoding: 7BIT\n";
			$body_top.="Content-description: Mail message body\n\n";
			$msg_body=$body_top.$msg_body;
			$msg_body.="\n\n--Message-Boundary\n";
			$msg_body.="Content-type: $file_type; name=\"$file\"\n";
			$msg_body.="Content-Transfer-Encoding: BASE64\n";
			$msg_body.="Content-disposition: attachment; filename=\"$file\"\n\n";
			$msg_body.="$encoded_file\n";
			$msg_body.="--Message-Boundary--\n";
			$email_log="Email was sent to '".$config['email_recipient']."' with '".$dump['backupdatei']."'.";
			$email_out=$lang['L_EMAIL_WAS_SEND']."`".$config['email_recipient']."`".$lang['L_WITH']."`".$dump['backupdatei']."`.<br>";
		}
	}
	else
	{
		//Multipart
		$mp_sub="Backup '".$databases['Name'][$dump['dbindex']]."' - ".date("d\.m\.Y",time());
		$subject=$mp_sub;
		$header.="FROM:".$config['email_sender']."\n";
		if (isset($config['email_recipient_cc'])&&trim($config['email_recipient_cc'])>'') $header.="Cc:     ".$config['email_recipient_cc']."\r\n";
		$header.="MIME-version: 1.0\n";
		$header.="X-Mailer: PHP/".phpversion()."\n";
		$header.="X-Sender-IP: $REMOTE_ADDR\n";
		$header.="Content-Type: text/html; charset=utf-8";
		$dateistamm=substr($dump['backupdatei'],0,strrpos($dump['backupdatei'],"part_"))."part_";
		$dateiendung=($config['compression']==1) ? ".sql.gz":".sql";
		$mpdatei=Array();
		$mpfiles="";
		for($i=1;$i<($dump['part']-$dump['part_offset']);$i++)
		{
			$mpdatei[$i-1]=$dateistamm.$i.$dateiendung;
			$sz=byte_output(@filesize($config['paths']['backup'].$mpdatei[$i-1]));
			$mpfiles.=$mpdatei[$i-1]." (".$sz.")<br>";
		}
		$msg_body=($config['send_mail_dump']==1) ? sprintf(addslashes($lang['L_EMAILBODY_MP_ATTACH']),$databases['Name'][$dump['dbindex']],$mpfiles):sprintf(addslashes($lang['L_EMAILBODY_MP_NOATTACH']),$databases['Name'][$dump['dbindex']],$mpfiles);
		$email_log="Email was sent to '".$config['email_recipient']."'";
		$email_out=$lang['L_EMAIL_WAS_SEND']."`".$config['email_recipient']."`<br>";
	}
	if (@mail($config['email_recipient'],stripslashes($subject),$msg_body,$header))
	{
		$out.='<span class="success">'.$email_out.'</span>';
		WriteLog("$email_log");
	}
	else
	{
		$out.='<span class="error">'.$lang['L_MAILERROR'].'</span><br>';
		WriteLog("Email to '".$config['email_recipient']."' failed !");
		ErrorLog("Email ",$databases['Name'][$dump['dbindex']],'Subject: '.stripslashes($subject),$lang['L_MAILERROR']);
		$dump['errors']++;
	}
	
	if (isset($mpdatei)&&$config['send_mail_dump']==1)
	{ // && ($config['email_maxsize']==0 || ($config['email_maxsize']>0 && $config['multipartgroesse2']<=$config['email_maxsize']))) {
		for($i=0;$i<count($mpdatei);$i++)
		{
			$file_name=$mpdatei[$i];
			$file_type=filetype($config['paths']['backup'].$mpdatei[$i]);
			$file_size=filesize($config['paths']['backup'].$mpdatei[$i]);
			$fp=fopen($config['paths']['backup'].$mpdatei[$i],"r");
			$contents=fread($fp,$file_size);
			$encoded_file=chunk_split(base64_encode($contents));
			fclose($fp);
			$subject=$mp_sub."  [Part ".($i+1)." / ".count($mpdatei)."]";
			$header="FROM:".$config['email_sender']."\n";
			if (isset($config['email_recipient_cc'])&&trim($config['email_recipient_cc'])>'') $header.="Cc:     ".$config['email_recipient_cc']."\r\n";
			$header.="MIME-version: 1.0\n";
			$header.="Content-type: multipart/mixed; ";
			$header.="boundary=\"Message-Boundary\"\n";
			$header.="Content-transfer-encoding: 7BIT\n";
			$header.="X-attachments: $file_name";
			$body_top="--Message-Boundary\n";
			$body_top.="Content-type: text/html; charset=utf-8\n";
			$body_top.="Content-transfer-encoding: 7BIT\n";
			$body_top.="Content-description: Mail message body\n\n";
			$msg_body=$body_top.addslashes($lang['L_EMAIL_ONLY_ATTACHMENT'].$lang['L_EMAILBODY_FOOTER']);
			$msg_body.="\n\n--Message-Boundary\n";
			$msg_body.="Content-type: $file_type; name=\"".$mpdatei[$i]."\"\n";
			$msg_body.="Content-Transfer-Encoding: BASE64\n";
			$msg_body.="Content-disposition: attachment; filename=\"".$mpdatei[$i]."\"\n\n";
			$msg_body.="$encoded_file\n";
			$msg_body.="--Message-Boundary--\n";
			$email_log="Email with $mpdatei[$i] was sent to '".$config['email_recipient']."'";
			$email_out=$lang['L_EMAIL_WAS_SEND']."`".$config['email_recipient']."`".$lang['L_WITH']."`".$mpdatei[$i]."`.<br>";
			
			if (@mail($config['email_recipient'],stripslashes($subject),$msg_body,$header))
			{
				$out.='<span class="success">'.$email_out.'</span>';
				WriteLog("$email_log");
			}
			else
			{
				$out.='<span class="error">'.$lang['L_MAILERROR'].'</span><br>';
				WriteLog("Email to '".$config['email_recipient']."' failed !");
				ErrorLog("Email ",$databases['Name'][$dump['dbindex']],'Subject: '.stripslashes($subject),$lang['L_MAILERROR']);
				$dump['errors']++;
			}
		}
	}
}

function DoFTP($i)
{
	global $config,$dump,$out;
	
	if ($config['multi_part']==0)
	{
		SendViaFTP($i,$dump['backupdatei'],1);
	}
	else
	{
		$dateistamm=substr($dump['backupdatei'],0,strrpos($dump['backupdatei'],"part_"))."part_";
		$dateiendung=($config['compression']==1) ? ".sql.gz":".sql";
		for($a=1;$a<($dump['part']-$dump['part_offset']);$a++)
		{
			$mpdatei=$dateistamm.$a.$dateiendung;
			SendViaFTP($i,$mpdatei,$a);
		}
	}
}

function SendViaFTP($i,$source_file,$conn_msg=1)
{
	global $config,$out,$lang;
	flush();
	if ($conn_msg==1) $out.='<span class="success">'.$lang['L_FILESENDFTP']."(".$config['ftp_server'][$i]." - ".$config['ftp_user'][$i].")</span><br>";
	// Herstellen der Basis-Verbindung
	if ($config['ftp_useSSL'][$i]==0) $conn_id=@ftp_connect($config['ftp_server'][$i],$config['ftp_port'][$i],$config['ftp_timeout'][$i]);
	else
		$conn_id=@ftp_ssl_connect($config['ftp_server'][$i],$config['ftp_port'][$i],$config['ftp_timeout'][$i]);
		// Einloggen mit Benutzername und Kennwort
	$login_result=@ftp_login($conn_id,$config['ftp_user'][$i],$config['ftp_pass'][$i]);
	if ($config['ftp_mode'][$i]==1) ftp_pasv($conn_id,true);
	
	// Verbindung überprüfen
	if ((!$conn_id)||(!$login_result))
	{
		$out.='<span class="error">'.$lang['L_FTPCONNERROR'].$config['ftp_server'][$i].$lang['L_FTPCONNERROR1'].$config['ftp_user'][$i].$lang['L_FTPCONNERROR2'].'</span><br>';
	}
	else
	{
		if ($conn_msg==1) $out.='<span class="success">'.$lang['L_FTPCONNECTED1'].$config['ftp_server'][$i].$lang['L_FTPCONNERROR1'].$config['ftp_user'][$i].'</span><br>';
	}
	
	// Upload der Datei
	$dest=$config['ftp_dir'][$i].$source_file;
	$source=$config['paths']['backup'].$source_file;
	$upload=@ftp_put($conn_id,$dest,$source,FTP_BINARY);
	
	// Upload-Status überprüfen
	if (!$upload)
	{
		$out.='<span class="error">'.$lang['L_FTPCONNERROR3']."<br>($source -> $dest)</span><br>";
	}
	else
	{
		$out.='<span class="success">'.$lang['L_FILE'].' <a href="'.$config['paths']['backup'].$source_file.'" class="smallblack">'.$source_file.'</a>'.$lang['L_FTPCONNECTED2'].$config['ftp_server'][$i].$lang['L_FTPCONNECTED3'].'</span><br>';
		WriteLog("'$source_file' sent via FTP.");
	}
	
	// Schließen des FTP-Streams
	@ftp_quit($conn_id);
}
?>
