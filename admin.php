<?php
/**
 * Administrative User Interface.
 *
 * Provides links for administrators to get to other administrative areas of the site
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Admin
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'admin.php');
require './includes/session.php';

if (!WT_USER_GEDCOM_ADMIN) {
	if (WT_USER_ID) {
		header("Location: index.php");
		exit;
	} else {
		header("Location: login.php?url=admin.php");
		exit;
	}
}

if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
if (isset($_REQUEST['logfilename'])) $logfilename = $_REQUEST['logfilename'];

if (!isset($action)) $action="";

print_header(i18n::translate('Administration'));
$pending_changes=WT_DB::prepare("SELECT 1 FROM {$TBLPREFIX}change WHERE status='pending' LIMIT 1")->fetchOne();
if ($pending_changes) {
	$d_wt_changes = "<a href=\"javascript:;\" onclick=\"window.open('edit_changes.php','_blank','width=600,height=500,resizable=1,scrollbars=1'); return false;\">".i18n::translate('Accept / Reject Changes').help_link('edit_changes.php')."</a>\n";
} else {
	$d_wt_changes = '&nbsp;';
}

if (!isset($logfilename)) {
	$logfilename = "";
}
$file_nr = 0;
$dir_var = opendir ($INDEX_DIRECTORY);
$dir_array = array();
while ($file = readdir ($dir_var)) {
	if (substr($file,-4)==".log" && substr($file,0,4)== "pgv-") {
		$dir_array[$file_nr] = $file;
		$file_nr++;
	}
}
closedir($dir_var);
$d_logfile_str = "&nbsp;";
if (count($dir_array)>0) {
	sort($dir_array);
	$d_logfile_str = "<form name=\"logform\" action=\"admin.php\" method=\"post\">";
	$d_logfile_str .= i18n::translate('View log files') . ": ";
	$d_logfile_str .= "\n<select name=\"logfilename\">\n";
	$ct = count($dir_array);
	for($x = 0; $x < $file_nr; $x++) {
		$ct--;
		$d_logfile_str .= "<option value=\"";
		$d_logfile_str .= $dir_array[$ct];
		if ($dir_array[$ct] == $logfilename) {
			$d_logfile_str .= "\" selected=\"selected";
		}
		$d_logfile_str .= "\">";
		$d_logfile_str .= $dir_array[$ct];
		$d_logfile_str .= "</option>\n";
	}
	$d_logfile_str .= "</select>\n";
	// $d_logfile_str .= "<input type=\"submit\" name=\"logfile\" value=\" &gt; \" />";
	$d_logfile_str .= "<input type=\"button\" name=\"logfile\" value=\" &gt; \" onclick=\"window.open('printlog.php?logfile='+document.logform.logfilename.options[document.logform.logfilename.selectedIndex].value, '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\" />";
	$d_logfile_str .= "</form>";
}

$err_write = file_is_writeable("config.php");

$verify_msg = false;
$warn_msg = false;
foreach(get_all_users() as $user_id=>$user_name) {
	if (get_user_setting($user_id, 'verified_by_admin')!='yes' && get_user_setting($user_id, 'verified')=='yes')  {
		$verify_msg = true;
	}
	$comment_exp=get_user_setting($user_id, 'comment_exp');
	if (!empty($comment_exp) && (strtotime($comment_exp) != "-1") && (strtotime($comment_exp) < time("U"))) {
		$warn_msg = true;
	}
	if ($verify_msg && $warn_msg) {
		break;
	}
}

echo WT_JS_START, 'function showchanges() {window.location.reload();}', WT_JS_END;
?>
<script type="text/javascript">
//<![CDATA[
  jQuery(document).ready(function(){
    jQuery("#tabs").tabs();
  });
//]]>
  </script>
<table class="center <?php echo $TEXT_DIRECTION ?> width90">
	<tr>
		<td colspan="2" class="center"><?php
		echo '<h2>', WT_WEBTREES, ' ', WT_VERSION_TEXT, '<br />', i18n::translate('Administration'), '</h2>';
		echo i18n::translate('Current Server Time:');
		echo " ".format_timestamp(time());
		echo "<br />".i18n::translate('Current User Time:');
		echo " ".format_timestamp(client_time());
		if (WT_USER_IS_ADMIN) {
			if ($err_write) {
				echo "<br /><span class=\"error\">";
				echo i18n::translate('Your <i>config.php</i> file is still writable.  For security, you should set the permissions of this file back to read-only when you have finished configuring your site.');
				echo "</span><br /><br />";
			}
			if ($verify_msg) {
				echo "<br />";
				echo "<a href=\"".encode_url("useradmin.php?action=listusers&filter=admunver")."\" class=\"error\">".i18n::translate('User accounts awaiting verification by admin')."</a>";
				echo "<br /><br />";
			}
			if ($warn_msg) {
				echo "<br />";
				echo "<a href=\"".encode_url("useradmin.php?action=listusers&filter=warnings")."\" class=\"error\" >".i18n::translate('One or more user accounts have warnings')."</a>";
				echo "<br /><br />";
			}
		}
		?></td>
	</tr>
	
	<tr><td colspan="2">
	
	<div id="tabs" class="width100">
	<ul>
		<li><a href="#info"><span><?php echo i18n::translate('Informational')?></span></a></li>
		<li><a href="#gedcom"><span><?php echo i18n::translate('Data and GEDCOM administration')?></span></a></li>
		<?php if (WT_USER_CAN_EDIT) { ?>
		<li><a href="#unlinked"><span><?php echo i18n::translate('Unlinked Records')?></span></a></li>
		<?php } ?>
		<?php if (WT_USER_IS_ADMIN) { ?>
		<li><a href="#site"><span><?php echo i18n::translate('Site administration')?></span></a></li>
		<?php } ?>
		<?php 
		$modules = WT_Module::getInstalledModules();
		if (WT_USER_IS_ADMIN || count($modules)>0) {?>
		<!-- ---- MODIFIED BY BH ------------------------------------ -->
			<!-- <li><a href="#modules"><span><?php // echo i18n::translate('Module Administration')?></span></a></li> -->
			<li><a href="#modules" onclick="window.location='module_admin.php';" ><span><?php echo i18n::translate('Module Administration')?></span></a></li>
		<!-- -------------------------------------------------------- -->
		<?php } ?>
	</ul>
	<div id="info">
		<table class="center <?php echo $TEXT_DIRECTION ?> width100">
			<tr>                                                                                                                                             
	            <td colspan="2" class="topbottombar" style="text-align:center; "><?php echo i18n::translate('Informational'); ?></td>                            
	    	</tr>
			<tr>
				<td class="optionbox width50">
					<a href="readme.txt" target="manual" title="<?php echo i18n::translate('View readme.txt file'); ?>"><?php echo i18n::translate('README documentation'); ?></a>
					<?php echo help_link('readmefile'); ?>
				</td>
				<td class="optionbox width50">
					<a href="pgvinfo.php?action=phpinfo" title="<?php echo i18n::translate('Show PHP information page'); ?>"><?php echo i18n::translate('PHP information'); ?></a>
					<?php echo help_link('phpinfo'); ?>
				</td>
			</tr>
		</table>
	</div>
	<div id="gedcom">
		<table class="center <?php echo $TEXT_DIRECTION ?> width100">
			<tr>                                                                                                                                             
	            <td colspan="2" class="topbottombar" style="text-align:center; "><?php echo i18n::translate('Data and GEDCOM administration'); ?></td>                            
	    	</tr>
			<tr>
				<td class="optionbox width50"><a
					href="editgedcoms.php"><?php echo i18n::translate('Manage GEDCOMs and edit Privacy');?></a><?php echo help_link('edit_gedcoms'); ?></td>
				<td class="optionbox width50"><a
					href="edit_merge.php"><?php echo i18n::translate('Merge records'); ?></a><?php echo help_link('help_edit_merge.php'); ?></td>
			</tr>
			<tr>
				<td class="optionbox width50"><?php if (WT_USER_IS_ADMIN) {  echo '<a href="dir_editor.php">', i18n::translate('Cleanup Index directory'), '</a>', help_link('help_dir_editor.php'); } ?></td>
				<td class="optionbox width50"><?php echo $d_wt_changes; ?></td>
			</tr>
			<?php if (WT_USER_GEDCOM_ADMIN && is_dir('./modules/batch_update')) { ?>
			<tr>
				<td class="optionbox with50"><a
					href="module.php?mod=batch_update"><?php echo i18n::translate('Batch Update'); ?></a><?php echo help_link('batch_update'); ?></td>
				<td class="optionbox width50">&nbsp;</td>
			</tr>
			<?php } ?>
		</table>
	</div>
	
	<?php if (WT_USER_CAN_EDIT) { 
		?>
		<div id="unlinked">
		<table class="center <?php echo $TEXT_DIRECTION ?> width100">
		<tr>                                                                                                                                             
			<td colspan="2" class="topbottombar" style="text-align:center; "><?php echo i18n::translate('Unlinked Records'); ?></td>                            
		</tr>
		<tr>
			<td class="optionbox with50">
				<a href="javascript: <?php echo i18n::translate('Add an unlinked person'); ?> "onclick="addnewchild(''); return false;"><?php echo i18n::translate('Add an unlinked person'); ?></a><?php echo help_link('edit_add_unlinked_person'); ?>
			</td>
			<td class="optionbox width50">
				<a href="javascript: <?php echo i18n::translate('Add an unlinked source'); ?> "onclick="addnewsource(''); return false;"><?php echo i18n::translate('Add an unlinked source'); ?></a><?php echo help_link('edit_add_unlinked_source'); ?>
			</td>
		</tr>
		<tr>
			<td class="optionbox with50"><a
				href="javascript: <?php echo i18n::translate('Add an unlinked note'); ?> "onclick="addnewnote(''); return false;"><?php echo i18n::translate('Add an unlinked note'); ?></a><?php echo help_link('edit_add_unlinked_note'); ?>
			</td>
			<td class="optionbox width50">
				&nbsp;
			</td>
		</tr>
		</table>
		</div>
		<?php 
	} 
	
	if (WT_USER_IS_ADMIN) { 
		?>
		<div id="site">
		<table class="center <?php echo $TEXT_DIRECTION ?> width100">
		<tr>                                                                                                                                             
            <td colspan="2" class="topbottombar" style="text-align:center; "><?php echo i18n::translate('Site administration'); ?></td>                            
		</tr>
		<tr>
			<td class="optionbox width50"><a
				href="siteconfig.php"><?php echo i18n::translate('Configuration');?></a><?php echo help_link('help_editconfig.php'); ?></td>
			<td class="optionbox width50"><a
				href="manageservers.php"><?php echo i18n::translate('Manage Sites');?></a><?php echo help_link('help_managesites'); ?></td>
		</tr>
		<tr>
			<td class="optionbox width50"><a
				href="useradmin.php"><?php echo i18n::translate('User administration');?></a><?php echo help_link('help_useradmin.php'); ?></td>
				<td class="optionbox width50"><a href="logs.php"><?php echo i18n::translate('Logs'); ?></a><?php echo help_link('logs.php'); ?></td>
		</tr>
		<tr>
			<td class="optionbox width50"><a
				href="faq.php"><?php echo i18n::translate('FAQ List');?></a><?php echo help_link('help_faq.php'); ?></td>
			<td class="optionbox width50"><?php echo $d_logfile_str; ?></td>
		</tr>
		</table>
		</div>
		<?php
	} 

	if (WT_USER_IS_ADMIN || count($modules)>0) {
		echo '<div id="modules">';
			// Added by BH ------------------------
			echo i18n::translate('Loading...'); 
			// ------------------------------------
		echo '</div>';
	} ?>

</div>
</td>
</tr></table>
	<?php 
	if (isset($logfilename) && ($logfilename != "")) {
		echo "<hr><table align=\"center\" width=\"70%\"><tr><td class=\"listlog\">";
		echo "<strong>";
		echo i18n::translate('Content of log file');
		echo " [" . $INDEX_DIRECTORY . $logfilename . "]</strong><br /><br />";
		$lines=file($INDEX_DIRECTORY . $logfilename);
		$num = sizeof($lines);
		for ($i = 0; $i < $num ; $i++) {
			echo $lines[$i] . "<br />";
		}
		echo "</td></tr></table><hr>";
	}
	echo WT_JS_START;
	echo 'function manageservers() {';
	echo ' window.open("manageservers.php", "", "top=50,left=50,width=700,height=500,scrollbars=1,resizable=1");';
	echo '}';
	echo WT_JS_END;
echo '<br /><br />';
print_footer();
?>
