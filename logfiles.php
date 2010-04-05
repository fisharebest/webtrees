<?php
/**
 * Logfiles centre
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 * $Id$
 * @package webtrees
 */

define('WT_SCRIPT_NAME', 'logfiles.php');
require './includes/session.php';

if (!WT_USER_IS_ADMIN) {
	header("Location: index.php");
	exit;
}

print_header(i18n::translate('View log files'));
echo "<div class =\"center\">";
echo "\n\t<h2>".i18n::translate('View log files')."</h2>";

if (count(get_all_gedcoms())>0) {
echo "<table class=\"gedcom_table\">";


// print the table of available GEDCOMs
	foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
		if (WT_USER_IS_ADMIN) {
			if (empty($DEFAULT_GEDCOM)) $DEFAULT_GEDCOM = $ged_name;
			
			// Row 1: Heading
			echo "<tr>";
			echo "<td colspan=\"1\" class=\"list_label\">".i18n::translate('GEDCOM title')."</td>";
			echo "<td colspan=\"6\" class=\"list_value_wrap\">";
			if ($DEFAULT_GEDCOM==$ged_name) echo "<span class=\"label\">";
			echo PrintReady(get_gedcom_setting($ged_id, 'title'))."&nbsp;&nbsp;";
			if ($TEXT_DIRECTION=="rtl") echo getRLM() . "(".$ged_id.")" . getRLM();
			else echo getLRM() . "(".$ged_id.")" . getLRM();
			if ($DEFAULT_GEDCOM==$ged_name) echo "</span>";
			echo "</td>";
			echo "</tr>";

			// Row 2: titles
			echo "<tr>";
			echo "<td class=\"list_label\" valign=\"middle\">";		// Column 1  (row legend)
			echo i18n::translate('Description');
			echo "</td>";
			
			echo "<td class=\"list_label\" valign=\"middle\">";		// Column 2  (notices)
			echo i18n::translate('Archive log files');
			echo "</td>";
			
			echo "<td class=\"list_label\" valign=\"middle\">";		// Columns 3  (file name selector)
			echo i18n::translate('Log files');
			echo "</td>";
			echo "</tr>";

			// Row 3: Search Log File
			echo "<tr>";
			echo "<td class=\"list_label\" valign=\"middle\">";		// Column 1  (row legend)
			echo i18n::translate('SearchLog files');
			echo "</td>";
			echo "<td class=\"list_label\" valign=\"middle\">";		// Column 2  (notices)
			switch ($CHANGELOG_CREATE) {
				case 'none':    echo i18n::translate('None'); break;
				case 'daily':   echo i18n::translate('Daily'); break;
				case 'weekly':  echo i18n::translate('Weekly'); break;
				case 'monthly': echo i18n::translate('Monthly'); break;
				case 'yearly':  echo i18n::translate('Yearly'); break;
			}
			echo "</td>";

			echo "<td class=\"list_label\"valign=\"middle\">";		// Columns 3  (file name selector)
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
			echo "</td>";

			echo "</tr>";


			// Row 4: Change Log File
			echo "<tr>";
			echo "<td class=\"list_label\" valign=\"middle\">";		// Column 1  (row legend)
			echo i18n::translate('ChangeLog files');
			echo "</td>";
			echo "<td class=\"list_label\" valign=\"middle\">";		// Column 2  (notices)
			switch ($CHANGELOG_CREATE) {
				case 'none':    echo i18n::translate('None'); break;
				case 'daily':   echo i18n::translate('Daily'); break;
				case 'weekly':  echo i18n::translate('Weekly'); break;
				case 'monthly': echo i18n::translate('Monthly'); break;
				case 'yearly':  echo i18n::translate('Yearly'); break;
			}
			echo "</td>";

			echo "<td class=\"list_label\" valign=\"middle\">";		// Columns 3  (file name selector)
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
				$d_logfile_str .= "<input type=\"button\" name=\"logfile\" value=\" &gt; \" onclick=\"window.open('printlog.php?logfile='+this.form.logfilename.options[this.form.logfilename.selectedIndex].value, '_blank', 'top=50,left=10,width=880,height=500,scrollbars=1,resizable=1');\" />";
				$d_logfile_str .= "</form>";
				echo $d_logfile_str;
			}
			echo "</td>";

			echo "</tr>";
			
			// Row 3: Main Log File
			echo "<tr>";
			echo "<td class=\"list_label\" valign=\"middle\">";		// Column 1  (row legend)
			echo i18n::translate('Log files');
			echo "</td>";
			echo "<td class=\"list_label\" valign=\"middle\">";		// Column 2  (notices)
			switch ($CHANGELOG_CREATE) {
				case 'none':    echo i18n::translate('None'); break;
				case 'daily':   echo i18n::translate('Daily'); break;
				case 'weekly':  echo i18n::translate('Weekly'); break;
				case 'monthly': echo i18n::translate('Monthly'); break;
				case 'yearly':  echo i18n::translate('Yearly'); break;
			}
			echo "</td>";

			echo "<td class=\"list_label\" valign=\"middle\">";		// Columns 3  (file name selector)
			// Get the logfiles
			if (!isset($logfilename)) $logfilename = "";
			$file_nr = 0;
			$dir_var = opendir ($INDEX_DIRECTORY);
			$dir_array = array();
			while ($file = readdir ($dir_var))
			{
				if ((strpos($file, ".log") > 0) && (strstr($file, "pgv-") !== false )){$dir_array[$file_nr] = $file; $file_nr++;}
			}
			closedir($dir_var);
			$d_logfile_str = "&nbsp;";
			if (count($dir_array)>0) {
				$d_logfile_str = "<form name=\"logform\" action=\"editgedcoms.php\" method=\"post\">";
				$d_logfile_str .= "\n<select name=\"logfilename\">\n";
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
			$d_logfile_str .= "<input type=\"button\" name=\"logfile\" value=\" &gt; \" onclick=\"window.open('printlog.php?logfile='+this.form.logfilename.options[this.form.logfilename.selectedIndex].value, '_blank', 'top=50,left=10,width=920,height=500,scrollbars=1,resizable=1');\" />";
				$d_logfile_str .= "</form>";
				echo $d_logfile_str;
			}
			echo "</td>";

			echo "</tr>";
		}
		echo "<tr></tr>";
	}

echo "</table>\n";
}

echo "</div>\n";
if(empty($SEARCH_SPIDER)) {
	print_footer();
}
else {
	echo "</div>\n</body>\n</html>\n";
}
?>
