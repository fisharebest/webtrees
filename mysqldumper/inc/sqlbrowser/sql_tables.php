<?php
if (!defined('MSD_VERSION')) die('No direct access.');
//Tabellen
echo $aus.'<h6>'.$lang['L_SQL_TABLESOFDB'].' `'.$databases['Name'][$dbid].'` '.$lang['L_SQL_EDIT'].'</h6>';

//Primaerschluessel loeschen
if (isset($_GET['killPrimaryKey']))
{
	$keys=getPrimaryKeys($databases['Name'][$dbid],$_GET['tablename']);
	//Zu loeschenden Schluessel aus dem Array entfernen
	$keyPos=array_search($_GET['killPrimaryKey'],$keys['name']);
	if (!(false===$keyPos))
	{
		unset($keys['name'][$keyPos]);
		unset($keys['size'][$keyPos]);
		$keys['name']=array_values($keys['name']);
		$keys['size']=array_values($keys['size']);
		$res=setNewPrimaryKeys($databases['Name'][$dbid], $_GET['tablename'], $keys['name'], $keys['size']);
		if ($res)
		{
			echo '<script language="JavaScript">
						alert("'.$lang['L_PRIMARYKEY_DELETED'].': '.$_GET['killPrimaryKey'].'");
					</script>';
		}
		else
		{
			echo '<script language="JavaScript">
						alert("'.$lang['L_PRIMARYKEYS_CHANGINGERROR'].': '.$_GET['killPrimaryKey'].'");
					</script>';
		}
	}
	else
	{
		echo '<script language="JavaScript">
					alert("'.$lang['L_PRIMARYKEY_NOTFOUND'].': '.$_GET['killPrimaryKey'].'");
				</script>';
	}
}
//Primärschlüssel löschen ende


//Neue Schlüssel setzen
if (isset($_POST['setNewKeys']))
{
	$fields=getAllFields($databases['Name'][$dbid],$_GET['tablename']);
	$newKeysArray=Array();
	$newKeySizesArray=Array();
	foreach ($fields as $index=>$field)
	{
		if ((isset($_POST["setNewKey".$index]))&&($_POST["setNewKey".$index]!=""))
		{
			$newKeysArray[]=$_POST["setNewKey".$index];
			$newKeySizesArray[]=isset($_POST["indexSize".$index]) ? (int) $_POST["indexSize".$index]:'';
		}
	}
	//doppelte Elemente entfernen
	$newKeysArray=array_unique($newKeysArray);
	$newKeySizesArray=array_intersect_key($newKeySizesArray, $newKeysArray);

	if ($_POST["indexType"]=="primary")
	{
		$res=setNewPrimaryKeys($databases['Name'][$dbid], $_GET['tablename'], $newKeysArray, $newKeySizesArray);
		if ($res)
		{
			echo '<script language="JavaScript">
						alert("'.$lang['L_PRIMARYKEYS_CHANGED'].'");
					</script>';
		}
		else
		{
			echo '<script language="JavaScript">
						alert("'.$lang['L_PRIMARYKEYS_CHANGINGERROR'].'");
					</script>';
		}
	}
	else
	{
		if ($_POST["indexType"]=="unique")
		{
			$newIndexType="UNIQUE";
		}
		elseif ($_POST["indexType"]=="fulltext")
		{
			$newIndexType="FULLTEXT";
		}
		else
		{
			$newIndexType="INDEX";
		}

		$res=setNewKeys($databases['Name'][$dbid], $_GET['tablename'], $newKeysArray, $newIndexType, $_POST['indexName'], $newKeySizesArray);
		if ($res)
		{
			echo '<script language="JavaScript">
						alert("'.$lang['L_KEY_ADDED'].'");
					</script>';
		}
		else
		{
			echo '<script language="JavaScript">
						alert("'.$lang['L_KEY_ADDERROR'].'");
					</script>';
		}
	}
}

//Andere Indizes löschen
if (isset($_GET['killIndex']))
{
	$res = killKey($databases['Name'][$dbid], $_GET['tablename'], $_GET['killIndex']);
	if ($res)
	{
		echo '<script language="JavaScript">
					alert("'.$lang['L_KEY_DELETED'].': '.$_GET['killIndex'].'");
				</script>';
	}
	else
	{
		echo '<script language="JavaScript">
					alert("'.$lang['L_KEY_DELETEERROR'].': '.$_GET['killIndex'].'");
				</script>';
	}
}

if (isset($_GET['kill']))
{
	if ($_GET['anz']==1) echo '<p class="error">'.$lang['L_SQL_NOFIELDDELETE'].'</p>';
	else
	{
		$sql_alter="ALTER TABLE `".$databases['Name'][$dbid]."`.`".$_GET['tablename']."` DROP COLUMN `".$_GET['kill']."`";
		$res = MSD_DoSQL($sql_alter);
		if ($res)
		{
			echo '<div align="left" id="sqleditbox" style="font-size: 11px;width:90%;padding=6px;">';
			echo '<p class="success">'.$lang['L_SQL_FIELDDELETE1'].' `'.$_GET['kill'].'` '.$lang['L_SQL_DELETED'].'.</p>'.highlight_sql($out).'</div>';
		}
	}
}
if (isset($_POST['tablecopysubmit']))
{
	$table_edit_name=$_GET['tablename'];
	if ($_POST['tablecopyname']=="")
	{
		echo '<p class="error">'.$lang['L_SQL_NODEST_COPY'].'</p>';
	}
	elseif (Table_Exists($databases['Name'][$dbid],$_POST['tablecopyname']))
	{
		echo '<p class="error">'.$lang['L_SQL_DESTTABLE_EXISTS'].'</p>';
	}
	else
	{
		Table_Copy("`".$databases['Name'][$dbid]."`.`".$table_edit_name."`",$_POST['tablecopyname'],$_POST['copyatt']);
		echo '<div align="left" id="sqleditbox">';
        echo ($_POST['copyatt']==0) ? '<p class="success">'.sprintf($lang['L_SQL_SCOPY'],$table_edit_name,$_POST['tablecopyname']).'</p>' : sprintf($lang['L_SQL_TCOPY'],$table_edit_name,$_POST['tablecopyname']).'</p>';		echo highlight_sql($out).'</div>';
		$tablename=$_POST['tablecopyname'];
	}
}
if (isset($_POST['newtablesubmit']))
{
	if ($_POST['newtablename']=="")
	{
		echo '<p class="error">'.$lang['L_SQL_TABLENONAME'].'</p>';
	}
	else
	{
		$sql_alter="CREATE TABLE `".$databases['Name'][$dbid]."`.`".$_POST['newtablename']."` (`id` int(11) unsigned not null AUTO_INCREMENT PRIMARY KEY ) ".((MSD_NEW_VERSION) ? "ENGINE" : "TYPE")."=MyISAM;";
		$res = MSD_DoSQL($sql_alter);
		if ($res)
		{
			echo SQLOutput($out,$lang['L_TABLE'].' `'.$_POST['newtablename'].'` '.$lang['L_SQL_CREATED']);
		}
	}
}
if (isset($_POST['t_edit_submit']))
{
	$sql_alter="ALTER TABLE `".$databases['Name'][$dbid]."`.`".$_POST['table_edit_name']."` ";
	if ($_POST['t_edit_name']=="") echo '<p class="error">'.$lang['L_SQL_TBLNAMEEMPTY'].'</p>';
	elseif (MSD_NEW_VERSION&&$_POST['t_edit_collate']!=""&&substr($_POST['t_edit_collate'],0,strlen($_POST['t_edit_charset']))!=$_POST['t_edit_charset']) echo '<p class="error">'.$lang['L_SQL_COLLATENOTMATCH'].'</p>';
	else
	{
		if ($_POST['table_edit_name']!=$_POST['t_edit_name'])
		{
			$sql_alter.="RENAME TO `".$_POST['t_edit_name']."`, ";
			$table_edit_name=$_POST['t_edit_name'];
		}
		else
			$table_edit_name=$_POST['table_edit_name'];
		if ($_POST['t_edit_engine']!="") $sql_alter.=((MSD_NEW_VERSION) ? "ENGINE=" : "TYPE=").$_POST['t_edit_engine'].", ";
		if ($_POST['t_edit_rowformat']!="") $sql_alter.="ROW_FORMAT=".$_POST['t_edit_rowformat'].", ";
		if (MSD_NEW_VERSION&&$_POST['t_edit_charset']!="") $sql_alter.="DEFAULT CHARSET=".$_POST['t_edit_charset'].", ";
		if (MSD_NEW_VERSION&&$_POST['t_edit_collate']!="") $sql_alter.="COLLATE ".$_POST['t_edit_collate'].", ";
		$sql_alter.="COMMENT='".$_POST['t_edit_comment']."' ";

		$res = MSD_DoSQL($sql_alter);
		if ($res)
		{
			echo SQLOutput($out,$lang['L_TABLE'].' `'.$_POST['table_edit_name'].'` '.$lang['L_SQL_CHANGED']);
		}
	}
}
else
{
	if (!isset($table_edit_name)||$table_edit_name=="")
	{
		$table_edit_name=(isset($_GET['tablename'])) ? $_GET['tablename'] : "";
		if (isset($_POST['tableselect'])) $table_edit_name=$_POST['tableselect'];
		if (isset($_POST['newtablesubmit'])) $table_edit_name=$_POST['newtablename'];
	}
}
if (isset($_POST['newfield_posted']))
{
	//build sql for alter
	if ($_POST['f_name']=='')
	{
		echo '<p class="error">'.$lang['L_SQL_FIELDNAMENOTVALID'].' ('.$_POST['f_name'].')</p>';
		$field_fehler=1;
	}
	else
	{
		//alter Key
		$oldkeys[0]=$_POST['f_primary'];
		$oldkeys[1]=$_POST['f_unique'];
		$oldkeys[2]=$_POST['f_index'];
		$oldkeys[3]=$_POST['f_fulltext'];
		//neuer Key
		$newkeys[0]=($_POST['f_index_new']=="primary") ? 1 : 0;
		$newkeys[1]=($_POST['f_index_new']=="unique") ? 1 : 0;
		$newkeys[2]=($_POST['f_index_new']=="index") ? 1 : 0;
		$newkeys[3]=(isset($_POST['f_indexfull'])) ? 1 : 0;

		$add_sql.=ChangeKeys($oldkeys,$newkeys,$_POST['f_name'],$_POST['f_size'],"drop_only");

		$sql_stamm="ALTER TABLE `".$databases['Name'][$dbid]."`.`$table_edit_name` ";
		$sql_alter=$sql_stamm.((isset($_POST['editfield'])) ? "CHANGE COLUMN `".$_POST['fieldname']."` `".$_POST['f_name']."` " : "ADD COLUMN `".$_POST['f_name']."` ");
		$sql_alter.=$_POST['f_type'];
		$wl=stripslashes($_POST['f_size']);
		if ($wl!=""&&!preg_match('@^(DATE|DATETIME|TIME|TINYBLOB|TINYTEXT|BLOB|TEXT|MEDIUMBLOB|MEDIUMTEXT|LONGBLOB|LONGTEXT)$@i',$_POST['f_type']))
		{
			$sql_alter.="($wl) ";
		}
		elseif ($_POST['f_size']==""&&preg_match('@^(VARCHAR)$@i',$_POST['f_type']))
		{
			$sql_alter.="("."255".") ";
		}
		else
			$sql_alter.=" ";
		$sql_alter.=$_POST['f_attribut']." ";
		$sql_alter.=$_POST['f_null']." ";
		$sql_alter.=($_POST['f_default']!="") ? "DEFAULT '".addslashes($_POST['f_default'])."' " : "";

		if (MSD_NEW_VERSION&&$_POST['f_collate']!="") $sql_alter.="COLLATE ".$_POST['f_collate']." ";

		if ($_POST['f_extra']=="AUTO_INCREMENT")
		{
			$sql_alter.=" AUTO_INCREMENT ";
		}
		$sql_alter.=$_POST['f_position'];

		if ($newkeys[0]==1) $sql_alter.=", ADD PRIMARY KEY (`".$_POST['f_name']."`)";
		if ($newkeys[1]==1) $sql_alter.=", ADD UNIQUE (`".$_POST['f_name']."`)";
		if ($newkeys[2]==1) $sql_alter.=", ADD INDEX (`".$_POST['f_name']."`)";
		if ($newkeys[3]==1) $sql_alter.=", ADD FULLTEXT INDEX (`".$_POST['f_name']."`)";

		$sql_alter.=";";

		if ($add_sql!="")
		{
			$add_sql=$sql_stamm.$add_sql;
			$sql_alter="$sql_alter\n$add_sql;";
		}
		$res = MSD_DoSQL($sql_alter);
		if ($res)
		{
			echo '<div align="left" id="sqleditbox" style="font-size: 11px;width:90%;padding=6px;">';
			echo '<p class="success"> `'.$_POST['f_name'].'` '.((isset($_POST['editfield'])) ? $lang['L_SQL_CHANGED'] : $lang['L_SQL_CREATED']).'</p>';
			echo highlight_sql($out).'</div>';
		}
		$fields_infos=getFieldinfos($databases['Name'][$dbid],$table_edit_name);
	}
}
mysql_select_db($databases['Name'][$dbid]);
$sqlt="SHOW TABLE STATUS FROM `".$databases['Name'][$dbid]."` ;";
$res=MSD_query($sqlt);
$anz_tabellen=mysql_numrows($res);
$p="sql.php?db=".$databases['Name'][$dbid]."&amp;dbid=$dbid&amp;tablename=$table_edit_name&amp;context=2";

echo '<form action="sql.php?db='.$databases['Name'][$dbid].'&amp;dbid='.$dbid.'&amp;tablename='.$table_edit_name.'&amp;context=2" method="post">';
echo '<table class="bdr"><tr class="dbrow"><td>'.$lang['L_SQL_CREATETABLE'].': </td><td colspan="2"><input type="text" class="text" name="newtablename" size="30" maxlength="150"></td><td><input type="submit" name="newtablesubmit" value="'.$lang['L_SQL_CREATETABLE'].'" class="Formbutton"></td></tr>';
echo '<tr class="dbrow1"><td>'.$lang['L_SQL_COPYTABLE'].': </td><td><input type="text" class="text" name="tablecopyname" size="20" maxlength="150"></td><td><select name="copyatt"><option value="0">'.$lang['L_SQL_STRUCTUREONLY'].'</option>'.((MSD_NEW_VERSION) ? '<option value="1">'.$lang['L_SQL_STRUCTUREDATA'].'</option>' : '').'</select></td><td><input type="submit" class="Formbutton" name="tablecopysubmit" value="'.$lang['L_SQL_COPYTABLE'].'" '.(($table_edit_name=="") ? "disabled=\"disabled\"" : "").'></td></tr>';

if ($anz_tabellen==0)
{
	echo '<tr><td>'.$lang['L_SQL_NOTABLESINDB'].' `'.$databases['Name'][$dbid].'`</td></tr>';
}
else
{

	echo '<tr><td>'.$lang['L_SQL_SELECTTABLE'].':&nbsp;&nbsp;&nbsp;</td>';
	echo '<td colspan="2"><select name="tableselect" onchange="this.form.submit()"><option value="1" SELECTED></option>';
	for ($i=0; $i<$anz_tabellen; $i++)
	{
		$row=mysql_fetch_array($res);
		echo '<option value="'.$row['Name'].'">'.$row['Name'].'</option>';
	}
	echo '</select>&nbsp;&nbsp;</td>';
	echo '<td><input type="button" class="Formbutton" value="'.$lang['L_SQL_SHOWDATATABLE'].'" onclick="location.href=\'sql.php?db='.$databases['Name'][$dbid].'&amp;dbid='.$dbid.'&amp;tablename='.$tablename.'\'"></td></tr>';
}
echo '</table></form><p>&nbsp;</p>';
if ($table_edit_name!="")
{
	$sqlf="SHOW FULL FIELDS FROM `".$databases['Name'][$dbid]."`.`$table_edit_name` ;";
	$res=MSD_query($sqlf);
	$anz_fields=mysql_num_rows($res);
	$fields_infos=getFieldinfos($databases['Name'][$dbid],$table_edit_name);

	if (MSD_NEW_VERSION) $t_engine=(isset($fields_infos['_tableinfo_']['ENGINE'])) ? $fields_infos['_tableinfo_']['ENGINE'] : "MyISAM";
	else
		$t_engine=(isset($fields_infos['_tableinfo_']['TYPE'])) ? $fields_infos['_tableinfo_']['TYPE'] : "MyISAM";

	$t_charset=(isset($fields_infos['_tableinfo_']['DEFAULT CHARSET'])) ? $fields_infos['_tableinfo_']['DEFAULT CHARSET'] : "";
	$t_collation=isset($row['Collation']) ? $row['Collation'] : ""; //(isset($fields_infos['_tableinfo_']['COLLATE'])) ? $fields_infos['_tableinfo_']['COLLATE'] : "";
	$t_comment=(isset($fields_infos['_tableinfo_']['COMMENT'])) ? substr($fields_infos['_tableinfo_']['COMMENT'],1,strlen($fields_infos['_tableinfo_']['COMMENT'])-2) : "";
	$t_rowformat=(isset($fields_infos['_tableinfo_']['ROW_FORMAT'])) ? $fields_infos['_tableinfo_']['ROW_FORMAT'] : "";
	echo "<h6>".$lang['L_TABLE']." `$table_edit_name`</h6>";
	$td='<td valign="top" nowrap="nowrap" class="small">';

	//Tabelleneigenschaften
	echo '<form action="'.$p.'" method="post"><input type="hidden" name="table_edit_name" value="'.$table_edit_name.'"><table class="bdr">';
	echo '<tr class="sqlNew"><td colspan="4" style="font-size:10pt;font-weight:bold;">'.$lang['L_SQL_TBLPROPSOF'].' `'.$table_edit_name.'` ('.$anz_fields.' '.$lang['L_FIELDS'].')</td>';
	echo '<td class="small" colspan="2" align="center">Name<br><input type="text" class="text" name="t_edit_name" value="'.$table_edit_name.'" size="30" maxlength="150" style="font-size:11px;"></td></tr>';
	echo '<tr class="sqlNew">';
	echo '<td class="small" align="center">Engine<br><select name="t_edit_engine"  style="font-size:11px;">'.EngineCombo($t_engine).'</select></td>';
	echo '<td class="small" align="center">Row Format<br><select name="t_edit_rowformat"  style="font-size:11px;">'.GetOptionsCombo($feldrowformat,$t_rowformat).'</select></td>';
	echo '<td class="small" align="center">'.$lang['L_CHARSET'].'<br><select name="t_edit_charset"  style="font-size:11px;">'.CharsetCombo($t_charset).'</select></td>';
	echo '<td class="small" align="center">'.$lang['L_COLLATION'].'<br><select name="t_edit_collate"  style="font-size:11px;">'.CollationCombo($t_collation).'</select></td>';
	echo '<td class="small" align="center">'.$lang['L_COMMENT'].'<br><input type="text" class="text" name="t_edit_comment" value="'.$t_comment.'" size="30" maxlength="100" style="font-size:11px;"></td>';
	echo '<td class="small" align="center">&nbsp;<br><input type="submit" name="t_edit_submit" value="'.$lang['L_CHANGE'].'" class="Formbutton"></td></tr>';
	echo '</table></form><p>&nbsp;</p>';

	$field_fehler=0;
	echo '<h6>'.$lang['L_FIELDS_OF_TABLE'].' `'.$table_edit_name.'`</h6>';

	$d_collate='';
	$d_comment='';

	if (isset($_GET['newfield'])||isset($_GET['editfield'])||$field_fehler>0||isset($_POST['newfield_posted']))
	{
		if (isset($_GET['editfield'])) $id=$_GET['editfield'];
		$d_name=(isset($_GET['editfield'])) ? $fields_infos[$id]['name'] : "";
		$d_type=(isset($_GET['editfield'])) ? $fields_infos[$id]['type'] : "";
		$d_size=(isset($_GET['editfield'])) ? $fields_infos[$id]['size'] : "";
		$d_null=(isset($_GET['editfield'])) ? $fields_infos[$id]['null'] : "";
		$d_attribute=(isset($_GET['editfield'])) ? $fields_infos[$id]['attributes'] : "";

		$d_default='';
		if (isset($id)&&isset($fields_infos[$id])&&isset($fields_infos[$id]['default']))
		{
			if ($fields_infos[$id]['default']=='NULL') $d_default='NULL';
			else
				$d_default=substr($fields_infos[$id]['default'],1,strlen($fields_infos[$id]['default'])-2);
		}
		$d_extra=(isset($_GET['editfield'])) ? $fields_infos[$id]['extra'] : "";

		$d_primary=$d_unique=$d_index=$d_fulltext=0;
		if (isset($id))
		{
			if (isset($fields_infos[$id]['collate'])) $d_collate=(isset($_GET['editfield'])) ? $fields_infos[$id]['collate'] : "";
			if (isset($fields_infos[$id]['comment'])) $d_comment=(isset($_GET['editfield'])) ? $fields_infos[$id]['comment'] : "";
		}
		$d_privileges=(isset($_GET['editfield'])) ? $fields_infos[$id]['privileges'] : "";
		if (isset($_GET['editfield']))
		{
			$d_primary=(in_array($fields_infos[$id]['name'],$fields_infos['_primarykeys_'])) ? 1 : 0;
			$d_index=(in_array($fields_infos[$id]['name'],$fields_infos['_key_'])) ? 1 : 0;
			$d_fulltext=(in_array($fields_infos[$id]['name'],$fields_infos['_fulltextkey_'])) ? 1 : 0;
			$d_unique=(in_array($fields_infos[$id]['name'],$fields_infos['_uniquekey_'])) ? 1 : 0;
		}
		echo '<form action="'.$p.'" method="post" id="smallform"><input type="hidden" name="newfield_posted" value="1">';
		if (isset($_GET['editfield'])) echo '<input type="hidden" name="editfield" value="'.$id.'"><input type="hidden" name="fieldname" value="'.$d_name.'">';
		if (isset($_POST['newtablesubmit'])) echo '<input type="hidden" name="newtablename" value="'.$_POST['newtablename'].'">';
		echo '<input type="hidden" name="f_primary" value="'.$d_primary.'"><input type="hidden" name="f_unique" value="'.$d_unique.'">';
		echo '<input type="hidden" name="f_index" value="'.$d_index.'"><input type="hidden" name="f_fulltext" value="'.$d_fulltext.'">';
		echo '<table class="bdr"><tr class="thead"><th colspan="6" align="center">'.((isset($_GET['editfield'])) ? $lang['L_SQL_EDITFIELD']." `".$d_name."`" : $lang['L_SQL_NEWFIELD']).'</th></tr>';
		echo '<tr><td class="small">Name<br><input type="text" class="text" value="'.$d_name.'" name="f_name" size="30"></td>';
		echo '<td>Type<br><select name="f_type">'.GetOptionsCombo($feldtypen,$d_type).'</select></td>';
		echo '<td>Size&nbsp;<br><input type="text" class="text" value="'.$d_size.'" name="f_size" size="3" maxlength="80"></td>';
		echo '<td>NULL<br><select name="f_null">'.GetOptionsCombo($feldnulls,$d_null).'</select></td>';
		echo '<td align="center">Default<br><input type="text" class="text" name="f_default" value="'.$d_default.'" size="10"></td>';
		echo '<td align="center">Extra<br><select name="f_extra">'.GetOptionsCombo($feldextras,$d_extra).'</select></td>';

		echo '</tr><tr><td align="center">'.$lang['L_SQL_INDEXES'].'<br>';
		echo '<input type="radio" class="radio" name="f_index_new" id="k_no_index" value="no" '.(($d_primary+$d_unique+$d_index+$d_fulltext==0) ? 'checked="checked"' : '').'>';
		echo '<label for="k_no_index">'.$icon['key_nokey'].'</label>&nbsp;&nbsp;';

		echo '<input type="radio" class="radio" name="f_index_new" id="k_primary" value="primary" '.(($d_primary==1) ? "checked" : "").'>';
		echo '<label for="k_primary">'.$icon['key_primary'].'</label>&nbsp;&nbsp;';

		echo '<input type="radio" class="radio" name="f_index_new" id="k_unique" value="unique" '.(($d_unique==1) ? "checked" : "").'>';
		echo '<label for="k_unique">'.$icon['key_unique'].'</label>&nbsp;&nbsp;';

		echo '<input type="radio" class="radio" name="f_index_new" id="k_index" value="index" '.(($d_index==1) ? "checked" : "").'>&nbsp;';
		echo '<label for="k_index">'.$icon['index'].'</label>&nbsp;&nbsp;';

		echo '<input type="checkbox" class="checkbox" name="f_indexfull" id="k_fulltext" value="1" '.(($d_fulltext==1) ? "checked" : "").'>';
		echo '<label for="k_fulltext">'.$icon['key_fulltext'].'</label>&nbsp;&nbsp;</td>';

		echo '<td align="center" colspan="2" >'.$lang['L_COLLATION'].'<br><select name="f_collate">'.CollationCombo($d_collate).'</select></td>';
		echo '<td align="center">'.$lang['L_SQL_ATTRIBUTES'].'<br><select name="f_attribut">'.AttributeCombo($d_attribute).'</select></td>';
		echo '<td align="center">'.$lang['L_SQL_ATPOSITION'].':<br><select name="f_position"><option value=""></option><option value="FIRST">'.$lang['L_SQL_FIRST'].'</option>';
		if ($anz_fields>0)
		{
			for ($i=0; $i<$anz_fields; $i++)
			{
				echo '<option value="AFTER `'.$fields_infos[$i]['name'].'`">'.$lang['L_SQL_AFTER'].' `'.$fields_infos[$i]['name'].'`</option>';
			}
		}
		echo '</select></td><td align="center"><br><input type="submit" name="newfieldsubmit" value="'.((isset($_GET['editfield'])) ? $lang['L_SQL_CHANGEFIELD'] : $lang['L_SQL_INSERTFIELD']).'" class="Formbutton"></td></tr></table></form><p>&nbsp;</p>';
	}
	else
		echo '<a style="font-size:8pt;padding-bottom:8px;" href="'.$p.'&amp;newfield=1">'.$lang['L_SQL_INSERTNEWFIELD'].'</a><br><br>';
		//Felder ausgeben
	echo '<table class="bdr">';
	for ($i=0; $i<$anz_fields; $i++)
	{
		$cl=($i%2) ? "dbrow" : "dbrow1";
		if ($i==0) echo '<tr class="thead"><th colspan="2">&nbsp;</th><th>Field</th><th>Type</th><th>Size</th><th>NULL</th><th>Key</th><th>Attribute</th><th>Default</th><th>Extra</th><th>'.
		$lang['L_COLLATION'].'</th><th>'.$lang['L_COMMENT'].'</th></tr>';
		echo '<tr class="'.$cl.'">';
		echo '<td nowrap="nowrap">';
		echo '<a href="'.$p.'&amp;editfield='.$i.'"><img src="'.$config['files']['iconpath'].'edit.gif" title="edit field" alt="edit field" border="0"></a>&nbsp;&nbsp;';
		echo '<a href="'.$p.'&amp;kill='.$fields_infos[$i]['name'].'&amp;anz='.$anz_fields.'" onclick="if(!confirm(\''.$lang['L_ASKDELETEFIELD'].'\')) return false;"><img src="'.$config['files']['iconpath'].'delete.gif" alt="delete field" border="0"></a>&nbsp;&nbsp;';

		echo '</td>';
		echo '<td style="text-align:right">'.($i+1).'.</td>';

		echo '<td><strong>'.$fields_infos[$i]['name'].'</strong></td><td>'.$fields_infos[$i]['type'].'</td><td>'.$fields_infos[$i]['size'].'</td>';
		echo '<td>'.get_output_attribut_null($fields_infos[$i]['null']).'</td><td>';
		//key
		if (in_array($fields_infos[$i]['name'],$fields_infos['_primarykeys_'])) echo $icon['key_primary'];
		if (in_array($fields_infos[$i]['name'],$fields_infos['_fulltextkey_'])) echo $icon['key_fulltext'];
		if (in_array($fields_infos[$i]['name'],$fields_infos['_uniquekey_'])) echo $icon['key_unique'];
		if (in_array($fields_infos[$i]['name'],$fields_infos['_key_'])) echo $icon['index'];
		echo '</td><td>'.$fields_infos[$i]['attributes'].'</td>';
		echo '<td>'.$fields_infos[$i]['default'].'</td>'.$td.$fields_infos[$i]['extra'].'</td>';
		echo '<td>'.((MSD_NEW_VERSION) ? $fields_infos[$i]['collate'] : "&nbsp;").'</td>';
        echo '<td>'.((isset($fields_infos[$i]['comment'])) ? $fields_infos[$i]['comment'] : "&nbsp;").'</td>';
        echo "</tr>";
	}
	echo '</table><br>';

	echo '<h6>'.$lang['L_SQL_TABLEINDEXES'].' `'.$table_edit_name.'`</h6>';
	echo '<table class="bdr">
	   <tr class="thead">
	       <th colspan="2">&nbsp;</th>
	       <th>'.$lang['L_NAME'].'</th>
	       <th>'.$lang['L_SQL_COLUMNS'].'</th>
	       <th>'.$lang['L_INFO_SIZE'].'</th>
	       '.((MSD_NEW_VERSION) ? '<th>'.$lang['L_TABLE_TYPE'].'</th>' : '').'
	       <th>'.$lang['L_SQL_ALLOWDUPS'].'</th>
	       <th>'.$lang['L_SQL_CARDINALITY'].'</th>
	       <th>'.$lang['L_COMMENT'].'</th>
	   </tr>';
	$sqlk="SHOW KEYS FROM `".$databases['Name'][$dbid]."`.`$table_edit_name`;";
	$res=MSD_query($sqlk);
	$num=mysql_numrows($res);
	if ($num==0)
	{
		echo '<tr><td colspan="6">'.$lang['L_SQL_TABLENOINDEXES'].'</td></tr>';
	}
	else
	{
		for ($i=0; $i<$num; $i++)
		{
			$row=mysql_fetch_array($res,MYSQL_ASSOC);
			if (!isset($row['Comment'])) {
			    $row['Comment'] = '';
			}
			$cl=($i%2) ? "dbrow" : "dbrow1";
			//Images
			echo '<tr class="'.$cl.'">';
			echo '<td>';
			if ($row['Key_name']=="PRIMARY")
			{
				echo '<a href="'.$p.'&amp;killPrimaryKey='.$row['Column_name'].'" onclick="if(!confirm(\''.$lang['L_PRIMARYKEY_CONFIRMDELETE'].'\')) return false;">';
				echo '<img src="'.$config['files']['iconpath'].'delete.gif" alt="" border="0">';
				echo '</a>';
			}
			else
			{
				echo '<a href="'.$p.'&amp;killIndex='.$row['Key_name'].'" onclick="if(!confirm(\''.$lang['L_KEY_CONFIRMDELETE'].'\')) return false;">';
				echo '<img src="'.$config['files']['iconpath'].'delete.gif" alt="" border="0">';
				echo '</a>';
			}
			echo '</td>';
			echo '<td style="text-align:right">'.($i+1).'.</td>';
			echo '<td>'.$row['Key_name'].'</td>';
			echo '<td>'.$row['Column_name'].'</td>';
            echo '<td class="right">';
            if (isset($row['Sub_part']) && $row['Sub_part']>0) echo $row['Sub_part'];
            echo '</td>';
			if (MSD_NEW_VERSION) echo '<td>'.$row['Index_type'].'</td>';
			echo '<td align="center">'.(($row['Non_unique']==1) ? $lang['L_YES'] : $lang['L_NO']).'</td>';
			echo '<td>'.(($row['Cardinality']>=0) ? $row['Cardinality'] : $lang['L_NO']).'</td>';
            echo '<td>'.$row['Comment'].'</td>';
			echo '</tr>';
		}
	}
	echo '</table><br><input type="Button" value="'.$lang['L_SQL_CREATEINDEX'].'" onclick="location.href=\''.$p.'&amp;sql_createindex=1#setnewkeys\'" class="Formbutton">';

	if ((isset($_GET['sql_createindex']))&&($_GET['sql_createindex']=="1"))
	{ ?>
<script type="text/javascript">
	function toggleIndexLength(id)
	{
		var mysqlStrings = ['<?php echo implode("','", $mysql_string_types);?>'];
		var field = 'setNewKey'+id;
		var sel = document.getElementById(field).selectedIndex;
		var val = document.getElementById(field).options[sel].innerHTML;
		document.getElementById('indexSize'+id).disabled = true;
		for (i=0;i<mysqlStrings.length;i++)
		{
			if (val.indexOf("["+mysqlStrings[i]) != -1)
			{
				document.getElementById('indexSize'+id).disabled = false;
			}
		}
	}
</script>
    <?php
		echo '<br><a name="setnewkeys"></a>';
		echo '<form action="'.$p.'" method="POST">';
		echo '<h6>'.$lang['L_SETKEYSFOR'].' `'.$table_edit_name.'`</h6>';
		//kopf
		echo '<table class="bdr">';
		//body
		$sqlFelder="DESCRIBE `".$databases['Name'][$dbid]."`.`".$_GET['tablename']."`;";
		$res=MSD_query($sqlFelder);
		$num=mysql_numrows($res);
		if ($num==0)
		{
			echo '<tr><td>'.$lang['L_SQL_TABLENOINDEXES'].'</td></tr>';
		}
		else
		{
	       	echo '<tr class="thead"><th>'.$lang['L_NAME'].'</th><th>'.$lang['L_TABLE_TYPE'].'</th></tr>';
            echo '<tr>';
			echo '<td><input type="text" name="indexName" value="" class="text">';
			echo '<td><select name="indexType"><option value="primary">'.$lang['L_TITLE_KEY_PRIMARY'].'</option><option value="unique">'.$lang['L_TITLE_KEY_UNIQUE'].'</option><option value="index" selected="selected">'.$lang['L_TITLE_INDEX'].'</option><option value="fulltext">'.$lang['L_TITLE_KEY_FULLTEXT'].'</option></select></td>';
			echo '</tr>';
    		echo '</table><br>';

    		//alle Felder holen
			$feldArray=Array();
            echo '<table class="bdr">';
    		echo '<tr class="thead"><th>#</th><th>'.$lang['L_PRIMARYKEY_FIELD'].'</th><th>'.$lang['L_INFO_SIZE'].'</th>';

			while ($row=mysql_fetch_array($res, MYSQL_ASSOC))
			{
				$feldArray[$row['Field']]=$row['Type'];
			}
			//Primaerschluessel holen, um automatisch vorzuselektieren
			$primaryKeys=getPrimaryKeys($databases['Name'][$dbid],$_GET['tablename']);
			//eine Select-Box pro Feld anzeigen, Felder für Name und Typ nur in der ersten Zeile
			for ($i=0; $i<$num; $i++)
			{
				echo '<tr><td class="right">'.($i+1).'.</td><td>';
				$options ="\n\n".'<option value="">---</option>';
				$selectedFeldTyp = false;
				foreach ($feldArray as $feldName=>$feldTyp)
				{
					$options.="\n".'<option value="'.$feldName.'"';
					//alle Primaerschluessel vorselektieren
					if (isset($primaryKeys['name'][$i]) && $primaryKeys['name'][$i]==$feldName) {
					    $options.=' selected="selected"';
					    $selectedFeldTyp = $feldTyp;
					}
					$options.='>'.$feldName.' ['.$feldTyp.']</option>';
				}
				echo '<select id="setNewKey'.$i.'" name="setNewKey'.$i.'" onchange="toggleIndexLength('.$i.');">';
				echo $options."\n".'</select></td>';
				echo '<td>';
                $type =explode('(', $selectedFeldTyp);
				echo '<input type="text" id="indexSize'.$i.'" name="indexSize'.$i.'" value="" size="10" class="text"';
				if (!isset($type[0]) || !in_array($type[0], $mysql_string_types)) echo ' disabled="disabled"';
				echo '></td>';
				echo '</tr>';
			}
			$i ++;
		}
		echo '</table>';
		//Speichern Knopf
		echo '<br><input name="setNewKeys" type="submit" value="'.$lang['L_SAVE'].'" class="Formbutton">';
		echo '</form>';
	}
}