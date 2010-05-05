<?php
/**
* Controller for the Clippings Page
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
* @package webtrees
* @subpackage Charts
* @version $Id: clippings_ctrl.php 6607 2009-12-23 17:43:48Z yalnifj $
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLIPPINGS_CTRL', '');

require_once WT_ROOT.'includes/classes/class_grampsexport.php';
require_once WT_ROOT.'includes/classes/class_person.php';
require_once WT_ROOT.'includes/functions/functions.php';
require_once WT_ROOT.'includes/controllers/basecontrol.php';
require_once WT_ROOT.'library/pclzip.lib.php';

function same_group($a, $b) {
	if ($a['type'] == $b['type'])
	return strnatcasecmp($a['id'], $b['id']);
	if ($a['type'] == 'source')
	return 1;
	if ($a['type'] == 'indi')
	return -1;
	if ($b['type'] == 'source')
	return -1;
	if ($b['type'] == 'indi')
	return 1;
	return 0;
}

function id_in_cart($id) {
	global $cart, $GEDCOM;
	$ct = count($cart);
	for ($i = 0; $i < $ct; $i++) {
		$temp = $cart[$i];
		if ($temp['id'] == $id && $temp['gedcom'] == $GEDCOM) {
			return true;
		}
	}
	return false;
}

/**
* Main controller class for the Clippings page.
*/
class ClippingsControllerRoot extends BaseController {

	var $download_data;
	var $media_list = array();
	var $addCount = 0;
	var $privCount = 0;
	var $type="";
	var $id="";
	var $IncludeMedia;
	var $conv_path;
	var $conv_slashes;
	var $privatize_export;
	var $Zip;
	var $filetype;
	var $level1;  // number of levels of ancestors
	var $level2;
	var $level3; // number of levels of descendents

	/**
	 * @param string $thing the id of the person
	 */
	function ClippingsControllerRoot() {
		parent :: BaseController();
	}
	//----------------beginning of function definitions for ClippingsControllerRoot
	function init() {
		global $ENABLE_CLIPPINGS_CART, $SCRIPT_NAME, $SERVER_URL, $HOME_SITE_TEXT, $HOME_SITE_URL, $MEDIA_DIRECTORY;
		global $GEDCOM, $cart;

		if (!isset ($ENABLE_CLIPPINGS_CART))
		$ENABLE_CLIPPINGS_CART = WT_PRIV_HIDE;
		if ($ENABLE_CLIPPINGS_CART === true)
		$ENABLE_CLIPPING_CART = WT_PRIV_PUBLIC;
		if ($ENABLE_CLIPPINGS_CART < WT_USER_ACCESS_LEVEL) {
			header("Location: index.php");
			exit;
		}

		if (!isset($_SESSION['exportConvPath'])) $_SESSION['exportConvPath'] = $MEDIA_DIRECTORY;
		if (!isset($_SESSION['exportConvSlashes'])) $_SESSION['exportConvSlashes'] = 'forward';

		$this->action = safe_GET("action");
		$this->id = safe_GET('id');
		$remove = safe_GET('remove',"","no");
		$convert = safe_GET('convert',"","no");
		$this->Zip = safe_GET('Zip');
		$this->IncludeMedia = safe_GET('IncludeMedia');
		$this->conv_path = safe_GET('conv_path', WT_REGEX_NOSCRIPT, $_SESSION['exportConvPath']);
		$this->conv_slashes = safe_GET('conv_slashes', array('forward', 'backward'), $_SESSION['exportConvSlashes']);
		$this->privatize_export = safe_GET('privatize_export', array('none', 'visitor', 'user', 'gedadmin', 'admin'));
		$this->filetype = safe_GET('filetype');
		$this->level1 = safe_GET('level1');
		$this->level2 = safe_GET('level2');
		$this->level3 = safe_GET('level3');
		if (empty($this->filetype)) $this->filetype = "gedcom";
		$others = safe_GET('others');
		$item = safe_GET('item');
		if (!isset($cart)) $cart = $_SESSION['cart'];
		$this->type = safe_GET('type');

		$this->conv_path = stripLRMRLM($this->conv_path);
		$_SESSION['exportConvPath'] = $this->conv_path;		// remember this for the next Download
		$_SESSION['exportConvSlashes'] = $this->conv_slashes;

		if ($this->action == 'add') {
			if (empty($this->type) && !empty($this->id)) {
				$this->type="";
				$obj = GedcomRecord::getInstance($this->id);
				if (is_null($obj)) {
					$this->id="";
					$this->action="";
				}
				else $this->type = strtolower($obj->getType());
			}
			else if (empty($this->id)) $this->action="";
			if (!empty($this->id) && $this->type != 'fam' && $this->type != 'indi' && $this->type != 'sour')
			$this->action = 'add1';
		}

		if ($this->action == 'add1') {
			$clipping = array ();
			$clipping['type'] = $this->type;
			$clipping['id'] = $this->id;
			$clipping['gedcom'] = $GEDCOM;
			$ret = $this->add_clipping($clipping);
			if ($ret) {
				if ($this->type == 'sour') {
					if ($others == 'linked') {
						foreach (fetch_linked_indi($this->id, 'SOUR', WT_GED_ID) as $indi) {
							if ($indi->canDisplayName()) {
								$this->add_clipping(array('type'=>'indi', 'id'=>$indi->getXref()));
							}
						}
						foreach (fetch_linked_fam($this->id, 'SOUR', WT_GED_ID) as $fam) {
							if ($fam->canDisplayName()) {
								$this->add_clipping(array('type'=>'fam', 'id'=>$fam->getXref()));
							}
						}
					}
				}
				if ($this->type == 'fam') {
					if ($others == 'parents') {
						$parents = find_parents($this->id);
						if (!empty ($parents["HUSB"])) {
							$clipping = array ();
							$clipping['type'] = "indi";
							$clipping['id'] = $parents["HUSB"];
							$ret = $this->add_clipping($clipping);
						}
						if (!empty ($parents["WIFE"])) {
							$clipping = array ();
							$clipping['type'] = "indi";
							$clipping['id'] = $parents["WIFE"];
							$ret = $this->add_clipping($clipping);
						}
					} else
					if ($others == "members") {
						$this->add_family_members($this->id);
					} else
					if ($others == "descendants") {
						$this->add_family_descendancy($this->id);
					}
				} else
				if ($this->type == 'indi') {
					if ($others == 'parents') {
						$famids = find_family_ids($this->id);
						foreach ($famids as $indexval => $famid) {
							$clipping = array ();
							$clipping['type'] = "fam";
							$clipping['id'] = $famid;
							$ret = $this->add_clipping($clipping);
							if ($ret) {
								$this->add_family_members($famid);
							}
						}
					} else
					if ($others == 'ancestors') {
						$this->add_ancestors_to_cart($this->id, $this->level1);
					} else
					if ($others == 'ancestorsfamilies') {
						$this->add_ancestors_to_cart_families($this->id, $this->level2);
					} else
					if ($others == 'members') {
						$famids = find_sfamily_ids($this->id);
						foreach ($famids as $indexval => $famid) {
							$clipping = array ();
							$clipping['type'] = "fam";
							$clipping['id'] = $famid;
							$ret = $this->add_clipping($clipping);
							if ($ret)
							$this->add_family_members($famid);
						}
					} else
					if ($others == 'descendants') {
						$famids = find_sfamily_ids($this->id);
						foreach ($famids as $indexval => $famid) {
							$clipping = array ();
							$clipping['type'] = "fam";
							$clipping['id'] = $famid;
							$ret = $this->add_clipping($clipping);
							if ($ret)
							$this->add_family_descendancy($famid, $this->level3);
						}
					}
				}
			}
		} else
		if ($this->action == 'remove') {
			$ct = count($cart);
			for ($i = $item +1; $i < $ct; $i++) {
				$cart[$i -1] = $cart[$i];
			}
			unset ($cart[$ct -1]);
		} else
		if ($this->action == 'empty') {
			$cart = array ();
			$_SESSION["cart"] = $cart;
		} else
		if ($this->action == 'download') {
			usort($cart, "same_group");
			if ($this->filetype == "gedcom") {
				$path = substr($SCRIPT_NAME, 0, strrpos($SCRIPT_NAME, "/"));
				if (empty ($path))
				$path = "/";
				if ($path[strlen($path) - 1] != "/")
				$path .= "/";
				if ($SERVER_URL[strlen($SERVER_URL) - 1] == "/") {
					$dSERVER_URL = substr($SERVER_URL, 0, strlen($SERVER_URL) - 1);
				} else
				$dSERVER_URL = $SERVER_URL;
				$media = array ();
				$mediacount = 0;
				$ct = count($cart);
				$filetext = "0 HEAD\n1 SOUR ".WT_WEBTREES."\n2 NAME ".WT_WEBTREES."\n2 VERS ".WT_VERSION_TEXT."\n1 DEST DISKETTE\n1 DATE " . date("j M Y") . "\n2 TIME " . date("H:i:s") . "\n";
				$filetext .= "1 GEDC\n2 VERS 5.5\n2 FORM LINEAGE-LINKED\n1 CHAR UTF-8\n";
				$head = find_gedcom_record("HEAD", WT_GED_ID);
				$placeform = trim(get_sub_record(1, "1 PLAC", $head));
				if (!empty ($placeform))
				$filetext .= $placeform . "\n";
				else
				$filetext .= "1 PLAC\n2 FORM " . "City, County, State/Province, Country" . "\n";
				if ($convert == "yes") {
					$filetext = str_replace("UTF-8", "ANSI", $filetext);
					$filetext = utf8_decode($filetext);
				}

				$tempUserID = '#ExPoRt#';
				if ($this->privatize_export!='none') {
					// Create a temporary userid
					$export_user_id = createTempUser($tempUserID, $this->privatize_export, $GEDCOM);	// Create a temporary userid

					// Temporarily become this user
					$_SESSION["org_user"]=$_SESSION["wt_user"];
					$_SESSION["wt_user"]=$tempUserID;
				}

				for ($i = 0; $i < $ct; $i++) {
					$clipping = $cart[$i];
					if ($clipping['gedcom'] == $GEDCOM) {
						$record = find_gedcom_record($clipping['id'], WT_GED_ID);
						$savedRecord = $record;		// Save this for the "does this file exist" check
						if ($clipping['type']=='obje') $record = convert_media_path($record, $this->conv_path, $this->conv_slashes);
						$record = privatize_gedcom($record);
						$record = remove_custom_tags($record, $remove);
						if ($convert == "yes")
						$record = utf8_decode($record);
						switch ($clipping['type']) {
						case 'indi':
							$ft = preg_match_all("/1 FAMC @(.*)@/", $record, $match, PREG_SET_ORDER);
							for ($k = 0; $k < $ft; $k++) {
								if (!id_in_cart($match[$k][1])) {
									$record = preg_replace("/1 FAMC @" . $match[$k][1] . "@.*/", "", $record);
								}
							}
							$ft = preg_match_all("/1 FAMS @(.*)@/", $record, $match, PREG_SET_ORDER);
							for ($k = 0; $k < $ft; $k++) {
								if (!id_in_cart($match[$k][1])) {
									$record = preg_replace("/1 FAMS @" . $match[$k][1] . "@.*/", "", $record);
								}
							}
							$ft = preg_match_all("/\d FILE (.*)/", $savedRecord, $match, PREG_SET_ORDER);
							for ($k = 0; $k < $ft; $k++) {
								$filename = $MEDIA_DIRECTORY.extract_filename(trim($match[$k][1]));
								if (file_exists($filename)) {
									$media[$mediacount] = array (PCLZIP_ATT_FILE_NAME => $filename);
									$mediacount++;
								}
//								$record = preg_replace("|(\d FILE )" . addslashes($match[$k][1]) . "|", "$1" . $filename, $record);
							}
							$filetext .= trim($record) . "\n";
							$filetext .= "1 SOUR @SPGV1@\n";
							$filetext .= "2 PAGE " . $dSERVER_URL . "/individual.php?pid=" . $clipping['id'] . "\n";
							$filetext .= "2 DATA\n";
							$filetext .= "3 TEXT " . i18n::translate('This Individual was downloaded from:') . "\n";
							$filetext .= "4 CONT " . $dSERVER_URL . "/individual.php?pid=" . $clipping['id'] . "\n";
							break;

						case 'fam':
							$ft = preg_match_all("/1 CHIL @(.*)@/", $record, $match, PREG_SET_ORDER);
							for ($k = 0; $k < $ft; $k++) {
								if (!id_in_cart($match[$k][1])) {
									/* if the child is not in the list delete the record of it */
									$record = preg_replace("/1 CHIL @" . $match[$k][1] . "@.*/", "", $record);
								}
							}

							$ft = preg_match_all("/1 HUSB @(.*)@/", $record, $match, PREG_SET_ORDER);
							for ($k = 0; $k < $ft; $k++) {
								if (!id_in_cart($match[$k][1])) {
									/* if the husband is not in the list delete the record of him */
									$record = preg_replace("/1 HUSB @" . $match[$k][1] . "@.*/", "", $record);
								}
							}

							$ft = preg_match_all("/1 WIFE @(.*)@/", $record, $match, PREG_SET_ORDER);
							for ($k = 0; $k < $ft; $k++) {
								if (!id_in_cart($match[$k][1])) {
									/* if the wife is not in the list delete the record of her */
									$record = preg_replace("/1 WIFE @" . $match[$k][1] . "@.*/", "", $record);
								}
							}

							$ft = preg_match_all("/\d FILE (.*)/", $savedRecord, $match, PREG_SET_ORDER);
							for ($k = 0; $k < $ft; $k++) {
								$filename = $MEDIA_DIRECTORY.extract_filename(trim($match[$k][1]));
								if (file_exists($filename)) {
									$media[$mediacount] = array (PCLZIP_ATT_FILE_NAME => $filename);
									$mediacount++;
								}
//								$record = preg_replace("|(\d FILE )" . addslashes($match[$k][1]) . "|", "$1" . $filename, $record);
							}

							$filetext .= trim($record) . "\n";
							$filetext .= "1 SOUR @SPGV1@\n";
							$filetext .= "2 PAGE " . $dSERVER_URL . $path . "family.php?famid=" . $clipping['id'] . "\n";
							$filetext .= "2 DATA\n";
							$filetext .= "3 TEXT " . i18n::translate('This Family was downloaded from:') . "\n";
							$filetext .= "4 CONT " . $dSERVER_URL . "/family.php?famid=" . $clipping['id'] . "\n";
							break;

						case 'source':
							$ft = preg_match_all("/\d FILE (.*)/", $savedRecord, $match, PREG_SET_ORDER);
							for ($k = 0; $k < $ft; $k++) {
								$filename = $MEDIA_DIRECTORY.extract_filename(trim($match[$k][1]));
								if (file_exists($filename)) {
									$media[$mediacount] = array (PCLZIP_ATT_FILE_NAME => $filename);
									$mediacount++;
								}
//								$record = preg_replace("|(\d FILE )" . addslashes($match[$k][1]) . "|", "$1" . $filename, $record);
							}
							$filetext .= trim($record) . "\n";
							$filetext .= "1 NOTE " . i18n::translate('This Source was downloaded from:') . "\n";
							$filetext .= "2 CONT " . $dSERVER_URL . "/source.php?sid=" . $clipping['id'] . "\n";
							break;

						default:
							$ft = preg_match_all("/\d FILE (.*)/", $savedRecord, $match, PREG_SET_ORDER);
							for ($k = 0; $k < $ft; $k++) {
								$filename = $MEDIA_DIRECTORY.extract_filename(trim($match[$k][1]));
								if (file_exists($filename)) {
									$media[$mediacount] = array (PCLZIP_ATT_FILE_NAME => $filename);
									$mediacount++;
								}
//								$record = preg_replace("|(\d FILE )" . addslashes($match[$k][1]) . "|", "$1" . $filename, $record);
							}
							$filetext .= trim($record) . "\n";
							break;
						}
					}
				}

				if ($this->privatize_export!='none') {
					$_SESSION["wt_user"]=$_SESSION["org_user"];
					delete_user($export_user_id);
					AddToLog("deleted dummy user -> {$tempUserID} <-", 'auth');
				}

				if($this->IncludeMedia == "yes")
				{
					$this->media_list = $media;
				}
				$filetext .= "0 @SPGV1@ SOUR\n";
				if ($user_id=get_gedcom_setting(WT_GED_ID, 'CONTACT_EMAIL')) {
					$filetext .= "1 AUTH " . getUserFullName($user_id) . "\n";
				}
				$filetext .= "1 TITL " . $HOME_SITE_TEXT . "\n";
				$filetext .= "1 ABBR " . $HOME_SITE_TEXT . "\n";
				$filetext .= "1 PUBL " . $HOME_SITE_URL . "\n";
				$filetext .= "0 TRLR\n";
				//-- make sure the preferred line endings are used
				$filetext = preg_replace("/[\r\n]+/", WT_EOL, $filetext);
				$this->download_data = $filetext;
				$this->download_clipping();
			} else
			if ($this->filetype == "gramps") {
				// Sort the clippings cart because the export works better when the cart is sorted
				usort($cart, "same_group");
				require_once WT_ROOT.'includes/classes/class_geclippings.php';
				$gramps_Exp = new GEClippings();
				$gramps_Exp->begin_xml();
				$ct = count($cart);
				usort($cart, "same_group");

				for ($i = 0; $i < $ct; $i++) {
					$clipping = $cart[$i];

					switch ($clipping['type']) {
					case 'indi':
						$rec = find_person_record($clipping['id'], WT_GED_ID);
						$rec = remove_custom_tags($rec, $remove);
						if ($this->privatize_export!='none') $rec = privatize_gedcom($rec);
						$gramps_Exp->create_person($rec, $clipping['id']);
						break;

					case 'fam':
						$rec = find_family_record($clipping['id'], WT_GED_ID);
						$rec = remove_custom_tags($rec, $remove);
						if ($this->privatize_export!='none') $rec = privatize_gedcom($rec);
						$gramps_Exp->create_family($rec, $clipping['id']);
						break;

					case 'source':
						$rec = find_source_record($clipping['id'], WT_GED_ID);
						$rec = remove_custom_tags($rec, $remove);
						if ($this->privatize_export!='none') $rec = privatize_gedcom($rec);
						$gramps_Exp->create_source($rec, $clipping['id']);
						break;
					}
				}
				$this->download_data = $gramps_Exp->dom->saveXML();
				if ($convert) $this->download_data = utf8_decode($this->download_data);
				$this->media_list = $gramps_Exp->get_all_media();
				$this->download_clipping();
			}
		}
	}
	/**
	 * Loads everything in the clippings cart into a zip file.
	 */
	function zip_cart()
	{
		global $INDEX_DIRECTORY;

		switch ($this->filetype) {
		case 'gedcom':
			$tempFileName = 'clipping'.rand().'.ged';
			break;

		case 'gramps':
			$tempFileName = 'clipping'.rand().'.gramps';
			break;
		}
		$fp = fopen($INDEX_DIRECTORY.$tempFileName, "wb");
		if($fp)
		{
			flock($fp,LOCK_EX);
			fwrite($fp,$this->download_data);
			flock($fp,LOCK_UN);
			fclose($fp);
			$zipName = "clippings".rand(0, 1500).".zip";
			$fname = $INDEX_DIRECTORY.$zipName;
			$comment = "Created by ".WT_WEBTREES." ".WT_VERSION_TEXT." on ".date("d M Y").".";
			$archive = new PclZip($fname);
			// add the ged/gramps file to the root of the zip file (strip off the index_directory)
			$this->media_list[]= array (PCLZIP_ATT_FILE_NAME => $INDEX_DIRECTORY.$tempFileName, PCLZIP_ATT_FILE_NEW_FULL_NAME => $tempFileName);
			$v_list = $archive->create($this->media_list, PCLZIP_OPT_COMMENT, $comment);
			if ($v_list == 0) print "Error : ".$archive->errorInfo(true)."</td></tr>";
			else {
				$openedFile = fopen($fname,"rb");
				$this->download_data = fread($openedFile,filesize($fname));
				fclose($openedFile);
				unlink($fname);
			}
			unlink($INDEX_DIRECTORY.$tempFileName);
		}
		else
		{
			print i18n::translate('Cannot create')." ".$INDEX_DIRECTORY."$tempFileName ".i18n::translate('Check access rights on this directory.')."<br /><br />";
		}
	}
	/**
	 * Brings up the download dialog box and allows the user to download the file
	 * based on the options he or she selected
	 */
	function download_clipping(){
		if ($this->IncludeMedia == "yes" || $this->Zip == "yes")
		{
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="clipping.zip"');
			$this->zip_cart();
		}
		else
		{
			switch ($this->filetype) {
				case 'gedcom':
					{
						header('Content-Type: text/plain');
						header('Content-Disposition: attachment; filename="clipping.ged"');
					}
					break;
				case 'gramps':
					{
						header('Content-Type: text/xml');
						header('Content-Disposition: attachment; filename="clipping.gramps"');
					}
					break;
			}
		}


		header("Content-length: ".strlen($this->download_data));
		print_r ($this->download_data);
		exit;
	}
	/**
	 * Inserts a clipping into the clipping cart
	 *
	 * @param
	 */
	function add_clipping($clipping) {
		global $cart, $SHOW_SOURCES, $MULTI_MEDIA, $GEDCOM;
		if (($clipping['id'] == false) || ($clipping['id'] == ""))
		return false;
		
		if (!id_in_cart($clipping['id'])) {
			$clipping['gedcom'] = $GEDCOM;
			if ($clipping['type'] == "indi") {
				if (displayDetailsById($clipping['id']) || showLivingNameById($clipping['id'])) {
					$cart[] = $clipping;
					$this->addCount++;
				} else {
					$this->privCount++;
					return false;
				}
			} else
			if ($clipping['type'] == "fam") {
				$parents = find_parents($clipping['id']);
				if ((displayDetailsById($parents['HUSB']) || showLivingNameById($parents['HUSB'])) && (displayDetailsById($parents['WIFE']) || showLivingNameById($parents['WIFE']))) {
					$cart[] = $clipping;
					$this->addCount++;
				} else {
					$this->privCount++;
					return false;
				}
			} else {
				if (displayDetailsById($clipping['id'], strtoupper($clipping['type'])))
				{
					$cart[] = $clipping;
					$this->addCount++;
				}
				else {
					$this->privCount++;
					return false;
				}
			}
			//-- look in the gedcom record for any linked SOUR, NOTE, or OBJE and also add them to the
			//- clippings cart
			$gedrec = find_gedcom_record($clipping['id'], WT_GED_ID);
			if ($SHOW_SOURCES >= WT_USER_ACCESS_LEVEL) {
				$st = preg_match_all("/\d SOUR @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
				for ($i = 0; $i < $st; $i++) {
					// add SOUR
					$clipping = array ();
					$clipping['type'] = "source";
					$clipping['id'] = $match[$i][1];
					$clipping['gedcom'] = $GEDCOM;
					$this->add_clipping($clipping);
					// add REPO
					$sourec = find_gedcom_record($match[$i][1], WT_GED_ID);
					$rt = preg_match_all("/\d REPO @(.*)@/", $sourec, $rmatch, PREG_SET_ORDER);
					for ($j = 0; $j < $rt; $j++) {
						$clipping = array ();
						$clipping['type'] = "repository";
						$clipping['id'] = $rmatch[$j][1];
						$clipping['gedcom'] = $GEDCOM;
						$this->add_clipping($clipping);
					}
				}
			}
			$nt = preg_match_all("/\d NOTE @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
			for ($i = 0; $i < $nt; $i++) {
				$clipping = array ();
				$clipping['type'] = "note";
				$clipping['id'] = $match[$i][1];
				$clipping['gedcom'] = $GEDCOM;
				$this->add_clipping($clipping);
			}
			if ($MULTI_MEDIA) {
				$nt = preg_match_all("/\d OBJE @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
				for ($i = 0; $i < $nt; $i++) {
					$clipping = array ();
					$clipping['type'] = "obje";
					$clipping['id'] = $match[$i][1];
					$clipping['gedcom'] = $GEDCOM;
					$this->add_clipping($clipping);
				}
			}
		}
		return true;
	}

	// --------------------------------- Recursive function to traverse the tree
	function add_family_descendancy($famid, $level="") {
		global $cart;

		if (!$famid)
			return;
		$famrec = find_family_record($famid, WT_GED_ID);
		if ($famrec) {
			$parents = find_parents_in_record($famrec);
			if (!empty ($parents["HUSB"])) {
				$clipping = array ();
				$clipping['type'] = "indi";
				$clipping['id'] = $parents["HUSB"];
				$this->add_clipping($clipping);
			}
			if (!empty ($parents["WIFE"])) {
				$clipping = array ();
				$clipping['type'] = "indi";
				$clipping['id'] = $parents["WIFE"];
				$this->add_clipping($clipping);
			}
			$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch, PREG_SET_ORDER);
			for ($i = 0; $i < $num; $i++) {
				$cfamids = find_sfamily_ids($smatch[$i][1]);
				if (count($cfamids) > 0) {
					foreach ($cfamids as $indexval => $cfamid) {
						if (!id_in_cart($cfamid)) {
							$clipping = array ();
							$clipping['type'] = "fam";
							$clipping['id'] = $cfamid;
							$ret = $this->add_clipping($clipping); // add the childs family
							if ($level=="" || $level>0) {
								if ($level!="") $level--;
								$this->add_family_descendancy($cfamid, $level); // recurse on the childs family
							}
						}
					}
				} else {
					$clipping = array ();
					$clipping['type'] = "indi";
					$clipping['id'] = $smatch[$i][1];
					$this->add_clipping($clipping);
				}
			}
		}
	}

	function add_family_members($famid) {
		global $cart;
		$parents = find_parents($famid);
		if (!empty ($parents["HUSB"])) {
			$clipping = array ();
			$clipping['type'] = "indi";
			$clipping['id'] = $parents["HUSB"];
			$this->add_clipping($clipping);
		}
		if (!empty ($parents["WIFE"])) {
			$clipping = array ();
			$clipping['type'] = "indi";
			$clipping['id'] = $parents["WIFE"];
			$this->add_clipping($clipping);
		}
		$famrec = find_family_record($famid, WT_GED_ID);
		if ($famrec) {
			$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch, PREG_SET_ORDER);
			for ($i = 0; $i < $num; $i++) {
				$clipping = array ();
				$clipping['type'] = "indi";
				$clipping['id'] = $smatch[$i][1];
				$this->add_clipping($clipping);
			}
		}
	}

	//-- recursively adds direct-line ancestors to cart
	function add_ancestors_to_cart($pid, $level="") {
		global $cart;
		$famids = find_family_ids($pid);
		if (count($famids) > 0) {
			foreach ($famids as $indexval => $famid) {
				if ($level=="" || $level > 0) {
					if ($level!="") $level = $level -1;
					$clipping = array ();
					$clipping['type'] = "fam";
					$clipping['id'] = $famid;
					$ret = $this->add_clipping($clipping);
					if ($ret) {
						$parents = find_parents($famid);
						if (!empty ($parents["HUSB"])) {
							$clipping = array ();
							$clipping['type'] = "indi";
							$clipping['id'] = $parents["HUSB"];
							$this->add_clipping($clipping);
							$this->add_ancestors_to_cart($parents["HUSB"], $level);
						}
						if (!empty ($parents["WIFE"])) {
							$clipping = array ();
							$clipping['type'] = "indi";
							$clipping['id'] = $parents["WIFE"];
							$this->add_clipping($clipping);
							$this->add_ancestors_to_cart($parents["WIFE"], $level);
						}
					}
				}
			}
		}
	}

	//-- recursively adds direct-line ancestors and their families to the cart
	function add_ancestors_to_cart_families($pid, $level="") {
		global $cart;
		$famids = find_family_ids($pid);
		if (count($famids) > 0) {
			foreach ($famids as $indexval => $famid) {
				if ($level=="" || $level > 0) {
					if ($level!="")$level = $level -1;
					$clipping = array ();
					$clipping['type'] = "fam";
					$clipping['id'] = $famid;
					$ret = $this->add_clipping($clipping);
					if ($ret) {
						$parents = find_parents($famid);
						if (!empty ($parents["HUSB"])) {
							$clipping = array ();
							$clipping['type'] = "indi";
							$clipping['id'] = $parents["HUSB"];
							$ret = $this->add_clipping($clipping);
							$this->add_ancestors_to_cart_families($parents["HUSB"], $level);
						}
						if (!empty ($parents["WIFE"])) {
							$clipping = array ();
							$clipping['type'] = "indi";
							$clipping['id'] = $parents["WIFE"];
							$ret = $this->add_clipping($clipping);
							$this->add_ancestors_to_cart_families($parents["WIFE"], $level);
						}
						$famrec = find_family_record($famid, WT_GED_ID);
						if ($famrec) {
							$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch, PREG_SET_ORDER);
							for ($i = 0; $i < $num; $i++) {
								$clipping = array ();
								$clipping['type'] = "indi";
								$clipping['id'] = $smatch[$i][1];
								$this->add_clipping($clipping);
							}
						}
					}
				}
			}
		}
	}
}
// -- end of class

//-- load a user extended class if one exists
if (file_exists(WT_ROOT.'includes/controllers/clippings_ctrl_user.php')) {
	require_once WT_ROOT.'includes/controllers/clippings_ctrl_user.php';
} else {
	class ClippingsController extends ClippingsControllerRoot {
	}
}

?>
