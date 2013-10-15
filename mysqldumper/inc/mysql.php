<?php
if (!defined('MSD_VERSION')) die('No direct access.');

//Feldspezifikationen
$feldtypen=Array(
				"VARCHAR",
				"TINYINT",
				"TEXT",
				"DATE",
				"SMALLINT",
				"MEDIUMINT",
				"INT",
				"BIGINT",
				"FLOAT",
				"DOUBLE",
				"DECIMAL",
				"DATETIME",
				"TIMESTAMP",
				"TIME",
				"YEAR",
				"CHAR",
				"TINYBLOB",
				"TINYTEXT",
				"BLOB",
				"MEDIUMBLOB",
				"MEDIUMTEXT",
				"LONGBLOB",
				"LONGTEXT",
				"ENUM",
				"SET"
);
$feldattribute=ARRAY(
					"",
					"BINARY",
					"UNSIGNED",
					"UNSIGNED ZEROFILL"
);
$feldnulls=Array(
				"NOT NULL",
				"NULL"
);
$feldextras=Array(
				"",
				"AUTO_INCREMENT"
);
$feldkeys=Array(
				"",
				"PRIMARY KEY",
				"UNIQUE KEY",
				"FULLTEXT"
);
$feldrowformat=Array(

					"",
					"FIXED",
					"DYNAMIC",
					"COMPRESSED"
);

$rechte_daten=Array(
					"SELECT",
					"INSERT",
					"UPDATE",
					"DELETE",
					"FILE"
);
$rechte_struktur=Array(
					"CREATE",
					"ALTER",
					"INDEX",
					"DROP",
					"CREATE TEMPORARY TABLES"
);
$rechte_admin=Array(
					"GRANT",
					"SUPER",
					"PROCESS",
					"RELOAD",
					"SHUTDOWN",
					"SHOW DATABASES",
					"LOCK TABLES",
					"REFERENCES",
					"EXECUTE",
					"REPLICATION CLIENT",
					"REPLICATION SLAVE"
);
$rechte_resourcen=Array(
						"MAX QUERIES PER HOUR",
						"MAX UPDATES PER HOUR",
						"MAX CONNECTIONS PER HOUR"
);

$sql_keywords=array(
					'ALTER',
					'AND',
					'ADD',
					'AUTO_INCREMENT',
					'BETWEEN',
					'BINARY',
					'BOTH',
					'BY',
					'BOOLEAN',
					'CHANGE',
					'CHARSET',
					'CHECK',
					'COLLATE',
					'COLUMNS',
					'COLUMN',
					'CROSS',
					'CREATE',
					'DATABASES',
					'DATABASE',
					'DATA',
					'DELAYED',
					'DESCRIBE',
					'DESC',
					'DISTINCT',
					'DELETE',
					'DROP',
					'DEFAULT',
					'ENCLOSED',
					'ENGINE',
					'ESCAPED',
					'EXISTS',
					'EXPLAIN',
					'FIELDS',
					'FIELD',
					'FLUSH',
					'FOR',
					'FOREIGN',
					'FUNCTION',
					'FROM',
					'GROUP',
					'GRANT',
					'HAVING',
					'IGNORE',
					'INDEX',
					'INFILE',
					'INSERT',
					'INNER',
					'INTO',
					'IDENTIFIED',
					'JOIN',
					'KEYS',
					'KILL',
					'KEY',
					'LEADING',
					'LIKE',
					'LIMIT',
					'LINES',
					'LOAD',
					'LOCAL',
					'LOCK',
					'LOW_PRIORITY',
					'LEFT',
					'LANGUAGE',
					'MEDIUMINT',
					'MODIFY',
					'MyISAM',
					'NATURAL',
					'NOT',
					'NULL',
					'NEXTVAL',
					'OPTIMIZE',
					'OPTION',
					'OPTIONALLY',
					'ORDER',
					'OUTFILE',
					'OR',
					'OUTER',
					'ON',
					'PROCEEDURE',
					'PROCEDURAL',
					'PRIMARY',
					'READ',
					'REFERENCES',
					'REGEXP',
					'RENAME',
					'REPLACE',
					'RETURN',
					'REVOKE',
					'RLIKE',
					'RIGHT',
					'SHOW',
					'SONAME',
					'STATUS',
					'STRAIGHT_JOIN',
					'SELECT',
					'SETVAL',
					'TABLES',
					'TEMINATED',
					'TO',
					'TRAILING',
					'TRUNCATE',
					'TABLE',
					'TEMPORARY',
					'TRIGGER',
					'TRUSTED',
					'UNIQUE',
					'UNLOCK',
					'USE',
					'USING',
					'UPDATE',
					'UNSIGNED',
					'VALUES',
					'VARIABLES',
					'VIEW',
					'WITH',
					'WRITE',
					'WHERE',
					'ZEROFILL',
					'XOR',
					'ALL',
					'ASC',
					'AS',
					'SET',
					'IN',
					'IS',
					'IF'
);
$mysql_doc=Array(
				"Feldtypen" => "http://dev.mysql.com/doc/mysql/de/Column_types.html"
);
$mysql_string_types = array(
    'char',
    'varchar',
    'tinytext',
    'text',
    'mediumtext',
    'longtext',
    'binary',
    'varbinary',
    'tinyblob',
    'mediumblob',
    'blob',
    'longblob',
    'enum',
    'set'
);
$mysql_SQLhasRecords=array(

						'SELECT',
						'SHOW',
						'EXPLAIN',
						'DESCRIBE',
						'DESC'
);

function MSD_mysql_connect($encoding='utf8', $keycheck_off=false, $actual_table='')
{
	global $config,$databases;
    if (isset($config['dbconnection']) && is_resource($config['dbconnection'])) {
        return $config['dbconnection'];
    }
	$port=( isset($config['dbport']) && !empty($config['dbport']) ) ? ':' . $config['dbport'] : '';
	$socket=( isset($config['dbsocket']) && !empty($config['dbsocket']) ) ? ':' . $config['dbsocket'] : '';
	$config['dbconnection']=@mysql_connect($config['dbhost'] . $port . $socket,$config['dbuser'],$config['dbpass']) or die(SQLError("Error establishing a database connection!", mysql_error()));
	if (!defined('MSD_MYSQL_VERSION')) GetMySQLVersion();

	if (!isset($config['mysql_standard_character_set']) || $config['mysql_standard_character_set'] == '') get_sql_encodings();

	if ($config['mysql_standard_character_set'] != $encoding)
	{
		$set_encoding=@mysql_query('SET NAMES \'' . $encoding . '\'',$config['dbconnection']);
		if ($set_encoding === false) $config['mysql_can_change_encoding']=false;
		else $config['mysql_can_change_encoding']=true;
	}
	if ($keycheck_off) {
	    // only called with this param when restoring
	    mysql_query('SET FOREIGN_KEY_CHECKS=0',$config['dbconnection']);
	    // also set SQL-Mode NO_AUTO_VALUE_ON_ZERO for magento users
	    mysql_query('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"', $config['dbconnection']);
	}
	return $config['dbconnection'];
}

function GetMySQLVersion()
{
	$res=MSD_query("select version()");
	$row=mysql_fetch_array($res);
	$version=$row[0];
	if (!defined('MSD_MYSQL_VERSION')) define('MSD_MYSQL_VERSION',$version);
	$versions=explode('.',$version);
	$new=false;
	if ($versions[0] == 4 && $versions[1] >= 1) $new=true;
	if ($versions[0] > 4) $new=true;
	if (!defined('MSD_NEW_VERSION')) define('MSD_NEW_VERSION',$new);
	return $version;
}

function MSD_query($query, $error_output=true)
{
	global $config;
	if (!isset($config['dbconnection'])) MSD_mysql_connect();
	//echo "<br>Query: ".htmlspecialchars($query);
	$res=mysql_query($query,$config['dbconnection']);
	if (false === $res && $error_output) SQLError($query,mysql_error($config['dbconnection']));
	return $res;

}

function SQLError($sql, $error, $return_output=false)
{
	global $lang;

	$ret='<div align="center"><table style="border:1px solid #ff0000" cellspacing="0">
<tr bgcolor="#ff0000"><td style="color:white;font-size:16px;"><strong>MySQL-ERROR</strong></td></tr>
<tr><td style="width:80%;overflow: auto;">' . $lang['L_SQL_ERROR2'] . '<br><span style="color:red;">' . $error . '</span></td></tr>
<tr><td width="600"><br>' . $lang['L_SQL_ERROR1'] . '<br>' . Highlight_SQL($sql) . '</td></tr>
</table></div><br />';
	if ($return_output) return $ret;
	else echo $ret;
}

function Highlight_SQL($sql)
{
	global $sql_keywords;

	$end='';
	$tickstart=false;
	if (function_exists("token_get_all")) $a=@token_get_all("<?php $sql?>");
	else return $sql;
	foreach ($a as $token)
	{
		if (!is_array($token))
		{
			if ($token == '`') $tickstart=!$tickstart;
			$end.=$token;
		}
		else
		{
			if ($tickstart) $end.=$token[1];
			else
			{
				switch (token_name($token[0]))
				{
					case "T_STRING":
					case "T_AS":
					case "T_FOR":

						$end.=( in_array(strtoupper($token[1]),$sql_keywords) ) ? "<span style=\"color:#990099;font-weight:bold;\">" . $token[1] . "</span>" : $token[1];
						break;
					case "T_IF":
					case "T_LOGICAL_AND":
					case "T_LOGICAL_OR":
					case "T_LOGICAL_XOR":
						$end.=( in_array(strtoupper($token[1]),$sql_keywords) ) ? "<span style=\"color:#0000ff;font-weight:bold;\">" . $token[1] . "</span>" : $token[1];
						break;
					case "T_CLOSE_TAG":
					case "T_OPEN_TAG":
						break;
					default:
						$end.=$token[1];
				}
			}
		}
	}
	$end=preg_replace("/`(.*?)`/si","<span style=\"color:red;\">`$1`</span>",$end);
	return $end;
}

function Fieldlist($db, $tbl)
{
	$fl='';
	$res=MSD_query("SHOW FIELDS FROM `$db`.`$tbl`;");
	if ($res)
	{
		$fl='(';
		for ($i=0; $i < mysql_num_rows($res); $i++)
		{
			$row=mysql_fetch_row($res);
			$fl.='`' . $row[0] . '`,';
		}
		$fl=substr($fl,0,strlen($fl) - 1) . ')';
	}
	return $fl;
}

// reads all Tableinfos and place them in $dump-Array
function getDBInfos()
{
	global $databases,$dump,$config,$tbl_sel,$flipped;
	for ($ii=0; $ii < count($databases['multi']); $ii++)
	{
		$dump['dbindex']=$flipped[$databases['multi'][$ii]];
		$tabellen=mysql_query('SHOW TABLE STATUS FROM `' . $databases['Name'][$dump['dbindex']] . '`',$config['dbconnection']) or die('getDBInfos: ' . mysql_error());
		$num_tables=mysql_num_rows($tabellen);
		// Array mit den gewünschten Tabellen zusammenstellen... wenn Präfix angegeben, werden die anderen einfach nicht übernommen
		if ($num_tables > 0)
		{
			for ($i=0; $i < $num_tables; $i++)
			{
				$row=mysql_fetch_array($tabellen);
				if (isset($row['Type'])) $row['Engine']=$row['Type'];
				if (isset($row['Comment']) && substr(strtoupper($row['Comment']),0,4) == 'VIEW') $dump['table_types'][]='VIEW';
				else $dump['table_types'][]=strtoupper($row['Engine']);
				// check if data needs to be backed up
				if (strtoupper($row['Comment']) == 'VIEW' || ( isset($row['Engine']) && in_array(strtoupper($row['Engine']),array(
                    'MEMORY'
				)) ))
				{
					$dump['skip_data'][]=$databases['Name'][$dump['dbindex']] . '|' . $row['Name'];
				}
                    if ($config['optimize_tables_beforedump'] == 1 && $dump['table_offset'] == -1
                        && $databases['Name'][$dump['dbindex']]!='information_schema') {
                        mysql_select_db($databases['Name'][$dump['dbindex']]);
                        $opt = 'OPTIMIZE TABLE `' . $row['Name'] . '`';
                        $res = mysql_query('OPTIMIZE TABLE `' . $row['Name'] . '`');
                        if ($res === false) {
                            die("Error in ".$opt." -> ".mysql_error());
                        }
                    }

            if (isset($tbl_sel))
				{
					if (in_array($row['Name'],$dump['tblArray']))
					{
						$dump['tables'][]=$databases['Name'][$dump['dbindex']] . '|' . $row['Name'];
						$dump['records'][]=$databases['Name'][$dump['dbindex']] . '|' . $row['Rows'];
						$dump['totalrecords']+=$row['Rows'];
					}
				}
				elseif ($databases['praefix'][$dump['dbindex']] != '' && !isset($tbl_sel))
				{
					if (substr($row['Name'],0,strlen($databases['praefix'][$dump['dbindex']])) == $databases['praefix'][$dump['dbindex']])
					{
						$dump['tables'][]=$databases['Name'][$dump['dbindex']] . '|' . $row['Name'];
						$dump['records'][]=$databases['Name'][$dump['dbindex']] . '|' . $row['Rows'];
						$dump['totalrecords']+=$row['Rows'];
					}
				}
				else
				{
					$dump['tables'][]=$databases['Name'][$dump['dbindex']] . '|' . $row['Name'];
					$dump['records'][]=$databases['Name'][$dump['dbindex']] . '|' . $row['Rows'];

					// Get nr of records -> need to do it this way because of incorrect returns when using InnoDBs
					$sql_2="SELECT count(*) as `count_records` FROM `" . $databases['Name'][$dump['dbindex']] . "`.`" . $row['Name'] . "`";
					$res2=@mysql_query($sql_2);
					if ($res2 === false)
					{
						$read_error='(' . mysql_errno() . ') ' . mysql_error();
						SQLError($read_error,$sql_2);
						WriteLog($read_error);
						if ($config['stop_with_error'] > 0)
						{
							die($read_error);
						}
					}
					else
					{
						$row2=@mysql_fetch_array($res2);
						$row['Rows']=$row2['count_records'];
						$dump['totalrecords']+=$row['Rows'];
					}
				}
			}
			// Correct total number of records; substract skipped data
			foreach ($dump['skip_data'] as $skip_data)
			{
				$index=false;
				$records_to_skip=0;
				//find index of table to get the nr of records
				$count=sizeof($dump['tables']);
				for ($a=0; $a < $count; $a++)
				{
					if ($dump['tables'][$a] == $skip_data)
					{
						$index=$a;
						$t=explode('|',$dump['records'][$a]);
						$rekords_to_skip=$t[1];
						break;
					}
				}
				if ($index) $dump['totalrecords']-=$rekords_to_skip;
			}
		}
	}
}

// gets the numeric index in dump-array and returns it
function getDBIndex($db, $table)
{
	global $dump;
	$index=array_keys($dump['tables'],$db . '|' . $table);
	return $index[0];
}
?>
