<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

class lightbox_WT_Module extends WT_Module implements WT_Module_Tab {
	private $facts;

	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Album');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Album” module */ WT_I18N::translate('An alternative to the “media” tab, and an enhanced image viewer.');
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 60;
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		return WT_USER_CAN_EDIT || $this->get_facts();
	}

	
	// Implement WT_Module_Tab
	public function isGrayedOut() {
		return !$this->get_facts();
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $controller, $sort_i;

		// Group the media objects by type.
		// (The old code further grouped them into 5 categories - was this really wanted??)
		$media_list = array();
		foreach ($this->get_facts() as $fact) {
			preg_match_all('/(?:^1|\n\d) OBJE @(' . WT_REGEX_XREF . ')@/', $fact->getGedcom(), $matches);
			foreach ($matches[1] as $match) {
				$media = WT_Media::getInstance($match);
				if ($media && $media->canShow()) {
					$media_list[] = array('fact' => $fact, 'media' => $media);
				}
			}
		}
		// TODO: sort these using _WT_OBJE_SORT

		$html='<div id="'.$this->getName().'_content">';
		//Show Lightbox-Album header Links
		if (WT_USER_CAN_EDIT) {
			$html.='<table class="facts_table"><tr>';
			$html.='<td class="descriptionbox rela">';
			// Add a new media object
			if (get_gedcom_setting(WT_GED_ID, 'MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
				$html.='<span><a href="#" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid='.$controller->record->getXref().'\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=780,width=600\');return false;">';
				$html.='<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'lightbox/images/image_add.png" id="head_icon" class="icon" title="'.WT_I18N::translate('Add a new media object').'" alt="'.WT_I18N::translate('Add a new media object').'">';
				$html.=WT_I18N::translate('Add a new media object');
				$html.='</a></span>';
				// Link to an existing item
				$html.='<span><a href="#" onclick="window.open(\'inverselink.php?linktoid='.$controller->record->getXref().'&linkto=person\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=300,width=450\');">';
				$html.= '<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'lightbox/images/image_link.png" id="head_icon" class="icon" title="'.WT_I18N::translate('Link to an existing media object').'" alt="'.WT_I18N::translate('Link to an existing media object').'">';
				$html.=WT_I18N::translate('Link to an existing media object');
				$html.='</a></span>';
			}
			if (WT_USER_GEDCOM_ADMIN && $this->get_facts()) {
				// Popup Reorder Media
				$html.='<span><a href="#" onclick="reorder_media(\''.$controller->record->getXref().'\')">';
				$html.='<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'lightbox/images/images.png" id="head_icon" class="icon" title="'.WT_I18N::translate('Re-order media').'" alt="'.WT_I18N::translate('Re-order media').'">';
				$html.=WT_I18N::translate('Re-order media');
				$html.='</a></span>';
				$html.='</td>';
			}
			$html.='</tr></table>';
		}
		$media_found = false;

		// Used when sorting media on album tab page
		$html .= '<table width="100%" cellpadding="0" border="0">';
		$html .= '<tr>';
		$html .= '<td class="facts_value">';
		$html .= '<div class="thumbcontainer">';
		$html .= '<ul class="thumblist">';
		foreach ($media_list as $media_list_item) {
			if ($media_list_item['fact']->isNew()) {
				$html .= '<li class="li_new">';
			} elseif ($media_list_item['fact']->isOld()) {
				$html .= '<li class="li_old">';
			} else {
				$html .= '<li class="li_norm">';
			}
			// ...and now the actual image
			if (strpos($media_list_item['media']->getFilename(), 'http://maps.google.')===0) {
				$html .= '<table width="10px" style="margin-top:-90px;" class="pic" border="0"><tr>';
			} else {
				$html .= '<table width="10px" class="pic" border="0"><tr>';
			}
			$html .= '<td align="center" rowspan="2">';
			$html .= '<div style="width:1px; height:100px;"></div>';
			$html .= '</td>';
			$html .= '<td colspan="3" valign="middle" align="center">';
			$html .= $media_list_item['media']->displayImage();
			$html .= '</td></tr>';

			//View Edit Menu ----------------------------------
			$html .= '<tr>';
			$html .= '<td width="5px"></td>';
			$html .= '<td valign="bottom" align="center" class="nowrap">';



			//Get media item Notes
			$haystack = $media_list_item['media']->getGedcom();
			$needle   = '1 NOTE';
			$before   = substr($haystack, 0, strpos($haystack, $needle));
			$after    = substr(strstr($haystack, $needle), strlen($needle));
			$final    = $before.$needle.$after;
			$notes    = htmlspecialchars(addslashes(print_fact_notes($final, 1, true, true)), ENT_QUOTES);

			// Prepare Below Thumbnail  menu ----------------------------------------------------
			$mtitle = '<div style="max-width:120px;overflow:hidden;text-overflow:ellipsis;">' . $media_list_item['media']->getFullName() . '</div>';
			$menu = new WT_Menu();
			$menu->addLabel($mtitle, 'right');

			if ($media_list_item['fact']->isOld()) {
				// Do not print menu if item has changed and this is the old item
			} else {
				// Continue printing menu
				$menu->addClass('', 'submenu');

				// View Notes
				if (strpos($media_list_item['media']->getGedcom(), "\n1 NOTE")) {
					$submenu = new WT_Menu(WT_I18N::translate('View Notes'), '#');
					// Notes Tooltip ----------------------------------------------------
					$submenu->addOnclick("modalNotes('". $notes ."','". WT_I18N::translate('View Notes') ."'); return false;");
					$submenu->addClass("submenuitem");
					$menu->addSubMenu($submenu);
				}
				//View Details
				$submenu = new WT_Menu(WT_I18N::translate('View Details'), WT_SERVER_NAME.WT_SCRIPT_PATH . "mediaviewer.php?mid=".$media_list_item['media']->getXref().'&amp;ged='.WT_GEDURL, 'right');
				$submenu->addClass("submenuitem");
				$menu->addSubMenu($submenu);

				//View Sources
				$source_menu = null;
				foreach ($media->getFacts('SOUR') as $source_fact) {
					$source = $source_fact->getTarget();
					if ($source && $source->canShow()) {
						if (!$source_menu) {
							// Group sources under a top level menu
							$source_menu = new WT_Menu(WT_I18N::translate('Sources'), '#', null, 'right', 'right');
							$source_menu->addClass('submenuitem', 'submenu');
						}
						//now add a link to the actual source as a submenu
						$submenu = new WT_Menu(new WT_Menu(strip_tags($source->getFullName()), $source->getHtmlUrl()));
						$submenu->addClass('submenuitem', 'submenu');
						$source_menu->addSubMenu($submenu);
					}
				}
				if ($source_menu) {
					$menu->addSubMenu($source_menu);
				}

				if (WT_USER_CAN_EDIT) {
					// Edit Media
					$submenu = new WT_Menu(WT_I18N::translate('Edit media'));
					$submenu->addOnclick("return window.open('addmedia.php?action=editmedia&amp;pid=".$media_list_item['media']->getXref()."', '_blank', edit_window_specs);");
					$submenu->addClass("submenuitem");
					$menu->addSubMenu($submenu);
					if (WT_USER_IS_ADMIN) {
						// Manage Links
						if (array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
							$submenu = new WT_Menu(WT_I18N::translate('Manage links'));
							$submenu->addOnclick("return window.open('inverselink.php?mediaid=".$media_list_item['media']->getXref()."&amp;linkto=manage', '_blank', find_window_specs);");
							$submenu->addClass("submenuitem");
							$menu->addSubMenu($submenu);
						} else {
							$submenu = new WT_Menu(WT_I18N::translate('Set link'), '#', null, 'right', 'right');
							$submenu->addClass('submenuitem', 'submenu');

							$ssubmenu = new WT_Menu(WT_I18N::translate('To Person'));
							$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid=".$media_list_item['media']->getXref()."&amp;linkto=person', '_blank', find_window_specs);");
							$ssubmenu->addClass('submenuitem', 'submenu');
							$submenu->addSubMenu($ssubmenu);

							$ssubmenu = new WT_Menu(WT_I18N::translate('To Family'));
							$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid=".$media_list_item['media']->getXref()."&amp;linkto=family', '_blank', find_window_specs);");
							$ssubmenu->addClass('submenuitem', 'submenu');
							$submenu->addSubMenu($ssubmenu);

							$ssubmenu = new WT_Menu(WT_I18N::translate('To Source'));
							$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid=".$media_list_item['media']->getXref()."&amp;linkto=source', '_blank', find_window_specs);");
							$ssubmenu->addClass('submenuitem', 'submenu');
							$submenu->addSubMenu($ssubmenu);

							$menu->addSubMenu($submenu);
						}
						// Unlink Media
						$submenu = new WT_Menu(WT_I18N::translate('Unlink Media'));
						$submenu->addOnclick("return delete_fact('".$media_list_item['media']->getXref()."', 'OBJE', '".$media_list_item['media']->getXref()."', '".WT_I18N::translate('Are you sure you want to delete this fact?')."');");
						$submenu->addClass("submenuitem");
						$menu->addSubMenu($submenu);
					}
				}
				$html .= $menu->getMenu();
			}
			$html .= '</td>';
			$html .= '<td width="5px"></td>';
			$html .= '</tr>';
			$html .= '</table>';
			$html .= '<input type="hidden" name="order1[' . $media_list_item['media']->getXref() . ']" value="' . $sort_i . '">';
			$sort_i++;
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '<div class="clearlist"></div>';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table></div>';
		return $html;
	}

	// Get all facts containing media links for this person and their spouse-family records
	private function get_facts() {
		global $controller;

		if ($this->facts === null) {
			$facts = $controller->record->getFacts();
			foreach ($controller->record->getSpouseFamilies() as $family) {
				if ($family->canShow()) {
					foreach ($family->getFacts() as $fact) {
						$facts[] = $fact;
					}
				}
			}
			$this->facts = array();
			foreach ($facts as $fact) {
				if (preg_match('/(?:^1|\n\d) OBJE @' . WT_REGEX_XREF . '@/', $fact->getGedcom())) {
					$this->facts[] = $fact;
				}
			}
		}
		return $this->facts;
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		global $SEARCH_SPIDER;

		return !$SEARCH_SPIDER; // Search engines cannot use AJAX
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		return '';
	}
}
