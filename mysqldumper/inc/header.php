<?php
$msd_path=realpath(dirname(__FILE__) . '/../') . '/';
if (!defined('MSD_PATH')) define('MSD_PATH',$msd_path);
session_name('MySQLDumper');
session_start();
if (!isset($download))
{
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0",false);
	header("Pragma: no-cache");
}
include ( MSD_PATH . 'inc/functions.php' );
include ( MSD_PATH . 'inc/mysql.php' );
if (!defined('MSD_VERSION')) die('No direct access.');
if (!file_exists($config['files']['parameter'])) $error=TestWorkDir();
read_config($config['config_file']);
include ( MSD_PATH . 'language/lang_list.php' );
if (!isset($databases['db_selected_index'])) $databases['db_selected_index']=0;
SelectDB($databases['db_selected_index']);
$config['files']['iconpath']='./css/' . $config['theme'] . '/icons/';
if (isset($error)) echo $error;