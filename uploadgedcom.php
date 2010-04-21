<?php
/**
 * Allow admin users to upload a new gedcom using a web interface.
 *
 * When importing a gedcom file, some of the gedcom structure is changed
 * so a new file is written during the import and then copied over the old
 * file.
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

// TODO: Progress bars don't show until </table> or </div>
// TODO: Upload ZIP support alternative path and name

// NOTE: $GEDFILENAME = The filename of the uploaded GEDCOM
// NOTE: $action = Which form we should present
// NOTE: $check = Which check to be performed
// NOTE: $timelimit = The time limit for the import process
// NOTE: $cleanup = If set to yes, the GEDCOM contains invalid tags
// NOTE: $no_upload = When the user cancelled, we want to restore the original settings
// NOTE: $path = The path to the GEDCOM file
// NOTE: $continue = When the user decided to move on to the next step
// NOTE: $import_existing = See if we are just importing an existing GEDCOM
// NOTE: $replace_gedcom = When uploading a GEDCOM, user will be asked to replace an existing one. If yes, overwrite
// NOTE: $bakfile = Name and path of the backupfile, this file is created if a file with the same name exists

define('WT_SCRIPT_NAME', 'uploadgedcom.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_import.php';
require_once WT_ROOT.'includes/functions/functions_export.php';

if (!WT_USER_GEDCOM_ADMIN) {
	header("Location: login.php?url=uploadgedcom.php");
	exit;
}

// editconfig.php and uploadgedcom.php make extensive use of
// import_request_variables and are heavily inter-dependent.
@import_request_variables('cgp');

@ini_set('zlib.output_compression','0');

if (empty ($action)) $action = "upload_form";
if (!isset ($path)) $path = "";
if (!isset ($check)) $check = "";
if (!isset ($error)) $error = "";
if (!isset ($verify)) $verify = "";
if (!isset ($import)) $import = false;
if (!isset ($bakfile)) $bakfile = "";
if (!isset ($cleanup_needed)) $cleanup_needed = false;
if (!isset ($ok)) $ok = false;
if (!isset ($startimport)) $startimport = false;
if (!isset ($timelimit)) $timelimit = get_site_setting('MAX_EXECUTION_TIME');
if (!isset ($importtime)) $importtime = 0;
if (!isset ($no_upload)) $no_upload = false;
if (!isset ($override)) $override = false;
if ($no_upload == "cancel_upload" || $override == "no") $check = "cancel_upload";
if (!isset ($exists)) $exists = false;
if (!isset ($config_gedcom)) $config_gedcom = "";
if (!isset ($continue)) $continue = false;
if (!isset ($import_existing)) $import_existing = false;
if (!isset($utf8convert)) $utf8convert = "no";
if (isset($_REQUEST['keepmedia']) && $_REQUEST['keepmedia']=='yes') $keepmedia=true;
else $keepmedia = false;

// NOTE: GEDCOM was uploaded
if ($check == "upload") {
	$verify = "verify_gedcom";
	$ok = true;
}
// NOTE: GEDCOM was added
else if ($check == "add") {
	$verify = "verify_gedcom";
	$ok = true;
} else if ($check == "add_new") {
	if (((!file_exists($INDEX_DIRECTORY.$GEDFILENAME)) && !file_exists($path.$GEDFILENAME)) || $override == "yes") {
		if ($path != "") $fp = fopen($path.$GEDFILENAME, "wb");
		else $fp = fopen($INDEX_DIRECTORY.$GEDFILENAME, "wb");
		if ($fp) {
			$newgedcom = gedcom_header($GEDFILENAME).
			"0 @I1@ INDI\n".
			"1 NAME Given Names /Surname/\n".
			"1 SEX M\n".
			"1 BIRT\n".
			"2 DATE 01 JAN 1850\n".
			"2 PLAC Click edit and change me\n".
			"0 TRLR\n";
			$newgedcom = preg_replace('/[\r\n]+/', WT_EOL, $newgedcom);
			fwrite($fp, $newgedcom);
			fclose($fp);
			$logline = AddToLog($GEDFILENAME." updated", 'config');
			$verify = "validate_form";
			$exists = true;
			// NOTE: Go straight to import, no other settings needed
			$xreftype = "NA";
			$utf8convert = "no";
			$ged = $GEDFILENAME;
			$startimport = "true";
			//-- set the current GEDCOM to be this new GEDCOM file
			//-- so that the import can proceed correctly
			$GEDCOM = $GEDFILENAME;
			$FILE = $GEDCOM;
		}
	} else {
		if ($path != "")
		$fp = fopen($path.$GEDFILENAME.".bak", "wb");
		else
		$fp = fopen($INDEX_DIRECTORY.$GEDFILENAME.".bak", "wb");
		if ($fp) {
			$newgedcom = gedcom_header($GEDFILENAME).
			"0 @I1@ INDI\n".
			"1 NAME Given Names /Surname/\n".
			"1 SEX M\n".
			"1 BIRT\n".
			"2 DATE 01 JAN 1850\n".
			"2 PLAC Click edit and change me\n".
			"0 TRLR\n";
			$newgedcom = preg_replace('/[\r\n]+/', WT_EOL, $newgedcom);
			fwrite($fp, $newgedcom);
			fclose($fp);
			if ($path != "")
			$bakfile = $path.$GEDFILENAME.".bak";
			else
			$bakfile = $INDEX_DIRECTORY.$GEDFILENAME.".bak";
			$ok = false;
			$verify = "verify_gedcom";
			$exists = true;
		}
	}
} else
if ($check == "cancel_upload") {
	if ($exists) {
		delete_gedcom(get_id_from_gedcom($GEDFILENAME));
		if ($action == "add_new_form")
		@ unlink($INDEX_DIRECTORY.$GEDFILENAME);
	}
	// NOTE: Cleanup everything no longer needed
	if (isset($bakfile) && file_exists($bakfile)) unlink($bakfile);
	$verify = "";
	unset($GEDFILENAME);
	$startimport = "";
	$import = false;
	$cleanup_needed = false;
	$noupload = true;
	header("Location: editgedcoms.php");
}

if ($cleanup_needed == "cleanup_needed" && $continue == i18n::translate('Continue')) {
	require_once WT_ROOT.'includes/functions/functions_tools.php';

	$filechanged = false;
	if (file_is_writeable(get_gedcom_setting(get_id_from_gedcom($GEDFILENAME), 'path')) && (file_exists(get_gedcom_setting(get_id_from_gedcom($GEDFILENAME), 'path')))) {
		$l_BOMcleanup = false;
		$l_headcleanup = false;
		$l_macfilecleanup = false;
		$l_lineendingscleanup = false;
		$l_placecleanup = false;
		$l_datecleanup = false;
		$l_isansi = false;
		$fp = fopen(get_gedcom_setting(get_id_from_gedcom($GEDFILENAME), 'path'), "rb");
		$fw = fopen($INDEX_DIRECTORY."/".$GEDFILENAME.".bak", "wb");
		//-- read the gedcom and test it in 8KB chunks
		while (!feof($fp)) {
			$fcontents = fread($fp, 1024 * 8);
			$lineend = "\n";
			if (need_macfile_cleanup()) {
				$l_macfilecleanup = true;
				$lineend = "\r";
			}

			//-- read ahead until the next line break
			$byte = "";
			while ((!feof($fp)) && ($byte != $lineend)) {
				$byte = fread($fp, 1);
				$fcontents .= $byte;
			}

			if (!$l_BOMcleanup && need_BOM_cleanup()) {
				BOM_cleanup();
				$l_BOMcleanup = true;
			}

			if (!$l_headcleanup && need_head_cleanup()) {
				head_cleanup();
				$l_headcleanup = true;
			}

			if ($l_macfilecleanup) {
				macfile_cleanup();
			}

			if (isset ($_POST["cleanup_places"]) && $_POST["cleanup_places"] == "YES") {
				if (($sample = need_place_cleanup()) !== false) {
					$l_placecleanup = true;
					place_cleanup();
				}
			}

			if (line_endings_cleanup()) {
				$filechanged = true;
			}

			if (isset ($_POST["datetype"])) {
				$filechanged = true;
				//month first
				date_cleanup($_POST["datetype"]);
			}
			/**
			if($_POST["xreftype"]!="NA") {
				$filechanged=true;
				xref_change($_POST["xreftype"]);
				}
				**/
			if (isset ($_POST["utf8convert"]) == "YES") {
				$filechanged = true;
				convert_ansi_utf8();
			}
			fwrite($fw, $fcontents);
		}
		fclose($fp);
		fclose($fw);
		copy($INDEX_DIRECTORY."/".$GEDFILENAME.".bak", get_gedcom_setting(get_id_from_gedcom($GEDFILENAME), 'path'));
		$cleanup_needed = false;
		$import = "true";
	} else {
		$error = i18n::translate('The GEDCOM file, <b>%s</b>, is not writable. Please check attributes and access rights.', $GEDFILENAME);
	}
}

// NOTE: Change header depending on action
if ($action == "upload_form") {
	print_header(i18n::translate('Upload GEDCOM'));
} elseif ($action == "add_form") {
	print_header(i18n::translate('Add GEDCOM'));
} elseif ($action == "add_new_form") {
	print_header(i18n::translate('Create a new GEDCOM'));
} else {
	print_header(i18n::translate('Import'));
}

// NOTE: Print form header
echo "<form enctype=\"multipart/form-data\" method=\"post\" name=\"configform\" action=\"uploadgedcom.php\">";

// NOTE: Print table header
echo "\n<table class=\"facts_table center ", $TEXT_DIRECTION, "\">";

// NOTE: Add GEDCOM form
if ($action == "add_form") {
	echo "<tr><td class=\"topbottombar ", $TEXT_DIRECTION, "\" colspan=\"2\">";
	echo "<a href=\"javascript: ";
	if ($import_existing) {
		echo i18n::translate('Import');
	} else {
		echo i18n::translate('Add GEDCOM');
	}
	echo "\" onclick=\"expand_layer('add-form');return false\"><img id=\"add-form_img\" src=\"", $WT_IMAGE_DIR, "/";
	if ($startimport != "true") {
		echo $WT_IMAGES["minus"]["other"];
	} else {
		echo $WT_IMAGES["plus"]["other"];
	}
	echo "\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
	echo help_link('add_gedcom');
	echo "&nbsp;<a href=\"javascript: ";
	if ($import_existing) {
		echo i18n::translate('Import');
	} else {
		echo i18n::translate('Add GEDCOM');
	}
	print "\" onclick=\"expand_layer('add-form');return false\">";
	if ($import_existing) {
		echo i18n::translate('Import');
	} else {
		echo i18n::translate('Add GEDCOM');
	}
	echo "</a>";
	echo "</td></tr>";
	echo "<tr><td class=\"optionbox\">";
	echo "<div id=\"add-form\" style=\"display: ";
	if ($startimport != "true") {
		echo "block ";
	} else {
		echo "none ";
	}
	echo "\">";
	?>
	<input type="hidden" name="check" value="add" />
	<input type="hidden" name="action" value="<?php echo $action; ?>" />
	<input type="hidden" name="import_existing" value="<?php echo $import_existing; ?>" />
	<table class="facts_table">
	<?php

	$i = 0;
	if (!empty ($error)) {
		echo "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
		echo "<span class=\"error\">", $error, "</span>\n";
		echo "</td></tr>";
	}
	?>
	<tr>
	<td class="descriptionbox width20 wrap">
	<?php echo i18n::translate('GEDCOM File:'), help_link('gedcom_path'); ?></td>
	<td class="optionbox"><input type="text" name="GEDFILENAME" value="<?php if (isset($GEDFILENAME) && strlen($GEDFILENAME) > 4) echo get_gedcom_setting(get_id_from_gedcom($GEDFILENAME), 'path'); ?>"
					size="60" dir ="ltr" tabindex="<?php $i++; echo $i?>" <?php if ((!$no_upload && isset($GEDFILENAME)) && (empty($error))) echo "disabled "; ?> />
	</td>
	</tr>
	</table>
	</div>
	</td></tr>
<?php
}
// NOTE: Upload GEDCOM form
elseif ($action == "upload_form") {
	echo "<tr><td class=\"topbottombar ", $TEXT_DIRECTION, "\" colspan=\"2\">";
	echo "<a href=\"javascript: ", i18n::translate('Upload GEDCOM'), "\" onclick=\"expand_layer('upload_gedcom'); return false;\"><img id=\"upload_gedcom_img\" src=\"", $WT_IMAGE_DIR, "/";
	if ($startimport != "true") {
		echo $WT_IMAGES["minus"]["other"];
	} else {
		echo $WT_IMAGES["plus"]["other"];
	}
	echo "\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
	echo "&nbsp;<a href=\"javascript: ", i18n::translate('Upload GEDCOM'), "\" onclick=\"expand_layer('upload_gedcom');return false\">", i18n::translate('Upload GEDCOM'), "</a>";
	echo help_link('upload_gedcom');
	echo "</td></tr>";
	echo "<tr><td class=\"optionbox wrap\">";
	echo "<div id=\"upload_gedcom\" style=\"display: ";
	if ($startimport != "true") {
		echo "block ";
	} else {
		echo "none ";
	}
	echo "\">";
?>
<input type="hidden" name="action" value="<?php echo $action; ?>" />
<input type="hidden" name="check" value="upload" />
<table class="facts_table">
<?php

if (!empty ($error)) {
	echo "<span class=\"error\">", $error, "</span><br />\n";
	echo i18n::translate('This error probably means that the file you tried to upload exceeded the limit set by your host.  The default limit in PHP is 2MB.  You can contact your host\'s Support group to have them increase the limit in the php.ini file, or you can upload the file using FTP.  Use the <a href="uploadgedcom.php?action=add_form"><b>Add GEDCOM</b></a> page to add a GEDCOM file you have uploaded using FTP.');;
	echo "<br />\n";
}
?>
<tr>
<td class="descriptionbox width20 wrap">
<?php echo i18n::translate('GEDCOM File:');?></td>
<td class="optionbox" dir="ltr">
<?php

if (isset($GEDFILENAME)) {
	echo PrintReady($path.$GEDFILENAME);
} elseif (isset($UPFILE)) {
	echo PrintReady($UPFILE["name"]);
} else {
	echo "<input name=\"UPFILE\" type=\"file\" size=\"60\" />";
	if (!$filesize = ini_get('upload_max_filesize')) {
		$filesize = "2M";
	}
	echo " ( ", i18n::translate('Maximum upload size: '), " $filesize )";
}
?>
</td>
</tr>
</table>
<?php

echo "</div>";
echo "</td></tr>";
}
// NOTE: Add new GEDCOM form
elseif ($action == "add_new_form") {
	echo "<tr><td class=\"topbottombar ", $TEXT_DIRECTION, "\" colspan=\"2\">";
	echo "<a href=\"javascript: ", i18n::translate('Create a new GEDCOM'), "\" onclick=\"expand_layer('add_new_gedcom');return false\"><img id=\"add_new_gedcom_img\" src=\"", $WT_IMAGE_DIR, "/";
	if ($startimport != "true") {
		echo $WT_IMAGES["minus"]["other"];
	} else {
		echo $WT_IMAGES["plus"]["other"];
	}
	echo "\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
	echo "&nbsp;<a href=\"javascript: ", i18n::translate('Create a new GEDCOM'), "\" onclick=\"expand_layer('add_new_gedcom');return false\">", i18n::translate('Create a new GEDCOM'), "</a>";
	echo help_link('add_gedcom_instructions');
	echo "</td></tr>";
	echo "<tr><td class=\"optionbox\">";
	echo "<div id=\"add-form\" style=\"display: ";
	if ($startimport != "true") {
		echo "block ";
	} else {
		echo "none ";
	}
	echo "\">";
?>
<input type="hidden" name="action" value="<?php echo $action; ?>" />
<input type="hidden" name="check" value="add_new" />
<table class="facts_table">
<?php

if (!empty ($error)) {
	echo "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
	echo "<span class=\"error\">", $error, "</span>\n";
	echo "</td></tr>";
}
?>
<tr>
<td class="descriptionbox width20 wrap">
<?php echo i18n::translate('GEDCOM File:');?>
</td>
<td class="optionbox"><input name="GEDFILENAME" type="text" value="<?php if (isset($GEDFILENAME)) echo $path, $GEDFILENAME; ?>" size="60" <?php if (isset($GEDFILENAME) && !$no_upload) echo "disabled"; ?> /></td>
</tr>
</table>
<?php

echo "</div>";
echo "</td></tr>";
}

if ($verify == "verify_gedcom") {
	// NOTE: Check if GEDCOM has been imported into DB
	$all_record_counts=count_all_records(get_id_from_gedcom($GEDFILENAME));
	$totalRecords = 0;
	foreach ($all_record_counts as $recordCount) {
		$totalRecords += $recordCount;
	}
	$imported = !empty($totalRecords);
	if ($imported || (!empty($bakfile) && file_exists($bakfile))) {
		// NOTE: If GEDCOM exists show warning
		print "<tr><td class=\"topbottombar $TEXT_DIRECTION\" colspan=\"2\">";
		print "<a href=\"javascript: ".i18n::translate('Verify GEDCOM')."\" onclick=\"expand_layer('verify_gedcom');return false\"><img id=\"verify_gedcom_img\" src=\"".$WT_IMAGE_DIR."/";
		if ($startimport != "true")
		print $WT_IMAGES["minus"]["other"];
		else
		print $WT_IMAGES["plus"]["other"];
		print "\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
		print "&nbsp;<a href=\"javascript: ".i18n::translate('Verify GEDCOM')."\" onclick=\"expand_layer('verify_gedcom');return false\">".i18n::translate('Verify GEDCOM')."</a>";
		print help_link('verify_gedcom');
		print "</td></tr>";
		print "<tr><td class=\"optionbox\" colspan=\"2\">";
		print "<div id=\"verify_gedcom\" style=\"display: ";
		if ($startimport != "true")
		print "block ";
		else
		print "none ";
		print "\">";
		print "\n<table class=\"facts_table\">";
		print "<tr><td class=\"descriptionbox width20 wrap\" colspan=\"2\">";
		?>
		<input type="hidden" name="no_upload" value="" />
		<input type="hidden" name="check" value="" />
		<input type="hidden" name="verify" value="validate_form" />
		<input type="hidden" name="GEDFILENAME" value="<?php if (isset($GEDFILENAME)) print $GEDFILENAME; ?>" />
		<input type="hidden" name="bakfile" value="<?php if (isset($bakfile)) print $bakfile; ?>" />
		<input type="hidden" name="path" value="<?php if (isset($path)) print $path; ?>" />

		<?php

		if ($imported) {
			print "<span class=error>".i18n::translate('A GEDCOM with this file name has already been imported into the database.')."</span><br /><br />";
			print "<span class=error>".i18n::translate('This GEDCOM file is <em>not</em> synchronized with the database.  It may not contain the latest version of your data.  To re-import from the database rather than the file, you should download and re-upload.')."</span><br /><br />";
		}
		if ($bakfile != "") print i18n::translate('A GEDCOM file with the same name has been found. If you choose to continue, the old GEDCOM file will be replaced with the file that you uploaded and the Import process will begin again.  If you choose to cancel, the old GEDCOM will remain unchanged.')."</td></tr>";
		// NOTE: Check for existing changes
		$changes=WT_DB::prepare("SELECT 1 FROM {$TBLPREFIX}change WHERE status='pending' AND gedcom_id=?")->execute(array(get_id_from_gedcom($GEDFILENAME)))->fetchOne();
		if ($changes) {
			echo i18n::translate('The current GEDCOM has changes pending review.  If you continue this Import, these pending changes will be discarded.  You should review the pending changes before continuing the Import.');
			echo "<br /><br />";
		}
		print "<tr><td class=\"descriptionbox width20 wrap\">".i18n::translate('Do you want to erase the old data and replace it with this new data?')."</td><td class=\"optionbox vmiddle\">\n";
		print "<select name=\"override\">";
		print "<option value=\"yes\" ";
		if ($override == "yes")
			print "selected=\"selected\"";
		print ">".i18n::translate('Yes')."</option>";
		print "<option value=\"no\" ";
		if ($override != "yes")
			print "selected=\"selected\"";
		print ">".i18n::translate('No')."</option>";
		print "</select></td></tr>";
		//-- check if there are media in the DB already
		if (array_key_exists('OBJE', $all_record_counts)) {
			?>
			<tr>
			<td class="descriptionbox wrap width20">
			<?php echo i18n::translate('Keep media links'), help_link('keep_media'); ?></td>
			<td class="optionbox">
			<select name="keepmedia">
			<option value="yes" <?php if ($keepmedia) print "selected=\"selected\"";?>><?php print i18n::translate('Yes'); ?></option>
			<option value="no" <?php if (!$keepmedia) print "selected=\"selected\"";?>><?php print i18n::translate('No'); ?></option>
			</select>
			</td>
			</tr>
			<?php
		}
		print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
		print "</td></tr></table>";
	} else {
		$verify = "validate_form";
	}
}

if ($verify == "validate_form") {
	print "<tr><td class=\"topbottombar $TEXT_DIRECTION\" colspan=\"2\">";
	print "<a href=\"javascript: ".i18n::translate('Validate GEDCOM')."\" onclick=\"expand_layer('validate_gedcom');return false\"><img id=\"validate_gedcom_img\" src=\"".$WT_IMAGE_DIR."/";
	if ($startimport != "true")
	print $WT_IMAGES["minus"]["other"];
	else
	print $WT_IMAGES["plus"]["other"];
	print "\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
	print "&nbsp;<a href=\"javascript: ".i18n::translate('Validate GEDCOM')."\" onclick=\"expand_layer('validate_gedcom');return false\">".i18n::translate('Validate GEDCOM')."</a>";
	print help_link('validate_gedcom');
	print "</td></tr>";
	print "<tr><td class=\"optionbox\">";
	print "<div id=\"validate_gedcom\" style=\"display: ";
	if ($startimport != "true")
	print "block ";
	else
	print "none ";
	print "\">";
	print "\n<table class=\"facts_table\">";
	print "<tr><td class=\"descriptionbox\" colspan=\"2\">".i18n::translate('Performing GEDCOM validation...')."<br />";
	if (!empty ($error))
	print "<span class=\"error\">$error</span>\n";

	if ($import != true) {
		require_once WT_ROOT.'includes/functions/functions_tools.php';

		$l_BOMcleanup = false;
		$l_headcleanup = false;
		$l_macfilecleanup = false;
		$l_lineendingscleanup = false;
		$l_placecleanup = false;
		$l_datecleanup = false;
		$l_isansi = false;
		$fp = fopen(get_gedcom_setting(get_id_from_gedcom($GEDFILENAME), 'path'), "r");

		// TODO - there are two problems with this next block of code.  Firstly, by
		// checking the file in chunks (rather than complete records), we won't spot
		// any problem that spans chunk boundaries.  Secondly, the date checking
		// stops after the first error.  If the first error is not an ambiguous date
		// then we won't ask the user to choose between DMY and YMD.

		//-- read the gedcom and test it in 8KB chunks
		while ($fp && !feof($fp)) {
			$fcontents = fread($fp, 1024 * 8);
			if (!$l_BOMcleanup && need_BOM_cleanup()) $l_BOMcleanup = true;
			if (!$l_headcleanup && need_head_cleanup()) $l_headcleanup = true;
			if (!$l_macfilecleanup && need_macfile_cleanup()) $l_macfilecleanup = true;
			if (!$l_lineendingscleanup && need_line_endings_cleanup()) $l_lineendingscleanup = true;
			if (!$l_placecleanup && ($placesample = need_place_cleanup()) !== false) $l_placecleanup = true;
			if (!$l_datecleanup && ($datesample = need_date_cleanup()) !== false) $l_datecleanup = true;
			if (!$l_isansi && is_ansi()) $l_isansi = true;
		}
		fclose($fp);

		$cleanup_needed = false;
		if (!$l_datecleanup && !$l_isansi && !$l_BOMcleanup && !$l_headcleanup && !$l_macfilecleanup && !$l_placecleanup && !$l_lineendingscleanup) {
			print i18n::translate('Valid GEDCOM detected. No cleanup required.');
			print "</td></tr>";
			$import = true;
		} else {
			$cleanup_needed = true;
			print "<input type=\"hidden\" name=\"cleanup_needed\" value=\"cleanup_needed\">";
			if (!file_is_writeable(get_gedcom_setting(get_id_from_gedcom($GEDFILENAME), 'path')) && (file_exists(get_gedcom_setting(get_id_from_gedcom($GEDFILENAME), 'path')))) {
				print "<span class=\"error\">".i18n::translate('The GEDCOM file, <b>%s</b>, is not writable. Please check attributes and access rights.', $GEDCOM)."</span>\n";
				print "</td></tr>";
			}
			// NOTE: Check for BOM cleanup
			if ($l_BOMcleanup) {
				print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
				print "<span class=\"error\">".i18n::translate('A Byte Order Mark (BOM) was detected at the beginning of the file. On cleanup, this special code will be removed.')."</span>\n";
				print help_link('BOM_detected');
				print "</td></tr>";
			}
			// NOTE: Check for head cleanup
			if ($l_headcleanup) {
				print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
				print "<span class=\"error\">".i18n::translate('Detected lines before the GEDCOM header <b>0&nbsp;HEAD</b>.  On cleanup, these lines will be removed.')."</span>\n";
				print help_link('invalid_header');
				print "</td></tr>";
			}
			// NOTE: Check for mac file cleanup
			if ($l_macfilecleanup) {
				print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
				print "<span class=\"error\">".i18n::translate('Macintosh file detected.  On cleanup your file will be converted to a DOS file.')."</span>\n";
				print help_link('macfile_detected');
				print "</td></tr>";
			}
			// NOTE: Check for line endings cleanup
			if ($l_lineendingscleanup) {
				print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
				print "<span class=\"error\">".i18n::translate('Empty lines were detected in your GEDCOM file.  On cleanup, these empty lines will be removed.')."</span>\n";
				print help_link('empty_lines_detected');
				print "</td></tr>";
			}
			// NOTE: Check for place cleanup
			if ($l_placecleanup) {
				print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
				print "\n<table class=\"facts_table\">";
				print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
				print "<span class=\"error\">".i18n::translate('Invalid place encodings were detected.  These errors should be fixed.')."</span>\n";
				print "</td></tr>";
				print "<tr><td class=\"descriptionbox wrap width20\">";
				print i18n::translate('Cleanup Places');
				print help_link('cleanup_places');
				print "</td><td class=\"optionbox\" colspan=\"2\"><select name=\"cleanup_places\">\n";
				print "<option value=\"YES\" selected=\"selected\">".i18n::translate('Yes')."</option>\n<option value=\"NO\">".i18n::translate('No')."</option>\n</select>";
				print "</td></tr>";
				print "</td></tr><tr><td class=\"optionbox\" colspan=\"2\">".i18n::translate('Example of invalid place from your GEDCOM:')."<br />".PrintReady(nl2br($placesample[0]));
				print "</table>\n";
				print "</td></tr>";
			}
			// NOTE: Check for date cleanup
			if ($l_datecleanup) {
				print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
				print "<span class=\"error\">".i18n::translate('Detected invalid date formats, on cleanup these will be changed to format of DD MMM YYYY (eg. 1 JAN 2004).')."</span>\n";
				print "\n<table class=\"facts_table\">";
				print "<tr><td class=\"descriptionbox width20\">";
				print i18n::translate('Date format');
				print help_link('detected_date');

				print "</td><td class=\"optionbox\" colspan=\"2\">";
				if (isset ($datesample["choose"])) {
					print "<select name=\"datetype\">\n";
					print "<option value=\"1\">".i18n::translate('Day before Month (DD MM YYYY)')."</option>\n<option value=\"2\">".i18n::translate('Month before Day (MM DD YYYY)')."</option>\n</select>";
				} else
				print "<input type=\"hidden\" name=\"datetype\" value=\"3\" />";
				print "</td></tr><tr><td class=\"optionbox\" colspan=\"2\">".i18n::translate('Example of invalid date from your GEDCOM:')."<br />".$datesample[0];
				print "</td></tr>";
				print "</table>\n";
				print "</td></tr>";
			}
			// NOTE: Check for ansi encoding
			if ($l_isansi) {
				print "<tr><td class=\"optionbox\" colspan=\"2\">";
				print "<span class=\"error\">".i18n::translate('ANSI file encoding detected.  webtrees works best with files encoded in UTF-8.')."</span>\n";
				print "\n<table class=\"facts_table\">";
				print "<tr><td class=\"descriptionbox wrap width20\">";
				print i18n::translate('Convert this ANSI encoded GEDCOM to UTF-8?');
				print help_link('detected_ansi2utf');
				print "</td><td class=\"optionbox\"><select name=\"utf8convert\">\n";
				print "<option value=\"YES\" selected=\"selected\">".i18n::translate('Yes')."</option>\n";
				print "<option value=\"NO\">".i18n::translate('No')."</option>\n</select>";
				print "</td></tr>";
				print "</table>\n";
			}
		}
	} else if (!$cleanup_needed) {
		print i18n::translate('Valid GEDCOM detected. No cleanup required.');
		$import = true;
	} else $import = true;
	?>
	<input type = "hidden" name="GEDFILENAME" value="<?php if (isset($GEDFILENAME)) print $GEDFILENAME; ?>" />
	<input type = "hidden" name="verify" value="validate_form" />
	<input type = "hidden" name="bakfile" value="<?php if (isset($bakfile)) print $bakfile; ?>" />
	<input type = "hidden" name="path" value="<?php if (isset($path)) print $path; ?>" />
	<input type = "hidden" name="no_upload" value="<?php if (isset($no_upload)) print $no_upload; ?>" />
	<input type = "hidden" name="override" value="<?php if (isset($override)) print $override; ?>" />
	<input type = "hidden" name="ok" value="<?php if (isset($ok)) print $ok; ?>" />
	<input type = "hidden" name="keepmedia" value="<?php print $keepmedia?'yes':'no'; ?>" />
	</table>
	</div>
	</td></tr>
<?php
}

if ($import == true) {
	// NOTE: Additional import options
	print "<tr><td class=\"topbottombar $TEXT_DIRECTION\" colspan=\"2\">";
	print "<a href=\"javascript: ".i18n::translate('Import Options')."\" onclick=\"expand_layer('import_options');return false\"><img id=\"import_options_img\" src=\"".$WT_IMAGE_DIR."/";
	if ($startimport != "true")
	print $WT_IMAGES["minus"]["other"];
	else
	print $WT_IMAGES["plus"]["other"];
	print "\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
	print "&nbsp;<a href=\"javascript: ".i18n::translate('Import Options')."\" onclick=\"expand_layer('import_options');return false\">".i18n::translate('Import Options')."</a>";
	print help_link('import_options');
	print "</td></tr>";
	print "<tr><td class=\"optionbox\" colspan=\"2\">";
	print "<div id=\"import_options\" style=\"display: ";
	if ($startimport != "true")
	print "block ";
	else
	print "none ";
	print "\">";
	print "\n<table class=\"facts_table\">";

	// NOTE: Time limit for import
	print "<tr><td class=\"descriptionbox width20 wrap\">";
	print i18n::translate('Time limit:');
	print help_link('time_limit');
	print "</td><td class=\"optionbox\"><input type=\"text\" name=\"timelimit\" value=\"".$timelimit."\" size=\"5\"";
	if ($startimport == "true")
	print " disabled ";
	print "/>\n";
	print "</td></tr>";

	// NOTE: Auto-click "Continue" button
	print "<tr><td class=\"descriptionbox width20 wrap\">";
	print i18n::translate('Automatically press «Continue» button');
	print help_link('autoContinue');
	print "</td><td class=\"optionbox\"><select name=\"autoContinue\">\n";
	print "<option value=\"YES\" selected=\"selected\">".i18n::translate('Yes')."</option>\n";
	print "<option value=\"NO\">".i18n::translate('No')."</option>\n</select>";
	print "</td></tr>";

	// NOTE: change XREF to RIN, REFN, or Don't change
	print "<tr><td class=\"descriptionbox wrap\">";
	print i18n::translate('Change Individual ID to:');
	print help_link('change_indi2id');
	print "</td><td class=\"optionbox\">";
	if ($startimport == "true") {
		if ($xreftype == "NA")
		print i18n::translate_c('Do not change Individual ID', 'Do not change');
		else
		print $xreftype;
	} else {
		print "<select name=\"xreftype\">\n";
		print "<option value=\"NA\">".i18n::translate_c('Do not change Individual ID', 'Do not change')."</option>\n<option value=\"RIN\">RIN</option>\n";
		print "<option value=\"REFN\">REFN</option>\n</select>";
	}
	print "</td></tr>\n";

	print "<input type=\"hidden\" name=\"startimport\" value=\"true\" />";
	print "<input type=\"hidden\" name=\"ged\" value=\"";
	if (isset ($GEDFILENAME))
	print $GEDFILENAME;
	print "\" />";
	print "<input type=\"hidden\" name=\"GEDFILENAME\" value=\"";
	if (isset ($GEDFILENAME))
	print $GEDFILENAME;
	print "\" />";
	print "<input type=\"hidden\" name=\"exists\" value=\"";
	if (isset ($exists))
	print $exists;
	print "\" />";
	print "<input type=\"hidden\" name=\"ok\" value=\"".$ok."\" />";
	print "<input type=\"hidden\" name=\"import\" value=\"".$import."\" />";
	print "<input type=\"hidden\" name=\"l_isansi\" value=\"";
	if (isset ($l_isansi))
	print $l_isansi;
	print "\" />";
	print "<input type=\"hidden\" name=\"check\" value=\"\" />";
	print "</table></div>";
	print "</td></tr>";
}

if ($startimport == "true") {
	set_gedcom_setting(get_id_from_gedcom($GEDFILENAME), 'imported', false);

	if (isset ($exectime)) {
		$oldtime = time() - $exectime;
		$skip_table = 0;
	} else
	$oldtime = time();

	/**
	 * function that sets up the html required to run the progress bar
	 * @param long $FILE_SIZE the size of the file
	 */
	function setup_progress_bar($FILE_SIZE) {
		global $ged, $timelimit;
		?>
		<script type="text/javascript">
		<!--
		function complete_progress(time, exectext, go_pedi, go_welc) {
			progress = document.getElementById("progress_header");
			if (progress) progress.innerHTML = '<span class="error"><b><?php print i18n::translate('Import complete'); ?></b></span><br />'+exectext+' '+time+' <?php print i18n::translate('sec.'); ?>';
			progress = document.getElementById("link1");
			if (progress) progress.innerHTML = '<a href="pedigree.php?ged=<?php print encode_url(str_replace("'", "\'", $ged)); ?>">'+go_pedi+'</a>';
			progress = document.getElementById("link2");
			if (progress) progress.innerHTML = '<a href="index.php?ctype=gedcom&ged=<?php print encode_url(str_replace("'", "\'", $ged)); ?>">'+go_welc+'</a>';
			progress = document.getElementById("link3");
			if (progress) progress.innerHTML = '<a href="editgedcoms.php"><?php print i18n::translate('Manage GEDCOMs and edit Privacy'); ?></a>';
		}
		function wait_progress() {
			progress = document.getElementById("progress_header");
			if (progress) progress.innerHTML = '<?php print i18n::translate('Please be patient'); ?>';
		}

		var FILE_SIZE = <?php print $FILE_SIZE; ?>;
		var TIME_LIMIT = <?php print $timelimit; ?>;

		function update_progress(bytes, time) {
			perc = Math.round(1000*(bytes / FILE_SIZE))/10;
			if (perc>100) perc = 100;
			progress = document.getElementById("progress_div");
			if (progress) {
				progress.style.width = perc+"%";
				progress.innerHTML = perc+"%";
			}
			perc = Math.round(100*(time / TIME_LIMIT));
			if (perc>100) perc = 100;
			progress = document.getElementById("time_div");
			if (progress) {
				progress.style.width = perc+"%"; progress.innerHTML = perc+"%";
			}
		}
		//-->
		</script>
		<?php
		print "\n<table style=\"width: 800px;\"><tr><td>";
		print "<div id=\"progress_header\" class=\"person_box\" style=\"width: 350px; margin: 10px; text-align: center;\">\n";
		print "<b>".i18n::translate('Import Progress...')."</b>";
		print "<div style=\"left: 10px; right: 10px; width: 300px; height: 20px; border: inset #CCCCCC 3px; background-color: #000000;\">\n";
		print "<div id=\"progress_div\" class=\"person_box\" style=\"width: 1%; height: 18px; text-align: center; overflow: hidden;\">1%</div>\n";
		print "</div>\n";
		print "</div>\n";
		print "</td><td style=\"text-align: center;\"><div id=\"link1\">&nbsp;</div>";
		print "<div id=\"link2\">&nbsp;</div><div id=\"link3\">&nbsp;</div>";
		print "</td></tr></table>";
		print "\n<table style=\"width: 800px;\"><tr><td>";
		print "<div id=\"progress_header\" class=\"person_box\" style=\"width: 350px; margin: 10px; text-align: center;\">\n";
		if ($timelimit == 0)
		print "<b>".i18n::translate('Time limit:')." ".i18n::translate('None')."</b>";
		else
		print "<b>".i18n::translate('Time limit:')." ".$timelimit." ".i18n::translate('sec.')."</b>";
		print "<div style=\"left: 10px; right: 10px; width: 300px; height: 20px; border: inset #CCCCCC 3px; background-color: #000000;\">\n";
		print "<div id=\"time_div\" class=\"person_box\" style=\"width: 1%; height: 18px; text-align: center; overflow: hidden;\">1%</div>\n";
		print "</div>\n";
		print "</div>\n";
		print "</td><td style=\"text-align: center;\"><div id=\"link1\">&nbsp;</div>";
		print "<div id=\"link2\">&nbsp;</div><div id=\"link3\">&nbsp;</div>";
		print "</td></tr></table>";
		flush();
	}
//-- end of setup_progress_bar function

if (!isset ($stage))
$stage = 0;
if ((empty ($ged)) || (!get_id_from_gedcom($ged))) {
	$ged = $GEDCOM;
}
$ged_id=get_id_from_gedcom($ged);
$temp = $THEME_DIR;
$GEDCOM_FILE = get_gedcom_setting($ged_id, 'path');
$FILE = $ged;
$TITLE = get_gedcom_setting($ged_id, 'title');
require get_config_file($ged_id);

$temp2 = $THEME_DIR;
$THEME_DIR = $temp;
$THEME_DIR = $temp2;

if (isset ($GEDCOM_FILE)) {
	if ((!isFileExternal($GEDCOM_FILE)) && (!file_exists($GEDCOM_FILE))) {
		print "<span class=\"error\"><b>Could not locate gedcom file at $GEDCOM_FILE<br /></b></span>\n";
		unset ($GEDCOM_FILE);
	}
}

if ($stage == 0) {
	$_SESSION["resumed"] = 0;
	if (file_exists($INDEX_DIRECTORY.basename($GEDCOM_FILE).".new"))
	unlink($INDEX_DIRECTORY.basename($GEDCOM_FILE).".new");
	empty_database($ged_id, $keepmedia);
	$stage = 1;
}
flush();

// Importing generates 1000's of separate updates, and in auto-commit mode, each of these
// is a separate transaction, which must be flushed to disk.  This limits writes to approx
// 100 per second (on a typical 6000 RPM disk).
// By wrapping it all in one transaction, we only have one disk flush.
WT_DB::exec("START TRANSACTION");

if ($stage == 1) {
	@ set_time_limit($timelimit);
	//-- make sure that we are working with the true time limit
	//-- commented out for now because PHP does not seem to be reporting it correctly on Linux
	//$timelimit = ini_get("max_execution_time");

	$FILE_SIZE = filesize($GEDCOM_FILE);
	print "<tr><td class=\"topbottombar $TEXT_DIRECTION\" colspan=\"2\">";
	print i18n::translate('Reading GEDCOM file')." ".$GEDCOM_FILE;
	print "</td></tr>";
	print "</table>";

	print i18n::translate('The status bars below will let you know how the Import is progressing.  If the time limit runs out the Import will be stopped and you will be asked to press a <b>Continue</b> button.  If you don\'t see the <b>Continue</b> button, you must restart the Import with a smaller time limit value.');
	//print "<tr><td class=\"optionbox\">";
	setup_progress_bar($FILE_SIZE);
	//print "</td></tr>";
	flush();

	// ------------------------------------------------------ Begin importing data
	$i = 0;

	//-- as we are importing the file, a new file is being written to store any
	//-- changes that might have occurred to the gedcom file (eg. conversion of
	//-- media objects).  After the import is complete the new file is
	//-- copied over the old file.
	//-- The records are written during the import_record() method and the
	//-- update_media() method
	//-- open handle to read file
	$fpged = fopen($GEDCOM_FILE, "rb");
	//-- open handle to write changed file
	$fpnewged = fopen($INDEX_DIRECTORY.basename($GEDCOM_FILE).".new", "ab");
	$BLOCK_SIZE = 1024 * 4; //-- 4k bytes per read (4kb is usually the page size of a virtual memory system)
	//-- resume a halted import from the session
	if (!empty ($_SESSION["resumed"])) {
		$import_stats=$_SESSION['import_stats'];
		$start_time  =$_SESSION['start_time'];
		$TOTAL_BYTES =$_SESSION["TOTAL_BYTES"];
		$fcontents = $_SESSION["fcontents"];
		$media_count = $_SESSION["media_count"];
		$found_ids = $_SESSION["found_ids"];
		$MAX_IDS = $_SESSION["MAX_IDS"];
		$autoContinue = $_SESSION["autoContinue"];
		fseek($fpged, $TOTAL_BYTES);
	} else {
		$fcontents = "";
		$TOTAL_BYTES = 0;
		$media_count = 0;
		$MAX_IDS = array();
		$_SESSION["resumed"] = 1;
		$import_stats=array();
		$start_time=microtime(true);
	}
	while (!feof($fpged)) {
		$temp = fread($fpged, $BLOCK_SIZE);
		$fcontents .= $temp;
		$TOTAL_BYTES += strlen($temp);
		$pos1 = 0;
		while ($pos1 !== false) {
			//-- find the start of the next record
			$pos2 = strpos($fcontents, "\n0", $pos1 +1);
			while ((!$pos2) && (!feof($fpged))) {
				$temp = fread($fpged, $BLOCK_SIZE);
				$fcontents .= $temp;
				$TOTAL_BYTES += strlen($temp);
				$pos2 = strpos($fcontents, "\n0", $pos1 +1);
			}

			//-- pull the next record out of the file
			if ($pos2) {
				$indirec = substr($fcontents, $pos1, $pos2 - $pos1);
			} else {
				$indirec = substr($fcontents, $pos1);
			}

			try {
				$record_type=import_record($indirec, $ged_id, false);
			} catch (PDOException $ex) {
				// Import errors are likely to be caused by duplicate records.
				// There is no safe way of handling these.  Just display them
				// and let the user decide.
				echo '<pre class="error">', $ex->getMessage(), '</pre>';
				echo '<pre>', WT_GEDCOM, ': ', $ged_id, '</pre>';
				echo '<pre>', htmlspecialchars($indirec), '</pre>';
				// Don't let the error message disappear off the screen.
				$autoContinue=false;
				$record_type=i18n::translate('invalid');
			}

			// Generate import statistics
			if (!isset($import_stats[$record_type])) {
				$import_stats[$record_type]=array(
					'records'=>0,
					'bytes'  =>0,
					'seconds'=>0
				);
			}
			$end_time=microtime(true);
			$import_stats[$record_type]['records']++;
			$import_stats[$record_type]['bytes'  ]+=$pos2 ? $pos2-$pos1 : strlen($indirec);
			$import_stats[$record_type]['seconds']+=$end_time-$start_time;
			$start_time=$end_time;

			//-- move the cursor to the start of the next record
			$pos1 = $pos2;

			$i ++;

			//-- update the progress bars at every 10 records
			if ($i % 10 == 0) {
				$newtime = time();
				$exectime = $newtime - $oldtime;
				echo WT_JS_START, "update_progress($TOTAL_BYTES, $exectime);", WT_JS_END;
				flush();
			} else {
				print ' ';
			}

			//-- check if we are getting close to timing out
			if ($i % 5 == 0) {
				//-- keep the browser informed by sending more data
				print "\n";
				$newtime = time();
				$exectime = $newtime - $oldtime;
				if (($timelimit != 0) && ($timelimit - $exectime) < 2) {
					$importtime = $importtime + $exectime;
					$fcontents = substr($fcontents, $pos2);
					//-- store the resume information in the session
					$_SESSION['import_stats']=$import_stats;
					$_SESSION['start_time']=$start_time;
					$_SESSION["media_count"] = $media_count;
					$_SESSION["TOTAL_BYTES"] = $TOTAL_BYTES;
					$_SESSION["fcontents"] = $fcontents;
					$_SESSION["importtime"] = $importtime;
					$_SESSION["MAX_IDS"] = $MAX_IDS;
					$_SESSION["found_ids"] = $found_ids;
					$_SESSION["autoContinue"] = $autoContinue;

					//-- close the file connection
					fclose($fpged);
					fclose($fpnewged);
					$_SESSION["resumed"]++;
					print "\n<table class=\"facts_table\">";
					?>
					<tr>
						<td class="descriptionbox"><?php print i18n::translate('The execution time limit was reached.  Click the Continue button below to resume importing the GEDCOM file.'); ?></td>
					</tr>
					<tr>
						<td class="topbottombar"><input type="hidden" name="ged"
							value="<?php print $ged; ?>" /> <input type="hidden" name="stage"
							value="1" /> <input type="hidden" name="timelimit"
							value="<?php print $timelimit; ?>" /> <input type="hidden"
							name="importtime" value="<?php print $importtime; ?>" />
						<input type="hidden" name="xreftype" value="<?php print $xreftype; ?>" />
						<input type="hidden" name="utf8convert"
							value="<?php print $utf8convert; ?>" /> <input type="hidden"
							name="verify" value="<?php print $verify; ?>" /> <input type="hidden"
							name="startimport" value="<?php print $startimport; ?>" /> <input
							type="hidden" name="import" value="<?php print $import; ?>" /> <input
							type="hidden" name="FILE" value="<?php print $FILE; ?>" /> <input
							type="submit" name="continue"
							value="<?php print i18n::translate('Continue'); ?>" /></td>
					</tr>
					</table>
					<?php if ($autoContinue=="YES") { ?>
					<script type="text/javascript">
						<!--
						(function (fn) {
							if (window.addEventListener) window.addEventListener('load', fn, false);
							else window.attachEvent('onload', fn);
						})
						(function() {
							document.forms['configform'].elements['continue'].click();
						});
						//-->
					</script>
					<?php
					}
					print_footer();
					session_write_close();
					exit;
				}
			}
		}
		$fcontents = substr($fcontents, $pos2);
	}
	fclose($fpged);
	fclose($fpnewged);
	//-- as we are importing the file, a new file is being written to store any
	//-- changes that might have occurred to the gedcom file (eg. conversion of
	//-- media objects).  After the import is complete the new file is
	//-- copied over the old file.
	//-- The records are written during the import_record() method and the
	//-- update_media() method
	$res = @ copy($GEDCOM_FILE, $INDEX_DIRECTORY.basename($GEDCOM_FILE).".bak");
	if (!$res)
		print "<span class=\"error\">Unable to create backup of the GEDCOM file at ".$INDEX_DIRECTORY.basename($GEDCOM_FILE).".bak</span><br />";
	//unlink($GEDCOM_FILE);
	$res = @ copy($INDEX_DIRECTORY.basename($GEDCOM_FILE).".new", $GEDCOM_FILE);
	if (!$res) {
		print "<span class=\"error\">Unable to copy updated GEDCOM file ".$INDEX_DIRECTORY.basename($GEDCOM_FILE).".new to ".$GEDCOM_FILE."</span><br />";
	} else {
		@unlink($INDEX_DIRECTORY.basename($GEDCOM_FILE).".new");
		$logline = AddToLog($GEDCOM_FILE." updated", 'config');
	}
	$newtime = time();
	$exectime = $newtime - $oldtime;
	$importtime = $importtime + $exectime;
	$exec_text = i18n::translate('Execution time:');
	$go_pedi = i18n::translate('Click here to go to the Pedigree tree.');
	$go_welc = i18n::translate('Home Page');
	if (WT_LOCALE == "fr" || WT_LOCALE == "it") { // TODO Just escape it properly!
		echo WT_JS_START, "complete_progress($importtime, \"$exec_text\", \"$go_pedi\", \"$go_welc\");", WT_JS_END;
	} else {
		echo WT_JS_START, "complete_progress($importtime, '$exec_text', '$go_pedi', '$go_welc');", WT_JS_END;
	}
	flush();

	// Import Statistics
	$show_table1  = "<table class=\"list_table\"><tr>";
	$show_table1 .= "<tr><td class=\"topbottombar\" colspan=\"4\">".i18n::translate('Import')."</td></tr>";
	$show_table1 .= "<td class=\"descriptionbox\">".i18n::translate('Execution time:')."</td>";
	$show_table1 .= "<td class=\"descriptionbox\">".i18n::translate('Bytes read:')."</td>";
	$show_table1 .= "<td class=\"descriptionbox\">".i18n::translate('Found record')."</td>";
	$show_table1 .= "<td class=\"descriptionbox\">".i18n::translate('Type')."</td></tr>";
	$total_seconds=0;
	$total_bytes  =0;
	$total_records=0;
	foreach ($import_stats as $type=>$stats) {
		$total_seconds+=$stats['seconds'];
		$total_bytes  +=$stats['bytes'];
		$total_records+=$stats['records'];
		$show_table1 .= "<tr><td class=\"optionbox indent\">".sprintf("%.2f %s", $stats['seconds'], i18n::translate('sec.'))."</td>";
		$show_table1 .= "<td class=\"optionbox indent\">".$stats['bytes']."</td>";
		$show_table1 .= "<td class=\"optionbox indent\">".$stats['records']."</td>";
		$show_table1 .= "<td class=\"optionbox\">".$type."</td></tr>";
	}
	$show_table1 .= "<tr><td class=\"optionbox indent\">".sprintf("%.2f %s", $total_seconds, i18n::translate('sec.'))."</td>";
	$show_table1 .= "<td class=\"optionbox indent\">".$total_bytes.WT_JS_START."update_progress($total_bytes, $exectime);".WT_JS_END;
	$show_table1 .= "<td class=\"optionbox indent\">". $total_records."</td>";
	$show_table1 .= "<td class=\"optionbox\">&nbsp;</td></tr>";
	$show_table1 .= "</table>";
	echo "<tr><td class=\"topbottombar $TEXT_DIRECTION\">", i18n::translate('Import Statistics'), "</td></tr>";
	print "<tr><td class=\"optionbox\">";
	print "<table cellspacing=\"20px\"><tr><td class=\"optionbox\" style=\"vertical-align: top;\">";
	if (isset ($skip_table)) {
	 print "<br />...";
	} else {
		print $show_table1;
	}
	print "</td></tr></table>";
	// NOTE: Finished Links
	import_max_ids($ged_id, $MAX_IDS);
	set_gedcom_setting($ged_id, 'imported', true);
	print "</td></tr>";

	$record_count = 0;
	$_SESSION["resumed"] = 0;
	unset ($_SESSION['import_stats']);
	unset ($_SESSION["TOTAL_BYTES"]);
	unset ($_SESSION["fcontents"]);
}
}
?>
<tr>
	<td class="topbottombar" colspan="2"><?php

	if ($startimport != "true")
	print "<input type=\"submit\" name=\"continue\" value=\"".i18n::translate('Continue')."\" />&nbsp;";
	if ($verify && $startimport != "true")
	print "<input type=\"button\" name=\"cancel\" value=\"".i18n::translate('Cancel')."\" onclick=\"document.configform.override.value='no'; document.configform.no_upload.value='cancel_upload'; document.configform.submit(); \" />";
	?></td>
</tr>
</table>
</form>
	<?php

WT_DB::exec("COMMIT");

	print_footer();
	?>
