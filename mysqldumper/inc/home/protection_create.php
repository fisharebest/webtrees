<?php
if (!defined('MSD_VERSION')) die('No direct access.');
include ('./language/'.$config['language'].'/lang_sql.php');

$dba=$hta_dir=$Overwrite=$msg='';
$error=array();
$is_htaccess=(file_exists('./.htaccess'));
if ($is_htaccess)
{
	$Overwrite='<p class="error">'.$lang['L_HTACCESS8'].'</p>';
	$htaccess_exist=file('.htaccess'); // read .htaccess
}

$step=(isset($_POST['step'])) ? intval($_POST['step']) : 0;
$type=0; // default encryption type set to crypt()
if (strtoupper(substr(MSD_OS,0,3))=='WIN') $type=2; // we are on a Win-System; pre-select encryption type
if (isset($_POST['type'])) $type=intval($_POST['type']);
$username=(isset($_POST['username'])) ? $_POST['username'] : '';
$userpass1=(isset($_POST['userpass1'])) ? $_POST['userpass1'] : '';
$userpass2=(isset($_POST['userpass2'])) ? $_POST['userpass2'] : '';

header('Pragma: no-cache');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: -1");
header('Content-Type: text/html; charset=UTF-8');
$tpl=new MSDTemplate();
$tpl->set_filenames(array(
	'show' => './tpl/home/protection_create.tpl'));
$tpl->assign_vars(array(
	'THEME' => $config['theme'],
	'HEADLINE' => headline($lang['L_HTACC_CREATE'])));

if (isset($_POST['username']))
{
	// Form submitted
	if ($username=='') $error[]=$lang['L_HTACC_NO_USERNAME'];
	if (($userpass1!=$userpass2)||($userpass1=='')) $error[]=$lang['L_PASSWORDS_UNEQUAL'];

	if (sizeof($error)==0)
	{
		$htaccess = "<IfModule mod_rewrite.c>\nRewriteEngine off\n</IfModule>\n";
		$realm='MySQLDumper';
		$htaccess.="AuthName \"".$realm."\"\nAuthType Basic\nAuthUserFile \""
		  .$config['paths']['root'].".htpasswd\"\nrequire valid-user";
		switch ($type)
		{
			// Crypt
			case 0:
				$userpass=crypt($userpass1);
				break;
			// MD5
			case 1:
				$userpass=md5($username.':'.$realm.':'.$userpass1);
				break;
			// WIn - no encryption
			case 2:
				$userpass=$userpass1;
				break;
			// SHA
			case 3:
				$userpass='{SHA}'.base64_encode(sha1($userpass1,TRUE));
				break;
		}
		$htpasswd=$username.':'.$userpass;
		@chmod($config['paths']['root'],0777);

		// save .htpasswd
		if ($file_htpasswd=@fopen('.htpasswd','w'))
		{
			$saved=fputs($file_htpasswd,$htpasswd);
			fclose($file_htpasswd);
		}
		else
			$saved=false;

		// save .htaccess
		if (false!==$saved)
		{
			$file_htaccess=@fopen('.htaccess','w');
			if ($file_htaccess)
			{
				$saved=fputs($file_htaccess,$htaccess);
				fclose($file_htaccess);
			}
			else
				$saved=false;
		}

		if (false!==$saved)
		{
		    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
                $output  = array(
                    'HTACCESS' => nl2br(htmlspecialchars($htaccess), false),
                    'HTPASSWD' => nl2br(htmlspecialchars($htpasswd), false)
                );
            } else {
                $output  = array(
                    'HTACCESS' => nl2br(htmlspecialchars($htaccess)),
                    'HTPASSWD' => nl2br(htmlspecialchars($htpasswd))
                );
		    }

		    $msg='<span class="success">'.$lang['L_HTACC_CREATED'].'</span>';
			$tpl->assign_block_vars('CREATE_SUCCESS', $output);
			@chmod($config['paths']['root'],0755);
		}
		else
		{
			$tpl->assign_block_vars('CREATE_ERROR',array(
				'HTACCESS' => htmlspecialchars($htaccess),
				'HTPASSWD' => htmlspecialchars($htpasswd)));
		}
	}
}

if (sizeof($error)>0||!isset($_POST['username']))
{
	$tpl->assign_vars(array(
		'PASSWORDS_UNEQUAL' => my_addslashes($lang['L_PASSWORDS_UNEQUAL']),
		'HTACC_CONFIRM_DELETE' => my_addslashes($lang['L_HTACC_CONFIRM_DELETE'])));

	$tpl->assign_block_vars('INPUT',array(
		'USERNAME' => htmlspecialchars($username),
		'USERPASS1' => htmlspecialchars($userpass1),
		'USERPASS2' => htmlspecialchars($userpass2),
		'TYPE0_CHECKED' => $type==0 ? ' checked="checked"' : '',
		'TYPE1_CHECKED' => $type==1 ? ' checked="checked"' : '',
		'TYPE2_CHECKED' => $type==2 ? ' checked="checked"' : '',
		'TYPE3_CHECKED' => $type==3 ? ' checked="checked"' : ''));
}

if (sizeof($error)>0) $msg='<span class="error">'.implode('<br>',$error).'</span>';
if ($msg>'') $tpl->assign_block_vars('MSG',array(
	'TEXT' => $msg));

$tpl->pparse('show');

echo MSDFooter();
ob_end_flush();
die();