<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class lightbox_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Album');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the "Album" module */ WT_I18N::translate('An alternative to the “media” tab, and an enhanced image viewer.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
		case 'album':
			// TODO: these files should be methods in this class
			require WT_ROOT.WT_MODULES_DIR.$this->getName().'/'.$mod_action.'.php';
			break;
		default:
			header('HTTP/1.0 404 Not Found');
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_config';
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 60;
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		return WT_USER_CAN_EDIT || $this->get_media_count()>0;
	}

	// Implement WT_Module_Tab
	public function isGrayedOut() {
		return $this->get_media_count()==0;
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $controller, $sort_i;

		require_once WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lightbox_print_media.php';
		$html='<div id="'.$this->getName().'_content">';
		// If in re-order mode do not show header links, but instead, show drag and drop title.
		if (safe_GET_bool('reorder')) {
			$html.='<center><b>'.WT_I18N::translate('Drag-and-drop thumbnails to re-order media items').'</b></center>';
			$html.='<br>';
		} else {
			//Show Lightbox-Album header Links
			if (WT_USER_CAN_EDIT) {
				$html.='<table class="facts_table"><tr>';
				$html.='<td class="descriptionbox rela">';
				// Add a new media object
				if (get_gedcom_setting(WT_GED_ID, 'MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
					$html.='<span><a href="#" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid='.$controller->record->getXref().'\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=780,width=600\');return false;">';
					$html.='<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'lightbox/images/image_add.gif" id="head_icon" class="icon" title="'.WT_I18N::translate('Add a new media object').'" alt="'.WT_I18N::translate('Add a new media object').'">';
					$html.=WT_I18N::translate('Add a new media object');
					$html.='</a></span>';
					// Link to an existing item
					$html.='<span><a href="#" onclick="window.open(\'inverselink.php?linktoid='.$controller->record->getXref().'&linkto=person\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=300,width=450\');">';
					$html.= '<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'lightbox/images/image_link.gif" id="head_icon" class="icon" title="'.WT_I18N::translate('Link to an existing media object').'" alt="'.WT_I18N::translate('Link to an existing media object').'">';
					$html.=WT_I18N::translate('Link to an existing media object');
					$html.='</a></span>';
				}
				if (WT_USER_GEDCOM_ADMIN && $this->get_media_count()>1) {
					// Popup Reorder Media
					$html.='<span><a href="#" onclick="reorder_media(\''.$controller->record->getXref().'\')">';
					$html.='<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'lightbox/images/images.gif" id="head_icon" class="icon" title="'.WT_I18N::translate('Re-order media').'" alt="'.WT_I18N::translate('Re-order media').'">';
					$html.=WT_I18N::translate('Re-order media');
					$html.='</a></span>';
					$html.='</td>';
				}
				$html.='</tr></table>';
			}
		}
		$media_found = false;
		$reorder=safe_GET_bool('reorder');
		// Used when sorting media on album tab page
		if ($reorder==1) {
			$sort_i=0; // Used in sorting on lightbox_print_media.php page
			// This script saves the dranNdrop reordered info into a hidden form input element (name=order2)
			$js='function saveOrder() {
				// var sections = document.getElementsByClassName("section");
				var sections = $(".section");
				var order = "";
				sections.each(function(section) {
					order += Sortable.sequence(section) + ",";
					document.getElementById("ord2").value = order;
				});
			};';
			$controller->addInlineJavaScript($js);
			$html.='<form name="reorder_form" method="post" action="edit_interface.php">
				<input type="hidden" name="action" value="al_reorder_media_update">
				<input type="hidden" name="pid" value="'.$controller->record->getXref().'">
				<input type="hidden" id="ord2" name="order2" value="">
				<center>
					<button type="submit" title="'.WT_I18N::translate('Saves the sorted media to the database').'" onclick="saveOrder();" >'.WT_I18N::translate('Save').'</button>&nbsp;
					<button type="submit" title="'.WT_I18N::translate('Reset to the original order').'" onclick="document.reorder_form.action.value=\'al_reset_media_update\'; document.reorder_form.submit();">'.WT_I18N::translate('Reset').'</button>&nbsp;
					<button type="button" title="'.WT_I18N::translate('Quit and return').'" onClick="location.href=\''.WT_SCRIPT_NAME.'?pid='.$controller->record->getXref().'\'">'.WT_I18N::translate('Cancel').'</button>
				</center>
				</form>';
		}
		$html.='<table width="100%" cellpadding="0" border="0"><tr>';
		$html.='<td width="100%" valign="top" >';
		ob_start();
		lightbox_print_media($controller->record->getXref(), 0, true, 1); // map, painting, photo, tombstone)
		lightbox_print_media($controller->record->getXref(), 0, true, 2); // card, certificate, document, magazine, manuscript, newspaper
		lightbox_print_media($controller->record->getXref(), 0, true, 3); // electronic, fiche, film
		lightbox_print_media($controller->record->getXref(), 0, true, 4); // audio, book, coat, video, other
		lightbox_print_media($controller->record->getXref(), 0, true, 5); // footnotes
		return
			$html.
			ob_get_clean().
			'</td></tr></table></div>';
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		global $SEARCH_SPIDER;

		return !$SEARCH_SPIDER; // Search engines cannot use AJAX
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		$this->getJS();
	}

	// Implement WT_Module_Tab
	public function getJSCallback() {
		return 'CB_Init();';
	}

	protected $mediaCount = null;

	private function get_media_count() {
		global $controller;

		if ($this->mediaCount===null) {
			$ct = preg_match_all("/\d OBJE/", $controller->record->getGedcomRecord(), $match);
			foreach ($controller->record->getSpouseFamilies() as $sfam)
				$ct += preg_match_all("/\d OBJE/", $sfam->getGedcomRecord(), $match);
			$this->mediaCount = $ct;
		}
		return $this->mediaCount;
	}

	private function getJS() {
		global $controller, $TEXT_DIRECTION;

		$LB_MUSIC_FILE=get_module_setting('lightbox', 'LB_MUSIC_FILE', WT_STATIC_URL.WT_MODULES_DIR.'lightbox/music/music.mp3');
		$js='var CB_ImgDetails = "'.WT_I18N::translate('Details').'";
		var CB_Detail_Info = "'.WT_I18N::translate('View image details').'";
		var CB_ImgNotes = "'.WT_I18N::translate('Notes').'";
		var CB_Note_Info = "";
		var CB_Pause_SS = "'.WT_I18N::translate('Pause Slideshow').'";
		var CB_Start_SS = "'.WT_I18N::translate('Start Slideshow').'";
		var CB_Music = "'.WT_I18N::translate('Turn Music On/Off').'";
		var CB_Zoom_Off = "'.WT_I18N::translate('Disable Zoom').'";
		var CB_Zoom_On = "'.WT_I18N::translate('Zoom is enabled ... Use mousewheel or i and o keys to zoom in and out').'";
		var CB_Close_Win = "'.WT_I18N::translate('Close Lightbox window').'";
		var CB_Balloon = "false";'; // Notes Tooltip Balloon or not
		if ($TEXT_DIRECTION=='ltr') {
			$js.='var CB_Alignm = "left";'; // Notes LTR Tooltip Balloon Text align
		} else {
			$js.='var CB_Alignm = "right";'; // Notes RTL Tooltip Balloon Text align
		}
		$js.='var CB_ImgNotes2 = "'.WT_I18N::translate('Notes').'";'; // Notes RTL Tooltip for Full Image
		if ($LB_MUSIC_FILE == '') {
			$js.='var myMusic = null;';
		} else {
			$js.='var myMusic  = "'.$LB_MUSIC_FILE.'";';   // The music file
		}
		$js.='var CB_SlShowTime  = "'.get_module_setting('lightbox', 'LB_SS_SPEED', '6').'"; // Slide show timer
		var CB_Animation = "'.get_module_setting('lightbox', 'LB_TRANSITION', 'warp').'";'; // Next/Prev Image transition effect
		$controller->addInlineJavaScript($js)
			->addExternalJavaScript(WT_STATIC_URL.WT_MODULES_DIR.$this->getName().'/js/Sound.js')
			->addExternalJavaScript(WT_STATIC_URL.WT_MODULES_DIR.$this->getName().'/js/clearbox.js')
			->addExternalJavaScript(WT_STATIC_URL.WT_MODULES_DIR.$this->getName().'/js/wz_tooltip.js')
			->addExternalJavaScript(WT_STATIC_URL.WT_MODULES_DIR.$this->getName().'/js/tip_centerwindow.js');
		if ($TEXT_DIRECTION=='ltr') {
			$controller->addExternalJavaScript(WT_STATIC_URL.WT_MODULES_DIR.$this->getName().'/js/tip_balloon.js');
		} else {
			$controller->addExternalJavaScript(WT_STATIC_URL.WT_MODULES_DIR.$this->getName().'/js/tip_balloon_RTL.js');
		}
		return true;
	}

	static public function getMediaListMenu($mediaobject) {
		$html='<div id="lightbox-menu"><ul class="makeMenu lb-menu">';
		$menu = new WT_Menu(WT_I18N::translate('Edit Details'), '#', 'lb-image_edit');
		$menu->addOnclick("return window.open('addmedia.php?action=editmedia&amp;pid=".$mediaobject->getXref()."', '_blank', edit_window_specs);");
		$html.=$menu->getMenuAsList().'</ul><ul class="makeMenu lb-menu">';
		$menu = new WT_Menu(WT_I18N::translate('Set link'), '#', 'lb-image_link');
		$menu->addOnclick("return ilinkitem('".$mediaobject->getXref()."','person')");
		$submenu = new WT_Menu(WT_I18N::translate('To Person'), '#');
		$submenu->addOnclick("return ilinkitem('".$mediaobject->getXref()."','person')");
		$menu->addSubMenu($submenu);
		$submenu = new WT_Menu(WT_I18N::translate('To Family'), '#');
		$submenu->addOnclick("return ilinkitem('".$mediaobject->getXref()."','family')");
		$menu->addSubMenu($submenu);
		$submenu = new WT_Menu(WT_I18N::translate('To Source'), '#');
		$submenu->addOnclick("return ilinkitem('".$mediaobject->getXref()."','source')");
		$menu->addSubMenu($submenu);
		$html.=$menu->getMenuAsList().'</ul><ul class="makeMenu lb-menu">';
		$menu = new WT_Menu(WT_I18N::translate('View Details'), $mediaobject->getHtmlUrl(), 'lb-image_view');
		$html.=$menu->getMenuAsList();
		$html.='</ul></div>';
		return $html;
	}
}
