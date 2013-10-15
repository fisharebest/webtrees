<?php
if (!defined('MSD_VERSION')) die('No direct access.');
get_sql_encodings();

//Datenbanken
if (isset($_GET['dbrefresh'])) SetDefault();

echo $aus . '<h4>' . $lang['L_TOOLS'] . '</h4>';
if (isset($_POST['dbdosubmit']))
{
	$newname=$_POST['newname'];
	$db_index=$_POST['db_index'];
	$db_action=$_POST['db_action'];
	$changed=false;
	$ausgabe=$out="";
	switch ($db_action)
	{
		case "drop":
            if (MSD_DoSQL("DROP DATABASE `" . $databases['Name'][$db_index] . "`"))
            {
                echo SQLOutput($out,'<p class="success">' . $lang['L_DB'] . ' `' . $databases['Name'][$db_index] . '` ' . $lang['L_SQL_DELETED'] . '</p>');
                $changed=true;
            }
		    break;
		case "empty":
			EmptyDB($databases['Name'][$db_index]);
			echo SQLOutput($out,'<p class="success">' . $lang['L_DB'] . ' `' . $databases['Name'][$db_index] . '` ' . $lang['L_SQL_WASEMPTIED'] . '.</p>');
			break;
		case "rename":
			$dbold=$databases['Name'][$db_index];
            if (DB_Copy($dbold,$newname,1))
            {
                echo SQLOutput($out,'<p class="success">' . $lang['L_DB'] . ' `' . $dbold . '` ' . $lang['L_SQL_RENAMEDTO'] . ' `' . $newname . '`.</p>');
                $changed=true;
            }
			break;
		case "copy":
			$dbold=$databases['Name'][$db_index];
            if (DB_Copy($dbold,$newname))
            {
                $changed=true;
                echo SQLOutput($out,'<p class="success">' . sprintf($lang['L_SQL_DBCOPY'],$dbold,$newname) . '</p>');
            }
            break;
		case "structure":
            if (DB_Copy($databases['Name'][$db_index],$newname,0,0))
            {
                $changed=true;
                echo SQLOutput($out,'<p class="success">' . sprintf($lang['L_SQL_DBSCOPY'],$databases['Name'][$db_index],$newname) . '</p>');
            }
            break;
		case "rights":
			break;
	}

	if ($changed==true)
	{
		SetDefault();
		include ( $config['files']['parameter'] );
		echo '<script language="JavaScript" type="text/javascript">parent.MySQL_Dumper_menu.location.href="menu.php?action=dbrefresh";</script>';

	}
}
if (isset($_POST['dbwantaction']))
{
	if (isset($_POST['db_createnew']))
	{
		$newname=trim($_POST['db_create']);
		if (!empty($newname))
		{
			$sqlc="CREATE DATABASE `$newname`";
			$col=( MSD_NEW_VERSION ) ? $_POST['db_collate'] : "";
			if (isset($_POST['db_default_charset']) && intval(substr(MSD_NEW_VERSION,0,1)) > 3)
			{
				$db_default_charset_string=$config['mysql_possible_character_sets'][$_POST['db_default_charset']];
				$db_default_charset=explode(' ',$db_default_charset_string);
				if (isset($db_default_charset[0])) $sqlc.=' DEFAULT CHARACTER SET `' . $db_default_charset[0] . '`';
			}
			$db_default_collation=@explode('|',$col);
			if (isset($db_default_collation[1])) $sqlc.=' COLLATE `' . $db_default_collation[1] . '`';

            if (MSD_query($sqlc))
            {
                echo $lang['L_DB'] . " `$newname` " . $lang['L_SQL_WASCREATED'] . ".<br>";
                SetDefault();
                include ( $config['files']['parameter'] );
                echo '<script language="JavaScript" type="text/javascript">parent.MySQL_Dumper_menu.location.href="menu.php?action=dbrefresh";</script>';
            }
		}
	}
	$db_action=$newname="";
	$db_index=-1;
	for ($i=0; $i < count($databases['Name']); $i++)
	{
		if (isset($_POST['db_do_' . $i]))
		{
			$newname=$_POST['db_rename' . $i];
			$db_index=$i;
			$db_action=$_POST['db_do_action_' . $i];
			break;
		}
	}
	if ($db_action != "")
	{
		echo '<div><div align="left" id="sqleditbox">';
		echo '<form action="sql.php?context=3" method="post">
						<input type="hidden" name="db_action" value="' . $db_action . '">
						<input type="hidden" name="newname" value="' . $newname . '">
						<input type="hidden" name="db_index" value="' . $db_index . '">';
		switch ($db_action)
		{
			case "drop":
				echo '<strong>' . sprintf($lang['L_ASKDBDELETE'],$databases['Name'][$i]) . '</strong><br><br>';
				echo '<input type="submit" name="dbdosubmit" value="' . $lang['L_DO_NOW'] . '" class="Formbutton">';
				break;
			case "empty":
				echo '<strong>' . sprintf($lang['L_ASKDBEMPTY'],$databases['Name'][$i]) . '</strong><br><br>';
				echo '<input type="submit" name="dbdosubmit" value="' . $lang['L_DO_NOW'] . '" class="Formbutton">';
				break;
			case "rename":
				echo '<strong>' . $lang['L_SQL_RENAMEDB'] . ' `' . $databases['Name'][$db_index] . '` ' . $lang['L_IN'] . ' `' . $newname . '`</strong><br><br>';
                if ($newname == "") echo '<p class="error">' . $lang['L_SQL_NAMEDEST_MISSING'] . '</p>';
                else
                {
                    echo '<input type="submit" name="dbdosubmit" value="' . $lang['L_DO_NOW'] . '" class="Formbutton">';
                }
				break;
			case "copy":
				echo '<strong>' . sprintf($lang['L_ASKDBCOPY'],$databases['Name'][$db_index],$newname) . '</strong><br><br>';
				if ($newname == "") echo '<p class="error">' . $lang['L_SQL_NAMEDEST_MISSING'] . '</p>';
				else
				{
					echo '<input type="submit" name="dbdosubmit" value="' . $lang['L_DO_NOW'] . '" class="Formbutton">';
				}
				break;
			case "structure":
				echo '<strong>' . $lang['L_FM_ASKDBCOPY1'] . '`' . $databases['Name'][$db_index] . '`' . $lang['L_FM_ASKDBCOPY2'] . '`' . $newname . '`' . $lang['L_FM_ASKDBCOPY3'] . '</strong><br><br>';
				if ($newname == "") echo '<p class="error">' . $lang['L_SQL_NAMEDEST_MISSING'] . '</p>';
				else
				{
					echo '<input type="submit" name="dbdosubmit" value="' . $lang['L_DO_NOW'] . '" class="Formbutton">';
				}
				break;
			case "rights":
				break;
		}
		echo '</form></div></div><br>';
	}
}

echo '<br><form action="sql.php?context=3" method="post"><input type="hidden" name="dbwantaction" value="1">';
echo '<div><table class="bdr">';
echo '<tr><td colspan="2" align="center"><strong>' . $lang['L_CREATE_DATABASE'] . '</strong></td></tr>';
echo '<tr><td>Name:</td><td><input type="text" class="text" name="db_create" size="20"></td></tr>';

echo '<tr><td>' . $lang['L_DEFAULT_CHARSET'] . ':</td><td><select name="db_default_charset">';
echo make_options($config['mysql_possible_character_sets'],get_index($config['mysql_possible_character_sets'],$config['mysql_standard_character_set']));
echo '</select></td></tr>';

echo '<tr><td>' . $lang['L_COLLATION'] . '</td><td><select name="db_collate">' . CollationCombo('',1) . '</select></td></tr>';
echo '<tr><td colspan="2"><input type="submit" name="db_createnew" value="' . $lang['L_CREATE'] . '" class="Formbutton"></td></tr>';
echo '</table>';

echo '<br><table class="bdr">';
echo '<tr class="thead"><th>' . $lang['L_DBS'] . '</th><th>' . $lang['L_SQL_ACTIONS'] . '</th></tr>';
for ($i=0; $i < count($databases['Name']); $i++)
{
	$cl=( $i % 2 ) ? "dbrow" : "dbrow1";
	echo ( $i == $databases['db_selected_index'] ) ? '<tr class="dbrowsel">' : '<tr class="' . $cl . '">';
	echo '<td><a href="sql.php?db=' . $databases['Name'][$i] . '&amp;dbid=' . $i . '">' . $databases['Name'][$i] . '</a></td>';
	echo '<td nowrap="nowrap"><input type="text" class="text" name="db_rename' . $i . '" size="20">';
	echo '&nbsp;&nbsp;<select name="db_do_action_' . $i . '" onchange="db_do_' . $i . '.disabled=false;">';
	echo '<option value="">-- ' . $lang['L_SQL_CHOOSEACTION'] . ' --</option>';
	echo '<option value="drop">' . $lang['L_SQL_DELETEDB'] . '</option>';
	echo '<option value="empty">' . $lang['L_SQL_EMPTYDB'] . '</option>';
	if (MSD_NEW_VERSION) echo '<option value="rename">' . $lang['L_SQL_RENAMEDB'] . '</option>';
	if (MSD_NEW_VERSION) echo '<option value="copy">' . $lang['L_SQL_COPYDATADB'] . '</option>';
	echo '<option value="structure">' . $lang['L_SQL_COPYSDB'] . '</option>';

	echo '</select>';
	echo "\n\n" . '&nbsp;&nbsp;<input type="submit" name="db_do_' . $i . '" value="' . $lang['L_DO'] . '" disabled="disabled" class="Formbutton">';

	echo '&nbsp;&nbsp;<input type="Button" value="' . $lang['L_SQL_IMEXPORT'] . '" onclick="location.href=\'sql.php?db=' . $databases['Name'][$i] . '&amp;dbid=' . $i . '&amp;context=4\'" class="Formbutton"></td></tr>';
}

echo '</table></div></form>';

