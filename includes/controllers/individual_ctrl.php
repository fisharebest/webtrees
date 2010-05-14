<?php
/**
* Controller for the Individual Page
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2010 PGV Development Team. All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*
* @package webtrees
* @subpackage Charts
* @version $Id$
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_INDIVIDUAL_CTRL_PHP', '');

require_once WT_ROOT.'includes/functions/functions_print_facts.php';
require_once WT_ROOT.'includes/controllers/basecontrol.php';
require_once WT_ROOT.'includes/classes/class_menu.php';
require_once WT_ROOT.'includes/classes/class_person.php';
require_once WT_ROOT.'includes/classes/class_family.php';
require_once WT_ROOT.'includes/functions/functions_import.php';
require_once WT_ROOT.'includes/classes/class_module.php';

// -- array of GEDCOM elements that will be found but should not be displayed
$nonfacts[] = "FAMS";
$nonfacts[] = "FAMC";
$nonfacts[] = "MAY";
$nonfacts[] = "BLOB";
$nonfacts[] = "CHIL";
$nonfacts[] = "HUSB";
$nonfacts[] = "WIFE";
$nonfacts[] = "RFN";
$nonfacts[] = "_WT_OBJE_SORT";
$nonfacts[] = "_WT_OBJE_SORT";
$nonfacts[] = "";

//$nonfamfacts[] = "NCHI"; // Turning back on NCHI display for the indi page.
$nonfamfacts[] = "UID";
$nonfamfacts[] = "";

// SET Family Navigator for each Tab as necessary  - SHOW/HIDE ===============
$NAV_FACTS	 = "SHOW";		// Facts and Details Tab Navigator
$NAV_NOTES	 = "SHOW";		// Notes Tab Navigator
$NAV_SOURCES = "SHOW";		// Sources Tab Navigator
$NAV_MEDIA	 = "SHOW";		// Media Tab Navigator
$NAV_ALBUM	 = "SHOW";		// Album Tab Navigator
// ========================================================================

/**
* Main controller class for the individual page.
*/
class IndividualControllerRoot extends BaseController {
	var $pid = "";
	var $default_tab = 0;
	var $indi = null;
	var $diffindi = null;
	var $accept_success = false;
	var $visibility = "visible";
	var $position = "relative";
	var $display = "block";
	var $canedit = false;
	var $name_count = 0;
	var $total_names = 0;
	var $SEX_COUNT = 0;
	var $sexarray = array();
	var $tabs;
	var $Fam_Navigator = 'YES';
	var $NAME_LINENUM = 1;
	var $SEX_LINENUM = null;
	var $globalfacts = null;

	/**
	* constructor
	*/
	function IndividualControllerRoot() {
		parent::BaseController();
	}

	/**
	* Initialization function
	*/
	function init() {
		global $USE_RIN, $MAX_ALIVE_AGE, $GEDCOM, $GEDCOM_DEFAULT_TAB;
		global $USE_QUICK_UPDATE, $DEFAULT_PIN_STATE, $DEFAULT_SB_CLOSED_STATE, $pid;
		global $Fam_Navigator;

		$this->sexarray["M"] = i18n::translate('Male');
		$this->sexarray["F"] = i18n::translate('Female');
		$this->sexarray["U"] = i18n::translate('unknown');

		$this->pid = safe_GET_xref('pid');

		$show_famlink = $this->view!='preview';

		$pid = $this->pid;

		$this->default_tab = $GEDCOM_DEFAULT_TAB;
		$indirec = find_person_record($this->pid, WT_GED_ID);

		if ($USE_RIN && $indirec==false) {
			$this->pid = find_rin_id($this->pid);
			$indirec = find_person_record($this->pid, WT_GED_ID);
		}
		if (empty($indirec)) {
			$ct = preg_match('/(\w+):(.+)/', $this->pid, $match);
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				require_once WT_ROOT.'includes/classes/class_serviceclient.php';
				$service = ServiceClient::getInstance($servid);
				if ($service != null) {
					$newrec= $service->mergeGedcomRecord($remoteid, "0 @".$this->pid."@ INDI\n1 RFN ".$this->pid, false);
					$indirec = $newrec;
				}
			} else {
				$indirec = "0 @".$this->pid."@ INDI\n";
			}
		}
		//-- check for the user
		if (WT_USER_ID) {
			$this->default_tab=get_user_setting(WT_USER_ID, 'defaulttab');
		}

		//-- check for a cookie telling what the last tab was when they were last
		//-- visiting this individual
		if($this->default_tab == -2)
		{
			if (isset($_COOKIE['lasttabs'])) {
				$ct = preg_match("/".$this->pid."=(\d+)/", $_COOKIE['lasttabs'], $match);
				if ($ct>0) {
					$this->default_tab = $match[1]-1;
				}
			}
		}

		//-- set the default tab from a request parameter
		if (isset($_REQUEST['tab'])) {
			$this->default_tab = $_REQUEST['tab'];
		}

		$this->indi = new Person($indirec, false);
		$this->indi->ged_id=WT_GED_ID; // This record is from a file

		//-- if the person is from another gedcom then forward to the correct site
		/*
		if ($this->indi->isRemote()) {
			header('Location: '.encode_url(decode_url($this->indi->getLinkUrl()), false));
			exit;
		}
		*/
		if (!$this->isPrintPreview()) {
			$this->visibility = "hidden";
			$this->position = "absolute";
			$this->display = "none";
		}
		//-- perform the desired action
		switch($this->action) {
			case "addfav":
				$this->addFavorite();
				break;
			case "accept":
				if (WT_USER_CAN_ACCEPT) {
					accept_all_changes($this->pid, WT_GED_ID);
					$this->show_changes=false;
					$this->accept_success=true;
					//-- delete the record from the cache and refresh it
					$indirec = find_person_record($this->pid, WT_GED_ID);
					//-- check if we just deleted the record and redirect to index
					if (empty($indirec)) {
						header("Location: index.php?ctype=gedcom");
						exit;
					}
					$this->indi = new Person($indirec);
				}
				break;
			case "undo":
				if (WT_USER_CAN_ACCEPT) {
					reject_all_changes($this->pid, WT_GED_ID);
					$this->show_changes=false;
					$this->accept_success=true;
					//-- delete the record from the cache and refresh it
					$indirec = find_person_record($this->pid, WT_GED_ID);
					//-- check if we just deleted the record and redirect to index
					if (empty($indirec)) {
						header("Location: index.php?ctype=gedcom");
						exit;
					}
					$this->indi = new Person($indirec);
				}
				break;
		}

		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && WT_USER_CAN_EDIT) {
			$newrec = find_updated_record($this->pid, WT_GED_ID);
			if ($newrec) {
				//-- get the changed record from the file
				//print("jkdsakjhdkjsadkjsakjdhsakd".$newrec);
				$remoterfn = get_gedcom_value("RFN", 1, $newrec);
			} else {
				$remoterfn = get_gedcom_value("RFN", 1, $indirec);
			}
			// print "remoterfn=".$remoterfn;
			//-- get an updated record from the web service
			if (!empty($remoterfn)) {
				$parts = explode(':', $remoterfn);
				if (count($parts)==2) {
					$servid = $parts[0];
					$aliaid = $parts[1];
					if (!empty($servid)&&!empty($aliaid)) {
						require_once WT_ROOT.'includes/classes/class_serviceclient.php';
						$serviceClient = ServiceClient::getInstance($servid);
						if (!is_null($serviceClient)) {
							if (!empty($newrec)) $mergerec = $serviceClient->mergeGedcomRecord($aliaid, $newrec, true);
							else $mergerec = $serviceClient->mergeGedcomRecord($aliaid, $indirec, true);
							if ($serviceClient->type=="remote") {
								$newrec = $mergerec;
							}
							else {
								$indirec = $mergerec;
							}
						}
					}
				}
			}
			if (!empty($newrec)) {
				$this->diffindi = new Person($newrec, false);
				$this->diffindi->setChanged(true);
				$indirec = $newrec;
			}
		}

		if ($this->show_changes) {
			$this->indi->diffMerge($this->diffindi);
		}

		//-- only allow editors or users who are editing their own individual or their immediate relatives
		if ($this->indi->canDisplayDetails()) {
			$this->canedit = WT_USER_CAN_EDIT;
		}

		// Initialise tabs
		$this->tabs = WT_Module::getActiveTabs();
		foreach($this->tabs as $mod) {
			$mod->setController($this);
			if ($mod->hasTabContent()) {		
				if (empty($this->default_tab)) {
					$this->default_tab=$mod->getName();
				}
			}
		}
		
		if (!isset($_SESSION['WT_pin']) && $DEFAULT_PIN_STATE)
			 $_SESSION['WT_pin'] = true;
			 
		if (!isset($_SESSION['WT_sb_closed']) && $DEFAULT_SB_CLOSED_STATE)
			 $_SESSION['WT_sb_closed'] = true;
			 
		//-- handle ajax calls
		if ($this->action=="ajax") {
			$tab = 0;
			if (isset($_REQUEST['module'])) {
				$tabname = $_REQUEST['module'];
				header("Content-Type: text/html; charset=UTF-8"); //AJAX calls do not have the meta tag headers and need this set
				$mod = $this->tabs[$tabname];
				if ($mod) {
					echo $mod->getTabContent();
					// Allow the other tabs to modify this one - e.g. lightbox does this.
					$js='';
					foreach (WT_Module::getActiveTabs() as $module) {
						$js.=$module->getJSCallbackAllTabs();
					}
					if ($js) {
						echo WT_JS_START, $js, WT_JS_END;
					}
				}
			}
			
			if (isset($_REQUEST['pin'])) {
				if ($_REQUEST['pin']=='true') $_SESSION['WT_pin'] = true;
				else $_SESSION['WT_pin'] = false;
			}
			
			if (isset($_REQUEST['sb_closed'])) {
				if ($_REQUEST['sb_closed']=='true') $_SESSION['WT_sb_closed'] = true;
				else $_SESSION['WT_sb_closed'] = false;
			}
			
			//-- only get the requested tab and then exit
			if (WT_DEBUG_SQL) {
				echo WT_DB::getQueryLog();
			}
			exit;
		}
		
		if (WT_USER_CAN_EDIT) {
			
		}
	}
	//-- end of init function
	/**
	* Add a new favorite for the action user
	*/
	function addFavorite() {
		global $GEDCOM;
		if (WT_USER_ID && !empty($_REQUEST["gid"])) {
			$gid = strtoupper($_REQUEST["gid"]);
			$indirec = find_person_record($gid, WT_GED_ID);
			if ($indirec) {
				$favorite = array();
				$favorite["username"] = WT_USER_NAME;
				$favorite["gid"] = $gid;
				$favorite["type"] = "INDI";
				$favorite["file"] = $GEDCOM;
				$favorite["url"] = "";
				$favorite["note"] = "";
				$favorite["title"] = "";
				addFavorite($favorite);
			}
		}
	}

	/**
	* return the title of this page
	* @return string the title of the page to go in the <title> tags
	*/
	function getPageTitle() {
		if ($this->indi) {
			$name = $this->indi->getFullName();
			return $name." - ".$this->indi->getXref()." - ".i18n::translate('Individual Information');
		} else {
			return i18n::translate('Unable to find record with ID');
		}
	}

	/**
	* gets a string used for setting the value of a cookie using javascript
	*/
	function getCookieTabString() {
		$str = "";
		if (isset($_COOKIE['lasttabs'])) {
			$parts = explode(':', $_COOKIE['lasttabs']);
			foreach($parts as $i=>$val) {
				$inner = explode('=', $val);
				if (count($inner)>1) {
					if ($inner[0]!=$this->pid) $str .= $val.":";
				}
			}
		}
		return $str;
	}
	/**
	* check if we can show the highlighted media object
	* @return boolean
	*/
	function canShowHighlightedObject() {
		global $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $USE_SILHOUETTE, $WT_IMAGES;

		if (($this->indi->canDisplayDetails()) && ($MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES)) {
			$firstmediarec = $this->indi->findHighlightedMedia();
			if ($firstmediarec) return true;
		}
		if ($USE_SILHOUETTE && isset($WT_IMAGES["default_image_U"]["other"])) { return true; }
		return false;
	}
	/**
	* check if we can show the gedcom record
	* @return boolean
	*/
	function canShowGedcomRecord() {
		global $SHOW_GEDCOM_RECORD;
		if (WT_USER_CAN_EDIT && $SHOW_GEDCOM_RECORD && $this->indi->canDisplayDetails())
			return true;
	}
	/**
	* check if use can edit this person
	* @return boolean
	*/
	function userCanEdit() {
		return $this->canedit;
	}
	/**
	* get the highlighted object HTML
	* @return string HTML string for the <img> tag
	*/
	function getHighlightedObject() {
		global $USE_THUMBS_MAIN, $THUMBNAIL_WIDTH, $USE_MEDIA_VIEWER, $GEDCOM, $WT_IMAGE_DIR, $WT_IMAGES, $USE_SILHOUETTE, $sex;
		if ($this->canShowHighlightedObject()) {
			$firstmediarec = $this->indi->findHighlightedMedia();
			if (!empty($firstmediarec)) {
				$filename = thumb_or_main($firstmediarec);		// Do we send the main image or a thumbnail?
				if (!$USE_THUMBS_MAIN || $firstmediarec["_THUM"]=='Y') {
					$class = "image";
				} else {
					$class = "thumbnail";
				}
				$isExternal = isFileExternal($filename);
				if ($isExternal && $class=="thumbnail") $class .= "\" width=\"".$THUMBNAIL_WIDTH;
				if (!empty($filename)) {
					$result = "";
					$imgsize = findImageSize($firstmediarec["file"]);
					$imgwidth = $imgsize[0]+40;
					$imgheight = $imgsize[1]+150;
					//Gets the Media View Link Information and Concatenate
					$mid = $firstmediarec['mid'];

					$name = $this->indi->getFullName();
					if (WT_USE_LIGHTBOX) {
						print "<a href=\"" . $firstmediarec["file"] . "\" rel=\"clearbox[general_1]\" rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name, ENT_QUOTES, 'UTF-8')) . "\">" . "\n";
					} else if (!$USE_MEDIA_VIEWER && $imgsize) {
						$result .= "<a href=\"javascript:;\" onclick=\"return openImage('".encode_url(encrypt($firstmediarec["file"]))."', $imgwidth, $imgheight);\">";
					} else {
						$result .= "<a href=\"mediaviewer.php?mid={$mid}\">";
					}
					$result .= "<img src=\"$filename\" align=\"left\" class=\"".$class."\" border=\"none\" title=\"".PrintReady(htmlspecialchars(strip_tags($name), ENT_QUOTES, 'UTF-8'))."\" alt=\"".PrintReady(htmlspecialchars(strip_tags($name), ENT_QUOTES, 'UTF-8'))."\" />";
					$result .= "</a>";
					return $result;
				}
			}
		}
		if ($USE_SILHOUETTE && isset($WT_IMAGES["default_image_U"]["other"])) {
			$class = "\" width=\"".$THUMBNAIL_WIDTH;
			$sex = $this->indi->getSex();
			$result = "<img src=\"";
			if ($sex == 'F') {
				$result .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_F"]["other"];
			} 
			else if ($sex == 'M') {
				$result .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_M"]["other"];
			}
			else {
				$result .= $WT_IMAGE_DIR."/".$WT_IMAGES["default_image_U"]["other"];
			} 
			$result .="\" class=\"".$class."\" border=\"none\" alt=\"\" />";
			return $result;
		}
	}

	/**
	* print information for a name record
	*
	* Called from the individual information page
	* @see individual.php
	* @param Event $event the event object
	*/
	function print_name_record(&$event) {
		global $UNDERLINE_NAME_QUOTES;

		if (!$event->canShowDetails()) {
			return false;
		}
		$factrec = $event->getGedComRecord();
		$linenum = $event->getLineNumber();

		$this->name_count++;
		echo "\n<div id=\"nameparts", $this->name_count, '"';
		if (strpos($factrec, "WT_OLD")!==false) {
			echo " class=\"namered\"";
		}
		if (strpos($factrec, "WT_NEW")!==false) {
			echo " class=\"nameblue\"";
		}
		echo ">";
		if (!preg_match("/^2 (SURN)|(GIVN)/m", $factrec)) {
			$dummy=new Person($factrec);
			$dummy->setPrimaryName(0);
			echo '<span class="label">', i18n::translate('Name'), ': </span><br />';
			echo PrintReady($dummy->getFullName()), '<br />';
		}
		$ct = preg_match_all('/\n2 (\w+) (.*)/', $factrec, $nmatch, PREG_SET_ORDER);
		echo "\n\t\t<dl>";
		for($i=0; $i<$ct; $i++) {
			$fact = trim($nmatch[$i][1]);
			if (($fact!="SOUR")&&($fact!="NOTE")) {
				echo "\n\t\t\t<dt class=\"label\">";
				echo translate_fact($fact, $this->indi);
				echo "</dt><dd class=\"field\">";
				if (isset($nmatch[$i][2])) {
					$name = trim($nmatch[$i][2]);
					$name = preg_replace("'/,'", ",", $name);
					$name = preg_replace("'/'", " ", $name);
					if ($UNDERLINE_NAME_QUOTES) {
						$name=preg_replace('/"([^"]*)"/', '<span class="starredname">\\1</span>', $name);
					}
					$name=preg_replace('/(\S*)\*/', '<span class="starredname">\\1</span>', $name);
					echo PrintReady($name);
				}
				echo "</dd>";
			}
		}
		echo "\n\t\t</dl>";
		if ($this->total_names>1 && !$this->isPrintPreview() && $this->userCanEdit() && !strpos($factrec, 'WT_OLD')) {
			echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:;\" class=\"font9\" onclick=\"edit_name('".$this->pid."', ".$linenum."); return false;\">", i18n::translate('Edit Name'), "</a> | ";
			echo "<a class=\"font9\" href=\"javascript:;\" onclick=\"delete_record('".$this->pid."', ".$linenum."); return false;\">", i18n::translate('Delete Name'), "</a>";
			if ($this->name_count==2) {
				echo help_link('delete_name');
			}
			echo "<br />";
		}
		if (preg_match("/\d (NOTE)|(SOUR)/", $factrec)>0) {
			// -- find sources for this name
			echo "<div class=\"indent\">";
			print_fact_sources($factrec, 2);
			//-- find the notes for this name
			echo "&nbsp;&nbsp;&nbsp;";
			print_fact_notes($factrec, 2);
			echo "</div><br />";
		}
		echo "\n</div>\n";
	}

	/**
	* print information for a sex record
	*
	* Called from the individual information page
	* @see individual.php
	* @param Event $event the Event object
	*/
	function print_sex_record(&$event) {
		global $sex;

		if (!$event->canShowDetails()) return false;
		$factrec = $event->getGedComRecord();
		$sex = $event->getDetail();
		if (empty($sex)) $sex = "U";
		echo "<div id=\"sex\"";
		if (strpos($factrec, "WT_OLD")!==false) {
			echo " class=\"namered\"";
		}
		if (strpos($factrec, "WT_NEW")!==false) {
			echo " class=\"nameblue\"";
		}
		echo "><dl>";
		print "<dt class=\"label\">".i18n::translate('Gender')."</dt><dd class=\"field\">".$this->sexarray[$sex];
		if ($sex=='M') {
			echo Person::sexImage('M', 'small', '', i18n::translate('Male'));
		} elseif ($sex=='F') {
			echo Person::sexImage('F', 'small', '', i18n::translate('Female'));
		} else {
			echo Person::sexImage('U', 'small', '', i18n::translate('unknown'));
		}
		if ($this->SEX_COUNT>1) {
			if ((!$this->isPrintPreview()) && ($this->userCanEdit()) && (strpos($factrec, "WT_OLD")===false)) {
				if ($event->getLineNumber()=="new") {
					print "<br /><a class=\"font9\" href=\"javascript:;\" onclick=\"add_new_record('".$this->pid."', 'SEX'); return false;\">".i18n::translate('Edit')."</a>";
				} else {
						print "<br /><a class=\"font9\" href=\"javascript:;\" onclick=\"edit_record('".$this->pid."', ".$event->getLineNumber()."); return false;\">".i18n::translate('Edit')."</a> | ";
						print "<a class=\"font9\" href=\"javascript:;\" onclick=\"delete_record('".$this->pid."', ".$event->getLineNumber()."); return false;\">".i18n::translate('Delete')."</a>\n";
				}
			}
		}
		print "</dd></dl>";
		// -- find sources
//		print "&nbsp;&nbsp;&nbsp;";
		print_fact_sources($event->getGedComRecord(), 2);
		//-- find the notes
//		print "&nbsp;&nbsp;&nbsp;";
		print_fact_notes($event->getGedComRecord(), 2);
		print "</div>";
	}
	/**
	* get the edit menu
	* @return Menu
	*/
	function &getEditMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $GEDCOM;
		global $USE_QUICK_UPDATE;
		if ($TEXT_DIRECTION=="rtl") {
			$ff="_rtl";
		} else {
			$ff="";
		}
		//-- main edit menu
		$menu = new Menu(i18n::translate('Edit'));
		if (!empty($WT_IMAGES["edit_indi"]["large"])) {
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["edit_indi"]["large"]);
		}
		else if (!empty($WT_IMAGES["edit_indi"]["small"])) {
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["edit_indi"]["small"]);
		}
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");

		if (WT_USER_CAN_EDIT) {
			if (count($this->indi->getSpouseFamilyIds())>1) {
				$submenu = new Menu(i18n::translate('Reorder families'));
				$submenu->addOnclick("return reorder_families('".$this->pid."');");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}

			//--make sure the totals are correct
			$this->getGlobalFacts();
			if ($this->total_names<2) {
				$submenu = new Menu(i18n::translate('Edit Name'));
				$submenu->addOnclick("return edit_name('".$this->pid."', $this->NAME_LINENUM);");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}

			$submenu = new Menu(i18n::translate('Add new Name'));
			$submenu->addOnclick("return add_name('".$this->pid."');");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);

			if ($this->SEX_COUNT<2) {
				$submenu = new Menu(i18n::translate('Edit Gender'));
				if ($this->SEX_LINENUM=="new") $submenu->addOnclick("return add_new_record('".$this->pid."', 'SEX');");
				else $submenu->addOnclick("return edit_record('".$this->pid."', $this->SEX_LINENUM);");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}

			$menu->addSeparator();
		}

		if (find_updated_record($this->pid, WT_GED_ID)!==null) {
			if (!$this->show_changes) {
				$label = i18n::translate('This record has been updated.  Click here to show changes.');
				$link = $this->indi->getLinkUrl()."&show_changes=yes";
			} else {
				$label = i18n::translate('Click here to hide changes.');
				$link = $this->indi->getLinkUrl()."&show_changes=no";
			}
			$submenu = new Menu($label, encode_url($link));
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);

			if (WT_USER_CAN_ACCEPT) {
				$submenu = new Menu(i18n::translate('Undo all changes'), encode_url($this->indi->getLinkUrl()."&action=undo"));
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				$submenu = new Menu(i18n::translate('Accept all changes'), encode_url($this->indi->getLinkUrl()."&action=accept"));
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}

			$menu->addSeparator();
		}

		if (WT_USER_IS_ADMIN || $this->canShowGedcomRecord()) {
			$submenu = new Menu(i18n::translate('Edit raw GEDCOM record'));
			$submenu->addOnclick("return edit_raw('".$this->pid."');");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}

		$submenu = new Menu(i18n::translate('Delete this individual'));
		$submenu->addOnclick("return deleteperson('".$this->pid."');");
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);

		//-- get the link for the first submenu and set it as the link for the main menu
		if (isset($menu->submenus[0])) {
			$link = $menu->submenus[0]->onclick;
			$menu->addOnclick($link);
		}
		return $menu;
	}
	/**
	* check if we can show the other menu
	* @return boolean
	*/
	function canShowOtherMenu() {
		global $SHOW_GEDCOM_RECORD, $ENABLE_CLIPPINGS_CART;
		if ($this->indi->canDisplayDetails() && ($SHOW_GEDCOM_RECORD || $ENABLE_CLIPPINGS_CART>=WT_USER_ACCESS_LEVEL))
			return true;
		return false;
	}
	/**
	* get the "other" menu
	* @return Menu
	*/
	function &getOtherMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $GEDCOM;
		global $SHOW_GEDCOM_RECORD, $ENABLE_CLIPPINGS_CART;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";
		//-- main other menu item
		$menu = new Menu(i18n::translate('Other'));
		if ($SHOW_GEDCOM_RECORD) {
			if (!empty($WT_IMAGES["gedcom"]["small"])) $menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["gedcom"]["large"]);
			if ($this->show_changes && WT_USER_CAN_EDIT) $menu->addOnclick("return show_gedcom_record('new');");
			else $menu->addOnclick("return show_gedcom_record('');");
		} else {
			if (!empty($WT_IMAGES["clippings"]["small"])) $menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["clippings"]["small"]);
			$menu->addLink(encode_url("module.php?mod=clippings&mod_action=index&action=add&id={$this->pid}&type=indi"));
		}
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		if ($SHOW_GEDCOM_RECORD) {
			$submenu = new Menu(i18n::translate('View GEDCOM Record'));
			if (!empty($WT_IMAGES["gedcom"]["small"])) $submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["gedcom"]["small"]);
			if ($this->show_changes && WT_USER_CAN_EDIT) $submenu->addOnclick("return show_gedcom_record('new');");
			else $submenu->addOnclick("return show_gedcom_record();");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if ($this->indi->canDisplayDetails() && $ENABLE_CLIPPINGS_CART>=WT_USER_ACCESS_LEVEL) {
			$submenu = new Menu(i18n::translate('Add to Clippings Cart'), encode_url("module.php?mod=clippings&action=add&id={$this->pid}&type=indi"));
			if (!empty($WT_IMAGES["clippings"]["small"])) $submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["clippings"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if ($this->indi->canDisplayDetails() && WT_USER_NAME) {
			$submenu = new Menu(i18n::translate('Add to My Favorites'), encode_url($this->indi->getLinkUrl()."&action=addfav&gid={$this->pid}"));
			if (!empty($WT_IMAGES["favorites"]["small"])) $submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["favorites"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		return $menu;
	}
	/**
	* get global facts
	* global facts are NAME and SEX
	* @return array return the array of global facts
	*/
	function getGlobalFacts() {
		if ($this->globalfacts==null) {
			$this->globalfacts = $this->indi->getGlobalFacts();
			foreach ($this->globalfacts as $key => $value) {
				$fact = $value->getTag();
				if ($fact=="SEX") {
					$this->SEX_COUNT++;
					$this->SEX_LINENUM = $value->getLineNumber();
				}
				if ($fact=="NAME") {
					$this->total_names++;
					$this->NAME_LINENUM = $value->getLineNumber();
				}
			}
		}
		return $this->globalfacts;
	}
	/**
	* get the individual facts shown on tab 1
	* @return array
	*/
	function getIndiFacts() {
		$indifacts = $this->indi->getIndiFacts();
		sort_facts($indifacts);
		return $indifacts;
	}
	/**
	* get the other facts shown on tab 2
	* @return array
	*/
	function getOtherFacts() {
		$otherfacts = $this->indi->getOtherFacts();
		return $otherfacts;
	}
	
	/**
	* get the person box stylesheet class
	* for the given person
	* @param Person $person
	* @return string returns 'person_box', 'person_boxF', or 'person_boxNN'
	*/
	function getPersonStyle(&$person) {
		$sex = $person->getSex();
		switch($sex) {
			case "M":
				$isf = "";
				break;
			case "F":
				$isf = "F";
				break;
			default:
				$isf = "NN";
				break;
		}
		return "person_box".$isf;
	}
	
	/**
	* build an array of Person that will be used to build a list
	* of family members on the close relatives tab
	* @param Family $family the family we are building for
	* @return array an array of Person that will be used to iterate through on the indivudal.php page
	*/
	function buildFamilyList(&$family, $type) {
		global $PEDI_CODES, $PEDI_CODES_F, $PEDI_CODES_M;

		$people = array();
		if (!is_object($family)) return $people;
		$labels = array();
		if ($type=="parents") {
			$labels["parent"] = i18n::translate('Parent');
			$labels["mother"] = i18n::translate('Mother');
			$labels["father"] = i18n::translate('Father');
			$labels["sibling"] = i18n::translate('Sibling');
			$labels["sister"] = i18n::translate('Sister');
			$labels["brother"] = i18n::translate('Brother');
		}
		if ($type=="step"){
			$labels["parent"] = i18n::translate('Step-Parent');
			$labels["mother"] = i18n::translate('Step-Mother');
			$labels["father"] = i18n::translate('Step-Father');
			$labels["sibling"] = i18n::translate('Half-Sibling');
			$labels["sister"] = i18n::translate('Half-Sister');
			$labels["brother"] = i18n::translate('Half-Brother');
		}
		if ($type=="spouse") {
			if ($family->isNotMarried()) {
				$labels["parent"] = i18n::translate('Partner');
				$labels["mother"] = i18n::translate('Partner');
				$labels["father"] = i18n::translate('Partner');
			} elseif ($family->isDivorced()) {
				$labels["parent"] = i18n::translate('Ex-Spouse');
				$labels["mother"] = i18n::translate('Ex-Wife');
				$labels["father"] = i18n::translate('Ex-Husband');
			} else {
				$marr_rec = $family->getMarriageRecord();
				if (!empty($marr_rec)) {
					$type = $family->getMarriageType();
					if (empty($type) || stristr($type, "partner")===false) {
						$labels["parent"] = i18n::translate('Spouse');
						$labels["mother"] = i18n::translate('Wife');
						$labels["father"] = i18n::translate('Husband');
					} else {
						$labels["parent"] = i18n::translate('Partner');
						$labels["mother"] = i18n::translate('Partner');
						$labels["father"] = i18n::translate('Partner');
					}
				} else {
					$labels["parent"] = i18n::translate('Spouse');
					$labels["mother"] = i18n::translate('Wife');
					$labels["father"] = i18n::translate('Husband');
				}
			}
			$labels["sibling"] = i18n::translate('Child');
			$labels["sister"] = i18n::translate('Daughter');
			$labels["brother"] = i18n::translate('Son');
		}
		$newhusb = null;
		$newwife = null;
		$newchildren = array();
		$delchildren = array();
		$children = array();
		$husb = null;
		$wife = null;
		if (!$family->getChanged()) {
			$husb = $family->getHusband();
			$wife = $family->getWife();
			$children = $family->getChildren();
		}
		//-- step families : set the label for the common parent
		if ($type=="step") {
			$fams = $this->indi->getChildFamilies();
			foreach($fams as $key=>$fam) {
				if ($fam->hasParent($husb)) $labels["father"] = i18n::translate('Father');
				if ($fam->hasParent($wife)) $labels["mother"] = i18n::translate('Mother');
			}
		}
		//-- set the label for the husband
		if (!is_null($husb)) {
			$label = $labels["parent"];
			$sex = $husb->getSex();
			if ($sex=="F") {
				$label = $labels["mother"];
			}
			if ($sex=="M") {
				$label = $labels["father"];
			}
			if ($husb->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
			$husb->setLabel($label);
		}
		//-- set the label for the wife
		if (!is_null($wife)) {
			$label = $labels["parent"];
			$sex = $wife->getSex();
			if ($sex=="F") {
				$label = $labels["mother"];
			}
			if ($sex=="M") {
				$label = $labels["father"];
			}
			if ($wife->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
			$wife->setLabel($label);
		}
		if ($this->show_changes) {
			$newfamily = $family->getUpdatedFamily();
			if (!is_null($newfamily)) {
				$newhusb = $newfamily->getHusband();
				//-- check if the husband in the family has changed
				if (!is_null($newhusb) && !$newhusb->equals($husb)) {
					$label = $labels["parent"];
					$sex = $newhusb->getSex();
					if ($sex=="F") {
						$label = $labels["mother"];
					}
					if ($sex=="M") {
						$label = $labels["father"];
					}
					if ($newhusb->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
					$newhusb->setLabel($label);
				}
				else $newhusb = null;
				$newwife = $newfamily->getWife();
				//-- check if the wife in the family has changed
				if (!is_null($newwife) && !$newwife->equals($wife)) {
					$label = $labels["parent"];
					$sex = $newwife->getSex();
					if ($sex=="F") {
						$label = $labels["mother"];
					}
					if ($sex=="M") {
						$label = $labels["father"];
					}
					if ($newwife->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
					$newwife->setLabel($label);
				}
				else $newwife = null;
				//-- check for any new children
				$merged_children = array();
				$new_children = $newfamily->getChildren();
				$num = count($children);
				for($i=0; $i<$num; $i++) {
					$child = $children[$i];
					if (!is_null($child)) {
						$found = false;
						foreach($new_children as $key=>$newchild) {
							if (!is_null($newchild)) {
								if ($child->equals($newchild)) {
									$found = true;
									break;
								}
							}
						}
						if (!$found) $delchildren[] = $child;
						else $merged_children[] = $child;
					}
				}
				foreach($new_children as $key=>$newchild) {
					if (!is_null($newchild)) {
						$found = false;
						foreach($children as $key1=>$child) {
							if (!is_null($child)) {
								if ($child->equals($newchild)) {
									$found = true;
									break;
								}
							}
						}
						if (!$found) $newchildren[] = $newchild;
					}
				}
				$children = $merged_children;
			}
		}
		//-- set the labels for the children
		$num = count($children);
		for($i=0; $i<$num; $i++) {
			if (!is_null($children[$i])) {
				$label = $labels["sibling"];
				$sex = $children[$i]->getSex();
				if ($sex=="F") {
					$label = $labels["sister"];
				}
				if ($sex=="M") {
					$label = $labels["brother"];
				}
				if ($children[$i]->getXref()==$this->pid) {
					$label = "<img src=\"images/selected.png\" alt=\"\" />";
				}
				$famcrec = get_sub_record(1, "1 FAMC @".$family->getXref()."@", $children[$i]->gedrec);
				$pedi = get_gedcom_value("PEDI", 2, $famcrec, '', false);
				if ($pedi) {
					if ($sex=="F" && isset($PEDI_CODES[$pedi]))			$label .= " (".$PEDI_CODES_F[$pedi].")";
					else if ($sex=="M" && isset($PEDI_CODES[$pedi]))	$label .= " (".$PEDI_CODES_M[$pedi].")";
					else if (isset($PEDI_CODES[$pedi]))					$label .= " (".$PEDI_CODES[$pedi].")";
				}
				$children[$i]->setLabel($label);
			}
		}
		$num = count($newchildren);
		for($i=0; $i<$num; $i++) {
			$label = $labels["sibling"];
			$sex = $newchildren[$i]->getSex();
			if ($sex=="F") {
				$label = $labels["sister"];
			}
			if ($sex=="M") {
				$label = $labels["brother"];
		}
			if ($newchildren[$i]->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
			$pedi = $newchildren[$i]->getChildFamilyPedigree($family->getXref());
			if ($sex=="F" && isset($PEDI_CODES[$pedi]))			$label .= " (".$PEDI_CODES_F[$pedi].")";
			else if ($sex=="M" && isset($PEDI_CODES[$pedi]))	$label .= " (".$PEDI_CODES_M[$pedi].")";
			else if (isset($PEDI_CODES[$pedi]))					$label .= " (".$PEDI_CODES[$pedi].")";
			$newchildren[$i]->setLabel($label);
		}
		$num = count($delchildren);
		for($i=0; $i<$num; $i++) {
				$label = $labels["sibling"];
			$sex = $delchildren[$i]->getSex();
			if ($sex=="F") {
				$label = $labels["sister"];
			}
			if ($sex=="M") {
				$label = $labels["brother"];
			}
			if ($delchildren[$i]->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
			$pedi = $delchildren[$i]->getChildFamilyPedigree($family->getXref());
			if ($sex=="F" && isset($PEDI_CODES[$pedi]))			$label .= " (".$PEDI_CODES_F[$pedi].")";
			else if ($sex=="M" && isset($PEDI_CODES[$pedi]))	$label .= " (".$PEDI_CODES_M[$pedi].")";
			else if (isset($PEDI_CODES[$pedi]))					$label .= " (".$PEDI_CODES[$pedi].")";
			$delchildren[$i]->setLabel($label);
		}
		if (!is_null($newhusb)) $people['newhusb'] = $newhusb;
		if (!is_null($husb)) $people['husb'] = $husb;
		if (!is_null($newwife)) $people['newwife'] = $newwife;
		if (!is_null($wife)) $people['wife'] = $wife;
		$people['children'] = $children;
		$people['newchildren'] = $newchildren;
		$people['delchildren'] = $delchildren;
		return $people;
	}

// -----------------------------------------------------------------------------
// Functions for GedFact Assistant
// -----------------------------------------------------------------------------
	/**
	* include GedFact controller
	*/
	function census_assistant() {
		require WT_ROOT.'modules/GEDFact_assistant/_CENS/census_1_ctrl.php';
	}
	function medialink_assistant() {
		require WT_ROOT.'modules/GEDFact_assistant/_MEDIA/media_1_ctrl.php';
	}
// -----------------------------------------------------------------------------
// End GedFact Assistant Functions
// -----------------------------------------------------------------------------



}
// -- end of class

//-- load a user extended class if one exists
if (file_exists(WT_ROOT.'includes/controllers/individual_ctrl_user.php'))
{
	require_once WT_ROOT.'includes/controllers/individual_ctrl_user.php';
}
else
{
	class IndividualController extends IndividualControllerRoot
	{
	}
}

?>
