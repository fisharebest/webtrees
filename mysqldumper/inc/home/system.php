<?php
if (!defined('MSD_VERSION')) die('No direct access.');
$sysaction=(isset($_GET['dosys'])) ? $_GET['dosys'] : 0;
$msg="";
$res=@mysql_query("SHOW VARIABLES LIKE 'datadir'",$config['dbconnection']);
if ($res)
{
	$row=mysql_fetch_array($res);
	$data_dir=$row[1];
}
switch ($sysaction)
{
	case 1: //FLUSH PRIVILEGES
		$msg="&gt; operating FLUSH PRIVILEGES<br>";
		$res=@mysql_query("FLUSH PRIVILEGES",$config['dbconnection']);
		$meldung=mysql_error($config['dbconnection']);
		if ($meldung!="")
		{
			$msg.='&gt; MySQL-Error: '.$meldung;
		}
		else
		{
			$msg.="&gt; Privileges were reloaded.";
		}
		break;
	case 2: //FLUSH STATUS
		$msg="&gt; operating FLUSH STATUS<br>";
		$res=@mysql_query("FLUSH STATUS",$config['dbconnection']);
		$meldung=mysql_error($config['dbconnection']);
		if ($meldung!="")
		{
			$msg.='&gt; MySQL-Error: '.$meldung;
		}
		else
		{
			$msg.="&gt; Status was reset.";
		}
		break;
	case 3: //FLUSH HOSTS
		$msg="&gt; operating FLUSH HOSTS<br>";
		$res=@mysql_query("FLUSH HOSTS",$config['dbconnection']);
		$meldung=mysql_error($config['dbconnection']);
		if ($meldung!="")
		{
			$msg.='&gt; MySQL-Error: '.$meldung;
		}
		else
		{
			$msg.="&gt; Hosts were reloaded.";
			;
		}
		break;
	case 4: //SHOW MASTER LOGS
		$msg="> operating SHOW MASTER LOGS<br>";
		$res=@mysql_query("SHOW MASTER LOGS",$config['dbconnection']);
		$meldung=mysql_error($config['dbconnection']);
		if ($meldung!="")
		{
			$msg.='&gt; MySQL-Error: '.$meldung;
		}
		else
		{
			$numrows=mysql_num_rows($res);
			if ($numrows==0||$numrows===false)
			{
				$msg.='&gt; there are no master log-files';
			}
			else
			{
				$msg.='&gt; there are '.$numrows.' logfiles<br>';
				for ($i=0; $i<$numrows; $i++)
				{
					$row=mysql_fetch_row($res);
					$msg.='&gt; '.$row[0].'&nbsp;&nbsp;&nbsp;'.(($data_dir) ? byte_output(@filesize($data_dir.$row[0])) : '').'<br>';
				}
			}
		}
		break;
	case 5: //RESET MASTER
		$msg="&gt; operating RESET MASTER<br>";
		$res=@mysql_query("RESET MASTER",$config['dbconnection']);
		$meldung=mysql_error($config['dbconnection']);
		if ($meldung!="")
		{
			$msg.='&gt; MySQL-Error: '.$meldung;
		}
		else
		{
			$msg.="&gt; All Masterlogs were deleted.";
		}
		break;
}
echo '<h5>'.$lang['L_MYSQLSYS'].'</h5>';
echo '<div id="hormenu"><ul>
			<li><a href="main.php?action=sys&amp;dosys=1">Reload Privileges</a></li>
			<li><a href="main.php?action=sys&amp;dosys=2">Reset Status</a></li>
			<li><a href="main.php?action=sys&amp;dosys=3">Reload Hosts</a></li>
			<li><a href="main.php?action=sys&amp;dosys=4">Show Log-Files</a></li>
			<li><a href="main.php?action=sys&amp;dosys=5">Reset Master-Log</a></li>
			</ul></div>';
echo '<div align="center" class="MySQLbox">';
echo '&gt; MySQL Dumper v'.MSD_VERSION.' - Output Console<br><br>';
echo ($msg!="") ? $msg : '> waiting for operation ...<br>';
echo '</div>';
