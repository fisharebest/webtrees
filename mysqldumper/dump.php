<?php
if (!@ob_start("ob_gzhandler")) @ob_start();
session_name('MySQLDumper');
session_start();
$aus2=$page_parameter=$a=$out='';
include_once ('./inc/functions_dump.php');

// beim Erstaufruf Konfigurationsdatei auslesen und in Session speichern
if (isset($_GET['config']))
{
	// Session loeschen, damit keine alten Werte des letzten Laufs uebernommen werden
	if (isset($_SESSION['dump'])) unset($_SESSION['dump']);
	$search=array(

	'/','\\',':','@');
	$replace=array(

	'','','','');
	$config_file=str_replace($search,$replace,$_GET['config']);
	if (is_readable($config['paths']['config'].$config_file.'.php'))
	{
		$config['files']['parameter']=$config['paths']['config'].$config_file.'.php';
		$_SESSION['config_file']=$config_file;
		read_config($config['paths']['config'].$config['files']['parameter']);
		$_SESSION['config']=$config;
	}
	else
		die("Hacking attempt or configuration not found!");
}
$config=$_SESSION['config'];
include ('./'.$config['files']['parameter']);
$config['files']['iconpath']='./css/'.$config['theme'].'/icons/';
include ('./inc/mysql.php');
include ('./language/'.$config['language'].'/lang.php');
include ('./language/'.$config['language'].'/lang_dump.php');

$pageheader=MSDHeader();
$DumpFertig=0;
$relativ_path='./';
$flipped=array_flip($databases['Name']);

if (isset($_SESSION['dump'])&&!isset($_GET['config']))
{
	$dump=$_SESSION['dump'];
}
else
{
	$dump['tables']=Array();
	$dump['records']=Array();
	$dump['skip_data']=Array();
	$dump['totalrecords']=0;
	$dump['dbindex']=0;
	//$_POST-Parameter lesen
	$dump['kommentar']=(isset($_GET['comment'])) ? urldecode($_GET['comment']):'';
	if (isset($_POST['kommentar'])) $dump['kommentar']=urldecode($_POST['kommentar']);
	if (get_magic_quotes_gpc()) $dump['kommentar']=stripslashes($dump['kommentar']);

	$dump['backupdatei']=(isset($_POST['backupdatei'])) ? $_POST['backupdatei']:'';
	$dump['part']=(isset($_POST['part'])) ? $_POST['part']:1;
	$dump['part_offset']=(isset($_POST['part_offset'])) ? $_POST['part_offset']:0;
	$dump['verbraucht']=(isset($_POST['verbraucht'])) ? $_POST['verbraucht']:0;
	$dump['errors']=(isset($_POST['errors'])) ? $_POST['errors']:0;
	$dump['table_offset']=(isset($_POST['table_offset'])) ? $_POST['table_offset']:-1;
	$dump['zeilen_offset']=(isset($_POST['zeilen_offset'])) ? $_POST['zeilen_offset']:0;
	$dump['filename_stamp']=(isset($_POST['filename_stamp'])) ? $_POST['filename_stamp']:'';
	$dump['anzahl_zeilen']=(isset($_POST['anzahl_zeilen'])) ? $_POST['anzahl_zeilen']:(($config['minspeed']>0) ? $config['minspeed']:50);
	$dump['dump_encoding']=(isset($_POST['dump_encoding'])) ? urldecode($_POST['dump_encoding']):'';

	if (isset($_GET['sel_dump_encoding']))
	{
		// Erstaufruf -> encoding auswerten
		include_once ('./inc/functions_sql.php');
		get_sql_encodings();
		$encodingline=$config['mysql_possible_character_sets'][$_GET['sel_dump_encoding']];
		$encoding=explode(' ',$encodingline);
		$dump['dump_encoding']=isset($encoding[0]) ? $encoding[0]:$encodingline;
	}
	include ('./inc/define_icons.php');
	$dump['tabellen_gesamt']=0;
}

$mp2=array(

'Bytes','Kilobytes','Megabytes','Gigabytes');

FillMultiDBArrays();
if ($databases['db_actual_tableselected']!=''&&$config['multi_dump']==0)
{
	$dump['tblArray']=explode('|',$databases['db_actual_tableselected']);
	$tbl_sel=true;
	$msgTbl=sprintf($lang['L_NR_TABLES_SELECTED'],count($dump['tblArray']));
}
// Korrektur -> Multi-DB-Array ist gefuellt (damit die Infos in der Konfig nicht verloren gehen), aber Multidump ist nicht aktiviert)
if ($config['multi_dump']==0)
{
	unset($databases['multi']);
	$databases['multi']=array();
	$databases['multi'][0]=$databases['db_actual'];
}
else
{
	// wenn Multidump aktiviert ist, aber keine DB gewaehlt wurde -> aktuelle DB uebernehmen
	if (!isset($databases['multi'][0])) $databases['multi'][0]=$databases['db_actual'];
	// find correct dbindex -> take dbname from $databases['multi'] and get the correct index
	// from $databases['Name'] -> needed to set $dump['dbindex'] for first run of command_before_dump
	$dump['dbindex']=$flipped[$databases['multi'][0]];
}

//Zeitzähler aktivieren
$dump['max_zeit']=intval($config['max_execution_time']*$config['time_buffer']);
$dump['startzeit']=time();
$xtime=(isset($_POST['xtime'])) ? $_POST['xtime']:time();
$dump['countdata']=(!empty($_POST['countdata'])) ? $_POST['countdata']:0;
$dump['aufruf']=(!empty($_POST['aufruf'])) ? $_POST['aufruf']:0;
MSD_mysql_connect($dump['dump_encoding']);
if ($dump['table_offset']==-1) ExecuteCommand('b');

// only read tableinfos the first time and save it to session to speed up backing up process
if (!isset($_SESSION['dump'])) getDBInfos();

$num_tables=count($dump['tables']);

if ($config['optimize_tables_beforedump']==1&&$dump['table_offset']==-1) $out.=sprintf($lang['L_NR_TABLES_OPTIMIZED'],$num_tables).'<br>';
$dump['data']='';
$dump['dbindex']=(isset($_POST['dbindex'])) ? $_POST['dbindex']:$flipped[$databases['multi'][0]];

//Ausgaben-Header bauen
$aus_header[]=headline('Backup: '.(($config['multi_dump']==1) ? 'Multidump ('.count($databases['multi']).' '.$lang['L_DBS'].')':$lang['L_DB'].': '.$databases['Name'][$dump['dbindex']].(($databases['praefix'][$dump['dbindex']]!='') ? ' ('.$lang['L_WITHPRAEFIX'].' <span>'.$databases['praefix'][$dump['dbindex']].'</span>)':'')));
if (isset($aus_error)&&count($aus_error)>0) $aus_header=array_merge($aus_header,$aus_error);

if ($num_tables==0)
{
	//keine Tabellen gefunden
	$aus[]='<br><br><p class="error">'.$lang['L_ERROR'].': '.sprintf($lang['L_DUMP_NOTABLES'],$databases['Name'][$dump['dbindex']]).'</p>';
    if (!$config['multi_dump']==1)
	{
		echo $pageheader;
		echo get_page_parameter($dump);
		echo implode("\n",$aus);
		echo '</body></html>';
		exit();
	}
}
else
{
	if ($dump['table_offset']==-1)
	{
		// File anlegen, da Erstaufruf
		new_file();
		$dump['table_offset']=0; // jetzt kanns losgehen
		flush();
	}
	else
	{
		// SQL-Befehle ermitteln
		$dump['restzeilen']=$dump['anzahl_zeilen'];
		WHILE (($dump['table_offset']<$num_tables)&&($dump['restzeilen']>0))
		{
			$table=substr($dump['tables'][$dump['table_offset']],strpos($dump['tables'][$dump['table_offset']],'|')+1);
			$adbname=substr($dump['tables'][$dump['table_offset']],0,strpos($dump['tables'][$dump['table_offset']],'|'));
			if ($databases['Name'][$dump['dbindex']]!=$adbname)
			{
				//neue Datenbank
				$dump['data'].="\nSET FOREIGN_KEY_CHECKS=1;";
				$dump['data'].="\n".$mysql_commentstring.' EOB'."\n\n";
				WriteToDumpFile();
				WriteLog('Dump \''.$dump['backupdatei'].'\' finished.');
				ExecuteCommand('a');
				if ($config['multi_part']==1)
				{
					$out.=$lang['L_FINISHED'].'<br><div class="backupmsg">';
					$dateistamm=substr($dump['backupdatei'],0,strrpos($dump['backupdatei'],'part_')).'part_';
					$dateiendung=($config['compression']==1) ? '.sql.gz':'.sql';
					for($i=1;$i<($dump['part']-$dump['part_offset']);$i++)
					{
						$mpdatei=$dateistamm.$i.$dateiendung;
						clearstatcache();
						$sz=byte_output(@filesize($config['paths']['backup'].$mpdatei));
						$out.=$lang['L_FILE'].' <a href="'.$config['paths']['backup'].$mpdatei.'" class="smallblack">'.$mpdatei.' ('.$sz.')</a> '.$lang['L_DUMP_SUCCESSFUL'].'<br>';
					}
				}
				else
				{
					clearstatcache();
					$out.=$lang['L_FINISHED'].'<br><div class="backupmsg"><a href="'.$config['paths']['backup'].$dump['backupdatei'].'" class="smallblack">'.$dump['backupdatei'].' ('.byte_output(filesize($config['paths']['backup'].$dump['backupdatei'])).')</a><br>';
				}
				if ($config['send_mail']==1) DoEmail();

				for($i=0;$i<3;$i++)
				{
					if ($config['ftp_transfer'][$i]==1) DoFTP($i);
				}
				if (isset($flipped[$adbname])) $dump['dbindex']=$flipped[$adbname];
				$dump['part_offset']=$dump['part']-1;
				$out.='</div><br>';
				ExecuteCommand('b');
				new_file();
			}

			$aktuelle_tabelle=$dump['table_offset'];
			if ($dump['zeilen_offset']==0)
			{
				if ($config['minspeed']>0)
				{
					$dump['anzahl_zeilen']=$config['minspeed'];
					$dump['restzeilen']=$config['minspeed'];
				}

				$create_statement='';
				$create_statement=get_def($adbname,$table);

				if (!($create_statement===false))
				{
					$dump['data'].=$create_statement;
				}
				else
				{
					WriteToDumpFile(); // save data we have up to now
					// error reading table definition
					$read_create_error=sprintf($lang['L_FATAL_ERROR_DUMP'],$table,$adbname).': '.mysql_error($config['dbconnection']);
					Errorlog("DUMP",$databases['db_actual'],'',$read_create_error,0);
					WriteLog($read_create_error);
					if ($config['stop_with_error']>0)
					{
						die($read_create_error);
					}
					$dump['errors']++;
				}
			}
			WriteToDumpFile();
			if (!in_array($adbname.'|'.$table,$dump['skip_data'])&&$dump['table_types'][getDBIndex($adbname,$table)]!='VIEW')
			{
				get_content($adbname,$table);
				$dump['restzeilen']--;
			}
			else
			{
				// skip data
				if ($dump['table_types'][getDBIndex($adbname,$table)]!='VIEW') $dump['data'].='/*!40000 ALTER TABLE `'.$table.'` ENABLE KEYS */;'."\n";
				WriteToDumpFile();
				$dump['table_offset']++;
			}
			if ($config['memory_limit']>0&&strlen($dump['data'])>$config['memory_limit']) WriteToDumpFile();
		}
	}

	/////////////////////////////////
	// Anzeige - Fortschritt
	/////////////////////////////////
	if ($config['multi_dump']==1)
	{
		$mudbs='';
		$count_dbs=count($databases['multi']);
		for($i=0;$i<$count_dbs;$i++)
		{
			if ($databases['Name'][$dump['dbindex']]==$databases['multi'][$i]) $mudbs.='<span class="active_db">'.$databases['multi'][$i].'&nbsp;&nbsp;</span> ';
			else
				$mudbs.='<span class="success">'.$databases['multi'][$i].'&nbsp;&nbsp;</span> ';
		}
	}
	if ($config['multi_part']==1) $aus[]='<h5>Multipart-Backup: '.$config['multipartgroesse1'].' '.$mp2[$config['multipartgroesse2']].'</h5>';

	$aus[]='<h4>'.$lang['L_DUMP_HEADLINE'].'</h4>';

	if ($dump['kommentar']>'') $aus[]=$lang['L_COMMENT'].': <span><em>'.$dump['kommentar'].'</em></span><br>';
	$aus[]=($config['multi_dump']==1) ? $lang['L_DB'].': '.$mudbs:$lang['L_DB'].': <strong>'.$databases['Name'][$dump['dbindex']].'</strong>';
	$aus[]=(($databases['praefix'][$dump['dbindex']]!='') ? ' ('.$lang['L_WITHPRAEFIX'].' <span>'.$databases['praefix'][$dump['dbindex']].'</span>)':'').'<br>';
	if (isset($tbl_sel)) $aus[]=$msgTbl.'<br><br>';

	if ($config['multi_part']==1)
	{
		$aus[]='<span>Multipart-Backup File <strong>'.($dump['part']-$dump['part_offset']-1).'</strong></span><br>';
		$aus2=', '.($dump['part']-1).' files';
	}
	$aus[]=$lang['L_DUMP_FILENAME'].'<b>'.$dump['backupdatei'].'</b><br>'.$lang['L_CHARSET'].': <strong>'.$dump['dump_encoding'].'</strong>'.

	'<br>'.$lang['L_FILESIZE'].': <b>'.byte_output($dump['filesize']).'</b><br><br>'.$lang['L_GZIP_COMPRESSION'].' <b>';
	$aus[]=($config['compression']==1) ? $lang['L_ACTIVATED']:$lang['L_NOT_ACTIVATED'];
	$aus[]='</b>.<br>';
	if ($out>'') $aus[]='<br><span class="smallgrey">'.$out.'</span>';

	if (isset($dump['tables'][$dump['table_offset']]))
	{
		$table=substr($dump['tables'][$dump['table_offset']],strpos($dump['tables'][$dump['table_offset']],'|')+1);
		$adbname=substr($dump['tables'][$dump['table_offset']],0,strpos($dump['tables'][$dump['table_offset']],'|'));

		// get nr of recorsd from dump-array
		$record_string=$dump['records'][$dump['table_offset']];
		$record_string=explode('|',$record_string);
		$dump['zeilen_total']=$record_string[1];

		if ($dump['zeilen_total']>0) $fortschritt=intval((100*$dump['zeilen_offset'])/$dump['zeilen_total']);
		else
			$fortschritt=100;

		$aus[]=$lang['L_SAVING_TABLE'].'<b>'.($dump['table_offset']+1).'</b> '.$lang['L_OF'].'<b> '.sizeof($dump['tables']).'</b><br>'.$lang['L_ACTUAL_TABLE'].': <b>'.$table.'</b><br><br>'.$lang['L_PROGRESS_TABLE'].':<br>';

		$aus[]='<table border="0" width="380"><tr>'.'<td width="'.($fortschritt*3).'"><img src="'.$config['files']['iconpath'].'progressbar_dump.gif" alt="" width="'.($fortschritt*3).'" height="16" border="0"></td>'.'<td width="'.((100-$fortschritt)*3).'">&nbsp;</td>'.'<td width="80" align="right">'.($fortschritt).'%</td>';

		if ($dump['anzahl_zeilen']+$dump['zeilen_offset']>=$dump['zeilen_total'])
		{
			$eintrag=$dump['zeilen_offset']+1;
			$zeilen_gesamt=$dump['zeilen_total'];
			if ($zeilen_gesamt==0) $eintrag=0;
		}
		else
		{
			$zeilen_gesamt=$dump['zeilen_offset']+$dump['anzahl_zeilen'];
			$eintrag=$dump['zeilen_offset']+1;
		}

		$aus[]='</tr><tr>'.'<td colspan="3">'.$lang['L_ENTRY'].' <b>'.number_format($eintrag,0,',','.').'</b> '.$lang['L_UPTO'].' <b>'.number_format(($zeilen_gesamt),0,',','.').'</b> '.$lang['L_OF'].' <b>'.number_format($dump['zeilen_total'],0,',','.').'</b></td></tr></table>';

		$dump['tabellen_gesamt']=(isset($dump['tables'])) ? count($dump['tables']):0;

		$noch_zu_speichern=$dump['totalrecords']-$dump['countdata'];
		$prozent=($dump['totalrecords']>0) ? round(((100*$noch_zu_speichern)/$dump['totalrecords']),0):100;
		if ($noch_zu_speichern==0||$prozent>100) $prozent=100;

		$aus[]="\n".'<br>'.$lang['L_PROGRESS_OVER_ALL'].':'."\n".'<table border="0" width="550" cellpadding="0" cellspacing="0"><tr>'.'<td width="'.(5*(100-$prozent)).'"><img src="'.$config['files']['iconpath'].'progressbar_dump.gif" alt="" width="'.(5*(100-$prozent)).'" height="16" border="0"></td>'.'<td width="'.($prozent*5).'" align="center"></td>'.'<td width="50">'.(100-$prozent).'%</td></tr></table>';

		//Speed-Anzeige
		$fw=($config['maxspeed']==$config['minspeed']) ? 300:round(($dump['anzahl_zeilen']-$config['minspeed'])/($config['maxspeed']-$config['minspeed'])*300,0);
		if ($fw>300) $fw=300;
		$aus[]='<br><table border="0" cellpadding="0" cellspacing="0"><tr>'.'<td class="nomargin" width="60" valign="top" align="center" style="font-size:10px;" >'.'<strong>Speed</strong><br>'.$dump['anzahl_zeilen'].'</td><td class="nomargin" width="300">'.'<table border="0" width="100%" cellpadding="0" cellspacing="0"><tr>'.'<td class="nomargin small" align="left" width="300" nowrap="nowrap">'.'<img src="'.$config['files']['iconpath'].'progressbar_speed.gif" alt="" width="'.$fw.'" height="14" border="0" vspace="0" hspace="0">'.'</td></tr></table><table border="0" width="100%" cellpadding="0" cellspacing="0"><tr>'.'<td class="nomargin" align="left" nowrap="nowrap" style="font-size:10px;" >'.$config['minspeed'].'</td>'.'<td class="nomargin" nowrap="nowrap" style="font-size:10px;text-align:right;" >'.$config['maxspeed'].'</td>'.'</tr></table>'."\n".'</td></tr></table>'.

		//Status-Text
		'<p class="small">'.zeit_format(time()-$xtime).', '.$dump['aufruf'].' '.$lang['L_PAGE_REFRESHS'].$aus2;
		$aus[]=($dump['errors']>0) ? ', <span style="color:red;">'.$dump['errors'].' errors</span>':'';
		$aus[]='</p>';
	}
	else
		$dump['table_offset']++;
		// Ende Anzeige
	WriteToDumpFile();
	if (!isset($summe_eintraege)) $summe_eintraege=0;

	if ($dump['table_offset']<=$dump['tabellen_gesamt'])
	{
		$dauer=time()-($xtime+$dump['verbraucht']);
		$dump['verbraucht']+=$dauer;
		$summe_eintraege+=$dump['anzahl_zeilen'];

		//Zeitanpassung
		if ($dauer<$dump['max_zeit'])
		{
			$dump['anzahl_zeilen']=$dump['anzahl_zeilen']*$config['tuning_add'];
			if ($dauer<$dump['max_zeit']/2) $dump['anzahl_zeilen']*=1.8;
			if ($dump['anzahl_zeilen']>$config['maxspeed']) $dump['anzahl_zeilen']=$config['maxspeed'];
		}
		else
		{
			$dump['anzahl_zeilen']=$dump['anzahl_zeilen']*$config['tuning_sub'];
			if ($dump['anzahl_zeilen']<$config['minspeed']) $dump['anzahl_zeilen']=$config['minspeed'];
		}
		$dump['anzahl_zeilen']=intval($dump['anzahl_zeilen']);
		$dump['aufruf']++;
	}
	else
	{
		//Backup fertig
		$dump['data']="\nSET FOREIGN_KEY_CHECKS=1;";
		$dump['data'].="\n".$mysql_commentstring.' EOB'."\n\n";
		WriteToDumpFile();
		ExecuteCommand('a');
		chmod($config['paths']['backup'].$dump['backupdatei'],0777);
		if ($config['multi_part']==1)
		{
			$out.="\n".'<br><div class="backupmsg">';
			$dateistamm=substr($dump['backupdatei'],0,strrpos($dump['backupdatei'],'part_')).'part_';
			$dateiendung=($config['compression']==1) ? '.sql.gz':'.sql';
			clearstatcache();
			for($i=1;$i<($dump['part']-$dump['part_offset']);$i++)
			{
				$mpdatei=$dateistamm.$i.$dateiendung;
				$sz=byte_output(@filesize($config['paths']['backup'].$mpdatei));
				$out.="\n".$lang['L_FILE'].' <a href="'.$config['paths']['backup'].$mpdatei.'" class="smallblack">'.$mpdatei.' ('.$sz.')</a> '.$lang['L_DUMP_SUCCESSFUL'].'<br>';
			}

		}
		else
			$out.="\n".'<div class="backupmsg">'.$lang['L_FILE'].' <a href="'.$config['paths']['backup'].$dump['backupdatei'].'" class="smallblack">'.$dump['backupdatei'].' ('.byte_output(filesize($config['paths']['backup'].$dump['backupdatei'])).')'.'</a>'.$lang['L_DUMP_SUCCESSFUL'].'<br>';

		$xtime=time()-$xtime;
		$aus=Array();
		$aus[]='<br>'."\n";
		if ($config['multi_dump']==1)
		{
			WriteLog('Dump \''.$dump['backupdatei'].'\' finished.');
			WriteLog('Multidump: '.count($databases['multi']).' Databases in '.zeit_format($xtime).'.');
		}
		else
			WriteLog('Dump \''.$dump['backupdatei'].'\' finished in '.zeit_format($xtime).'.');

		if ($config['send_mail']==1) DoEmail();
		for($i=0;$i<3;$i++)
		{
			if ($config['ftp_transfer'][$i]==1) DoFTP($i);
		}

		$aus[]='<strong>'.$lang['L_DONE'].'</strong><br>';

		if ($config['multi_dump']==1)
		{
			$aus[]=sprintf($lang['L_MULTIDUMP'],count($databases['multi'])).': ';
			$aus[]='<strong>'.implode(', ',$databases['multi']).'</strong>';
			$aus2='';
			$out='';
		}
		else
		{
			$aus[]='<br>'.sprintf($lang['L_DUMP_ENDERGEBNIS'],$num_tables,number_format($dump['countdata'],0,',','.'));
		}
		if ($dump['errors']>0) $aus[]=sprintf($lang['L_DUMP_ERRORS'],$dump['errors']);

		$aus[]='<form action="dump.php?MySQLDumper='.session_id().'" method="POST">'.$out.'<br>'.'<p class="small">'.zeit_format($xtime).', '.$dump['aufruf'].' '.$lang['L_PAGE_REFRESHS'].$aus2.'</p>'."\n";
		$aus[]="\n".'<br><input class="Formbutton" type="button" value="'.$lang['L_BACK_TO_CONTROL'].'" onclick="self.location.href=\''.$relativ_path.'filemanagement.php\'">';
		$aus[]='&nbsp;&nbsp;&nbsp;<input class="Formbutton" type="button" value="'.$lang['L_BACK_TO_MINISQL'].'" onclick="self.location.href=\''.$relativ_path.'sql.php\'">';
		$aus[]='&nbsp;&nbsp;&nbsp;<input class="Formbutton" type="button" value="'.$lang['L_BACK_TO_OVERVIEW'].'" onclick="self.location.href=\''.$relativ_path.'main.php?action=db&amp;dbid='.$dump['dbindex'].'#dbid\'"><br><br>';
		$aus[]='</div></form>';

		$DumpFertig=1;
	}
}

//=====================================================================
//================= Anzeige ===========================================
//=====================================================================


//Seite basteln
$aus=array_merge($aus_header,$aus);

$dump['xtime']=$xtime;
if ($DumpFertig!=1)
{
	// save actual values to session
	$_SESSION['dump']=$dump;
	$page_parameter=get_page_parameter($dump);
	$pagefooter='</body></html>';
	$selbstaufruf=$page_parameter.'<script language="javascript" type="text/javascript">setTimeout("document.dump.submit()", 10);</script></div>';
}
else
{
	$dump=array();
	$_SESSION['dump']=$dump;
	$pagefooter=MSDFooter('',1);
	$selbstaufruf='';
}
$complete_page=$pageheader.implode("\n",$aus)."\n".$selbstaufruf."\n".$pagefooter;
echo $complete_page;
ob_end_flush();