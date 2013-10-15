<?php
if (!@ob_start("ob_gzhandler")) @ob_start();
include ( './inc/header.php' );
include_once ( './language/' . $config['language'] . '/lang_log.php' );
echo MSDHeader();

if (isset($_POST['r'])) $r=$_POST['r'];
else $r=( isset($_GET['r']) ) ? $_GET['r'] : 0;

$revers=( isset($_GET['revers']) ) ? $_GET['revers'] : 0;

//loeschen
if (isset($_POST['kill']))
{
	if ($_POST['r'] == 0)
	{
		DeleteLog();
	}
	elseif ($_POST['r'] == 1)
	{
		@unlink($config['files']['perllog']);
		@unlink($config['files']['perllog'] . '.gz');
	}
	elseif ($_POST['r'] == 2)
	{
		@unlink($config['files']['perllogcomplete']);
		@unlink($config['files']['perllogcomplete'] . '.gz');
	}
	elseif ($_POST['r'] == 3)
	{
		@unlink($config['paths']['log'] . "error.log");
		@unlink($config['paths']['log'] . "error.log.gz");
	}
	$r=0;
}

if ($r == 0)
{
	$lfile=$config['files']['log'];
	$lcap="PHP-Log";
}
elseif ($r == 1)
{
	$lfile=$config['files']['perllog'];
	$lcap="Perl-Log";
}
elseif ($r == 2)
{
	$lfile=$config['files']['perllogcomplete'];
	$lcap="Perl-Complete Log";
}
elseif ($r == 3)
{
	$lfile=$config['paths']['log'] . "error.log";
	$lcap="PHP Error-Log";
}
if ($config['logcompression'] == 1) $lfile.=".gz";
if (!file_exists($lfile) && $r == 0)
{
	DeleteLog();
}
$loginfo=LogFileInfo($config['logcompression']);

echo headline($lcap);
if (!is_writable($config['paths']['log'])) die('<p class="error">ERROR !<br>Logdir is not writable</p>');

//lesen
$errorbutton='';
$perlbutton='';
$perlbutton2='';

if (file_exists($loginfo['errorlog'])) $errorbutton='<td><input class="Formbutton" type="button" onclick="location.href=\'log.php?r=3\'" value="Error-Log"></td>';
if (file_exists($loginfo['perllog'])) $perlbutton='<td><input type="button" onclick="location.href=\'log.php?r=1\'" class="Formbutton" value="Perl-Log"></td>';
if (file_exists($loginfo['perllogcomplete'])) $perlbutton2='<td><input class="Formbutton" type="button" onclick="location.href=\'log.php?r=2\'" value="Perl-Complete Log"></td>';

//anzeigen
echo '<form action="log.php" method="post"><table><tr>';
echo '<td><input class="Formbutton" type="button" onclick="location.href=\'log.php?r=0\'" value="PHP-Log"></td>';
echo "\n" . $errorbutton . "\n" . $perlbutton . "\n" . $perlbutton2 . "\n";
echo '</tr></table><br>';

//Status Logfiles
echo '<div align="left"><table class="bdr"><tr><td><table><tr><td valign="top"><strong>' . $lang['L_LOGFILEFORMAT'] . '</strong><br><br>' . ( ( $config['logcompression'] == 1 ) ? '<img src="' . $config['files']['iconpath'] . 'gz.gif" width="32" height="32" alt="compressed" align="left">' : '<img src="' . $icon['blank'].'" width="32" height="32" alt="" align="left">' );
echo '' . ( ( $config['logcompression'] == 1 ) ? $lang['L_COMPRESSED'] : $lang['L_NOTCOMPRESSED'] ) . '</td>';
echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td valign="top" align="right">';
echo '<a href="' . $loginfo['log'] . '">' . substr($loginfo['log'],strrpos($loginfo['log'],"/") + 1) . '</a><br>';
echo ( $loginfo['errorlog_size'] > 0 ) ? '<a href="' . $loginfo['errorlog'] . '">' . substr($loginfo['errorlog'],strrpos($loginfo['errorlog'],"/") + 1) . '</a><br>' : substr($loginfo['errorlog'],strrpos($loginfo['errorlog'],"/") + 1) . '<br>';
echo ( $loginfo['perllog_size'] > 0 ) ? '<a href="' . $loginfo['perllog'] . '">' . substr($loginfo['perllog'],strrpos($loginfo['perllog'],"/") + 1) . '</a><br>' : substr($loginfo['perllog'],strrpos($loginfo['perllog'],"/") + 1) . '<br>';
echo ( $loginfo['perllogcomplete_size'] > 0 ) ? '<a href="' . $loginfo['perllogcomplete'] . '">' . substr($loginfo['perllogcomplete'],strrpos($loginfo['perllogcomplete'],"/") + 1) . '</a><br>' : substr($loginfo['perllogcomplete'],strrpos($loginfo['perllogcomplete'],"/") + 1) . '<br>';
echo '<strong>total</strong></td><td valign="top" align="right">' . byte_output($loginfo['log_size']) . '<br>' . byte_output($loginfo['errorlog_size']) . '<br>' . byte_output($loginfo['perllog_size']) . '<br>' . byte_output($loginfo['perllogcomplete_size']) . '<br><strong>' . byte_output($loginfo['log_totalsize']) . '</strong></td>';
echo '</tr><tr><td colspan="3" align="center"><a class="small" href="log.php?r=' . $r . '&amp;revers=0">' . $lang['L_NOREVERSE'] . '</a>&nbsp;&nbsp;&nbsp;<a class="small" href="log.php?r=' . $r . '&amp;revers=1">' . $lang['L_REVERSE'] . '</a></td></tr></table></td></tr></table></div>';

$out='';
if ($r != 2) $out.='<pre>';

if (file_exists($lfile))
{
	$zeilen=( $config['logcompression'] == 1 ) ? gzfile($lfile) : file($lfile);
	if ($r == 30)
	{
		echo '<pre>' . print_r($zeilen,true) . '</pre>';
		exit();
	}
	if ($revers == 1) $zeilen=array_reverse($zeilen);
	foreach ($zeilen as $zeile)
	{
		if ($r == 2)
		{
			$out.=$zeile . '<br>';
		}
		elseif ($r == 3)
		{
			$z=explode("|:|",$zeile);
			for ($i=0; $i < count($z); $i++)
			{
				$out.='<span>' . substr($z[$i],0,strpos($z[$i],": ")) . '</span> ' . substr($z[$i],strpos($z[$i],": ")) . "<br>";
			}
		}
		else
			$out.=$zeile;
	}
}
if ($r != 2) $out.='</pre>';

$suchen=array(
			
			'</html>', 
			'</body>'
);
$ersetzen=array(
				
				'', 
				''
);
$out=str_replace($suchen,$ersetzen,$out);

if ($out != "")
{
	echo '<div align="left" style="width:100%"><br>';
	echo '<input type="hidden" name="r" value="' . $r . '"><input class="Formbutton" type="submit" name="kill" value="' . $lang['L_LOG_DELETE'] . '">';
	echo '<br><br><div id="ilog">' . $out . '</div></div>';
}

echo '</form>';
echo MSDFooter();
ob_end_flush();