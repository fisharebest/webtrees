<?php
if (!@ob_start("ob_gzhandler")) @ob_start();
include_once ('./inc/header.php');
include ('./inc/template.php');
$lang_old=$config['language'];
$config_refresh='';

// define template
$tpl=new MSDTemplate();
$tpl->set_filenames(array(
	'header' => 'tpl/menu/header.tpl',
	'footer' => 'tpl/menu/footer.tpl',
	'content' => 'tpl/menu/content.tpl'));

$tpl->assign_vars(array(
	'MSD_VERSION' => MSD_VERSION,
	'CONFIG_HOMEPAGE' => $config['homepage'],
	'CONFIG_THEME' => $config['theme']));

if (isset($_POST['selected_config'])||isset($_GET['config']))
{
	if (isset($_POST['selected_config'])) $new_config=$_POST['selected_config'];
	// Configuration was switched in content frame?
	if (isset($_GET['config'])) $new_config=$_GET['config'];
	// restore the last active menuitem
	if (is_readable($config['paths']['config'].$new_config.'.php'))
	{
		clearstatcache();
		unset($databases);
		$databases=array();
		if (read_config($new_config))
		{
			$config['config_file']=$new_config;
			$_SESSION['config_file']=$new_config; //$config['config_file'];
			$config_refresh='
			<script language="JavaScript" type="text/javascript">
			if (parent.MySQL_Dumper_content.location.href.indexOf("config_overview.php")!=-1)
			{
				var selected_div=parent.MySQL_Dumper_content.document.getElementById("sel").value;
			}
			else selected_div=\'\';
			parent.MySQL_Dumper_content.location.href=\'config_overview.php?config='.urlencode($new_config).'&sel=\'+selected_div</script>';
		}
		if (isset($_GET['config'])) $config_refresh=''; //Neu-Aufruf bei Uebergabe aus Content-Bereich verhindern
	}
}

echo MSDHeader(1);
echo headline('',0);

if ($config_refresh>'')
{
	$tpl->assign_block_vars('CONFIG_REFRESH_TRUE',array());
	$tpl->assign_var('CONFIG_REFRESH',$config_refresh);
}

// changed language
if ($config['language']!=$lang_old)
{
	$tpl->assign_block_vars('CHANGED_LANGUAGE',array());
}

if (isset($_GET['action']))
{
	if ($_GET['action']=='dbrefresh')
	{
		// remember the name of the selected database
		$old_dbname=isset($databases['Name'][$databases['db_selected_index']]) ? $databases['Name'][$databases['db_selected_index']] : '';
		SetDefault();
		// select old database if it still is there
		SelectDB($old_dbname);
		$tpl->assign_block_vars('DB_REFRESH',array());
	}
}

if (isset($_POST['dbindex']))
{
	$dbindex=intval($_POST['dbindex']);
	$databases['db_selected_index']=$dbindex;
	$databases['db_actual']=$databases['Name'][$dbindex];

	SelectDB($dbindex);
	WriteParams(0);
	$tpl->assign_block_vars('DB_REFRESH',array());
}
else
	$dbindex=0;

if (isset($_GET['dbindex']))
{
	$dbindex=intval($_GET['dbindex']);
	$databases['db_selected_index']=$dbindex;
	$databases['db_actual']=$databases['Name'][$dbindex];
	SelectDB($dbindex);
	WriteParams(0);
}

if (isset($databases['Name'])&&count($databases['Name'])>0)
{
	$tpl->assign_block_vars('MAINTENANCE',array());
	$tpl->assign_vars(array(
		'DB_ACTUAL' => $databases['db_actual'],
		'DB_SELECTED_INDEX' => $databases['db_selected_index']));
}
$tpl->assign_var('GET_FILELIST',get_config_filelist());

if (isset($databases['Name'])&&count($databases['Name'])>0)
{
	$tpl->assign_block_vars('DB_LIST',array());
	$datenbanken=count($databases['Name']);
	for ($i=0; $i<$datenbanken; $i++)
	{
		$selected=($i==$databases['db_selected_index']) ? ' selected' : '';
		$tpl->assign_block_vars('DB_LIST.DB_ROW',array(
			'ID' => $i,
			'NAME' => $databases['Name'][$i],
			'SELECTED' => $selected));
	}
}
else
	$tpl->assign_block_vars('NO_DB_FOUND',array());

$tpl->assign_var('PIC_CACHE',PicCache());

if (!isset($databases['Name'])||count($databases['Name'])<1)
{
	$tpl->assign_block_vars('DB_NAME_TRUE',array());
}
else
	$tpl->assign_block_vars('DB_NAME_FALSE',array());

$tpl->pparse('header');
$tpl->pparse('content');
$tpl->pparse('footer');

ob_end_flush();