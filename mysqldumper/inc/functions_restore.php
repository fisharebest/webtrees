<?php
define('DEBUG',0);
if (!defined('MSD_VERSION')) die('No direct access.');
function get_sqlbefehl()
{
	global $restore,$config,$databases,$lang;

	//Init
	$restore['fileEOF']=false;
	$restore['EOB']=false;
	$complete_sql='';
	$sqlparser_status=0;
	if (!isset($restore['eintraege_ready'])) $restore['eintraege_ready']=0;

	//Parsen
	WHILE ($sqlparser_status!=100&&!$restore['fileEOF']&&!$restore['EOB'])
	{
		//nächste Zeile lesen
		$zeile=($restore['compressed']) ? gzgets($restore['filehandle']):fgets($restore['filehandle']);
		if (DEBUG) echo "<br><br>Zeile: ".htmlspecialchars($zeile);
		/******************* Setzen des Parserstatus *******************/
		// herausfinden um was für einen Befehl es sich handelt
		if ($sqlparser_status==0)
		{

			//Vergleichszeile, um nicht bei jedem Vergleich strtoupper ausführen zu müssen
			$zeile2=strtoupper(trim($zeile));
			// pre-built compare strings - so we need the CPU power only once :)
			$sub9=substr($zeile2,0,9);
			$sub7=substr($sub9,0,7);
			$sub6=substr($sub7,0,6);
			$sub4=substr($sub6,0,4);
			$sub3=substr($sub4,0,3);
			$sub2=substr($sub3,0,2);
			$sub1=substr($sub2,0,1);

			if ($sub7=='INSERT ')
			{
				$sqlparser_status=3; //Datensatzaktion
				$restore['actual_table']=get_tablename($zeile);
			}

			//Einfache Anweisung finden die mit Semikolon beendet werden
			elseif ($sub7=='LOCK TA') $sqlparser_status=4;
			elseif ($sub6=='COMMIT') $sqlparser_status=7;
			elseif (substr($sub6,0,5)=='BEGIN') $sqlparser_status=7;
			elseif ($sub9=='UNLOCK TA') $sqlparser_status=4;
			elseif ($sub3=='SET') $sqlparser_status=4;
			elseif ($sub6=='START ') $sqlparser_status=4;
			elseif ($sub3=='/*!') $sqlparser_status=5; //MySQL-Condition oder Kommentar
			elseif ($sub9=='ALTER TAB') $sqlparser_status=4; // Alter Table
			elseif ($sub9=='CREATE TA') $sqlparser_status=2; //Create Table
			elseif ($sub9=='CREATE AL') $sqlparser_status=2; //Create View
			elseif ($sub9=='CREATE IN') $sqlparser_status=4; //Indexaktion


			//Condition?
			elseif (($sqlparser_status!=5)&&(substr($zeile2,0,2)=='/*')) $sqlparser_status=6;

			// Delete actions
			elseif ($sub9=='DROP TABL') $sqlparser_status=1;
			elseif ($sub9=='DROP VIEW') $sqlparser_status=1;

			// Befehle, die nicht ausgeführt werden sollen
			elseif ($sub9=='CREATE DA') $sqlparser_status=7;
			elseif ($sub9=='DROP DATA ') $sqlparser_status=7;
			elseif ($sub3=='USE') $sqlparser_status=7;

			// Am Ende eines MySQLDumper-Backups angelangt?
			elseif ($sub6=='-- EOB'||$sub4=='# EO')
			{
				$restore['EOB']=true;
				$restore['fileEOF']=true;
				$zeile='';
				$zeile2='';
				$sqlparser_status=100;
			}

			// Kommentar?
			elseif ($sub2=='--'|| $sub1=='#')
			{
				$zeile='';
				$zeile2='';
				$sqlparser_status=0;
			}

			// Fortsetzung von erweiterten Inserts
			if ($restore['flag']==1) $sqlparser_status=3;

			if (($sqlparser_status==0)&&(trim($complete_sql)>'')&&($restore['flag']==-1))
			{
				// Unbekannten Befehl entdeckt
				v($restore);
				echo "<br>Sql: ".htmlspecialchars($complete_sql);
				echo "<br>Erweiterte Inserts: ".$restore['erweiterte_inserts'];
				die('<br>'.$lang['L_UNKNOWN_SQLCOMMAND'].': '.$zeile.'<br><br>'.$complete_sql);
			}
		/******************* Ende von Setzen des Parserstatus *******************/
		}

		$last_char=substr(rtrim($zeile),-1);
		// Zeilenumbrüche erhalten - sonst werden Schlüsselwörter zusammengefügt
		// z.B. 'null' und in der nächsten Zeile 'check' wird zu 'nullcheck'
		$complete_sql.=$zeile."\n";

		if ($sqlparser_status==3)
		{
			//INSERT
			if (SQL_Is_Complete($complete_sql))
			{
				$sqlparser_status=100;
				$complete_sql=trim($complete_sql);
				if (substr($complete_sql,-2)=='*/')
				{
					$complete_sql=remove_comment_at_eol($complete_sql);
				}

				// letzter Ausdruck des erweiterten Inserts erreicht?
				if (substr($complete_sql,-2)==');')
				{
					$restore['flag']=-1;
				}

				// Wenn am Ende der Zeile ein Klammer Komma -> erweiterter Insert-Modus -> Steuerflag setzen
				else
					if (substr($complete_sql,-2)=='),')
					{
						// letztes Komme gegen Semikolon tauschen
						$complete_sql=substr($complete_sql,0,-1).';';
						$restore['erweiterte_inserts']=1;
						$restore['flag']=1;
					}

				if (substr(strtoupper($complete_sql),0,7)!='INSERT ')
				{
					// wenn der Syntax aufgrund eines Reloads verloren ging - neu ermitteln
					if (!isset($restore['insert_syntax'])) $restore['insert_syntax']=get_insert_syntax($restore['actual_table']);
					$complete_sql=$restore['insert_syntax'].' VALUES '.$complete_sql.';';
				}
				else
				{
					// INSERT Syntax ermitteln und merken
					$ipos=strpos(strtoupper($complete_sql),' VALUES');
					if (!$ipos===false) $restore['insert_syntax']=substr($complete_sql,0,$ipos);
					else
						$restore['insert_syntax']='INSERT INTO `'.$restore['actual_table'].'`';
				}
			}
		}

		else
			if ($sqlparser_status==1)
			{
				//Löschaktion
				if ($last_char==';') $sqlparser_status=100; //Befehl komplett
				$restore['actual_table']=get_tablename($complete_sql);
			}

			else
				if ($sqlparser_status==2)
				{
					// Createanweisung ist beim Finden eines ; beendet
					if ($last_char==';')
					{
						if ($config['minspeed']>0) $restore['anzahl_zeilen']=$config['minspeed'];
						// Soll die Tabelle hergestellt werden?
						$do_it=true;
						if (is_array($restore['tables_to_restore']))
						{
							$do_it=false;
							if (in_array($restore['actual_table'],$restore['tables_to_restore']))
							{
								$do_it=true;
							}
						}
						if ($do_it)
						{
							$tablename=submit_create_action($complete_sql);
							$restore['actual_table']=$tablename;
							$restore['table_ready']++;
						}
						// Zeile verwerfen, da CREATE jetzt bereits ausgefuehrt wurde und naechsten Befehl suchen
						$complete_sql='';
						$sqlparser_status=0;
					}
				}

				// Index
				else
					if ($sqlparser_status==4)
					{ //Createindex
						if ($last_char==';')
						{
							if ($config['minspeed']>0)
							{
								$restore['anzahl_zeilen']=$config['minspeed'];
							}
							$complete_sql=del_inline_comments($complete_sql);
							$sqlparser_status=100;
						}
					}

					// Kommentar oder Condition
					else
						if ($sqlparser_status==5)
						{ //Anweisung
							$t=strrpos($zeile,'*/;');
							if (!$t===false)
							{
								$restore['anzahl_zeilen']=$config['minspeed'];
								$sqlparser_status=100;
								if ($config['ignore_enable_keys'] &&
								    strrpos($zeile, 'ENABLE KEYS ') !== false)
								{
                                    $sqlparser_status=100;
								    $complete_sql = '';
								}
							}
						}

						// Mehrzeiliger oder Inline-Kommentar
						else
							if ($sqlparser_status==6)
							{
								$t=strrpos($zeile,'*/');
								if (!$t===false)
								{
									$complete_sql='';
									$sqlparser_status=0;
								}
							}

							// Befehle, die verworfen werden sollen
							else
								if ($sqlparser_status==7)
								{ //Anweisung
									if ($last_char==';')
									{
										if ($config['minspeed']>0)
										{
											$restore['anzahl_zeilen']=$config['minspeed'];
										}
										$complete_sql='';
										$sqlparser_status=0;
									}
								}

		if (($restore['compressed'])&&(gzeof($restore['filehandle']))) $restore['fileEOF']=true;
		if ((!$restore['compressed'])&&(feof($restore['filehandle']))) $restore['fileEOF']=true;
	}
	// wenn bestimmte Tabellen wiederhergestellt werden sollen -> pruefen
	if (is_array($restore['tables_to_restore'])&&!(in_array($restore['actual_table'],$restore['tables_to_restore'])))
	{
		$complete_sql='';
	}
	return trim($complete_sql);
}

function submit_create_action($sql)
{
	//executes a create command
	$tablename=get_tablename($sql);
	if (strtoupper(substr($sql,0,16))=='CREATE ALGORITHM')
	{
		// It`s a VIEW. We need to substitute the original DEFINER with the actual MySQL-User
		$parts=explode(' ',$sql);
		for ($i=0,$count=sizeof($parts);$i<$count;$i++)
		{
			if (strtoupper(substr($parts[$i],0,8))=='DEFINER=')
			{
				global $config;
				$parts[$i]='DEFINER=`'.$config['dbuser'].'`@`'.$config['dbhost'].'`';
				$sql=implode(' ',$parts);
				$i=$count;
			}
		}
	}

	$res=@mysql_query($sql);
	if ($res===false)
	{
		// erster Versuch fehlgeschlagen -> zweiter Versuch - vielleicht versteht der Server die Inline-Kommentare nicht?
		$sql=del_inline_comments($sql);
		$res=@mysql_query(downgrade($sql));
		if ($res===false)
		{
			// wieder nichts. Ok, haben wir hier einen alten MySQL-Server 3.x oder 4.0.x?
			// versuchen wir es mal mit der alten Syntax
			$res=@mysql_query(downgrade($sql));
		}
	}
	if ($res===false)
	{
		// wenn wir hier angekommen sind hat nichts geklappt -> Fehler ausgeben und abbrechen
		SQLError($sql,mysql_error());
		die("<br>Fatal error: Couldn't create table or view `".$tablename."´");
	}
	return $tablename;
}

function get_insert_syntax($table)
{
	$insert='';
	$sql='SHOW COLUMNS FROM `'.$table.'`';
	$res=mysql_query($sql);
	if ($res)
	{
		$insert='INSERT INTO `'.$table.'` (';
		while ($row=mysql_fetch_object($res))
		{
			$insert.='`'.$row->Field.'`,';
		}
		$insert=substr($insert,0,strlen($insert)-1).') ';
	}
	else
	{
		global $restore;
		v($restore);
		SQLError($sql,mysql_error());
	}
	return $insert;
}

function del_inline_comments($sql)
{
	//$sql=str_replace("\n",'<br>',$sql);
	$array=array();
	preg_match_all("/(\/\*(.+)\*\/)/U",$sql,$array);
	if (is_array($array[0]))
	{
		$sql=str_replace($array[0],'',$sql);
		if (DEBUG) echo "Nachher: :<br>".$sql."<br><hr>";
	}
	//$sql=trim(str_replace('<br>',"\n",$sql));
	//Wenn nach dem Entfernen nur noch ein ; übrigbleibt -> entfernen
	if ($sql==';') $sql='';
	return $sql;
}

// extrahiert auf einfache Art den Tabellennamen aus dem "Create",Drop"-Befehl
function get_tablename($t)
{
	// alle Schluesselbegriffe entfernen, bis der Tabellenname am Anfang steht
	$t=substr($t,0,150); // verkuerzen, um Speicher zu sparen - wir brauchenhier nur den Tabellennamen
	$t=str_ireplace('DROP TABLE','',$t);
	$t=str_ireplace('DROP VIEW','',$t);
	$t=str_ireplace('CREATE TABLE','',$t);
	$t=str_ireplace('INSERT INTO','',$t);
	$t=str_ireplace('REPLACE INTO','',$t);
	$t=str_ireplace('IF NOT EXISTS','',$t);
	$t=str_ireplace('IF EXISTS','',$t);
	if (substr(strtoupper($t),0,16)=='CREATE ALGORITHM')
	{
		$pos=strpos($t,'DEFINER VIEW ');
		$t=substr($t,$pos,strlen($t)-$pos);
	}
	$t=str_ireplace(';',' ;',$t); // tricky -> insert space as delimiter
	$t=trim($t);

	// jetzt einfach nach dem ersten Leerzeichen suchen
	$delimiter=substr($t,0,1);
	if ($delimiter!='`') $delimiter=' ';
	$found=false;
	$position=1;
	WHILE (!$found)
	{
		if (substr($t,$position,1)==$delimiter) $found=true;
		if ($position>=strlen($t)) $found=true;
		$position++;
	}
	$t=substr($t,0,$position);
	$t=trim(str_replace('`','',$t));
	return $t;
}

// decide if an INSERT-Command is complete - simply count quotes and look for ); at the end of line
function SQL_Is_Complete($string)
{
	$string=str_replace('\\\\','',trim($string)); // trim and remove escaped backslashes
	$string=trim($string);
	$quotes=substr_count($string,'\'');
	$escaped_quotes=substr_count($string,'\\\'');
	if (($quotes-$escaped_quotes)%2==0)
	{
		$compare=substr($string,-2);
		if ($compare=='*/') $compare=substr(trim(remove_comment_at_eol($string)),-2);
		if ($compare==');') return true;
		if ($compare=='),') return true;
	}
	return false;
}

function remove_comment_at_eol($string)
{
	// check for Inline-Comments at the end of the line
	if (substr(trim($string),-2)=='*/')
	{
		$pos=strrpos($string,'/*');
		if ($pos>0)
		{
			$string=trim(substr($string,0,$pos));
		}
	}
	return $string;
}
