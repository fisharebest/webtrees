<?php
/**
 * UI for online updating of the config file.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 12 September 2005
 *
 * @author PGV Development Team
 * @package webtrees
 * @subpackage Admin
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'editgedcoms.php');
require './includes/session.php';

$all_gedcoms=get_all_gedcoms();
asort($all_gedcoms);
$action     =safe_GET('action', array('delete', 'setdefault'));
$ged        =safe_GET('ged',         $all_gedcoms);
$default_ged=safe_GET('default_ged', $all_gedcoms);

/**
 * Check if a gedcom file is downloadable over the internet
 *
 * @author opus27
 * @param string $gedfile gedcom file
 * @return mixed 	$url if file is downloadable, false if not
 */
function check_gedcom_downloadable($gedfile) {
	$url = "http://localhost/";
	if (substr($url,-1,1)!="/") $url .= "/";
	$url .= rawurlencode($gedfile);
	@ini_set('user_agent','MSIE 4\.0b2;'); // force a HTTP/1.0 request
	@ini_set('default_socket_timeout', '10'); // timeout
	$handle = @fopen ($url, "r");
	if ($handle==false) return false;
	// open successfull : now make sure this is a GEDCOM file
	$txt = fread ($handle, 80);
	fclose($handle);
	if (strpos($txt, " HEAD")==false) return false;
	return $url;
}

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
if (!WT_USER_GEDCOM_ADMIN) {
	header("Location: login.php?url=editgedcoms.php");
	exit;
}
if ($action=="delete") {
	delete_gedcom(get_id_from_gedcom($ged));
	// Reload this page, otherwise the page header will still reference the now-deleted gedcom
	header("Location: editgedcoms.php");
}

print_header(i18n::translate('GEDCOM Administration'));
print "<center>\n";

if (($action=="setdefault") && in_array($default_ged, $all_gedcoms)) {
	set_site_setting('DEFAULT_GEDCOM', $default_ged);
	$DEFAULT_GEDCOM=$default_ged;
} else {
	$DEFAULT_GEDCOM=get_site_setting('DEFAULT_GEDCOM');
}
?>
<h2><?php echo i18n::translate('Current GEDCOMs'); ?></h2>
<form name="defaultform" method="get" action="editgedcoms.php">
<input type="hidden" name="action" value="setdefault" />
<?php
// Default gedcom choice
print "<br />";
if (WT_USER_IS_ADMIN && count($all_gedcoms)>1) {
	echo i18n::translate('Default GEDCOM'), ' ', help_link('default_gedcom');
	print "<select name=\"default_ged\" class=\"header_select\" onchange=\"document.defaultform.submit();\">";
	if (!in_array($DEFAULT_GEDCOM, $all_gedcoms)) {
		echo '<option value="" selected="selected" onclick="document.defaultform.submit();">', htmlspecialchars($DEFAULT_GEDCOM), '</option>';
	}
	foreach ($all_gedcoms as $ged_id=>$ged_name) {
		print "<option value=\"".urlencode($ged_name)."\"";
		if ($DEFAULT_GEDCOM==$ged_name) print " selected=\"selected\"";
		print " onclick=\"document.defaultform.submit();\">";
		print PrintReady(get_gedcom_setting($ged_id, 'title'))."</option>";
	}
	print "</select><br /><br />";
}

echo'<a href="editgedcoms.php?check_download=true">', i18n::translate('Check if GEDCOM files are downloadable'), '</a>', help_link('SECURITY_CHECK_GEDCOM_DOWNLOADABLE');
// Print table heading
print "<table class=\"gedcom_table\">";
if (WT_USER_IS_ADMIN) {
	print "<tr><td class=\"list_label\">";
	print "<a href=\"editconfig_gedcom.php?source=add_form\">".i18n::translate('Add GEDCOM')."</a>".help_link('help_addgedcom.php');
	print "</td>";
	print "<td class=\"list_label\">";
	print "<a href=\"editconfig_gedcom.php?source=upload_form\">".i18n::translate('Upload GEDCOM')."</a>".help_link('help_uploadgedcom.php');
	print "</td>";
}
if (WT_USER_IS_ADMIN) {
	print "<td class=\"list_label\">";
	print "<a href=\"editconfig_gedcom.php?source=add_new_form\">".i18n::translate('Create a new GEDCOM')."</a>".help_link('help_addnewgedcom.php');
	print "</td>";
}
print  "<td class=\"list_label\"><a href=\"admin.php\">" . i18n::translate('Return to the Admin menu') . "</a></td></tr>";
print "</table>";
print "<table class=\"gedcom_table\">";
$GedCount = 0;

// Print the table of available GEDCOMs
foreach ($all_gedcoms as $ged_id=>$ged_name) {
	if (userGedcomAdmin(WT_USER_ID, $ged_id)) {
		// Row 0: Separator line
		if ($GedCount!=0) {
			print "<tr>";
			print "<td colspan=\"7\">";
			print "<br /><hr class=\"gedcom_table\" /><br />";
			print "</td>";
			print "</tr>";
		}
		$GedCount++;

		// Row 1: Heading
		print "<tr>";
		print "<td colspan=\"1\" class=\"list_label\">".i18n::translate('GEDCOM title')."</td>";
		print "<td colspan=\"7\" class=\"list_value_wrap\">";
		if ($DEFAULT_GEDCOM==$ged_name) print "<span class=\"label\">";
		print PrintReady(get_gedcom_setting($ged_id, 'title'))."&nbsp;&nbsp;";
		if ($TEXT_DIRECTION=="rtl") print getRLM() . "(".$ged_id.")" . getRLM();
		else print getLRM() . "(".$ged_id.")" . getLRM();
		if ($DEFAULT_GEDCOM==$ged_name) print "</span>";
		print "&nbsp;&nbsp;<a href=\"".encode_url("editconfig_gedcom.php?source=replace_form&path=".get_gedcom_setting($ged_id, 'path'))."&oldged=".get_gedcom_setting($ged_id, 'gedcom')."\">".i18n::translate('Upload Replacement')."</a>\n";
		print "</td>";
		print "</tr>";


		// Row 2: GEDCOM file name & functions
		print "<tr>";
		print "<td valign=\"top\">";		// Column 1 (row legend)
		echo i18n::translate('GEDCOM file');
		print "</td>";

		print "<td valign=\"top\">";		// Column 2 (file name & notices)
		if (file_exists(get_gedcom_setting($ged_id, 'path'))) {
			if ($TEXT_DIRECTION=="ltr") print get_gedcom_setting($ged_id, 'path')." (";
			else print getLRM() . get_gedcom_setting($ged_id, 'path')." " . getRLM() . "(";
			printf("%.2fKb", (filesize(get_gedcom_setting($ged_id, 'path'))/1024));
			print ")";
			/** deactivate [ 1573749 ]
			 * -- activating based on a request parameter instead of a config parameter
			 */
			if(!empty($_REQUEST['check_download'])){
				$url = check_gedcom_downloadable(get_gedcom_setting($ged_id, 'path'));
				if ($url!==false) {
					print "<br />\n";
					print "<span class=\"error\">".i18n::translate('This GEDCOM file is downloadable over the internet!<br />Please see the SECURITY section of the <a href="readme.txt"><b>readme.txt</b></a> file to fix this problem')." :</span>";
					print "<br /><a href=\"$url\">$url</a>";
				}
				else print "<br /><b>".i18n::translate('%s cannot be downloaded.', get_gedcom_setting($ged_id, 'path'))."</b><br />";
			}
		}
		else print "<span class=\"error\">".i18n::translate('File not found.')."</span>";
		print "</td>";

		print "<td valign=\"top\">";		// Column 3  (Import action)
		print "<a href=\"".encode_url("uploadgedcom.php?GEDFILENAME={$ged_name}&verify=verify_gedcom&action=add_form&import_existing=1")."\">".i18n::translate('Import')."</a>";
		if (!get_gedcom_setting($ged_id, 'imported')) {
			print "<br /><span class=\"error\">".i18n::translate('This GEDCOM has not yet been imported.')."</span>";
		}
		print "&nbsp;&nbsp;";
		print "</td>";

		echo '<td valign="top">';		// Column 4  (Export action)
		echo '<a href="javascript:" onclick="window.open(\'', encode_url("export_gedcom.php?export={$ged_name}"), '\', \'_blank\',\'left=50,top=50,width=500,height=500,resizable=1,scrollbars=1\');">', i18n::translate('Export'), '</a>';
		echo '</td>';

		print "<td valign=\"top\">";		// Column 5  (Delete action)
		print "<a href=\"".encode_url("editgedcoms.php?action=delete&ged={$ged_name}")."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to delete this GEDCOM')." ".str_replace("'", "\'", $ged_name)."?');\">".i18n::translate('Delete')."</a>";
		print "&nbsp;&nbsp;";
		print "</td>";

		print "<td valign=\"top\">";		// Column 6  (Download action)
		print "<a href=\"".encode_url("downloadgedcom.php?ged={$ged_name}")."\">".i18n::translate('Download')."</a>";
		print "&nbsp;&nbsp;";
		print "</td>";

		print "<td valign=\"top\">";		// Column 7  (Check action)
		print "<a href=\"".encode_url("gedcheck.php?ged={$ged_name}")."\">".i18n::translate('Check')."</a>";
		print "&nbsp;&nbsp;";
		print "</td>";

		print "</tr>";


		// Row 3: Configuration file
		print "<tr>";
		print "<td valign=\"top\">";		// Column 1  (row legend)
		echo i18n::translate('Configuration file');
		print "</td>";

		print "<td valign=\"top\">";		// Column 2  (file name & notices)
		print getLRM() . get_config_file($ged_id);
		print "</td>";

		print "<td valign=\"top\">";		// Column 3  (Edit action)
		print "<a href=\"".encode_url("editconfig_gedcom.php?ged={$ged_name}")."\">".i18n::translate('Edit')."</a>";
		print "</td>";

		print "<td colspan=\"4\" valign=\"top\">";		// Columns 4-7  (blank)
		print "&nbsp;";
		print "</td>";
		print "</tr>";

		// Row 4: Privacy File
		print "<tr>";
		print "<td valign=\"top\">";		// Column 1  (row legend)
		echo i18n::translate('Privacy file');
		print "</td>";

		print "<td valign=\"top\">";		// Column 2  (file name & notices)
		print getLRM() . get_privacy_file($ged_id);
		print "</td>";

		print "<td valign=\"top\">";		// Column 3  (Edit action)
		print "<a href=\"".encode_url("edit_privacy.php?ged={$ged_name}")."\">".i18n::translate('Edit')."</a>";
		print "</td>";

		print "<td colspan=\"4\" valign=\"top\">";		// Columns 4-7  (blank)
		print "&nbsp;";
		print "</td>";
		print "</tr>";

		// Row 5: Search Log File
		print "<tr>";
		print "<td valign=\"top\">";		// Column 1  (row legend)
		echo i18n::translate('SearchLog files');
		print "</td>";

		require get_config_file($ged_id);
		print "<td valign=\"top\">";		// Column 2  (notices)
		switch ($SEARCHLOG_CREATE) {
		case 'none':    echo i18n::translate('None'); break;
		case 'daily':   echo i18n::translate('Daily'); break;
		case 'weekly':  echo i18n::translate('Weekly'); break;
		case 'monthly': echo i18n::translate('Monthly'); break;
		case 'yearly':  echo i18n::translate('Yearly'); break;
		}
		print "</td>";

		print "<td colspan=\"5\" valign=\"top\">";		// Columns 3-7  (file name selector)
		// Get the logfiles
		if (!isset($logfilename)) $logfilename = "";
		$file_nr = 0;
		if (isset($dir_array)) unset($dir_array);
		$dir_var = opendir ($INDEX_DIRECTORY);
		while ($file = readdir ($dir_var))
		{
			if ((strpos($file, ".log") > 0) && (strstr($file, "srch-".$ged_name) !== false )) {$dir_array[$file_nr] = $file; $file_nr++;}
		}
		closedir($dir_var);
		$d_logfile_str  = "<form name=\"logform\" action=\"editgedcoms.php\" method=\"post\">";
		$d_logfile_str .= "\n<select name=\"logfilename\">\n";
		if(isset($dir_array)) {
			sort($dir_array);
			$ct = count($dir_array);
			for($x = 0; $x < $file_nr; $x++)
			{
				$ct--;
				$d_logfile_str .= "<option value=\"";
				$d_logfile_str .= $dir_array[$ct];
				if ($dir_array[$ct] == $logfilename) $d_logfile_str .= "\" selected=\"selected";
				$d_logfile_str .= "\">";
				$d_logfile_str .= $dir_array[$ct];
				$d_logfile_str .= "</option>\n";
			}
			$d_logfile_str .= "</select>\n";
			$d_logfile_str .= "<input type=\"button\" name=\"logfile\" value=\" &gt; \" onclick=\"window.open('printlog.php?logfile='+this.form.logfilename.options[this.form.logfilename.selectedIndex].value, '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\" />";
			$d_logfile_str .= "</form>";
			print $d_logfile_str;
		}
		print "</td>";

		print "</tr>";


		// Row 6: Change Log File
		print "<tr>";
		print "<td valign=\"top\">";		// Column 1  (row legend)
		echo i18n::translate('ChangeLog files');
		print "</td>";
		print "<td valign=\"top\">";		// Column 2  (notices)
		switch ($CHANGELOG_CREATE) {
		case 'none':    echo i18n::translate('None'); break;
		case 'daily':   echo i18n::translate('Daily'); break;
		case 'weekly':  echo i18n::translate('Weekly'); break;
		case 'monthly': echo i18n::translate('Monthly'); break;
		case 'yearly':  echo i18n::translate('Yearly'); break;
		}
		print "</td>";

		print "<td colspan=\"5\" valign=\"top\">";		// Columns 3-7  (file name selector)
		// Get the logfiles
		if (!isset($logfilename)) $logfilename = "";
		$file_nr = 0;
		if (isset($dir_array)) unset($dir_array);
		$dir_var = opendir ($INDEX_DIRECTORY);
		while ($file = readdir ($dir_var))
		{
			if ((strpos($file, ".log") > 0) && (strstr($file, "ged-".$ged_name) !== false )) {$dir_array[$file_nr] = $file; $file_nr++;}
		}
		closedir($dir_var);
		$d_logfile_str  = "<form name=\"logform2\" action=\"editgedcoms.php\" method=\"post\">";
		$d_logfile_str .= "\n<select name=\"logfilename\">\n";
		if(isset($dir_array)) {
			sort($dir_array);
			$ct = count($dir_array);
			for($x = 0; $x < $file_nr; $x++)
			{
				$ct--;
				$d_logfile_str .= "<option value=\"";
				$d_logfile_str .= $dir_array[$ct];
				if ($dir_array[$ct] == $logfilename) $d_logfile_str .= "\" selected=\"selected";
				$d_logfile_str .= "\">";
				$d_logfile_str .= $dir_array[$ct];
				$d_logfile_str .= "</option>\n";
			}
			$d_logfile_str .= "</select>\n";
			$d_logfile_str .= "<input type=\"button\" name=\"logfile\" value=\" &gt; \" onclick=\"window.open('printlog.php?logfile='+this.form.logfilename.options[this.form.logfilename.selectedIndex].value, '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\" />";
			$d_logfile_str .= "</form>";
			echo $d_logfile_str;
		}
		echo '</td></tr>';
	}
}
echo '</table></form></center>';

require get_config_file(WT_GED_ID);

print_footer();
?>
