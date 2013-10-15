<?php
$msd_path=realpath(dirname(__FILE__) . '/../') . '/';
if (!defined('MSD_PATH')) define('MSD_PATH',$msd_path);
if (file_exists(MSD_PATH.'inc/runtime.php')) include (MSD_PATH.'inc/runtime.php');
else
	die('Couldn\'t read runtime.php!');
if (!defined('MSD_VERSION')) die('No direct access.');

// places all Page Parameters in hidden-fields (needed fpr backup and restore in PHP)
function get_page_parameter($parameter, $ziel='dump')
{
	$page_parameter='<form action="'.$ziel.'.php" method="POST" name="'.$ziel.'">'."\n";
	foreach ($parameter as $key=>$val)
	{
		if (is_array($val))
		{
			foreach ($val as $key2=>$val2)
			{
				$page_parameter.='<input type="hidden" name="'.$key.'['.$key2.']'.'" value="'.$val2.'">'."\n";
			}
		}
		else
			$page_parameter.='<input type="hidden" name="'.$key.'" value="'.$val.'">'."\n";
	}
	$page_parameter.='</form>';
	return $page_parameter;
}

function mu_sort($array, $key_sort)
{
	$key_sorta=explode(',',$key_sort);
	$keys=array_keys($array[0]);
	$n=0;

	for ($m=0; $m<count($key_sorta); $m++)
	{
		$nkeys[$m]=trim($key_sorta[$m]);
	}
	$n+=count($key_sorta);

	for ($i=0; $i<count($keys); $i++)
	{
		if (!in_array($keys[$i],$key_sorta))
		{
			$nkeys[$n]=$keys[$i];
			$n+="1";
		}
	}
	for ($u=0; $u<count($array); $u++)
	{
		$arr=$array[$u];
		for ($s=0; $s<count($nkeys); $s++)
		{
			$k=$nkeys[$s];
			if (isset($array[$u][$k])) $output[$u][$k]=$array[$u][$k];
		}
	}
	// wenn die Sortierung nicht ab- sondern aufsteigend sein soll, muss sort() benutzt werden
	sort($output); // Sort=Aufsteigend -> oder rsort=absteigend
	return $output;
}

function FillMultiDBArrays()
{
	global $config,$databases;

	// Nur füllen wenn überhaupt Datenbanken gefunden wurden
	if ((isset($databases['Name']))&&(count($databases['Name'])>0))
	{
		$databases['multi']=Array();
		$databases['multi_praefix']=Array();
		if (!isset($databases['db_selected_index'])) $databases['db_selected_index']=0;
		if (!isset($databases['db_actual'])&&isset($databases['Name'])) $databases['db_actual']=$databases['Name'][$databases['db_selected_index']];
		if (!isset($databases['multisetting'])) $databases['multisetting']='';

		//		if($config['multi_dump']==1)
		//		{
		if ($databases['multisetting']=='')
		{
			//$databases['multi'][0]=$databases['db_actual'];
		//$databases['multi_praefix'][0]=(isset($databases['praefix'][0])) ? $databases['praefix'][0] : '';
		}
		else
		{
			$databases['multi']=explode(';',$databases['multisetting']);
			$flipped=array_flip($databases['Name']);
			for ($i=0; $i<count($databases['multi']); $i++)
			{
				if (isset($flipped[$databases['multi'][$i]]))
				{
					$ind=$flipped[$databases['multi'][$i]];
					$databases['multi_praefix'][$i]=(isset($databases['praefix'][$ind])) ? $databases['praefix'][$ind] : '';
				}
			}
		}

	//		}
	/*
		else
		{
			$databases['multi'][0]=(isset($databases['db_actual'])) ? $databases['db_actual'] : '';
			$databases['multi_praefix'][0]=(isset($databases['praefix'])) ? $databases['praefix'][$databases['db_selected_index']] : '';
		}
*/
	}

}

function DBDetailInfo($index)
{
	global $databases,$config;

	$databases['Detailinfo']['tables']=$databases['Detailinfo']['records']=$databases['Detailinfo']['size']=0;
	MSD_mysql_connect();
	if (isset($databases['Name'][$index]))
	{
		mysql_select_db($databases['Name'][$index]);
		$databases['Detailinfo']['Name']=$databases['Name'][$index];
		$res=@mysql_query('SHOW TABLE STATUS FROM `'.$databases['Name'][$index].'`');
		if ($res) $databases['Detailinfo']['tables']=mysql_num_rows($res);
		if ($databases['Detailinfo']['tables']>0)
		{
			$s1=$s2=0;
            while ($row= mysql_fetch_array($res, MYSQL_ASSOC))
			{
				$s1+=$row['Rows'];
				$s2+=$row['Data_length']+$row['Index_length'];
			}
			$databases['Detailinfo']['records']=$s1;
			$databases['Detailinfo']['size']=$s2;
		}
	}
}

function Stringformat($s, $count)
{
	if ($count>=strlen($s)) return str_repeat("0",$count-strlen($s)).$s;
	else
		return $s;
}

function getmicrotime()
{
	list ($usec, $sec)=explode(" ",microtime());
	return ((float) $usec+(float) $sec);
}

function MD_FreeDiskSpace()
{
	global $lang;
	$dfs=@diskfreespace("../");
	return ($dfs) ? byte_output($dfs) : $lang['L_NOTAVAIL'];
}

function WriteDynamicText($txt, $object)
{
	return '<script language="JavaScript">WP("'.addslashes($txt).','.$object.'");</script>';
}

function byte_output($bytes, $precision=2, $names=Array())
{
	if (!is_numeric($bytes)||$bytes<0)
	{
		return false;
	}
	for ($level=0; $bytes>=1024; $level++)
	{
		$bytes/=1024;
	}
	switch ($level)
	{
		case 0:
			$suffix=(isset($names[0])) ? $names[0] : '<span class="explain" title="Bytes">B</span>';
			break;
		case 1:
			$suffix=(isset($names[1])) ? $names[1] : '<span class="explain" title="KiloBytes">KB</span>';
			break;
		case 2:
			$suffix=(isset($names[2])) ? $names[2] : '<span class="explain" title="MegaBytes">MB</span>';
			break;
		case 3:
			$suffix=(isset($names[3])) ? $names[3] : '<span class="explain" title="GigaBytes">GB</span>';
			break;
		case 4:
			$suffix=(isset($names[4])) ? $names[4] : '<span class="explain" title="TeraBytes">TB</span>';
			break;
		case 5:
			$suffix=(isset($names[4])) ? $names[4] : '<span class="explain" title="PetaBytes">PB</span>';
			break;
		case 6:
			$suffix=(isset($names[4])) ? $names[4] : '<span class="explain" title="ExaBytes">EB</span>';
			break;
		case 7:
			$suffix=(isset($names[4])) ? $names[4] : '<span class="explain" title="YottaBytes">ZB</span>';
			break;

		default:
			$suffix=(isset($names[$level])) ? $names[$level] : '';
			break;
	}
	return sprintf("%01.".$precision."f",round($bytes,$precision)).' '.$suffix;
}

function ExtractDBname($s)
{
	$sp=explode('_',$s);
	$anz=count($sp)-1;
	$r=0;
	if ($anz>4)
	{
		$df=5; //Datumsfelder
		if ($sp[$anz-1]=='part') $df+=2;
		if ($sp[$anz-3]=='crondump'||$sp[$anz-1]=='crondump') $df+=2;
		$anz=$anz-$df; //Datum weg
		for ($i=0; $i<=$anz; $i++)
		{
			$r+=strlen($sp[$i])+1;
		}
		return substr($s,0,$r-1);
	}
	else
	{
		//Fremdformat
		return substr($s,0,strpos($s,'.'));
	}
}

function ExtractBUT($s)
{
	$i=strpos(strtolower($s),"part");
	if ($i>0) $s=substr($s,0,$i-1);
	$i=strpos(strtolower($s),"crondump");
	if ($i>0) $s=substr($s,0,$i-1);
	$i=strpos(strtolower($s),".sql");
	if ($i>0) $s=substr($s,0,$i);
	$sp=explode("_",$s);

	$anz=count($sp)-1;
	if (strtolower($sp[$anz])=='perl') $anz--;
	if ($anz>4)
	{
		return $sp[$anz-2].".".$sp[$anz-3].".".$sp[$anz-4]." ".$sp[$anz-1].":".$sp[$anz];
	}
	else
	{
		//Fremdformat
		return "";
	}
}

function WriteLog($aktion)
{
	global $config,$lang;
	$log=date('d.m.Y H:i:s').' '.htmlspecialchars($aktion)."\n";

	$logfile=($config['logcompression']==1) ? $config['files']['log'].'.gz' : $config['files']['log'];
	if (@filesize($logfile)+strlen($log)>$config['log_maxsize']) @unlink($logfile);

	//Datei öffnen und schreiben
	if ($config['logcompression']==1)
	{

		$fp=@gzopen($logfile,'a');
		if ($fp)
		{
			@gzwrite($fp,$log).'<br>';
			@gzclose($fp);
		}
		else
			echo '<p class="warnung">'.$lang['L_LOGFILENOTWRITABLE'].' ('.$logfile.')</p>';
	}
	else
	{
		$fp=@fopen($logfile,"ab");
		if ($fp)
		{
			@fwrite($fp,$log);
			@fclose($fp);
		}
		else
			echo '<p class="warnung">'.$lang['L_LOGFILENOTWRITABLE'].' ('.$logfile.')</p>';
	}
}

function ErrorLog($dest, $db, $sql, $error, $art=1)
{
	//$art=0 -> Fehlermeldung
	//$art=1 -> Hinweis


	global $config;
	if (strlen($sql)>100) $sql=substr($sql,0,100)." ... (snip)";
	//Error-Zeile generieren
	$errormsg=date('d.m.Y H:i:s').':  ';
	$errormsg.=($dest=='RESTORE') ? ' Restore of db `'.$db.'`|:|' : ' Dump of db `'.$db.'`|:|';

	if ($art==0)
	{
		$errormsg.='<font color="red">Error-Message: '.$error.'</font>|:|';
	}
	else
	{
		$errormsg.='<font color="green">Notice: '.$error.'</font>|:|';
	}

	if ($sql>'') $errormsg.='SQL: '.$sql."\n";

	//Datei öffnen und schreiben
	if ($config['logcompression']==1)
	{
		$fp=@gzopen($config['paths']['log'].'error.log.gz','ab');
		if ($fp)
		{
			@gzwrite($fp,($errormsg));
			@gzclose($fp);
		}
	}
	else
	{
		$fp=@fopen($config['paths']['log'].'error.log','ab');
		if ($fp)
		{
			@fwrite($fp,($errormsg));
			@fclose($fp);
		}
	}
}

function DirectoryWarnings($path="")
{
	global $config,$lang;
	$warn='';
	if (!is_writable($config['paths']['work'])) $warn.=sprintf($lang['L_WRONG_RIGHTS'],$config['paths']['work'],'0777');
	if (!is_writable($config['paths']['config'])) $warn.=sprintf($lang['L_WRONG_RIGHTS'],$config['paths']['config'],'0777');
	if (!is_writable($config['paths']['backup'])) $warn.=sprintf($lang['L_WRONG_RIGHTS'],$config['paths']['backup'],'0777');
	if (!is_writable($config['paths']['log'])) $warn.=sprintf($lang['L_WRONG_RIGHTS'],$config['paths']['log'],'0777');

	if ($warn!='') $warn='<span class="warnung"><strong>'.$warn.'</strong></span>';
	return $warn;
}

function TestWorkDir()
{
	global $config;

	$ret=SetFileRechte($config['paths']['work']);
	if ($ret===true) $ret=SetFileRechte($config['paths']['backup']);
	if ($ret===true) $ret=SetFileRechte($config['paths']['log']);
	if ($ret===true) $ret=SetFileRechte($config['paths']['config']);

	if ($ret===true)
	{
		if (!file_exists($config['files']['parameter'])) SetDefault(true);
		if (!file_exists($config['files']['log'])) DeleteLog();
	}
	return $ret;
}

function SetFileRechte($file, $is_dir=1, $perm=0777)
{
	global $lang;
	$ret=true;
	if ($is_dir==1)
	{
		if (substr($file,-1)!="/") $file.="/";
	}
	clearstatcache();

	// erst pruefen, ob Datei oder Verzeichnis existiert
	if (!file_exists($file))
	{
		// Wenn es sich um ein Verzeichnis handelt -> anlegen
		if ($is_dir==1)
		{
			$ret=@mkdir($file,$perm);
			if (!$ret===true)
			{
				// Hat nicht geklappt -> Rueckmeldung
				$ret=sprintf($lang['L_CANT_CREATE_DIR'],$file);
			}
		}
	}

	// wenn bisher alles ok ist -> Rechte setzen - egal ob Datei oder Verzeichnis
	if ($ret===true)
	{
		$ret=@chmod($file,$perm);
		if (!$ret===true) $ret=sprintf($lang['L_WRONG_RIGHTS'],$file,decoct($perm));
	}
	return $ret;
}

function SelectDB($index)
{
	global $databases;
	if (is_string($index)) {
	    // name given
	    $dbNames = array_flip($databases['Name']);
	    if (array_key_exists($index, $dbNames)) {
	        $index = $dbNames[$index];
	    }
	}
	if (isset($databases['Name'][$index]))
	{
		$databases['db_actual']=$databases['Name'][$index];
		if (isset($databases['praefix'][$index])) $databases['praefix'][$databases['db_selected_index']]=$databases['praefix'][$index];
		else
			$databases['praefix'][$databases['db_selected_index']]='';
		if (isset($databases['db_selected_index'])) $databases['db_selected_index']=$index;
		else
			$databases['db_selected_index']=0;
	}
	else
	{
		// keine DB vorhanden
		$databases['praefix'][$databases['db_selected_index']]='';
		$databases['db_selected_index']=0;
		$databases['db_actual']='';
	}
}

function EmptyDB($dbn)
{
	global $config;
	$t_sql=array();
	@mysql_query('SET FOREIGN_KEY_CHECKS=0');
	$res=mysql_query('SHOW TABLE STATUS FROM `'.$dbn.'`',$config['dbconnection']) or die('EmptyDB: '.mysql_error());
	WHILE ($row=mysql_fetch_array($res,MYSQL_ASSOC))
	{
		if (substr(strtoupper($row['Comment']),0,4)=='VIEW')
		{
			$t_sql[]='DROP VIEW `'.$dbn.'`.`'.$row['Name'].'`';
		}
		else
		{
			$t_sql[]='DROP TABLE `'.$dbn.'`.`'.$row['Name'].'`';
		}
	}
	if (sizeof($t_sql)>0)
	{
		for ($i=0; $i<count($t_sql); $i++)
		{
			$res=mysql_query($t_sql[$i]) or die('EmptyDB-Error: '.mysql_error());
		}
	}
	@mysql_query('SET FOREIGN_KEY_CHECKS=1');
}

function AutoDelete()
{
	global $del_files,$config,$lang,$out;
	$out='';
	if ($config['max_backup_files']>0)
	{
		//Files einlesen
		$dh=opendir($config['paths']['backup']);
		$dbbackups=array();
		$files=array();

		// Build assoc Array $db=>$timestamp=>$filenames
		while (false!==($filename=readdir($dh)))
		{
			if ($filename!='.'&&$filename!='..'&&!is_dir($config['paths']['backup'].$filename))
			{
				//statuszeile auslesen
				if (substr($filename,-2)=='gz')
				{
					$fp=gzopen($config['paths']['backup'].$filename,'r');
					$sline=gzgets($fp,40960);
					gzclose($fp);
				}
				else
				{
					$fp=fopen($config['paths']['backup'].$filename,'r');
					$sline=fgets($fp,500);
					fclose($fp);
				}
				$statusline=ReadStatusline($sline);
				if ($statusline['dbname']!='unknown')
				{
					$tabellenanzahl=($statusline['tables']==-1) ? '' : $statusline['tables'];
					$eintraege=($statusline['records']==-1) ? '' : $statusline['records'];
					$part=($statusline['part']=='MP_0'||$statusline['part']='') ? 0 : substr($statusline['part'],3);
					$db_name=$statusline['dbname'];
					$datum=substr($filename,strlen($db_name)+1);
					$timestamp=substr($datum,0,16);
					if (!isset($files[$db_name])) $files[$db_name]=array();
					if (!isset($files[$db_name][$timestamp])) $files[$db_name][$timestamp]=array();
					$files[$db_name][$timestamp][]=$filename;
				}
			}
		}
		$out=''; // stores output messages
		// Backups pro DB und Timestamp ermitteln
		foreach ($files as $db=>$val)
		{
			//echo "<br>DB ".$db." hat ".sizeof($val)." Backups.";
			if (sizeof($val)>$config['max_backup_files'])
			{
				$db_files=$val;
				krsort($db_files,SORT_STRING);
				//now latest backupfiles are on top -> delete all files with greater index
				$i=0;
				foreach ($db_files as $timestamp=>$filenames)
				{
					if ($i>=$config['max_backup_files'])
					{
						// Backup too old -> delete files
						foreach ($filenames as $f)
						{
							if ($out=='') $out.=$lang['L_FM_AUTODEL1'].'<br>';
							if (@unlink('./'.$config['paths']['backup'].$f))
							{
								$out.='<span class="nomargin">'.sprintf($lang['L_DELETE_FILE_SUCCESS'],$f).'</span><br>';
							}
							else
							{
								$out.=$lang['L_ERROR'].': <span class="error nomargin">'.sprintf($lang['L_DELETE_FILE_ERROR'],$f).'</span><br>';
							}
						}
					}
					$i++;
				}
			}
		}
	}
	return $out;
}

function DeleteFile($files, $function='max')
{
	global $config,$lang;
	$delfile=explode("|",$files);
	$r='<p class="error">'.$lang['L_FM_AUTODEL1'].'<br>';
	$r.=$delfile[3]."<br>";
	$part=$delfile[2];
	if ($part>0)
	{
		for ($i=$part; $i>0; $i--)
		{
			$delete=@unlink($config['paths']['backup'].$delfile[3]);
			if ($delete) WriteLog("autodeleted ($function) '$delfile[3]'.");
		}
	}
	else
	{
		WriteLog("autodeleted ($function) '$delfile[3]'.");
		unlink($config['paths']['backup'].$delfile[3]);
	}
	$r.='</p>';
	return $r;
}

function ReadStatusline($line)
{
	/*AUFBAU der Statuszeile:
		-- Status:tabellenzahl:datensätze:Multipart:Datenbankname:script:scriptversion:Kommentar:MySQLVersion:Backupflags:SQLBefore:SQLAfter:Charset:EXTINFO
		Aufbau Backupflags (1 Zeichen pro Flag, 0 oder 1, 2=unbekannt)
		(complete inserts)(extended inserts)(ignore inserts)(delayed inserts)(downgrade)(lock tables)(optimize tables)
	*/
	global $lang;
	$statusline=Array();
	if ((substr($line,0,8)!="# Status"&&substr($line,0,9)!="-- Status")||substr($line,0,10)=='-- StatusC')
	{
		//Fremdfile
		$statusline['tables']=-1;
		$statusline['records']=-1;
		$statusline['part']='MP_0';
		$statusline['dbname']='unknown';
		$statusline['script']='';
		$statusline['scriptversion']='';
		$statusline['comment']='';
		$statusline['mysqlversion']='unknown';
		$statusline['flags']='2222222';
		$statusline['sqlbefore']='';
		$statusline['sqlafter']='';
		$statusline['charset']='?';
	}
	else
	{
		// MySQLDumper-File - Informationen extrahieren
		$s=explode(':',$line);
		if (count($s)<12)
		{
			//fehlenden Elemente auffüllen
			$c=count($s);
			array_pop($s);
			for ($i=$c-1; $i<12; $i++)
			{
				$s[]='';
			}
		}
		$statusline['tables']=$s[1];
		$statusline['records']=$s[2];
		$statusline['part']=($s[3]==''||$s[3]=='MP_0') ? 'MP_0' : $s[3];
		$statusline['dbname']=$s[4];
		$statusline['script']=$s[5];
		$statusline['scriptversion']=$s[6];
		$statusline['comment']=$s[7];
		$statusline['mysqlversion']=$s[8];
		$statusline['flags']=$s[9];
		$statusline['sqlbefore']=$s[10];
		$statusline['sqlafter']=$s[11];
		if ((isset($s[12]))&&trim($s[12])!='EXTINFO') $statusline['charset']=$s[12];
		else
			$statusline['charset']='?';
	}

	//flags zerlegen
	if (strlen($statusline['flags'])<6) $statusline['flags']="2222222";
	$statusline['complete_inserts']=substr($statusline['flags'],0,1);
	$statusline['extended_inserts']=substr($statusline['flags'],1,1);
	$statusline['ignore_inserts']=substr($statusline['flags'],2,1);
	$statusline['delayed_inserts']=substr($statusline['flags'],3,1);
	$statusline['downgrade']=substr($statusline['flags'],4,1);
	$statusline['lock_tables']=substr($statusline['flags'],5,1);
	$statusline['optimize_tables']=substr($statusline['flags'],6,1);
	return $statusline;
}

function NextPart($s, $first=0, $keep_suffix=false)
{
	$nf=explode('_',$s);
	$i=array_search('part',$nf)+1;
	$p=substr($nf[$i],0,strpos($nf[$i],'.'));
	$ext=substr($nf[$i],strlen($p));
	if ($first==1)
	{
		$nf[$i]='1'.$ext;
	}
	else
	{
		$nf[$i]=++$p.$ext;
	}
	$filename=implode('_',$nf);
	return $filename;
}

function zeit_format($t)
{
	global $lang;
	$tt_m=floor($t/60);
	$tt_s=$t-($tt_m*60);
	if ($tt_m<1) return floor($tt_s).' '.$lang['L_SECONDS'];
	else if ($tt_m==1) return '1 '.$lang['L_MINUTE'].' '.floor($tt_s).' '.$lang['L_SECONDS'];
	else return $tt_m.' '.$lang['L_MINUTES'].' '.floor($tt_s).' '.$lang['L_SECONDS'];
}

function TesteFTP($i)
{
	global $lang,$config;
	if (!isset($config['ftp_timeout'][$i])) $config['ftp_timeout'][$i]=30;
	$s='';
	if ($config['ftp_port'][$i]==''||$config['ftp_port'][$i]==0) $config['ftp_port'][$i]=21;
	$pass=-1;
	if (!extension_loaded("ftp"))
	{
		$s='<br><span class="error">'.$lang['L_NOFTPPOSSIBLE'].'</span>';
	}
	else
		$pass=0;

	if ($pass==0)
	{
		if ($config['ftp_server'][$i]==''||$config['ftp_user'][$i]=='')
		{
			$s='<br><span class="error">'.$lang['L_WRONGCONNECTIONPARS'].'</span>';
		}
		else
			$pass=1;
	}

	if ($pass==1)
	{
		$s=$lang['L_CONNECT_TO'].' `'.$config['ftp_server'][$i].'` Port '.$config['ftp_port'][$i];

		if ($config['ftp_useSSL'][$i]==0)
		{
			$conn_id=@ftp_connect($config['ftp_server'][$i],$config['ftp_port'][$i],$config['ftp_timeout'][$i]);
		}
		else
		{
			$conn_id=@ftp_ssl_connect($config['ftp_server'][$i],$config['ftp_port'][$i],$config['ftp_timeout'][$i]);
		}
		if ($conn_id) $login_result=@ftp_login($conn_id,$config['ftp_user'][$i],$config['ftp_pass'][$i]);
		if (!$conn_id||(!$login_result))
		{
			$s.='<br><span class="error">'.$lang['L_CONN_NOT_POSSIBLE'].'</span>';
		}
		else
		{
			$pass=2;
			if ($config['ftp_mode'][$i]==1) ftp_pasv($conn_id,true);
		}
	}

	if ($pass==2)
	{
		$s.='<br><strong>Login ok</strong><br>'.$lang['L_CHANGEDIR'].' `'.$config['ftp_dir'][$i].'` ';
		$dirc=@ftp_chdir($conn_id,$config['ftp_dir'][$i]);
		if (!$dirc)
		{
			$s.='<br><span class="error">'.$lang['L_CHANGEDIRERROR'].'</span>';
		}
		else
		{
			$pass=3;
			$s.='<span class="success">'.$lang['L_OK'].'</span>';
		}
		@ftp_close($conn_id);
	}

	if ($pass==3) $s.='<br><strong>'.$lang['L_FTP_OK'].'</strong>';
	return $s;
}

function Realpfad($p)
{
	global $config;
	$dir=dirname(__FILE__);
	$dir=str_replace('inc','',$dir);
	$dir=str_replace('\\','/',$dir);
	$dir=str_replace('//','/',$dir);
	if (substr($dir,-1)!='/') $dir.='/';
	return $dir;
}

// liest die Dateiliste aller vorhanden Konfigurationsfiles
function get_config_filelist()
{
	global $config;
	$default=$config['config_file'];
	clearstatcache();
	$dh=opendir($config['paths']['config']);
	$r="";
	while (false!==($filename=readdir($dh)))
	{
		if ($filename!="."&&$filename!=".."&&!is_dir($config['paths']['config'].$filename)&&substr($filename,-9)==".conf.php")
		{
			$f=substr($filename,0,strlen($filename)-9);
			$r.='<option value="'.$f.'" ';
			if ($f==$default) $r.=' selected';
			$r.='>&nbsp;&nbsp;'.$f.'&nbsp;&nbsp;</option>'."\n";
		}
	}
	return $r;
}

function GetThemes()
{
	global $config;
	$default=$config['theme'];
	$dh=opendir($config['paths']['root']."css/");
	$r="";
	while (false!==($filename=readdir($dh)))
	{
		if ($filename!="."&&$filename!=".."&&is_dir($config['paths']['root']."css/".$filename)&&substr($filename,0,1)!='.'&&substr($filename,0,1)!='_')
		{

			$r.='<option value="'.$filename.'" ';
			if ($filename==$default) $r.=' SELECTED';
			$r.='>&nbsp;&nbsp;'.$filename.'&nbsp;&nbsp;</option>'."\n";
		}
	}
	return $r;
}

function GetLanguageCombo($k="op", $class="", $name="", $start="", $end="")
{
	global $config,$lang;
	$default=$config['language'];
	$dh=opendir($config['paths']['root']."language/");
	$r="";
	$lang_files=array();
	while (false!==($filename=readdir($dh)))
	{
		if ($filename!="."&&$filename!='.svn'&&$filename!=".."&&$filename!="flags"&&is_dir($config['paths']['root']."language/".$filename))
		{
			$lang_files[$lang[$filename]]=$filename;
		}
	}
	ksort($lang_files);
	$i=1;
	foreach ($lang_files as $filename)
	{
		if ($k=="op")
		{
			$r.=$start.'<option value="'.$filename.'" ';
			if ($filename==$default) $r.=' SELECTED';
			$r.=' class="'.$class.'">&nbsp;&nbsp;'.$lang[$filename].'&nbsp;&nbsp;</option>'.$end."\n";
		}
		elseif ($k=="radio")
		{
			$r.=$start.'<input type="radio" class="'.$class.'" name="'.$name.'" id="l'.$i.'" value="'.$filename.'" ';
			$r.=(($filename==$default) ? "checked" : "");
			$r.=' onclick="show_tooldivs(\''.$filename.'\');">';
			$r.='<label for="l'.$i.'">';
			$r.='&nbsp;<img src="language/flags/'.$filename.'.gif" alt="" width="25" height="15" border="0">';
			$r.='&nbsp;&nbsp;&nbsp;'.$lang[$filename].'</label>'.$end."\n";
		}
		$i++;
	}
	return $r;
}

// detect language subdirs and add them to the global definition of $lang
function GetLanguageArray()
{
	global $config,$lang;
	$dh=opendir($config['paths']['root']."language/");
	unset($lang['languages']);
	$lang['languages']=array();
	while (false!==($filename=readdir($dh)))
	{
		if ($filename!="."&&$filename!='.svn'&&$filename!=".."&&$filename!="flags"&&is_dir($config['paths']['root']."language/".$filename))
		{
			$lang['languages'][]=$filename;
		}
	}
}

function headline($title, $mainframe=1)
{
	global $config,$lang;
	$s='';
	if ($config['interface_server_caption']==1)
	{
		if ($config['interface_server_caption_position']==$mainframe)
		{
			$s.='<div id="server'.$mainframe.'">'.$lang['L_SERVER'].': <a class="server" href="'.getServerProtocol().$_SERVER['SERVER_NAME'].'" target="_blank" title="'.$_SERVER['SERVER_NAME'].'">'.$_SERVER['SERVER_NAME'].'</a></div>';
		}
	}
	if ($mainframe==1)
	{
		$s.='<div id="pagetitle">'.$title.'</div>';
		$s.='<div id="content">';
	}
	return $s;
}

function PicCache($rpath='./')
{
	global $BrowserIcon,$config;

	$t='<div style="display:none">';

	$dh=opendir($config['files']['iconpath']);
	while (false!==($filename=readdir($dh)))
	{
		if ($filename!="."&&$filename!=".."&&!is_dir($config['files']['iconpath'].$filename))
		{
			$t.='<img src="'.$config['files']['iconpath'].$filename.'" width="16" height="16" alt="">'."\n";
		}
	}
	$t.='</div>';
	return $t;
}

function MSDHeader($kind=0)
{
	global $config;
	header('Pragma: no-cache');
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: -1"); // Datum in der Vergangenheit
	header('Content-Type: text/html; charset=UTF-8');

	//kind 0=main 1=menu
	$r='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'."\n<html>\n<head>\n";
	$r.='<META HTTP-EQUIV="Pragma" CONTENT="no-cache">'."\n";
	$r.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";

	$r.='<title>MySqlDumper</title>'."\n";
	$r.='<link rel="stylesheet" type="text/css" href="css/'.$config['theme'].'/style.css">'."\n";
	$r.='<script language="JavaScript" src="js/script.js" type="text/javascript"></script>'."\n";
	$r.="</head>\n<body".(($kind==1) ? ' class="menu-frame"' : ' class="content"').'>';
	return $r;
}

function MSDFooter($rfoot='', $enddiv=1)
{
	/*
	global $config,$databases,$dump,$restore,$lang;
	if (isset($config['homepage']))
	{
		$f='<p id="footer">Autor: <a href="http://www.mysqldumper.de" target="_blank">
		Daniel Schlichtholz</a> | Infoboard:
		<a href="'.$config['homepage'].'" target="_blank">'.$config['homepage'].'</a></p>';
	}
	else
	*/
		$f='';
	if ($enddiv==1) $f.='</div>';

	$f.=$rfoot.'</body></html>';

	return $f;
}

function save_bracket($str)
{
	// Wenn Klammer zu am Ende steht, diese behalten
	$str=trim($str);
	if (substr($str,-1)==')') $str=')';
	else
		$str='';
	return $str;
}

function DownGrade($s, $show=true)
{
	$tmp=explode(",",$s);
	//echo "<pre>";print_r($tmp);echo "</pre>";


	for ($i=0; $i<count($tmp); $i++)
	{
		$t=strtolower($tmp[$i]);

		if (strpos($t,"collate "))
		{
			$tmp2=explode(" ",$tmp[$i]);
			for ($j=0; $j<count($tmp2); $j++)
			{
				if (strtolower($tmp2[$j])=="collate")
				{
					$tmp2[$j]="";
					$tmp2[$j+1]=save_bracket($tmp2[$j+1]);
					$j++;
				}
			}
			$tmp[$i]=implode(" ",$tmp2);
		}

		if (strpos($t,"engine="))
		{
			$tmp2=explode(" ",$tmp[$i]);
			for ($j=0; $j<count($tmp2); $j++)
			{
				if (substr(strtoupper($tmp2[$j]),0,7)=="ENGINE=") $tmp2[$j]="TYPE=".substr($tmp2[$j],7,strlen($tmp2[$j])-7);
				if (substr(strtoupper($tmp2[$j]),0,8)=="CHARSET=")
				{
					$tmp2[$j]="";
					$tmp2[$j-1]=save_bracket($tmp2[$j-1]);
				}
				if (substr(strtoupper($tmp2[$j]),0,8)=="COLLATE=")
				{
					$tmp2[$j]=save_bracket($tmp2[$j]);
					$tmp2[$j-1]="";
				}
			}
			$tmp[$i]=implode(" ",$tmp2);
		}

		// character Set sprache  entfernen
		if (strpos($t,"character set"))
		{
			$tmp2=explode(" ",$tmp[$i]);
			$end=false;

			for ($j=0; $j<count($tmp2); $j++)
			{
				if (strtolower($tmp2[$j])=="character")
				{
					$tmp2[$j]='';
					$tmp2[$j+1]=save_bracket($tmp2[$j+1]);
					$tmp2[$j+2]=save_bracket($tmp2[$j+2]);
				}
			}
			$tmp[$i]=implode(" ",$tmp2);
		}

		if (strpos($t,"timestamp"))
		{
			$tmp2=explode(" ",$tmp[$i]);
			$end=false;

			for ($j=0; $j<count($tmp2); $j++)
			{
				if ($end) $tmp2[$j]="";
				if (strtolower($tmp2[$j])=="timestamp")
				{
					$tmp2[$j]="TIMESTAMP(14)";
					$end=true;
				}
			}
			$tmp[$i]=implode(" ",$tmp2);
		}
	}
	$t=implode(",",$tmp);
	if (substr(rtrim($t),-1)!=";") $t=rtrim($t).";";
	return $t;
}

function MySQL_Ticks($s)
{
	$klammerstart=$lastklammerstart=$end=0;
	$inner_s_start=strpos($s,'(');
	$inner_s_end=strrpos($s,')');
	$inner_s=substr($s,$inner_s_start+1,$inner_s_end-(1+$inner_s_start));
	$pieces=explode(',',$inner_s);
	for ($i=0; $i<count($pieces); $i++)
	{
		$r=trim($pieces[$i]);
		$klammerstart+=substr_count($r,"(")-substr_count($r,")");
		if ($i==count($pieces)-1) $klammerstart+=1;
		if (substr(strtoupper($r),0,4)=="KEY "||substr(strtoupper($r),0,7)=="UNIQUE "||substr(strtoupper($r),0,12)=="PRIMARY KEY "||substr(strtoupper($r),0,13)=="FULLTEXT KEY ")
		{
			//nur ein Key
			$end=1;
		}
		else
		{
			if (substr($r,0,1)!='`'&&substr($r,0,1)!='\''&&$klammerstart==0&&$end==0&&$lastklammerstart==0)
			{
				$pos=strpos($r,' ');
				$r='`'.substr($r,0,$pos).'`'.substr($r,$pos);
			}
		}
		$pieces[$i]=$r;
		$lastklammerstart=$klammerstart;
	}
	$back=substr($s,0,$inner_s_start+1).implode(',',$pieces).');';
	return $back;
}

/**
 * Convert all array elements to UTF-8
 *
 * @param $array
 * @return array
 */
function convert_to_utf8($obj)
{
	global $config;
	$ret=$obj;
	// wenn die Verbindung zur Datenbank nicht auf utf8 steht, dann muessen die Rückgaben in utf8 gewandelt werden,
	// da die Webseite utf8-kodiert ist
	if (!isset($config['mysql_can_change_encoding'])) get_sql_encodings();

	if ($config['mysql_can_change_encoding']==false&&$config['mysql_standard_character_set']!='utf8')
	{
		if (is_array($obj))
		{
			foreach ($obj as $key=>$val)
			{
				//echo "<br> Wandle " . $val . " nach ";
				$obj[$key]=utf8_encode($val);
				//echo $obj[$key];
			}
		}
		if (is_string($obj)) $obj=utf8_encode($obj);
		$ret=$obj;
	}
	return $ret;
}

/**
 * Convert all array elements to Latin1
 *
 * @param $array
 * @return array
 */
function convert_to_latin1($obj)
{
	global $config;
	$ret=$obj;
	// wenn die Verbindung zur Datenbank nicht auf utf8 steht, dann muessen die Rückgaben in utf8 gewandelt werden,
	// da die Webseite utf8-kodiert ist
	if ($config['mysql_can_change_encoding']==false&&$config['mysql_standard_character_set']!='utf8')
	{
		if (is_array($obj))
		{
			foreach ($obj as $key=>$val)
			{
				$obj[$key]=utf8_decode($val);
			}
		}
		if (is_string($obj)) $obj=utf8_decode($obj);
		$ret=$obj;
	}
	return $ret;
}

// returns the index of the selected val in an optionlist
function get_index($arr, $selected)
{
	$ret=false; // return false if not found
	foreach ($arr as $key=>$val)
	{
		if (strtolower(substr($val,0,strlen($selected)))==strtolower($selected))
		{
			$ret=$key;
			break;
		}
	}
	return $ret;
}

/**
 * Check if config is readable
 *
 * @param $file
 * @return boolean
 */
function read_config($file=false)
{
	global $config,$databases;
	$ret=false;
	if (!$file) $file=$config['config_file'];
	// protect from including external files
	$search=array(':', 'http', 'ftp', ' ');
	$replace=array('', '', '', '');
	$file=str_replace($search,$replace,$file);

	if (is_readable($config['paths']['config'].$file.'.php'))
	{
		// to prevent modern server from caching the new configuration we need to evaluate it this way
		clearstatcache();
		$f=implode('',file($config['paths']['config'].$file.'.php'));
		$f=str_replace('<?php','',$f);
		$f=str_replace('?>','',$f);
		eval($f);
		$config['config_file']=$file;
		$_SESSION['config_file']=$config['config_file'];
		$ret=true;
	}
	return $ret;
}

/**
 * Get all work configurations from /work/config directory
 *
 * @return array
 */
function get_config_filenames()
{
	global $config;
	$configs=array();
	$dh=opendir($config['paths']['config']."/");
	while (false!==($filename=readdir($dh)))
	{
		if (substr($filename,-4)=='.php'&&substr($filename,-9)!='.conf.php'&&$filename!='dbs_manual.php')
		{
			$configs[]=substr($filename,0,-4);
		}
	}
	return $configs;
}

function table_output($text, $val, $small=false, $colspan=1)
{
	$ret='<tr>';
	$ret.='<td nowrap="nowrap"';
	if ($colspan>1) $ret.=' colspan="'.$colspan.'"';
	$ret.='>'.$text;
	if ($colspan==1) $ret.=': ';
	else
		$ret.='&nbsp;';
	if ($colspan==1) $ret.='</td><td nowrap="nowrap">';
	if ($small) $ret.='<span class="small">'.$val.'</span></td></tr>';
	else
		$ret.='<strong>'.$val.'</strong></td></tr>';
	return $ret;
}

/**
 * Receive all possible MySQL character sets and save standard to $config['mysql_standard_charset']
 */
function get_sql_encodings()
{
	global $config;
	unset($config['mysql_possible_character_sets']);
	if (!isset($config['dbconnection'])) MSD_mysql_connect();
	$erg=false;
	$config['mysql_standard_character_set']='';
	$config['mysql_possible_character_sets']=array();

	if (!defined('MSD_MYSQL_VERSION')) GetMySQLVersion();
	$v=explode('.',MSD_MYSQL_VERSION);
	$config['mysql_can_change_encoding']=false;
	if (($v[0]<=4&&$v[1]<1)||$v[0]<=3)
	{
		// MySQL < 4.1
		$config['mysql_can_change_encoding']=false;
		$sqlt='SHOW VARIABLES LIKE \'character_set%\'';
		$res=MSD_query($sqlt) or die(SQLError($sqlt,mysql_error()));
		if ($res)
		{
			WHILE ($row=mysql_fetch_row($res))
			{
				if ($row[0]=='character_set')
				{
					$config['mysql_standard_character_set']=$row[1];
					if ($v[0]==3) $config['mysql_possible_character_sets'][0]=$row[1];
				}

				if ($row[0]=='character_sets'&&$v[0]>3)
				{
					$config['mysql_possible_character_sets']=explode(' ',$row[1]);
					sort($config['mysql_possible_character_sets']);
				}
			}
		}
	}
	else
	{
		// MySQL-Version >= 4.1
		$config['mysql_can_change_encoding']=true;
		$sqlt='SHOW CHARACTER SET';
		$res=MSD_query($sqlt) or die(SQLError($sqlt,mysql_error()));

		if ($res)
		{
			WHILE ($row=mysql_fetch_row($res))
			{
				$config['mysql_possible_character_sets'][]=$row[0].' - '.$row[1];
			}
			sort($config['mysql_possible_character_sets']);
		}

		$sqlt='SHOW VARIABLES LIKE \'character_set_connection\'';
		$res=MSD_query($sqlt) or die(SQLError($sqlt,mysql_error()));

		if ($res)
		{
			WHILE ($row=mysql_fetch_row($res))
			{
				$config['mysql_standard_character_set']=$row[1];
			}
		}
	}
}

/**
 * Un-quotes a quoted string/array
 *
 * @param $value
 * @return string/array
 */
function stripslashes_deep($value)
{
	$value=is_array($value) ? array_map('stripslashes_deep',$value) : stripslashes($value);
	return $value;
}

/**
 * Remove whitespaces before and after an string or array
 *
 * @param $value
 * @return string/array
 */
function trim_deep($value)
{
	$value=is_array($value) ? array_map('trim_deep',$value) : trim($value);
	return $value;
}

/**
 * load external source from given URL and save content locally
 *
 * loads content from an external URL and saves it locally in $path with the name $local_file
 * return false on failure or true on success
 *
 * @param $url
 * @param $file
 * @param local_file
 * @param $path
 * @return boolean
 */
function fetchFileFromURL($url, $file, $local_path='./data/',$local_file)
{
	$data=fetchFileDataFromURL($url.$file);
	if ($data)
	{
		$d=fopen($local_path.$local_file,"wb");
		$ret=fwrite($d,$data);
		fclose($d);
		return $ret;
	}
	return false;
}

/**
 * Loads data from an external source via HTTP-socket
 *
 * Loads data from an external source $url given as URL
 * and returns the content as a binary string or an empty string on failure
 *
 * @param $url
 * @return string file data
 */
function fetchFileDataFromURL($url)
{
	$url_parsed=parse_url($url);
	$in='';

	$host=$url_parsed['host'];
	$port=isset($url_parsed['port']) ? intval($url_parsed['port']) : 80;
	if ($port==0) $port=80;
	$path=$url_parsed['path'];
	if (isset($url_parsed['query'])&&$url_parsed['query']!='') $path.='?'.$url_parsed['query'];

	$fp=fsockopen($host,$port,$errno,$errstr,3);
	if ($fp)
	{
		$out="GET $path HTTP/1.1\r\nHost: $host\r\n";
		$out.="Connection: close\r\n\r\n";
		fwrite($fp,$out);
		$body=false;
		while (!feof($fp))
		{
			$s=fgets($fp,1024);
			if ($body) $in.=$s;
			if ($s=="\r\n") $body=true;
		}

		fclose($fp);
	}
	return $in;
}

?>