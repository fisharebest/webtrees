<?php
// Controller for the Clippings Page
//
// NOTE THAT THIS IS NOT A PAGE CONTROLLER, AND DOES NOT EXTEND WT_CONTROLLER_BASE
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\User;

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/functions/functions_export.php';
require_once WT_ROOT.'library/pclzip.lib.php';

/**
 * Main controller class for the Clippings page.
 */
class WT_Controller_Clippings {

	var $download_data;
	var $media_list = array();
	var $addCount = 0;
	var $privCount = 0;
	var $type="";
	var $id="";
	var $IncludeMedia;
	var $conv_path;
	var $privatize_export;
	var $Zip;
	var $level1;  // number of levels of ancestors
	var $level2;
	var $level3; // number of levels of descendents

	public function __construct() {
		global $WT_TREE, $SCRIPT_NAME, $MEDIA_DIRECTORY, $WT_SESSION;

		// Our cart is an array of items in the session
		if (!is_array($WT_SESSION->cart)) {
			$WT_SESSION->cart=array();
		}
		if (!array_key_exists(WT_GED_ID, $WT_SESSION->cart)) {
			$WT_SESSION->cart[WT_GED_ID]=array();
		}

		$this->action           = WT_Filter::get('action');
		$this->id               = WT_Filter::get('id');
		$convert                = WT_Filter::get('convert', 'yes|no', 'no');
		$this->Zip              = WT_Filter::get('Zip');
		$this->IncludeMedia     = WT_Filter::get('IncludeMedia');
		$this->conv_path        = WT_Filter::get('conv_path');
		$this->privatize_export = WT_Filter::get('privatize_export', 'none|visitor|user|gedadmin', 'visitor');
		$this->level1           = WT_Filter::getInteger('level1');
		$this->level2           = WT_Filter::getInteger('level2');
		$this->level3           = WT_Filter::getInteger('level3');
		$others                 = WT_Filter::get('others');
		$this->type             = WT_Filter::get('type');

		if (($this->privatize_export=='none' || $this->privatize_export=='none') && !WT_USER_GEDCOM_ADMIN) {
			$this->privatize_export='visitor';
		}
		if ($this->privatize_export=='user' && !WT_USER_CAN_ACCESS) {
			$this->privatize_export='visitor';
		}

		if ($this->action == 'add') {
			if (empty($this->type) && !empty($this->id)) {
				$this->type="";
				$obj = WT_GedcomRecord::getInstance($this->id);
				if (is_null($obj)) {
					$this->id="";
					$this->action="";
				}
				else $this->type = strtolower($obj::RECORD_TYPE);
			}
			else if (empty($this->id)) $this->action="";
			if (!empty($this->id) && $this->type != 'fam' && $this->type != 'indi' && $this->type != 'sour')
			$this->action = 'add1';
		}

		if ($this->action == 'add1') {
			$obj = WT_GedcomRecord::getInstance($this->id);
			$this->addClipping($obj);
			if ($this->type == 'sour') {
				if ($others == 'linked') {
					foreach ($obj->linkedIndividuals('SOUR') as $indi) {
						$this->addClipping($indi);
					}
					foreach ($obj->linkedFamilies('SOUR') as $fam) {
						$this->addClipping($fam);
					}
				}
			}
			if ($this->type == 'fam') {
				if ($others == 'parents') {
					$this->addClipping($obj->getHusband());
					$this->addClipping($obj->getWife());
				} elseif ($others == "members") {
					$this->addFamilyMembers(WT_Family::getInstance($this->id));
				} elseif ($others == "descendants") {
					$this->addFamilyDescendancy(WT_Family::getInstance($this->id));
				}
			} elseif ($this->type == 'indi') {
				if ($others == 'parents') {
					foreach (WT_Individual::getInstance($this->id)->getChildFamilies() as $family) {
						$this->addFamilyMembers($family);
					}
				} elseif ($others == 'ancestors') {
					$this->addAncestorsToCart(WT_Individual::getInstance($this->id), $this->level1);
				} elseif ($others == 'ancestorsfamilies') {
					$this->addAncestorsToCartFamilies(WT_Individual::getInstance($this->id), $this->level2);
				} elseif ($others == 'members') {
					foreach (WT_Individual::getInstance($this->id)->getSpouseFamilies() as $family) {
						$this->addFamilyMembers($family);
					}
				} elseif ($others == 'descendants') {
					foreach (WT_Individual::getInstance($this->id)->getSpouseFamilies() as $family) {
						$this->addClipping($family);
						$this->addFamilyDescendancy($family, $this->level3);
					}
				}
				uksort($WT_SESSION->cart[WT_GED_ID], array('WT_Controller_Clippings', 'compareClippings'));
			}
		} elseif ($this->action == 'remove') {
			unset ($WT_SESSION->cart[WT_GED_ID][$this->id]);
		} elseif ($this->action == 'empty') {
			$WT_SESSION->cart[WT_GED_ID]=array();
		} elseif ($this->action == 'download') {
			$media = array ();
			$mediacount = 0;
			$filetext = gedcom_header(WT_GEDCOM);
			// Include SUBM/SUBN records, if they exist
			$subn=
				WT_DB::prepare("SELECT o_gedcom FROM `##other` WHERE o_type=? AND o_file=?")
				->execute(array('SUBN', WT_GED_ID))
				->fetchOne();
			if ($subn) {
				$filetext .= $subn."\n";
			}
			$subm=
				WT_DB::prepare("SELECT o_gedcom FROM `##other` WHERE o_type=? AND o_file=?")
				->execute(array('SUBM', WT_GED_ID))
				->fetchOne();
			if ($subm) {
				$filetext .= $subm."\n";
			}
			if ($convert == "yes") {
				$filetext = str_replace("UTF-8", "ANSI", $filetext);
				$filetext = utf8_decode($filetext);
			}

			switch($this->privatize_export) {
			case 'gedadmin':
				$access_level=WT_PRIV_NONE;
				break;
			case 'user':
				$access_level=WT_PRIV_USER;
				break;
			case 'visitor':
				$access_level=WT_PRIV_PUBLIC;
				break;
			case 'none':
				$access_level=WT_PRIV_HIDE;
				break;
			}

			foreach (array_keys($WT_SESSION->cart[WT_GED_ID]) as $xref) {
				$object=WT_GedcomRecord::getInstance($xref);
				if ($object) { // The object may have been deleted since we added it to the cart....
					$record = $object->privatizeGedcom($access_level);
					// Remove links to objects that aren't in the cart
					preg_match_all('/\n1 '.WT_REGEX_TAG.' @('.WT_REGEX_XREF.')@(\n[2-9].*)*/', $record, $matches, PREG_SET_ORDER);
					foreach ($matches as $match) {
						if (!array_key_exists($match[1], $WT_SESSION->cart[WT_GED_ID])) {
							$record=str_replace($match[0], '', $record);
						}
					}
					preg_match_all('/\n2 '.WT_REGEX_TAG.' @('.WT_REGEX_XREF.')@(\n[3-9].*)*/', $record, $matches, PREG_SET_ORDER);
					foreach ($matches as $match) {
						if (!array_key_exists($match[1], $WT_SESSION->cart[WT_GED_ID])) {
							$record=str_replace($match[0], '', $record);
						}
					}
					preg_match_all('/\n3 '.WT_REGEX_TAG.' @('.WT_REGEX_XREF.')@(\n[4-9].*)*/', $record, $matches, PREG_SET_ORDER);
					foreach ($matches as $match) {
						if (!array_key_exists($match[1], $WT_SESSION->cart[WT_GED_ID])) {
							$record=str_replace($match[0], '', $record);
						}
					}
					$record = convert_media_path($record, $this->conv_path);
					$savedRecord = $record; // Save this for the "does this file exist" check
					if ($convert=='yes') {
						$record=utf8_decode($record);
					}
					switch ($object::RECORD_TYPE) {
					case 'INDI':
						$filetext .= $record."\n";
						$filetext .= "1 SOUR @WEBTREES@\n";
						$filetext .= "2 PAGE ".WT_SERVER_NAME.WT_SCRIPT_PATH.$object->getRawUrl()."\n";
						break;
					case 'FAM':
						$filetext .= $record."\n";
						$filetext .= "1 SOUR @WEBTREES@\n";
						$filetext .= "2 PAGE ".WT_SERVER_NAME.WT_SCRIPT_PATH.$object->getRawUrl()."\n";
						break;
					case 'SOUR':
						$filetext .= $record."\n";
						$filetext .= "1 NOTE ".WT_SERVER_NAME.WT_SCRIPT_PATH.$object->getRawUrl()."\n";
						break;
					default:
						$ft = preg_match_all("/\n\d FILE (.+)/", $savedRecord, $match, PREG_SET_ORDER);
						for ($k = 0; $k < $ft; $k++) {
							// Skip external files and non-existant files
							if (file_exists(WT_DATA_DIR . $MEDIA_DIRECTORY . $match[$k][1])) {
								$media[$mediacount] = array (
									PCLZIP_ATT_FILE_NAME          => WT_DATA_DIR . $MEDIA_DIRECTORY . $match[$k][1],
									PCLZIP_ATT_FILE_NEW_FULL_NAME =>                                  $match[$k][1],
								);
								$mediacount++;
							}
						}
						$filetext .= trim($record) . "\n";
						break;
					}
				}
			}

			if ($this->IncludeMedia == "yes") {
				$this->media_list = $media;
			}
			$filetext .= "0 @WEBTREES@ SOUR\n1 TITL ".WT_SERVER_NAME.WT_SCRIPT_PATH."\n";
			if ($user_id = $WT_TREE->getPreference('CONTACT_EMAIL')) {
				$user = User::find($user_id);
				$filetext .= "1 AUTH " . $user->getRealName() . "\n";
			}
			$filetext .= "0 TRLR\n";
			//-- make sure the preferred line endings are used
			$filetext = preg_replace("/[\r\n]+/", WT_EOL, $filetext);
			$this->download_data = $filetext;
			$this->downloadClipping();
		}
	}

	// Loads everything in the clippings cart into a zip file.
	function zipCart() {
		$tempFileName = 'clipping'.rand().'.ged';
		$fp = fopen(WT_DATA_DIR.$tempFileName, "wb");
		if ($fp) {
			flock($fp,LOCK_EX);
			fwrite($fp,$this->download_data);
			flock($fp,LOCK_UN);
			fclose($fp);
			$zipName = "clippings".rand(0, 1500).".zip";
			$fname = WT_DATA_DIR.$zipName;
			$comment = "Created by ".WT_WEBTREES." ".WT_VERSION." on ".date("d M Y").".";
			$archive = new PclZip($fname);
			// add the ged file to the root of the zip file (strip off the data folder)
			$this->media_list[]= array (PCLZIP_ATT_FILE_NAME => WT_DATA_DIR.$tempFileName, PCLZIP_ATT_FILE_NEW_FULL_NAME => $tempFileName);
			$v_list = $archive->create($this->media_list, PCLZIP_OPT_COMMENT, $comment);
			if ($v_list == 0) {
				echo "Error : ".$archive->errorInfo(true)."</td></tr>";
			} else {
				$openedFile = fopen($fname,"rb");
				$this->download_data = fread($openedFile,filesize($fname));
				fclose($openedFile);
				unlink($fname);
			}
			unlink(WT_DATA_DIR.$tempFileName);
		} else {
			echo WT_I18N::translate('Cannot create')." ".WT_DATA_DIR."$tempFileName ".WT_I18N::translate('Check access rights on this directory.')."<br><br>";
		}
	}

	// Brings up the download dialog box and allows the user to download the file
	// based on the options he or she selected
	function downloadClipping() {
		Zend_Session::writeClose();

		if ($this->IncludeMedia == 'yes' || $this->Zip == 'yes') {
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="clipping.zip"');
			$this->zipCart();
		} else {
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename="clipping.ged"');
		}

		header('Content-length: ' . strlen($this->download_data));
		echo $this->download_data;
		exit;
	}

	// Inserts a clipping into the clipping cart
	function addClipping(WT_GedcomRecord $record) {
		global $WT_SESSION;

		if ($record->canShowName()) {
			$WT_SESSION->cart[WT_GED_ID][$record->getXref()]=true;
			// Add directly linked records
			preg_match_all('/\n\d (?:OBJE|NOTE|SOUR|REPO) @('.WT_REGEX_XREF.')@/', $record->getGedcom(), $matches);
			foreach ($matches[1] as $match) {
				$WT_SESSION->cart[WT_GED_ID][$match]=true;
			}
		}
	}

	// Recursive function to traverse the tree
	function addFamilyDescendancy(WT_Family $family = null, $level = PHP_INT_MAX) {
		if (!$family) {
			return;
		}
		foreach ($family->getSpouses() as $spouse) {
			$this->addClipping($spouse);
		}
		foreach ($family->getChildren() as $child) {
			$this->addClipping($child);
			foreach ($child->getSpouseFamilies() as $child_family) {
				$this->addClipping($child_family);
				if ($level>0) {
					$this->addFamilyDescendancy($child_family, $level-1); // recurse on the childs family
				}
			}
		}
	}

	// Add a family, and all its members
	function addFamilyMembers(WT_Family $family = null) {
		if (!$family) {
			return;
		}
		$this->addClipping($family);
		foreach ($family->getSpouses() as $spouse) {
			$this->addClipping($spouse);
		}
		foreach ($family->getChildren() as $child) {
			$this->addClipping($child);
		}
	}

	// Recursively add direct-line ancestors to cart
	function addAncestorsToCart(WT_Individual $person = null, $level = null) {
		if (!$person) {
			return;
		}
		$this->addClipping($person);
		if ($level>0) {
			foreach ($person->getChildFamilies() as $family) {
				$this->addClipping($family);
				$this->addAncestorsToCart($family->getHusband(), $level-1);
				$this->addAncestorsToCart($family->getWife(), $level-1);
			}
		}
	}

	// Recursively adds direct-line ancestors and their families to the cart
	function addAncestorsToCartFamilies(WT_Individual $person = null, $level = null) {
		if (!$person) {
			return;
		}
		if ($level>0) {
			foreach ($person->getChildFamilies() as $family) {
				$this->addFamilyMembers($family);
				$this->addAncestorsToCartFamilies($family->getHusband(), $level-1);
				$this->addAncestorsToCartFamilies($family->getWife(), $level-1);
			}
		}
	}

	// Helper function to sort records by type/name
	static function compareClippings($a, $b) {
		$a=WT_GedcomRecord::getInstance($a);
		$b=WT_GedcomRecord::getInstance($b);
		if ($a && $b) {
			switch ($a::RECORD_TYPE) {
			case 'INDI': $t1=1; break;
			case 'FAM':  $t1=2; break;
			case 'SOUR': $t1=3; break;
			case 'REPO': $t1=4; break;
			case 'OBJE': $t1=5; break;
			case 'NOTE': $t1=6; break;
			default:     $t1=7; break;
			}
			switch ($b::RECORD_TYPE) {
			case 'INDI': $t2=1; break;
			case 'FAM':  $t2=2; break;
			case 'SOUR': $t2=3; break;
			case 'REPO': $t2=4; break;
			case 'OBJE': $t2=5; break;
			case 'NOTE': $t2=6; break;
			default:     $t2=7; break;
			}
			if ($t1!=$t2) {
				return $t1-$t2;
			} else {
				return WT_GedcomRecord::compare($a, $b);
			}
		} else {
			return 0;
		}
	}
}
