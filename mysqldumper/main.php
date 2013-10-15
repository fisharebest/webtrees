<?php
if (!@ob_start("ob_gzhandler")) @ob_start();
include_once ('./inc/header.php');
include_once ('./inc/runtime.php');
include_once ('./language/'.$config['language'].'/lang_main.php');
include ('./inc/template.php');

$action=(isset($_GET['action'])) ? $_GET['action'] : 'status';

if ($action=='phpinfo')
{
	// output phpinfo
	echo '<p align="center"><a href="main.php">&lt;&lt; Home</a></p>';
	phpinfo();
	echo '<p align="center"><a href="main.php">&lt;&lt; Home</a></p>';
	exit();
}

if (isset($_POST['htaccess'])||$action=='schutz') include ('./inc/home/protection_create.php');
if ($action=='edithtaccess') include ('./inc/home/protection_edit.php');
if ($action=='deletehtaccess') include ('./inc/home/protection_delete.php');

// Output headnavi
$tpl=new MSDTemplate();
$tpl->set_filenames(array(
	'show' => 'tpl/home/headnavi.tpl'));
$tpl->assign_vars(array(
	'HEADER' => MSDHeader(), 
	'HEADLINE' => headline('Home')));
$tpl->pparse('show');

MSD_mysql_connect();
if ($action=='status') include ('./inc/home/home.php');
elseif ($action=='db') include ('./inc/home/databases.php');
elseif ($action=='sys') include ('./inc/home/system.php');
elseif ($action=='vars') include ('./inc/home/mysql_variables.php');

echo MSDFooter();
ob_end_flush();