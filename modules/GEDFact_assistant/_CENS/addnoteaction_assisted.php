<?php
/**
* Include for GEDFact Assistant - Census.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
* @subpackage Edit
* @version $Id$
*/

// This file is required by the addnoteaction_assisted function in edit_interface.php

//------------------------------------------------------------------------------
//-- create a shared note record from the incoming variables

	if (WT_DEBUG) {
		phpinfo(INFO_VARIABLES);
	}
	$newgedrec  = "0 @XREF@ NOTE\n";

	if (isset($_REQUEST['EVEN'])) $EVEN = $_REQUEST['EVEN'];
	if (!empty($EVEN) && count($EVEN)>0) {
		$newgedrec .= "1 DATA\n";
		$newgedrec .= "2 EVEN ".implode(",", $EVEN)."\n";
		if (!empty($EVEN_DATE)) $newgedrec .= "3 DATE ".$EVEN_DATE."\n";
		if (!empty($EVEN_PLAC)) $newgedrec .= "3 PLAC ".$EVEN_PLAC."\n";
		if (!empty($AGNC))      $newgedrec .= "2 AGNC ".$AGNC."\n";
	}
	if (isset($_REQUEST['ABBR'])) $ABBR = $_REQUEST['ABBR'];
	if (isset($_REQUEST['TITL'])) $TITL = $_REQUEST['TITL'];
	if (isset($_REQUEST['DATE'])) $DATE = $_REQUEST['DATE'];
	if (isset($_REQUEST['NOTE'])) $NOTE = $_REQUEST['NOTE'];
	if (isset($_REQUEST['_HEB'])) $_HEB = $_REQUEST['_HEB'];
	if (isset($_REQUEST['ROMN'])) $ROMN = $_REQUEST['ROMN'];
	if (isset($_REQUEST['AUTH'])) $AUTH = $_REQUEST['AUTH'];
	if (isset($_REQUEST['PUBL'])) $PUBL = $_REQUEST['PUBL'];
	if (isset($_REQUEST['REPO'])) $REPO = $_REQUEST['REPO'];
	if (isset($_REQUEST['CALN'])) $CALN = $_REQUEST['CALN'];
	
	if (isset($_REQUEST['pid_array'])) 	$pid_array	 = $_REQUEST['pid_array'];
	if (isset($_REQUEST['pid'])) 		$pid		 = $_REQUEST['pid'];
	
	global $pid;

	if (!empty($NOTE)) {
		$newlines = preg_split("/\r?\n/", $NOTE, -1);
		for ($k=0; $k<count($newlines); $k++) {
			if ( $k==0 && count($newlines)>1) {
				$newgedrec = "0 @XREF@ NOTE $newlines[$k]\n";
			}else if ( $k==0 ) {
				$newgedrec = "0 @XREF@ NOTE $newlines[$k]\n1 CONT\n";
			} else {
				$newgedrec .= "1 CONT $newlines[$k]\n";
			}
		}
	}

	if (!empty($ABBR)) $newgedrec .= "1 ABBR $ABBR\n";
	if (!empty($TITL)) {
		// $newgedrec .= "1 TITL $TITL\n";
		// $newgedrec .= "2 DATE $DATE\n";
		if (!empty($_HEB)) $newgedrec .= "2 _HEB $_HEB\n";
		if (!empty($ROMN)) $newgedrec .= "2 ROMN $ROMN\n";
	}
	if (!empty($AUTH)) $newgedrec .= "1 AUTH $AUTH\n";
	if (!empty($PUBL)) {
		$newlines = preg_split("/\r?\n/", $PUBL, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($newlines as $k=>$line) {
			if ( $k==0 ) {
				$newgedrec .= "1 PUBL $line\n";
			} else {
				$newgedrec .= "2 CONT $line\n";
			}
		}
	}
	if (!empty($NOTE)) {
		//$newgedrec .= "1 NOTE @$NOTE@\n";
		if (!empty($CALN)) $newgedrec .= "2 CALN $CALN\n";
	}
	if (WT_DEBUG) {
		echo "<pre>$newgedrec</pre>";
	}
	// $xref = "Test";
	if ($pid_array != '') {
		$xref = append_gedrec($newgedrec);
	} else {
		$xref="none";
		echo "<br /><br /><br />";
		echo "<div class=indent> No individuals entered, close and try again </div>";
		echo "<br /><br /><br />";	
	}
	
	if ($xref != "none") {
		echo "<br /><br />\n".i18n::translate('New Shared Note created successfully.')." (".$xref.")<br /><br />";
		echo "<br /><br />";
		echo " &nbsp;&nbsp;&nbsp; The Census event (when saved) will be linked to Indi id's: &nbsp;&nbsp;&nbsp;&nbsp; ". $pid_array;
		echo "<br /><br />";
		echo "<br /><br />";
		echo "&nbsp;&nbsp;&nbsp; <a href=\"javascript://NOTE $xref\" onclick=\"openerpasteid('$xref'); return false;\">".i18n::translate('Paste the following ID into your editing fields to reference the newly created record ')." <b>$xref</b></a>\n";
		echo "<br /><br /><br /><br />";

		?>
		<script>
		if (parent.opener.document.getElementById("pids_array_edit") == null || parent.opener.document.getElementById("pids_array_edit") == 'undefined') {
			//alert ("EDIT NOT HERE");
		} else {
		//	alert("WE ARE EDITING an EVENT");
		//	alert(parent.opener.document.editform.pids_array_edit.value);
			parent.opener.document.editform.pids_array_edit.value="<?php echo $pid_array; ?>";
		//	alert(parent.opener.document.editform.pids_array_edit.value);
		}
		if (parent.opener.document.getElementById("pids_array_add") == null || parent.opener.document.getElementById("pids_array_add") == 'undefined') {
			//alert ("ADD NOT HERE");
		} else {
		//	alert("WE ARE ADDING an EVENT");
		//	alert(parent.opener.document.addform.pids_array_add.value);
			parent.opener.document.addform.pids_array_add.value="<?php echo $pid_array; ?>";
		//	alert(parent.opener.document.addform.pids_array_add.value);
		}
		</script>
		<?php

		echo "<br /><br /><br /><br />";
	}


?>
