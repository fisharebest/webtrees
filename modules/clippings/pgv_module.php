<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2010 John Finlay
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
 * @subpackage Modules
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
require_once WT_ROOT.'includes/classes/class_module.php';

class clippings_WT_Module extends WT_Module implements WT_Module_Menu, WT_Module_Sidebar {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Clippings Cart');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('Provides a clippings cart, to copy records for export/download.');
	}

	// Extend class WT_Module
	public function defaultAccessLevel() {
		return WT_PRIV_USER;
	}

	// Implement WT_Module_Menu
	public function defaultMenuOrder() {
		return 20;
	}
	
	// Implement WT_Module_Menu
	public function getMenu() { 
		global $ENABLE_CLIPPINGS_CART, $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $GEDCOM, $SEARCH_SPIDER;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if ($SEARCH_SPIDER) {
			return new Menu("", "", "");
		}
		//-- main clippings menu item
		$menu = new Menu($this->getTitle(), encode_url('module.php?mod=clippings&amp;ged='.$GEDCOM), "down");
		if (!empty($WT_IMAGES["clippings"]["large"])) {
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["clippings"]["large"]);
		}
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_clippings");

		return $menu;
	}

	// Implement WT_Module_Sidebar
	public function defaultSidebarOrder() {
		return 50;
	}
	
	// Impelement WT_Module_Sidebar
	public function hasSidebarContent() {
		return true;
	}

	// Impelement WT_Module_Sidebar
	public function getSidebarContent() {
		require_once WT_ROOT.'modules/clippings/clippings_ctrl.php';
		global $WT_IMAGE_DIR, $WT_IMAGES;
		global $cart, $GEDCOM;

		$out = '';

		if ($this->controller) {
			$out .= '<script type="text/javascript">
			<!--
			jQuery(document).ready(function() {
				jQuery(".add_cart, .remove_cart").live("click", function(){
					jQuery("#sb_clippings_content").load(this.href);
					return false;
				});
			});
			//-->
			</script>
			<div id="sb_clippings_content">';
			$out .= $this->getCartList();
			$root = null;
			if ($this->controller->pid && !id_in_cart($this->controller->pid)) {
				$root = GedcomRecord::getInstance($this->controller->pid);
				if ($root && $root->canDisplayDetails()) 
					$out .= '<a href="sidebar.php?sb_action=clippings&amp;add='.$root->getXref().'" class="add_cart">
					<img src="'.$WT_IMAGE_DIR.'/'.$WT_IMAGES['clippings']['small'].'" width="20" /> '.i18n::translate('Add %s to cart', $root->getListName()).'</a>';
			}
			else if ($this->controller->famid && !id_in_cart($this->controller->pid)) {
				$fam = Family::getInstance($this->controller->famid);
				if ($fam && $fam->canDisplayDetails()) {
					$out .= '<a href="sidebar.php?sb_action=clippings&amp;add='.$fam->getXref().'" class="add_cart"> '.i18n::translate('Add %s to cart', $fam->getFullName()).'</a><br />';
				}
			}
			$out .= '</div>';
		}
		return $out;
	}

	// Impelement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		global $GEDCOM, $cart;
		require_once WT_ROOT.'modules/clippings/clippings_ctrl.php';
		$controller = new ClippingsController();
		$this->controller = $controller;
		$add = safe_GET_xref('add','');
		$add1 = safe_GET_xref('add1','');
		$remove = safe_GET('remove', WT_REGEX_INTEGER, -1);
		$others = safe_GET('others', WT_REGEX_ALPHANUM, '');
		$controller->level1 = safe_GET('level1');
		$controller->level2 = safe_GET('level2');
		$controller->level3 = safe_GET('level3');
		if (!empty($add)) {
			$record = GedcomRecord::getInstance($add);
			if ($record) {
				$controller->id=$record->getXref();
				$controller->type=$record->getType();
				$clipping = array ();
				$clipping['type'] = strtolower($record->getType());
				$clipping['id'] = $add;
				$clipping['gedcom'] = $GEDCOM;
				$ret = $controller->add_clipping($clipping);
				if (isset($_SESSION["cart"])) $_SESSION["cart"]=$cart;
				if ($ret) return $this->askAddOptions($record);
			}
		}
		else if (!empty($add1)) {
			$record = GedcomRecord::getInstance($add1);
			if ($record) {
				$controller->id=$record->getXref();
				$controller->type=strtolower($record->getType());
				if ($record->getType() == 'SOUR') {
					if ($others == 'linked') {
						foreach (fetch_linked_indi($record->getXref(), 'SOUR', WT_GED_ID) as $indi) {
							if ($indi->canDisplayName()) {
								$controller->add_clipping(array('type'=>'indi', 'id'=>$indi->getXref()));
							}
						}
						foreach (fetch_linked_fam($record->getXref(), 'SOUR', WT_GED_ID) as $fam) {
							if ($fam->canDisplayName()) {
								$controller->add_clipping(array('type'=>'fam', 'id'=>$fam->getXref()));
							}
						}
					}
				}
				if ($record->getType() == 'FAM') {
					if ($others == 'parents') {
						$parents = find_parents($record->getXref());
						if (!empty ($parents["HUSB"])) {
							$clipping = array ();
							$clipping['type'] = "indi";
							$clipping['id'] = $parents["HUSB"];
							$ret = $controller->add_clipping($clipping);
						}
						if (!empty ($parents["WIFE"])) {
							$clipping = array ();
							$clipping['type'] = "indi";
							$clipping['id'] = $parents["WIFE"];
							$ret = $controller->add_clipping($clipping);
						}
					} else
					if ($others == "members") {
						$controller->add_family_members($record->getXref());
					} else
					if ($others == "descendants") {
						$controller->add_family_descendancy($record->getXref());
					}
				} else
				if ($record->getType() == 'INDI') {
					if ($others == 'parents') {
						$famids = find_family_ids($record->getXref());
						foreach ($famids as $indexval => $famid) {
							$clipping = array ();
							$clipping['type'] = "fam";
							$clipping['id'] = $famid;
							$ret = $controller->add_clipping($clipping);
							if ($ret) {
								$controller->add_family_members($famid);
							}
						}
					} else
					if ($others == 'ancestors') {
						$controller->add_ancestors_to_cart($record->getXref(), $controller->level1);
					} else
					if ($others == 'ancestorsfamilies') {
						$controller->add_ancestors_to_cart_families($record->getXref(), $controller->level2);
					} else
					if ($others == 'members') {
						$famids = find_sfamily_ids($record->getXref());
						foreach ($famids as $indexval => $famid) {
							$clipping = array ();
							$clipping['type'] = "fam";
							$clipping['id'] = $famid;
							$ret = $controller->add_clipping($clipping);
							if ($ret)
							$controller->add_family_members($famid);
						}
					} else
					if ($others == 'descendants') {
						$famids = find_sfamily_ids($record->getXref());
						foreach ($famids as $indexval => $famid) {
							$clipping = array ();
							$clipping['type'] = "fam";
							$clipping['id'] = $famid;
							$ret = $controller->add_clipping($clipping);
							if ($ret)
							$controller->add_family_descendancy($famid, $controller->level3);
						}
					}
				}
			}
		}
		else if ($remove!=-1) {
			$ct = count($cart);
			for ($i = $remove +1; $i < $ct; $i++) {
				$cart[$i -1] = $cart[$i];
			}
			unset ($cart[$ct -1]);
		}
		else if (isset($_REQUEST['empty'])) {
			$cart = array ();
			$_SESSION["cart"] = $cart;
		}
		else if (isset($_REQUEST['download'])) {
			return $this->downloadForm();
		}
		if (isset($_SESSION["cart"])) $_SESSION["cart"]=$cart;
		return $this->getCartList();
	}

	public function getCartList() {
		global $WT_IMAGE_DIR, $WT_IMAGES;
		global $cart, $GEDCOM;
		$out ='<ul>';
		$ct = count($cart);
		if ($ct==0) $out .= '<br /><br />'.i18n::translate('Your Clippings Cart is empty.').'<br /><br />';
		else {
			for ($i=0; $i<$ct; $i++) {
				$clipping = $cart[$i];
				$tag = strtoupper(substr($clipping['type'], 0, 4)); // source => SOUR
				//print_r($clipping);
				//-- don't show clippings from other gedcoms
				if ($clipping['gedcom']==$GEDCOM) {
					$icon='';
					if ($tag=='INDI') $icon = "indis";
					if ($tag=='FAM' ) $icon = "sfamily";
					//	if ($tag=='SOUR') $icon = "source";
					//	if ($tag=='REPO') $icon = "repository";
					//	if ($tag=='NOTE') $icon = "notes";
					//	if ($tag=='OBJE') $icon = "media";
					if (!empty($icon)) {
						$out .= '<li>';
						if (!empty($icon)) {
							$out .= '<img src="'.$WT_IMAGE_DIR."/".$WT_IMAGES[$icon]["small"].'" border="0" alt="'.$tag.'" title="'.$tag.'" width="20" />';
						}
						$record=GedcomRecord::getInstance($clipping['id']);
						if ($record) {
							$out .= '<a href="'.encode_url($record->getLinkUrl()).'">';
							if ($record->getType()=="INDI") $out .=$record->getSexImage();
							$out .= ' '.$record->getFullName().' ';
							if ($record->getType()=="INDI" && $record->canDisplayDetails()) {
								$bd = $record->getBirthDeathYears(false,'');
								if (!empty($bd)) $out .= PrintReady(' ('.$bd.')');
							}
							$out .= '</a>';
						}
						$out .= '<a	class="remove_cart" href="sidebar.php?sb_action=clippings&amp;remove='.$i.'">
						<img src="'. $WT_IMAGE_DIR. "/". $WT_IMAGES["remove"]["other"].'" border="0" alt="'.i18n::translate('Remove').'" title="'.i18n::translate('Remove').'" /></a>';
						$out .='</li>';
					}
				}
			}
		}
		$out .= '</ul>';
		if (count($cart)>0) {
			$out .= '<a href="sidebar.php?sb_action=clippings&amp;empty=true" class="remove_cart">'.i18n::translate('Empty Cart').'</a>'.help_link('empty_cart', 'clippings');
			$out .= '<br /><a href="sidebar.php?sb_action=clippings&amp;download=true" class="add_cart">'.i18n::translate('Download Now').'</a>';
		}
		$out .= '<br />';
		return $out;
	}
	public function askAddOptions(&$person) {
		global $MAX_PEDIGREE_GENERATIONS;
		$out = "<b>".$person->getFullName()."</b>";
		$out .= WT_JS_START;
		$out .= 'function radAncestors(elementid) {var radFamilies=document.getElementById(elementid);radFamilies.checked=true;}
			function continueAjax(frm) {
				var others = jQuery("input[name=\'others\']:checked").val();
				var link = "sidebar.php?sb_action=clippings&add1="+frm.pid.value+"&others="+others+"&level1="+frm.level1.value+"&level2="+frm.level2.value+"&level3="+frm.level3.value;
				jQuery("#sb_clippings_content").load(link);
			}';
		$out .= WT_JS_END;
		if ($person->getType()=='FAM') {

			$out .= '<form action="module.php" method="get" onsubmit="continueAjax(this); return false;">
			<input type="hidden" name="mod" value="clippings" />
			<input type="hidden" name="pgv_action" value="index" />
			<table>
			<tr><td class="topbottombar">'.i18n::translate('Which other links from this family would you like to add?').'
			<input type="hidden" name="pid" value="'.$person->getXref().'" />
			<input type="hidden" name="type" value="'.$person->getType().'" />
			<input type="hidden" name="action" value="add1" /></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" checked value="none" />'.i18n::translate('Add just this family record.').'</td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="parents" />'.i18n::translate('Add parents\' records together with this family record.').'</td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="members" />'.i18n::translate('Add parents\' and children\'s records together with this family record.').'</td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="descendants" />'.i18n::translate('Add parents\' and all descendants\' records together with this family record.').'</td></tr>
			<tr><td class="topbottombar"><input type="submit" value="'.i18n::translate('Continue Adding').'" /></td></tr>
			</table>
			</form>';
		}
		else if ($person->getType()=='INDI') {
			$out .= '<form action="module.php" method="get" onsubmit="continueAjax(this); return false;">
			<input type="hidden" name="mod" value="clippings" />
			<input type="hidden" name="pgv_action" value="index" />
		'.i18n::translate('Which links from this person would you also like to add?').'
		<input type="hidden" name="pid" value="'.$person->getXref().'" />
		<input type="hidden" name="type" value="'.$person->getType().'" />
		<input type="hidden" name="action" value="add1" />
		<ul>
		<li><input type="radio" name="others" checked value="none" />'.i18n::translate('Add just this person.').'</li>
		<li><input type="radio" name="others" value="parents" />'.i18n::translate('Add this person, his parents, and siblings.').'</li>
		<li><input type="radio" name="others" value="ancestors" id="ancestors" />'.i18n::translate('Add this person and his direct line ancestors.').'<br />
				'.i18n::translate('Number of generations:').'<input type="text" size="4" name="level1" value="'.$MAX_PEDIGREE_GENERATIONS.'" onfocus="radAncestors(\'ancestors\');"/></li>
		<li><input type="radio" name="others" value="ancestorsfamilies" id="ancestorsfamilies" />'.i18n::translate('Add this person, his direct line ancestors, and their families.').'<br />
				'.i18n::translate('Number of generations:').' <input type="text" size="4" name="level2" value="'. $MAX_PEDIGREE_GENERATIONS.'" onfocus="radAncestors(\'ancestorsfamilies\');" /></li>
		<li><input type="radio" name="others" value="members" />'.i18n::translate('Add this person, his spouse, and children.').'</li>
		<li><input type="radio" name="others" value="descendants" id="descendants" />'.i18n::translate('Add this person, his spouse, and all descendants.').'<br >
				'.i18n::translate('Number of generations:').' <input type="text" size="4" name="level3" value="'.$MAX_PEDIGREE_GENERATIONS.'" onfocus="radAncestors(\'descendants\');" /></li>
		</ul>
		<input type="submit" value="'.i18n::translate('Continue Adding').'" />
		</form>';
		} else if ($person->getType()=='SOUR')  {
			$out .= '<form action="module.php" method="get" onsubmit="continueAjax(this); return false;">
		<input type="hidden" name="mod" value="clippings" />
		<input type="hidden" name="pgv_action" value="index" />
		<table>
		<tr><td class="topbottombar">'.i18n::translate('Which records linked to this source should be added?').'
		<input type="hidden" name="pid" value="'.$person->getXref().'" />
		<input type="hidden" name="type" value="'.$person->getType().'" />
		<input type="hidden" name="action" value="add1" /></td></tr>
		<tr><td class="optionbox"><input type="radio" name="others" checked value="none" />'.i18n::translate('Add just this source.').'</td></tr>
		<tr><td class="optionbox"><input type="radio" name="others" value="linked" />'.i18n::translate('Add this source and families/people linked to it.').'</td></tr>
		<tr><td class="topbottombar"><input type="submit" value="'.i18n::translate('Continue Adding').'" />
		</table>
		</form>';
		}
		else return $this->getSidebarContent();
		return $out;
	}
	
	public function downloadForm() {
		global $TEXT_DIRECTION;
		$controller = $this->controller;
		$out = WT_JS_START;
		$out .= 'function cancelDownload() {
				var link = "sidebar.php?sb_action=clippings";
				jQuery("#sb_clippings_content").load(link);
			}';
		$out .= WT_JS_END;
		$out .= '<form method="get" action="module.php">
		<input type="hidden" name="mod" value="clippings" />
		<input type="hidden" name="pgv_action" value="index" />
		<input type="hidden" name="action" value="download" />
		<table>
		<tr><td colspan="2" class="topbottombar"><h2>'.i18n::translate('File Information').'</h2></td></tr>
		<tr>
		<td class="descriptionbox width50 wrap">'.i18n::translate('File Type').help_link('file_type').'</td>
		<td class="optionbox">';
		if ($TEXT_DIRECTION=='ltr') {
			$out .= '<input type="radio" name="filetype" checked="checked" value="gedcom" />&nbsp;GEDCOM<br/><input type="radio" name="filetype" value="gramps" DISABLED />&nbsp;Gramps XML <!-- GRAMPS doesn\'t work right now -->';
		} else {
			$out .= 'GEDCOM&nbsp;'.getLRM().'<input type="radio" name="filetype" checked="checked" value="gedcom" />'.getLRM().'<br />Gramps XML&nbsp;'.getLRM().'<input type="radio" name="filetype" value="gramps" />'.getLRM();
		}
		$out .= '
		</td></tr>

		<tr><td class="descriptionbox width50 wrap">'.i18n::translate('Zip File(s)').help_link('zip').'</td>
		<td class="optionbox"><input type="checkbox" name="Zip" value="yes" checked="checked" /></td></tr>

		<tr><td class="descriptionbox width50 wrap">'.i18n::translate('Include Media (automatically zips files)').help_link('include_media').'</td>
		<td class="optionbox"><input type="checkbox" name="IncludeMedia" value="yes" checked="checked" /></td></tr>
		';
		
		// Determine the Privatize options available to this user
		if (WT_USER_IS_ADMIN) {
			$radioPrivatizeNone = 'checked="checked" ';
			$radioPrivatizeVisitor = '';
			$radioPrivatizeUser = '';
			$radioPrivatizeGedadmin = '';
			$radioPrivatizeAdmin = '';
		} else if (WT_USER_GEDCOM_ADMIN) {
			$radioPrivatizeNone = 'DISABLED ';
			$radioPrivatizeVisitor = 'checked="checked" ';
			$radioPrivatizeUser = '';
			$radioPrivatizeGedadmin = '';
			$radioPrivatizeAdmin = 'DISABLED ';
		} else if (WT_USER_ID) {
			$radioPrivatizeNone = 'DISABLED ';
			$radioPrivatizeVisitor = 'checked="checked" ';
			$radioPrivatizeUser = '';
			$radioPrivatizeGedadmin = 'DISABLED ';
			$radioPrivatizeAdmin = 'DISABLED ';
		} else {
			$radioPrivatizeNone = 'DISABLED ';
			$radioPrivatizeVisitor = 'checked="checked" DISABLED ';
			$radioPrivatizeUser = 'DISABLED ';
			$radioPrivatizeGedadmin = 'DISABLED ';
			$radioPrivatizeAdmin = 'DISABLED ';
		}
		$out .= '
		<tr><td class="descriptionbox width50 wrap">'.i18n::translate('Apply privacy settings?').help_link('apply_privacy').'</td>
		<td class="list_value">
		<input type="radio" name="privatize_export" value="none" '.$radioPrivatizeNone.'/>&nbsp;'.i18n::translate('None').'<br />
		<input type="radio" name="privatize_export" value="visitor" '.$radioPrivatizeVisitor.'/>&nbsp;'.i18n::translate('Visitor').'<br />
		<input type="radio" name="privatize_export" value="user" '.$radioPrivatizeUser.'/>&nbsp;'.i18n::translate('Authenticated user').'<br />
		<input type="radio" name="privatize_export" value="gedadmin" '.$radioPrivatizeGedadmin.'/>&nbsp;'.i18n::translate('GEDCOM administrator').'<br />
		<input type="radio" name="privatize_export" value="admin" '.$radioPrivatizeAdmin.'/>&nbsp;'.i18n::translate('Site administrator').'
		</td></tr>

		<tr><td class="descriptionbox width50 wrap">'.i18n::translate('Convert from UTF-8 to ANSI (ISO-8859-1)').help_link('utf8_ansi').'</td>
		<td class="optionbox"><input type="checkbox" name="convert" value="yes" /></td></tr>

		<tr><td class="descriptionbox width50 wrap">'.i18n::translate('Remove custom PGV tags? (eg. _PGVU, _THUM)').help_link('remove_tags').'</td>
		<td class="optionbox"><input type="checkbox" name="remove" value="yes" checked="checked" />
		<input type="hidden" name="conv_path" value="'.getLRM(). $controller->conv_path. getLRM().'" /></td></tr>

		<tr><td class="topbottombar" colspan="2">
		<input type="button" value="'.i18n::translate('Cancel').'" onclick="cancelDownload();" />
		<input type="submit" value="'.i18n::translate('Download Now').'" />
		</form>';
		
		return $out;
	}

}
