<?php
if (!@ob_start("ob_gzhandler")) @ob_start();
$download=(isset($_POST['f_export_submit'])&&(isset($_POST['f_export_sendresult'])&&$_POST['f_export_sendresult']==1));
include ('./inc/header.php');
include ('language/'.$config['language'].'/lang.php');
include ('language/'.$config['language'].'/lang_sql.php');
include ('./inc/functions_sql.php');
include ('./'.$config['files']['parameter']);
include ('./inc/template.php');
include ('./inc/define_icons.php');
$key='';
// stripslashes and trimming is done in runtime.php which is included and executet above
if (isset($_GET['rk']))
{
	$rk=urldecode($_GET['rk']);
	$key=urldecode($rk);
	if (!$rk=@unserialize($key)) $rk=$key;
}
else
	$rk='';
$mode=isset($_GET['mode']) ? $_GET['mode'] : '';

if (isset($_GET['recordkey']))
{
	$recordkey=$_GET['recordkey'];
	$key=isset($_GET['recordkey']) ? urldecode($recordkey) : $recordkey;
	if (!$recordkey=@unserialize(urldecode($key))) $recordkey=urldecode($key);
}
if (isset($_POST['recordkey'])) $recordkey=urldecode($_POST['recordkey']);

$context=(!isset($_GET['context'])) ? 0 : $_GET['context'];
$context=(!isset($_POST['context'])) ? $context : $_POST['context'];

if (!$download)
{
	echo MSDHeader();
	ReadSQL();
	echo '<script language="JavaScript" type="text/javascript">
		var auswahl = "document.getElementsByName(\"f_export_tables[]\")[0]";
		var msg1="'.$lang['L_SQL_NOTABLESSELECTED'].'";
		</script>';
}
//Variabeln
$mysql_help_ref='http://dev.mysql.com/doc/';
$mysql_errorhelp_ref='http://dev.mysql.com/doc/mysql/en/error-handling.html';
$no_order=false;
$tdcompact=(isset($_GET['tdc'])) ? $_GET['tdc'] : $config['interface_table_compact'];
$db=(!isset($_GET['db'])) ? $databases['db_actual'] : $_GET['db'];
$dbid=(!isset($_GET['dbid'])) ? $databases['db_selected_index'] : $_GET['dbid'];
$context=(!isset($_GET['context'])) ? 0 : $_GET['context'];
$context=(!isset($_POST['context'])) ? $context : $_POST['context'];
$tablename=(!isset($_GET['tablename'])) ? '' : $_GET['tablename'];
$limitstart=(isset($_POST['limitstart'])) ? intval($_POST['limitstart']) : 0;
if (isset($_GET['limitstart'])) $limitstart=intval($_GET['limitstart']);
$orderdir=(!isset($_GET['orderdir'])) ? '' : $_GET['orderdir'];
$order=(!isset($_GET['order'])) ? '' : $_GET['order'];
$sqlconfig=(isset($_GET['sqlconfig'])) ? 1 : 0;
$norder=($orderdir=="DESC") ? 'ASC' : 'DESC';
$sql['order_statement']=($order!='') ? ' ORDER BY `'.$order.'` '.$norder : '';
$sql['sql_statement']=(isset($_GET['sql_statement'])) ? urldecode($_GET['sql_statement']) : '';
if (isset($_POST['sql_statement'])) $sql['sql_statement']=$_POST['sql_statement'];

$showtables=(!isset($_GET['showtables'])) ? 0 : $_GET['showtables'];
$limit=$add_sql='';
$bb=(isset($_GET['bb'])) ? $_GET['bb'] : -1;
if (isset($_POST['tablename'])) $tablename=$_POST['tablename'];
$search=(isset($_GET['search'])) ? $_GET['search'] : 0;

//SQL-Statement geposted
if (isset($_POST['execsql']))
{
	$sql['sql_statement']=(isset($_POST['sqltextarea'])) ? $_POST['sqltextarea'] : '';
	$db=$_POST['db'];
	$dbid=$_POST['dbid'];
	$tablename=$_POST['tablename'];
	if (isset($_POST['tablecombo'])&&$_POST['tablecombo']>'')
	{
		$sql['sql_statement']=$_POST['tablecombo'];
		$tablename=ExtractTablenameFromSQL($sql['sql_statement']);
	
	}
	if (isset($_POST['sqltextarea'])&&$_POST['sqltextarea']>'') $tablename=ExtractTablenameFromSQL($_POST['sqltextarea']);
	if ($tablename=='') $tablename=ExtractTablenameFromSQL($sql['sql_statement']);
}

if ($sql['sql_statement']=='')
{
	if ($tablename!=''&&$showtables==0)
	{
		$sql['sql_statement']="SELECT * FROM `$tablename`";
	}
	else
	{
		$sql['sql_statement']="SHOW TABLE STATUS FROM `$db`";
		$showtables=1;
	}
}

//sql-type
$sql_to_display_data=0;
$Anzahl_SQLs=getCountSQLStatements($sql['sql_statement']);
$sql_to_display_data=sqlReturnsRecords($sql['sql_statement']);
if ($Anzahl_SQLs>1) $sql_to_display_data=0;
if ($sql_to_display_data==1)
{
	//nur ein SQL-Statement
	$limitende=($limitstart+$config['sql_limit']);
	
	//Darf editiert werden?
	$no_edit=(strtoupper(substr($sql['sql_statement'],0,6))!="SELECT"||$showtables==1||preg_match('@^((-- |#)[^\n]*\n|/\*.*?\*/)*(UNION|JOIN)@im',$sql['sql_statement']));
	if ($no_edit) $no_order=true;
	
	//Darf sortiert werden?
	$op=strpos(strtoupper($sql['sql_statement'])," ORDER ");
	if ($op>0)
	{
		//is order by last ?
		$sql['order_statement']=substr($sql['sql_statement'],$op);
		if (strpos($sql['order_statement'],')')>0) $sql['order_statement']='';
		else
			$sql['sql_statement']=substr($sql['sql_statement'],0,$op);
	}
}

if (isset($_POST['tableselect'])&&$_POST['tableselect']!='1') $tablename=$_POST['tableselect'];
MSD_mysql_connect();
mysql_select_db($db,$config['dbconnection']);

///*** EDIT / UPDATES / INSERTS ***///
///***                          ***///


// handle update action after submitting it
if (isset($_POST['update'])||isset($_GET['update']))
{
	GetPostParams();
	$f=explode('|',$_POST['feldnamen']);
	$sqlu='UPDATE `'.$_POST['db'].'`.`'.$tablename.'` SET ';
	for ($i=0; $i<count($f); $i++)
	{
		$index=isset($_POST[$f[$i]]) ? $f[$i] : correct_post_index($f[$i]);
		// Check if field is set to null
		if (isset($_POST['null_'.$index]))
		{
			// Yes, set it to NULL in Querystring
			$sqlu.='`'.$f[$i].'`=NULL, ';
		}
		else
			$sqlu.='`'.$f[$i].'`=\''.db_escape(convert_to_latin1($_POST[$index])).'\', ';
	}
	$sqlu=substr($sqlu,0,strlen($sqlu)-2).' WHERE '.$recordkey;
	$res=MSD_query($sqlu);
	$msg='<p class="success">'.$lang['L_SQL_RECORDUPDATED'].'</p>';
	if (isset($mode)&&$mode=='searchedit') $search=1;
	$sql_to_display_data=1;
}
// handle insert action after submitting it
if (isset($_POST['insert']))
{
	GetPostParams();
	$f=explode('|',$_POST['feldnamen']);
	$sqlu='INSERT INTO `'.$tablename.'` SET ';
	for ($i=0; $i<count($f); $i++)
	{
		$index=isset($_POST[$f[$i]]) ? $f[$i] : correct_post_index($f[$i]);
		if (isset($_POST['null_'.$index]))
		{
			// Yes, set it to NULL in Querystring
			$sqlu.='`'.$f[$i].'`=NULL, ';
		}
		else
			$sqlu.='`'.$f[$i].'`=\''.db_escape(convert_to_latin1($_POST[$index])).'\', ';
	}
	$sqlu=substr($sqlu,0,strlen($sqlu)-2);
	$res=MSD_query($sqlu);
	$msg='<p class="success">'.$lang['L_SQL_RECORDINSERTED'].'</p>';
	$sql_to_display_data=1;
}

if (isset($_POST['cancel'])) GetPostParams();

//Tabellenansicht
$showtables=(substr(strtoupper($sql['sql_statement']),0,10)=='SHOW TABLE') ? 1 : 0;
$tabellenansicht=(substr(strtoupper($sql['sql_statement']),0,5)=='SHOW ') ? 1 : 0;

if (!isset($limitstart)) $limitstart=0;
$limitende=$config['sql_limit'];
if (strtolower(substr($sql['sql_statement'],0,6))=='select') $limit=' LIMIT '.$limitstart.', '.$limitende.';';

$params="sql.php?db=".$db."&amp;tablename=".$tablename."&amp;dbid=".$dbid.'&amp;context='.$context.'&amp;sql_statement='.urlencode($sql['sql_statement']).'&amp;tdc='.$tdcompact.'&amp;showtables='.$showtables;
if ($order!="") $params.="&amp;order=".$order."&amp;orderdir=".$orderdir.'&amp;context='.$context;
if ($bb>-1) $params.="&amp;bb=".$bb;

$aus=headline($lang['L_SQL_BROWSER']);

if ($search==0&&!$download)
{
	echo $aus;
	$aus='';
	include ('./inc/sqlbrowser/sqlbox.php');
	
	if ($mode>''&&$context==0)
	{
		if (isset($recordkey)&&$recordkey>'') $rk=urldecode($recordkey);
		if (isset($_GET['tablename'])) $tablename=$_GET['tablename'];
		
		if ($mode=='kill'||$mode=='kill_view')
		{
			if ($showtables==0)
			{
				$sqlk="DELETE FROM `$tablename` WHERE ".$rk." LIMIT 1";
				$res=MSD_query($sqlk);
				//echo "<br>".$sqlk;
				$aus.='<p class="success">'.$lang['L_SQL_RECORDDELETED'].'</p>';
			}
			else
			{
				$sqlk="DROP TABLE `$rk`";
				if ($mode=='kill_view') $sqlk='DROP VIEW `'.$rk.'`';
				$res=MSD_query($sqlk);
				$aus.='<p class="success">'.sprintf($lang['L_SQL_RECORDDELETED'],$rk).'</p>';
			}
		}
		if ($mode=="empty")
		{
			
			if ($showtables!=0)
			{
				$sqlk="TRUNCATE `$rk`";
				$res=MSD_query($sqlk);
				$aus.='<p class="success">'.sprintf($lang['L_SQL_TABLEEMPTIED'],$rk).'</p>';
			}
		}
		if ($mode=="emptyk")
		{
			if ($showtables!=0)
			{
				$sqlk="TRUNCATE `$rk`;";
				$res=MSD_query($sqlk);
				$sqlk="ALTER TABLE `$rk` AUTO_INCREMENT=1;";
				$res=MSD_query($sqlk);
				$aus.='<p class="success">'.sprintf($lang['L_SQL_TABLEEMPTIEDKEYS'],$rk).'</p>';
			}
		}
		
		$javascript_switch='<script language="javascript" type="text/javascript">
function switch_area(textarea)
{
	var t=document.getElementById(\'area_\'+textarea);
	var c=document.getElementById(\'null_\'+textarea);
	if (c.checked==true) { t.className="off";t.disabled=true;  }
	else { t.className="";t.disabled=false;  }
}
</script>';
		
		if ($mode=='edit'||$mode=='searchedit') include ('./inc/sqlbrowser/sql_record_update_inputmask.php');
		if ($mode=='new') include ('./inc/sqlbrowser/sql_record_insert_inputmask.php');
	}
	if ($context==0) include_once ('./inc/sqlbrowser/sql_dataview.php');
	if ($context==1) include ('./inc/sqlbrowser/sql_commands.php');
	if ($context==2) include ('./inc/sqlbrowser/sql_tables.php');
	if ($context==3) include ('./inc/sql_tools.php');
}
if ($context==4) include ('./inc/sql_importexport.php');
if ($search==1) include ('./inc/sqlbrowser/mysql_search.php');
if (!$download)
{
	?>
<script language="JavaScript" type="text/javascript">
function BrowseInput(el)
{
	var txt=document.getElementsByName('imexta')[0].value;
	var win=window.open('about:blank','MSD_Output','resizable=1,scrollbars=yes');
	win.document.write(txt);
	win.document.close();
	win.focus();
}
</script>
<?php
	
	echo '<br><br><br>';
	echo MSDFooter();
	ob_end_flush();
}

function FormHiddenParams()
{
	global $db,$dbid,$tablename,$context,$limitstart,$order,$orderdir;
	
	$s='<input type="hidden" name="db" value="'.$db.'">';
	$s.='<input type="hidden" name="dbid" value="'.$dbid.'">';
	$s.='<input type="hidden" name="tablename" value="'.$tablename.'">';
	$s.='<input type="hidden" name="context" value="'.$context.'">';
	$s.='<input type="hidden" name="limitstart" value="'.$limitstart.'">';
	$s.='<input type="hidden" name="order" value="'.$order.'">';
	$s.='<input type="hidden" name="orderdir" value="'.$orderdir.'">';
	return $s;
}