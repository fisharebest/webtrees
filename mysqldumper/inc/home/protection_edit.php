<?php
if (!defined('MSD_VERSION')) die('No direct access.');
include ('./language/'.$config['language'].'/lang_sql.php');
echo MSDHeader();
echo headline($lang['L_HTACC_EDIT']);

$htaccessdontexist=0;

if (isset($_POST['hta_dir'])&&isset($_POST['hta_file'])&&is_dir($_POST['hta_dir']))
{
	$hta_dir=$_POST['hta_dir'];
	$hta_file=$_POST['hta_file'];
}
else
{
	$hta_dir=$config['paths']['root'];
	$hta_file='.htaccess';
}
if ($hta_dir!=''&substr($hta_dir,-1)!='/') $hta_dir.='/';
$hta_complete=$hta_dir.$hta_file;

if ((isset($_GET['create'])&&$_GET['create']==1)||(isset($_POST['create'])&&$_POST['create']==1))
{
	$fp=fopen($hta_complete,'w');
	fwrite($fp,"# created by MySQLDumper ".MSD_VERSION."\n");
	fclose($fp);
}

if (isset($_POST['submit'])&&isset($_POST['thta']))
{
	$fp=fopen($hta_complete,'w');
	fwrite($fp,$_POST['thta']);
	fclose($fp);
}
if (file_exists($hta_complete))
{
	$htaccess_exist=file($hta_complete);
}
else
{
	$htaccessdontexist=1;
}

echo $lang['L_HTACCESS32'];
echo '<br><br><form name="ehta" action="main.php?action=edithtaccess" method="post">';
echo '<table>';
echo '<tr><td>'.$lang['L_DIR'].':</td><td><input type="text" name="hta_dir" value="'.$hta_dir.'" size="60"></td></tr>';
echo '<tr><td>'.$lang['L_FILE'].':</td><td><input type="text" name="hta_file" value="'.$hta_file.'"></td></tr>';
echo '</table>';
if ($htaccessdontexist!=1)
{
	echo '<table class="bdr"><tr><td style="width:70%;"><textarea rows="25" cols="40" name="thta" id="thta">'.htmlspecialchars(implode("",$htaccess_exist)).'</textarea><br><br>';
	echo '</td><td valign="top">';
	//Presets
	echo '<h6>Presets</h6><p><strong>'.$lang['L_HTACCESS30'].'</strong><p>
		<a href="javascript:insertHTA(1,document.ehta.thta)">all-inkl</a><br>

		<br><p><strong>'.$lang['L_HTACCESS31'].'</strong></p>
		<a href="javascript:insertHTA(101,document.ehta.thta)">'.$lang['L_HTACCESS20'].'</a><br>
		<a href="javascript:insertHTA(102,document.ehta.thta)">'.$lang['L_HTACCESS21'].'</a><br>
		<a href="javascript:insertHTA(103,document.ehta.thta)">'.$lang['L_HTACCESS22'].'</a><br>
		<a href="javascript:insertHTA(104,document.ehta.thta)">'.$lang['L_HTACCESS23'].'</a><br>
		<a href="javascript:insertHTA(105,document.ehta.thta)">'.$lang['L_HTACCESS24'].'</a><br>
		<a href="javascript:insertHTA(106,document.ehta.thta)">'.$lang['L_HTACCESS25'].'</a><br>
		<a href="javascript:insertHTA(107,document.ehta.thta)">'.$lang['L_HTACCESS26'].'</a><br>
		<a href="javascript:insertHTA(108,document.ehta.thta)">'.$lang['L_HTACCESS27'].'</a><br>
		<a href="javascript:insertHTA(109,document.ehta.thta)">'.$lang['L_HTACCESS28'].'</a><br>
		<br><a href="http://httpd.apache.org/docs/2.0/mod/directives.html" target="_blank">'.$lang['L_HTACCESS29'].'</a>';
	echo '</td></tr>';
	echo '<tr><td colspan="2">';
	echo '<input type="submit" name="submit" value=" '.$lang['L_SAVE'].' " class="Formbutton">&nbsp;&nbsp;&nbsp;';
	echo '<input type="reset" name="reset" value=" '.$lang['L_RESET'].' " class="Formbutton">&nbsp;&nbsp;&nbsp;';
	echo '<input type="submit" name="newload" value=" '.$lang['L_HTACCESS19'].' " class="Formbutton">';
	echo '</td></tr></table></form>';
}
else
{
	echo '<br>'.$lang['L_FILE_MISSING'].': '.$hta_complete.'<br><br>';
	echo '<form action="" method="post"><input type="hidden" name="hta_dir" value="'.$hta_dir.'"><input type="hidden" name="hta_file" value="'.$hta_file.'"><input type="hidden" name="create" value="1"><input type="submit" name="createhtaccess" value="'.$lang['L_CREATE'].'" class="Formbutton"></form>';
}
echo '</div>';
ob_end_flush();
exit();