<?php
if (!defined('MSD_VERSION')) die('No direct access.');
function FilelisteCombo($fpath,$selected)
{
	$r='<select name="selectfile">';
	$r.='<option value="" '.(($selected=="") ? "SELECTED":"").'></option>';

	$dh=opendir($fpath);
	while (false!==($filename=readdir($dh)))
	{
		if ($filename!="."&&$filename!=".."&&!is_dir($fpath.$filename))
		{
			$r.='<option value="'.$filename.'" ';
			if ($filename==$selected) $r.=' SELECTED';
			$r.='>'.$filename.'</option>'."\n";
		}
	}
	$r.='</select>';
	return $r;
}

function sortierdatum($datum)
{
	$p=explode(' ',$datum);
	$uhrzeit=$p[1];
	$p2=explode('.',$p[0]);
	$day=$p2[0];
	$month=$p2[1];
	$year=$p2[2];
	return $year.'.'.$month.'.'.$day.' '.$uhrzeit;
}

function FileList($multi=0)
{
	global $config,$fpath,$lang,$databases,$href,$dbactiv,$action,$expand;

	$files=Array();
	//Backup-Dateien
	$Theader=$lang['L_FM_FILES1'].' '.$lang['L_OF'].' "'.$dbactiv.'"';
	$akind=1;
	$Sum_Files=0;
	$dh=opendir($fpath);
	$fl="";
	$i=0;
	while (false!==($filename=readdir($dh)))
	{
		if ($filename!='.'&&$filename!='..'&&!is_dir($fpath.$filename))
		{
			$files[$i]['name']=$filename;
			$Sum_Files++;
			$i++;
		}
	}

	$fl.='<div>'.$lang['L_FM_CHOOSE_FILE'].' ';
	$fl.='<span id="gd">&nbsp;</span><br><br>';

	$fl.='<table class="bdr">';
	$fl.='<tr><td colspan="8" align="left"><strong>'.$Theader.'</strong></td><td colspan="3" align="right"></td></tr>';

	//Tableheader
	$fl.='<tr class="thead"><th colspan="3">'.$lang['L_DB'].'</th>
	<th>gz</th>
	<th>Script</th>
	<th colspan="2">'.$lang['L_COMMENT'].'</th>
	<th>'.$lang['L_FM_FILEDATE'].'</th>
	<th>Multipart</th>
	<th>'.$lang['L_FM_TABLES'].' / '.$lang['L_FM_RECORDS'].'</th>
	<th>'.$lang['L_FM_FILESIZE'].'</th>
	<th>'.$lang['L_ENCODING'].'</th></tr>';

	$checkindex=$arrayindex=$gesamt=0;
	$db_summary_anzahl=Array();
	if (count($files)>0)
	{
		for($i=0;$i<sizeof($files);$i++)
		{
			// Dateigr&ouml;&szlig;e
			$size=filesize($fpath.$files[$i]['name']);
			$file_datum=date("d\.m\.Y H:i",filemtime($fpath.$files[$i]['name']));

			//statuszeile auslesen
			$sline='';

			if (substr($files[$i]['name'],-3)=='.gz')
			{
				if ($config['zlib'])
				{
					$fp=gzopen($fpath.$files[$i]['name'],"r");
					$sline=gzgets($fp,40960);
					gzclose($fp);
				}
			}
			else
			{
				$fp=fopen($fpath.$files[$i]['name'],"r");
				$sline=fgets($fp,5000);
				fclose($fp);
			}
			$statusline=ReadStatusline($sline);

			$but=ExtractBUT($files[$i]['name']);
			if ($but=='') $but=$file_datum;
			$dbn=$statusline['dbname'];
			if ($dbn=='unknown') $dbn='~unknown'; // needed for sorting - place unknown files at the end
			//jetzt alle in ein Array packen
			if ($statusline['part']=='MP_0'||$statusline['part']=='')
			{
				$db_backups[$arrayindex]['name']=$files[$i]['name'];
				$db_backups[$arrayindex]['db']=$dbn;
				$db_backups[$arrayindex]['size']=$size;
				$db_backups[$arrayindex]['date']=$but;
				$db_backups[$arrayindex]['sort']=sortierdatum($but);
				$db_backups[$arrayindex]['tabellen']=$statusline['tables'];
				$db_backups[$arrayindex]['eintraege']=$statusline['records'];
				$db_backups[$arrayindex]['multipart']=0;
				$db_backups[$arrayindex]['kommentar']=$statusline['comment'];
				$db_backups[$arrayindex]['script']=($statusline['script']!='') ? $statusline['script'].'('.$statusline['scriptversion'].')':'';
				$db_backups[$arrayindex]['charset']=$statusline['charset'];

				if (!isset($db_summary_last[$dbn])) $db_summary_last[$dbn]=$but;
				$db_summary_anzahl[$dbn]=(isset($db_summary_anzahl[$dbn])) ? $db_summary_anzahl[$dbn]+1:1;
				$db_summary_size[$dbn]=(isset($db_summary_size[$dbn])) ? $db_summary_size[$dbn]+$size:$size;
				if (sortierdatum($but)>sortierdatum($db_summary_last[$dbn])) $db_summary_last[$dbn]=$but;
			}
			else
			{
				//multipart nur einmal
				$done=0;
				if (!isset($db_summary_size[$dbn])) $db_summary_size[$dbn]=0;
				for($j=0;$j<$arrayindex;$j++)
				{
					if (isset($db_backups[$j]))
					{
						if (($db_backups[$j]['date']==$but)&&($db_backups[$j]['db']==$dbn))
						{
							$db_backups[$j]['multipart']++;
							$db_backups[$j]['size']+=$size;
							$db_summary_size[$dbn]+=$size;
							$done=1;
							break;
						}
					}
				}
				if ($done==1) $arrayindex--;

				if ($done==0)
				{
					//Eintrag war noch nicht vorhanden
					$db_backups[$arrayindex]['name']=$files[$i]['name'];
					$db_backups[$arrayindex]['db']=$dbn;
					$db_backups[$arrayindex]['size']=$size;
					$db_backups[$arrayindex]['date']=$but;
					$db_backups[$arrayindex]['sort']=sortierdatum($but);
					$db_backups[$arrayindex]['tabellen']=$statusline['tables'];
					$db_backups[$arrayindex]['eintraege']=$statusline['records'];
					$db_backups[$arrayindex]['multipart']=1;
					$db_backups[$arrayindex]['kommentar']=$statusline['comment'];
					$db_backups[$arrayindex]['script']=($statusline['script']!="") ? $statusline['script']."(".$statusline['scriptversion'].")":"";
					$db_backups[$arrayindex]['charset']=$statusline['charset'];

					if (!isset($db_summary_last[$dbn])) $db_summary_last[$dbn]=$but;
					$db_summary_anzahl[$dbn]=(isset($db_summary_anzahl[$dbn])) ? $db_summary_anzahl[$dbn]+1:1;
					$db_summary_size[$dbn]=(isset($db_summary_size[$dbn])) ? $db_summary_size[$dbn]+$size:$size;
					if (sortierdatum($but)>sortierdatum($db_summary_last[$dbn])) $db_summary_last[$dbn]=$but;

				}
			}
			// Gesamtgroesse aller Backupfiles
			$arrayindex++;
			$gesamt=$gesamt+$size;
		}
	}
	//Schleife fertig - jetzt Ausgabe
	if ((isset($db_backups))&&(is_array($db_backups))) $db_backups=mu_sort($db_backups,'sort,name');

	// Hier werden die Dateinamen ausgegeben
	$rowclass=0;
	if ($arrayindex>0)
	{
		for($i=$arrayindex;$i>=0;$i--)
		{
			if (isset($db_backups[$i]['db'])&&$db_backups[$i]['db']==$dbactiv)
			{
				$cl=($rowclass%2) ? 'dbrow':'dbrow1';
				$multi=($db_summary_anzahl[$dbactiv]>1&&$action=='files') ? 1:0;

				if ($db_backups[$i]['multipart']>0)
				{
					$dbn=NextPart($db_backups[$i]['name'],1);
				}
				else
				{
					$dbn=$db_backups[$i]['name'];
				}
				$fl.='<tr ';
				$fl.='class="'.(($rowclass%2) ? 'dbrow"':'dbrow1"');
				$fl.='>';
				$fl.='<td align="left" colspan="2" nowrap="nowrap">';
				$fl.='<input type="hidden" name="multi" value="'.$multi.'">';

				if ($multi==0)
				{
					$fl.='<input type="hidden" name="multipart[]" value="'.$db_backups[$i]['multipart'].'"><input name="file[]" type="radio" class="radio" value="'.$dbn.'" onClick="Check('.$checkindex++.',0);">';
				}
				else
				{
					$fl.='<input type="hidden" name="multipart[]" value="'.$db_backups[$i]['multipart'].'"><input name="file[]" type="checkbox" class="checkbox" value="'.$dbn.'" onClick="Check('.$checkindex++.',1);">';
				}

				if ($db_backups[$i]['multipart']==0)
				{
					$fl.='&nbsp;<a href="'.$fpath.urlencode($dbn).'" title="Backupfile: '.$dbn.'" style="font-size:8pt;" target="_blank">';
					$fl.=(($db_backups[$i]['db']=='~unknown') ? $dbn:$db_backups[$i]['db']).'</a></td>';
					$fl.='<td><a href="filemanagement.php?action=dl&amp;f='.urlencode($dbn).'" title="'.$lang['L_DOWNLOAD_FILE'].'" alt="'.$lang['L_DOWNLOAD_FILE'].'"><img src="'.$config['files']['iconpath'].'/openfile.gif"></a></td>';
				}
				else
					$fl.='&nbsp;<span style="font-size:8pt;">'.$db_backups[$i]['db'].'</span><td>&nbsp;</td></td>';

				$fl.='<td class="sm" nowrap="nowrap" align="center">'.((substr($dbn,-3)==".gz") ? '<img src="'.$config['files']['iconpath'].'gz.gif" alt="'.$lang['L_COMPRESSED'].'" width="16" height="16" border="0">':"&nbsp;").'</td>';
				$fl.='<td class="sm" nowrap="nowrap" align="center">'.$db_backups[$i]['script'].'</td>';
				$fl.='<td class="sm" nowrap="nowrap" align="right">'.(($db_backups[$i]['kommentar']!="") ? '<img src="'.$config['files']['iconpath'].'rename.gif" alt="'.$db_backups[$i]['kommentar'].'" title="'.$db_backups[$i]['kommentar'].'" width="16" height="16" border="0">':"&nbsp;").'</td>';
				$fl.='<td class="sm" nowrap="nowrap" align="left">'.(($db_backups[$i]['kommentar']!="") ? nl2br(wordwrap($db_backups[$i]['kommentar'],50)):"&nbsp;").'</td>';

				$fl.='<td class="sm" nowrap="nowrap">'.$db_backups[$i]['date'].'</td>';
				$fl.='<td style="text-align:center">';
				$fl.=($db_backups[$i]['multipart']==0) ? $lang['L_NO']:'<a style="font-size:11px;" href="filemanagement.php?action=files&amp;kind=0&amp;dbactiv='.$dbactiv.'&amp;expand='.$i.'">'.$db_backups[$i]['multipart'].' Files</a>'; //
				$fl.='</td><td  style="text-align:right;padding-right:12px;" nowrap="nowrap">';
				$fl.=($db_backups[$i]['eintraege']!=-1) ? $db_backups[$i]['tabellen'].' / '.number_format($db_backups[$i]['eintraege'],0,",","."):$lang['L_FM_OLDBACKUP'];
				$fl.='</td>';
				$fl.='<td style="font-size:8pt;text-align:right">'.byte_output($db_backups[$i]['size']).'</td>';
				$fl.='<td style="font-size:8pt;text-align:right">'.$db_backups[$i]['charset'].'</td>';
				$fl.='</tr>';

				if ($expand==$i)
				{
					$fl.='<tr '.(($dbactiv==$databases['db_actual']) ? 'class="dbrowsel"':'class="'.$cl.'"').'>';
					$fl.='<td class="sm" valign="top">All Parts:</td><td  class="sm" colspan="11" align="left">'.PartListe($db_backups[$i]['name'],$db_backups[$i]['multipart']).'</td>';
				}
				$rowclass++;
			}
		}
	}
	//v($db_backups);
	$fl.='<tr><td colspan="11" align="left"><br><strong>'.$lang['L_FM_ALL_BU'].'</strong></td></tr>';
	//Tableheader
	$fl.='<tr class="thead"><th colspan="5" align="left">'.$lang['L_FM_DBNAME'].'</th>
	<th align="left">'.$lang['L_FM_ANZ_BU'].'</th><th>'.$lang['L_FM_LAST_BU'].'</th>
	<th colspan="5" style="text-align:right;">'.$lang['L_FM_TOTALSIZE'].'</th></tr>';
	//die anderen Backups
	if (count($db_summary_anzahl)>0)
	{
		//lets sort the list
		ksort($db_summary_last);
		ksort($db_summary_anzahl);
		ksort($db_summary_size);

		$i=0;
		while (list ($key,$val)=each($db_summary_anzahl))
		{
			$cl=($i++%2) ? "dbrow":"dbrow1";
			$keyaus=($key=="~unknown") ? '<em>'.$lang['L_NO_MSD_BACKUPFILE'].'</em>':$key;
			$fl.='<tr class="'.$cl.'"><td colspan="5" align="left"><a href="'.$href.'&amp;dbactiv='.$key.'">'.$keyaus.'</a></td>';
			$fl.='<td style="text-align:right">'.$val.'&nbsp;&nbsp;</td>';
			$fl.='<td class="sm" nowrap="nowrap">'.((isset($db_summary_last[$key])) ? $db_summary_last[$key]:'').'</td>';
			$fl.='<td style="text-align:right;font-size:8pt;" colspan="5">'.byte_output($db_summary_size[$key]).'&nbsp;</td>';
			$fl.='</tr>';
		}
	}
	if (!is_array($files)) $fl.='<tr><td colspan="11">'.$lang['L_FM_NOFILESFOUND'].'</td></tr>';

	//--------------------------------------------------------
	//*** Ausgabe der Gesamtgr&ouml;&szlig;e aller Backupfiles ***
	//--------------------------------------------------------
	$space=MD_FreeDiskSpace();
	$fl.='<tr>';
	$fl.='<td align="left" colspan="8"><b>'.$lang['L_FM_TOTALSIZE'].' ('.$Sum_Files.' files): </b> </td>';
	$fl.='<td style="text-align:right" colspan="4"><b>'.byte_output($gesamt).'</b></td>';
	$fl.='</tr>';

	//--------------------------------------------------------
	//*** Ausgabe des freien Speicher auf dem Rechner ***
	//--------------------------------------------------------
	$fl.='<tr>';
	$fl.='<td colspan="8" align="left">'.$lang['L_FM_FREESPACE'].': </td>';
	$fl.='<td colspan="4"  style="text-align:right"><b>'.$space.'</b></td>';
	$fl.='</tr>';
	$fl.='</table></div>';

	return $fl;
}

function read_statusline_from_file($filename)
{
	global $config;
	if (strtolower(substr($filename,-2))=='gz')
	{
		$fp=gzopen($config['paths']['backup'].$filename,"r");
		if ($fp===false) die('Can\'t open file '.$filename);
		$sline=gzgets($fp,40960);
		gzclose($fp);
	}
	else
	{
		$fp=fopen($config['paths']['backup'].$filename,"r");
		if ($fp===false) die('Can\'t open file '.$filename);
		$sline=fgets($fp,5000);
		fclose($fp);
	}
	$statusline=ReadStatusline($sline);
	return $statusline;
}

function PartListe($f,$nr)
{
	global $config,$lang,$fpath;
	$dateistamm=substr($f,0,strrpos($f,"part_"))."part_";
	$dateiendung=(substr(strtolower($f),-2)=="gz") ? ".sql.gz":".sql";
	$s="";
	for($i=1;$i<=$nr;$i++)
	{
		if ($i>1) $s.="<br>";
		$s.='<a href="'.$fpath.urlencode($dateistamm.$i.$dateiendung).'">'.$dateistamm.$i.$dateiendung.'</a>&nbsp;&nbsp;&nbsp;'.byte_output(@filesize($config['paths']['backup'].$dateistamm.$i.$dateiendung));
		$s.='&nbsp;<a href="filemanagement.php?action=dl&amp;f='.urlencode($dateistamm.$i.$dateiendung).'" title="'.$lang['L_DOWNLOAD_FILE'].'" alt="'.$lang['L_DOWNLOAD_FILE'].'"><img src="'.$config['files']['iconpath'].'/openfile.gif"></a>';

	}
	return $s;
}

function Converter($filesource,$filedestination,$cp)
{
	global $config,$lang;

	$filesize=0;
	$max_filesize=1024*1024*10; //10 MB splitsize
	$part=1;
	$cps=(substr(strtolower($filesource),-2)=="gz") ? 1:0;
	$filedestination.='_'.date("Y_m_d_H_i",time());
	echo "<h5>".sprintf($lang['L_CONVERT_FILEREAD'],$filesource).".....</h5><span style=\"font-size:10px;\">";
	if (file_exists($config['paths']['backup'].$filedestination)) unlink($config['paths']['backup'].$filedestination);
	$f=($cps==1) ? gzopen($config['paths']['backup'].$filesource,"r"):fopen($config['paths']['backup'].$filesource,"r");
	$z=($cp==1) ? gzopen($config['paths']['backup'].$filedestination.'_part_1.sql.gz',"w"):fopen($config['paths']['backup'].$filedestination.'_part_1.sql',"w");

	$zeile=get_pseudo_statusline($part,$filedestination)."\r\n";
	($cp==1) ? gzwrite($z,$zeile):fwrite($z,$zeile);
	$zeile='';

	$insert=$mode="";
	$n=0;
	$eof=($cps==1) ? gzeof($f):feof($f);
	$splitable=false; // can the file be splitted? Try to avoid splitting before a command is completed
	WHILE (!$eof)
	{
		$eof=($cps==1) ? gzeof($f):feof($f);
		$zeile=($cps==1) ? gzgets($f,5144000):fgets($f,5144000);

		$t=strtolower(substr($zeile,0,10));
		if ($t>'')
		{
			switch ($t)
			{
				case 'insert int':
					{
						// eine neue Insert Anweisung beginnt
						if (strpos($zeile,'(')===false)
						{
							//Feldnamen stehen in der naechsten Zeile - holen
							$zeile.="\n\r";
							$zeile.=($cps==1) ? trim(gzgets($f,8192)):trim(fgets($f,8192));
							$zeile.=' ';
						}

						// get INSERT-Satement
						$insert=substr($zeile,0,strpos($zeile,'('));
						if (substr(strtoupper($insert),-7)!='VALUES ') $insert.=' VALUES ';
						$mode='insert';
						$zeile="\n\r".$zeile;
						$splitable=false;
						break;
					}

				case 'create tab':
					{
						$mode='create';
						WHILE (substr(rtrim($zeile),-1)!=';')
						{
							$zeile.=fgets($f,8192);
						}
						$zeile="\n\r".MySQL_Ticks($zeile)."\n\r";
						$splitable=true;
						break;
					}
			}
		}

		if ($mode=='insert')
		{
			if (substr(rtrim($zeile),strlen($zeile)-3,2)==');') $splitable=true;

			// Komma loeschen
			$zeile=str_replace('),(',");\n\r".$insert.' (',$zeile);
		}

		if ($splitable==true&&$filesize>$max_filesize) // start new file?
		{
			$part++;
			if ($mode=='insert') // Insert -> first complete Insert-Statement, then begin new file
			{
				if ($cp==1)
				{
					gzwrite($z,$zeile);
					gzclose($z);
					$z=gzopen($config['paths']['backup'].$filedestination.'_part_'.$part.'.sql.gz',"w");
					$zeile=get_pseudo_statusline($part,$filedestination)."\r\n";
					gzwrite($z,$zeile);
					$zeile='';
				}
				else
				{
					fwrite($z,$zeile);
					echo "<br>Neue Datei.Zeile: <br>".htmlspecialchars(substr($zeile,0,20))."..".htmlspecialchars(substr($zeile,strlen($zeile)-41,40))."<br>";
					fclose($z);
					$z=fopen($config['paths']['backup'].$filedestination.'_part_'.$part.'.sql',"w");
					$zeile=get_pseudo_statusline($part,$filedestination)."\r\n";
					gzwrite($z,$zeile);
					$zeile='';
				}
			}
			else // first close last file, then begin new one and write new beginning command
			{
				if ($cp==1)
				{
					gzclose($z);
					$z=gzopen($config['paths']['backup'].$filedestination.'_part_'.$part.'.sql.gz',"w");
					$zeile=get_pseudo_statusline($part,$filedestination)."\r\n".$zeile;
					gzwrite($z,$zeile);
				}
				else
				{
					fclose($z);
					$z=fopen($config['paths']['backup'].$filedestination.'_part_'.$part.'.sql',"w");
					$zeile=get_pseudo_statusline($part,$filedestination)."\r\n".$zeile;
					fwrite($z,$zeile);
				}
			}
			$filesize=0;
			$splitable=false;
		}
		else // no, append to actual file
		{
			$filesize+=strlen($zeile);
			if ($n>600)
			{
				$n=0;
				echo '<br>';
			}
			echo '.';
			if ($cps==1) gzwrite($z,$zeile);
			else
				fwrite($z,$zeile);
			flush();
		}
		$n++;
		//if ($part>4) break;
	}
	$zeile="\n-- EOB";
	if ($cps==1)
	{
		gzwrite($z,$zeile);
		gzclose($z);
	}
	else
	{
		fwrite($z,$zeile);
		fclose($z);
	}

	if ($cps==1) gzclose($f);
	else
		fclose($f);
	echo '</span><h5>'.sprintf($lang['L_CONVERT_FINISHED'],$filedestination).'</h5>';
}

function get_pseudo_statusline($part,$filedestination)
{
	echo '<br>Continue with part: '.$part.'<br>';
	$ret='-- Status:-1:-1:MP_'.($part).':'.$filedestination.":php:converter2:converted:unknown:1:::latin1:EXTINFO\r\n"."-- TABLE-INFO\r\n"."-- TABLE|unknown|0|0|2009-01-24 20:39:39\r\n"."-- EOF TABLE-INFO\r\n";
	return $ret;
}

?>