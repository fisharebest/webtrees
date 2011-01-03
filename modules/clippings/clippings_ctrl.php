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
* @version $Id$
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLIPPINGS_CTRL', '');

require_once WT_ROOT.'includes/functions/functions.php';
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

/**
* Main controller class for the Clippings page.
*/
class WT_Controller_Clippings extends WT_Controller_Base {

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
	var $level1;  // number of levels of ancestors
	var $level2;
	var $level3; // number of levels of descendents

	/**
	 * @param string $thing the id of the person
	 */
	function __construct() {
		parent::__construct();
	}
	//----------------beginning of function definitions for WT_Controller_Clippings
	function init() {
		global $SCRIPT_NAME, $MEDIA_DIRECTORY, $MEDIA_FIREWALL_ROOTDIR, $GEDCOM, $cart;

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
		$this->privatize_export = safe_GET('privatize_export', array('none', 'visitor', 'user', 'gedadmin'));
		$this->level1 = safe_GET('level1', WT_REGEX_INTEGER, PHP_INT_MAX);
		$this->level2 = safe_GET('level2', WT_REGEX_INTEGER, PHP_INT_MAX);
		$this->level3 = safe_GET('level3', WT_REGEX_INTEGER, PHP_INT_MAX);
		$others = safe_GET('others');
		$item = safe_GET('item');
		if (!isset($cart)) $cart = $_SESSION['cart'];
		$this->type = safe_GET('type');

		$this->conv_path = stripLRMRLM($this->conv_path);
		$_SESSION['exportConvPath'] = $this->conv_path; // remember this for the next Download
		$_SESSION['exportConvSlashes'] = $this->conv_slashes;

		if ($this->action == 'add') {
			if (empty($this->type) && !empty($this->id)) {
				$this->type="";
				$obj = WT_GedcomRecord::getInstance($this->id);
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
			$this->add_clipping(WT_GedcomRecord::getInstance($this->id));
			if ($this->type == 'sour') {
				if ($others == 'linked') {
					foreach (fetch_linked_indi($this->id, 'SOUR', WT_GED_ID) as $indi) {
						$this->add_clipping($indi);
					}
					foreach (fetch_linked_fam($this->id, 'SOUR', WT_GED_ID) as $fam) {
						$this->add_clipping($fam);
					}
				}
			}
			if ($this->type == 'fam') {
				if ($others == 'parents') {
					$this->add_clipping($obj->getHusband());
					$this->add_clipping($obj->getWife());
				} else
				if ($others == "members") {
					$this->add_family_members(WT_Family::getInstance($this->id));
				} else
				if ($others == "descendants") {
					$this->add_family_descendancy(WT_Family::getInstance($this->id));
				}
			} else
			if ($this->type == 'indi') {
				if ($others == 'parents') {
					foreach (WT_Person::getInstance($this->id)->getChildFamilies() as $family) {
						$this->add_family_members($family);
					}
				} else
				if ($others == 'ancestors') {
					$this->add_ancestors_to_cart(WT_Person::getInstance($this->id), $this->level1);
				} else
				if ($others == 'ancestorsfamilies') {
					$this->add_ancestors_to_cart_families(WT_Person::getInstance($this->id), $this->level2);
				} else
				if ($others == 'members') {
					foreach (WT_Person::getInstance($this->id)->getSpouseFamilies() as $family) {
						$this->add_family_members($family);
					}
				} else
				if ($others == 'descendants') {
					foreach (WT_Person::getInstance($this->id)->getSpouseFamilies() as $family) {
						$this->add_clipping($family);
						$this->add_family_descendancy($family, $this->level3);
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
				$export_user_id = createTempUser($tempUserID, $this->privatize_export, $GEDCOM); // Create a temporary userid

				// Temporarily become this user
				$_SESSION["org_user"]=$_SESSION["wt_user"];
				$_SESSION["wt_user"]=$export_user_id;
			}

			for ($i = 0; $i < $ct; $i++) {
				$clipping = $cart[$i];
				if ($clipping['gedcom'] == $GEDCOM) {
					$record = find_gedcom_record($clipping['id'], WT_GED_ID);
					$savedRecord = $record; // Save this for the "does this file exist" check
					if ($clipping['type']=='obje') $record = convert_media_path($record, $this->conv_path, $this->conv_slashes);
					$record = privatize_gedcom($record);
					$record = remove_custom_tags($record, $remove);
					if ($convert == "yes")
					$record = utf8_decode($record);
					switch ($clipping['type']) {
					case 'indi':
						$ft = preg_match_all("/1 FAMC @(.*)@/", $record, $match, PREG_SET_ORDER);
						for ($k = 0; $k < $ft; $k++) {
							if (!self::id_in_cart($match[$k][1])) {
								$record = preg_replace("/1 FAMC @" . $match[$k][1] . "@.*/", "", $record);
							}
						}
						$ft = preg_match_all("/1 FAMS @(.*)@/", $record, $match, PREG_SET_ORDER);
						for ($k = 0; $k < $ft; $k++) {
							if (!self::id_in_cart($match[$k][1])) {
								$record = preg_replace("/1 FAMS @" . $match[$k][1] . "@.*/", "", $record);
							}
						}
						$filetext .= trim($record) . "\n";
						$filetext .= "1 SOUR @WEBTREES@\n";
						$filetext .= "2 PAGE ".WT_SERVER_NAME.WT_SCRIPT_PATH."individual.php?pid={$clipping['id']}&ged=" . rawurlencode($clipping['gedcom']) . "\n";
						break;

					case 'fam':
						$ft = preg_match_all("/1 CHIL @(.*)@/", $record, $match, PREG_SET_ORDER);
						for ($k = 0; $k < $ft; $k++) {
							if (!self::id_in_cart($match[$k][1])) {
								/* if the child is not in the list delete the record of it */
								$record = preg_replace("/1 CHIL @" . $match[$k][1] . "@.*/", "", $record);
							}
						}

						$ft = preg_match_all("/1 HUSB @(.*)@/", $record, $match, PREG_SET_ORDER);
						for ($k = 0; $k < $ft; $k++) {
							if (!self::id_in_cart($match[$k][1])) {
								/* if the husband is not in the list delete the record of him */
								$record = preg_replace("/1 HUSB @" . $match[$k][1] . "@.*/", "", $record);
							}
						}

						$ft = preg_match_all("/1 WIFE @(.*)@/", $record, $match, PREG_SET_ORDER);
						for ($k = 0; $k < $ft; $k++) {
							if (!self::id_in_cart($match[$k][1])) {
								/* if the wife is not in the list delete the record of her */
								$record = preg_replace("/1 WIFE @" . $match[$k][1] . "@.*/", "", $record);
							}
						}

						$filetext .= trim($record) . "\n";
						$filetext .= "1 SOUR @WEBTREES@\n";
						$filetext .= "2 PAGE " . WT_SERVER_NAME.WT_SCRIPT_PATH . "family.php?famid={$clipping['id']}&ged=" . rawurlencode($clipping['gedcom']) . "\n";
						break;

					case 'source':
						$filetext .= trim($record) . "\n";
						$filetext .= "1 NOTE " . WT_SERVER_NAME.WT_SCRIPT_PATH . "source.php?sid={$clipping['id']}&ged=" . rawurlencode($clipping['gedcom']) . "\n";
						break;

					default:
						$ft = preg_match_all("/\n\d FILE (.+)/", $savedRecord, $match, PREG_SET_ORDER);
						for ($k = 0; $k < $ft; $k++) {
							$filename = $MEDIA_DIRECTORY.extract_filename($match[$k][1]);
							if (file_exists($filename)) {
								$media[$mediacount] = array (PCLZIP_ATT_FILE_NAME => $filename);
								$mediacount++;
							} else {
								$filename = $MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY.extract_filename($match[$k][1]);
								if (file_exists($filename)) {
									// Don't include firewall directory in zipfile.  It may start ../
									$media[$mediacount] = array (
										PCLZIP_ATT_FILE_NAME => $filename,
										PCLZIP_ATT_FILE_NEW_FULL_NAME => $MEDIA_DIRECTORY.extract_filename($match[$k][1])
									);
									$mediacount++;
								}
							}
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

			if ($this->IncludeMedia == "yes")
			{
				$this->media_list = $media;
			}
			$filetext .= "0 @WEBTREES@ SOUR\n1 TITL ".WT_SERVER_NAME.WT_SCRIPT_PATH."\n";
			if ($user_id=get_gedcom_setting(WT_GED_ID, 'CONTACT_EMAIL')) {
				$filetext .= "1 AUTH " . getUserFullName($user_id) . "\n";
			}
			$filetext .= "0 TRLR\n";
			//-- make sure the preferred line endings are used
			$filetext = preg_replace("/[\r\n]+/", WT_EOL, $filetext);
			$this->download_data = $filetext;
			$this->download_clipping();
		}
	}

	public static function id_in_cart($id) {
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
	 * Loads everything in the clippings cart into a zip file.
	 */
	function zip_cart()
	{
		$INDEX_DIRECTORY=get_site_setting('INDEX_DIRECTORY');

		$tempFileName = 'clipping'.rand().'.ged';
		$fp = fopen($INDEX_DIRECTORY.$tempFileName, "wb");
		if ($fp)
		{
			flock($fp,LOCK_EX);
			fwrite($fp,$this->download_data);
			flock($fp,LOCK_UN);
			fclose($fp);
			$zipName = "clippings".rand(0, 1500).".zip";
			$fname = $INDEX_DIRECTORY.$zipName;
			$comment = "Created by ".WT_WEBTREES." ".WT_VERSION_TEXT." on ".date("d M Y").".";
			$archive = new PclZip($fname);
			// add the ged file to the root of the zip file (strip off the index_directory)
			$this->media_list[]= array (PCLZIP_ATT_FILE_NAME => $INDEX_DIRECTORY.$tempFileName, PCLZIP_ATT_FILE_NEW_FULL_NAME => $tempFileName);
			$v_list = $archive->create($this->media_list, PCLZIP_OPT_COMMENT, $comment);
			if ($v_list == 0) echo "Error : ".$archive->errorInfo(true)."</td></tr>";
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
			echo WT_I18N::translate('Cannot create')." ".$INDEX_DIRECTORY."$tempFileName ".WT_I18N::translate('Check access rights on this directory.')."<br /><br />";
		}
	}
	/**
	 * Brings up the download dialog box and allows the user to download the file
	 * based on the options he or she selected
	 */
	function download_clipping() {
		if ($this->IncludeMedia == "yes" || $this->Zip == "yes") {
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="clipping.zip"');
			$this->zip_cart();
		} else {
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename="clipping.ged"');
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
		global $cart, $MULTI_MEDIA, $GEDCOM;

		if (!$clipping || !$clipping->canDisplayName()) {
			return;
		}

		$clipping=array(
			'type'  =>strtolower($clipping->getType()),
			'id'    =>$clipping->getXref(),
			'gedcom'=>get_id_from_gedcom($clipping->getGedId())
		);

		if (!self::id_in_cart($clipping['id'])) {
			$clipping['gedcom'] = $GEDCOM;
			$ged_id=get_id_from_gedcom($GEDCOM);
			$gedrec=find_gedcom_record($clipping['id'], $ged_id);
			if (canDisplayRecord($ged_id, $gedrec) || showLivingNameById($clipping['id'])) {
				$cart[] = $clipping;
				$this->addCount++;
			} else {
				$this->privCount++;
				return false;
			}
			//-- look in the gedcom record for any linked SOUR, NOTE, or OBJE and also add them to the
			//- clippings cart
			$gedrec = find_gedcom_record($clipping['id'], WT_GED_ID);
			$st = preg_match_all("/\d SOUR @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
			for ($i = 0; $i < $st; $i++) {
				// add SOUR
				$this->add_clipping(WT_Source::getInstance($match[$i][1]));
				// add REPO
				$sourec = find_gedcom_record($match[$i][1], WT_GED_ID);
				$rt = preg_match_all("/\d REPO @(.*)@/", $sourec, $rmatch, PREG_SET_ORDER);
				for ($j = 0; $j < $rt; $j++) {
					$this->add_clipping(WT_Repository::getInstance($rmatch[$j][1]));
				}
			}
			$nt = preg_match_all("/\d NOTE @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
			for ($i = 0; $i < $nt; $i++) {
				$this->add_clipping(WT_Note::getInstance($match[$i][1]));
			}
			if ($MULTI_MEDIA) {
				$nt = preg_match_all("/\d OBJE @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
				for ($i = 0; $i < $nt; $i++) {
					$this->add_clipping(WT_Media::getInstance($match[$i][1]));
				}
			}
		}
		return true;
	}

	// --------------------------------- Recursive function to traverse the tree
	function add_family_descendancy($family, $level) {
		if (!$family) {
			return;
		}
		$this->add_clipping($family->getHusband());
		$this->add_clipping($family->getWife());
		foreach ($family->getChildren() as $child) {
			$this->add_clipping($child);
			foreach ($child->getSpouseFamilies() as $child_family) {
				$this->add_clipping($child_family);
				if ($level>0) {
					$this->add_family_descendancy($child_family, $level-1); // recurse on the childs family
				}
			}
		}
	}

	// Add a family, and all its members
	function add_family_members($family) {
		if (!$family) {
			return;
		}
		$this->add_clipping($family);
		$this->add_clipping($family->getHusband());
		$this->add_clipping($family->getWife());
		foreach ($family->getChildren() as $child) {
			$this->add_clipping($child);
		}
	}

	//-- recursively adds direct-line ancestors to cart
	function add_ancestors_to_cart($person, $level) {
		if (!$person) {
			return;
		}
		$this->add_clipping($person);
		if ($level>0) {
			foreach ($person->getChildFamilies() as $family) {
				$this->add_clipping($family);
				$this->add_ancestors_to_cart($family->getHusband(), $level-1);
				$this->add_ancestors_to_cart($family->getWife(), $level-1);
			}
		}
	}

	//-- recursively adds direct-line ancestors and their families to the cart
	function add_ancestors_to_cart_families($person, $level) {
		if (!$person) {
			return;
		}
		if ($level>0) {
			foreach ($person->getChildFamilies() as $family) {
				$this->add_family_members($family);
				$this->add_ancestors_to_cart_families($family->getHusband(), $level-1);
				$this->add_ancestors_to_cart_families($family->getWife(), $level-1);
			}
		}
	}
}
