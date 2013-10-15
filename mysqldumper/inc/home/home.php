<?php
if (!defined('MSD_VERSION')) die('No direct access.');
$Sum_Files=$Sum_Size=0;
$Last_BU=Array();
$is_htaccess=(file_exists('./.htaccess'));

// find latest backup file
$dh=opendir($config['paths']['backup']);
while (false!==($filename=readdir($dh)))
{
	if ($filename!='.'&&$filename!='..'&&!is_dir($config['paths']['backup'].$filename))
	{
		$files[]=$filename;
		$Sum_Files++;
		$Sum_Size+=filesize($config['paths']['backup'].$filename);
		$ft=filectime($config['paths']['backup'].$filename);
		if (!isset($Last_BU[2])||(isset($Last_BU[2])&&$ft>$Last_BU[2]))
		{
			$Last_BU[0]=$filename;
			$Last_BU[1]=date("d.m.Y H:i",$ft);
			$Last_BU[2]=$ft;
		}
	}
}
$directory_warnings=DirectoryWarnings();

$tpl=new MSDTemplate();
$tpl->set_filenames(array(
	'show' => 'tpl/home/home.tpl'));
$tpl->assign_vars(array(
	'THEME' => $config['theme'], 
	'MSD_VERSION' => MSD_VERSION, 
	'OS' => MSD_OS, 
	'OS_EXT' => MSD_OS_EXT, 
	'MYSQL_VERSION' => MSD_MYSQL_VERSION, 
	'PHP_VERSION' => PHP_VERSION, 
	'MEMORY' => byte_output($config['php_ram']*1024*1024), 
	'MAX_EXECUTION_TIME' => $config['max_execution_time'], 
	'PHP_EXTENSIONS' => $config['phpextensions'], 
	'SERVER_NAME' => $_SERVER['SERVER_NAME'], 
	'MSD_PATH' => $config['paths']['root'], 
	'DB' => $databases['db_actual'], 
	'NR_OF_BACKUP_FILES' => $Sum_Files, 
	'SIZE_BACKUPS' => byte_output($Sum_Size), 
	'FREE_DISKSPACE' => MD_FreeDiskSpace()));
if ($directory_warnings>'') $tpl->assign_block_vars('DIRECTORY_WARNINGS',array(
	'MSG' => $directory_warnings));

if ($config['disabled']>'') $tpl->assign_block_vars('DISABLED_FUNCTIONS',array(
	'PHP_DISABLED_FUNCTIONS' => str_replace(',',', ',$config['disabled'])));

// Zlib is buggy from version 4.3.0 upto 4.3.2, so lets check for these versions
if (version_compare(PHP_VERSION,'4.3.0','>=')&&version_compare(PHP_VERSION,'4.3.2','<=')) $tpl->assign_block_vars('ZLIBBUG',array());
if (!extension_loaded('ftp')) $tpl->assign_block_vars('NO_FTP',array());
if (!$config['zlib']) $tpl->assign_block_vars('NO_ZLIB',array());
if ($is_htaccess) $tpl->assign_block_vars('HTACCESS_EXISTS',array());
else
	$tpl->assign_block_vars('HTACCESS_DOESNT_EXISTS',array());
if ($Sum_Files>0&&isset($Last_BU[1])) $tpl->assign_block_vars('LAST_BACKUP',array(
	'LAST_BACKUP_INFO' => $Last_BU[1], 
	'LAST_BACKUP_LINK' => $config['paths']['backup'].urlencode($Last_BU[0]), 
	'LAST_BACKUP_NAME' => $Last_BU[0]));
$tpl->pparse('show');
