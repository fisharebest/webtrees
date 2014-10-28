<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

class lightbox_WT_Module extends WT_Module implements WT_Module_Tab {
	private $media_list;

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
		return WT_USER_CAN_EDIT || $this->get_media();
	}


	// Implement WT_Module_Tab
	public function isGrayedOut() {
		return !$this->get_media();
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $WT_TREE, $controller;

		$html='<div id="'.$this->getName().'_content">';
		//Show Lightbox-Album header Links
		if (WT_USER_CAN_EDIT) {
			$html.='<table class="facts_table"><tr><td class="descriptionbox rela">';
			// Add a new media object
			if ($WT_TREE->getPreference('MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
				$html .= '<span><a href="#" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid='.$controller->record->getXref().'\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=780,width=600\');return false;">';
				$html .= '<img src="'.WT_CSS_URL.'images/image_add.png" id="head_icon" class="icon" title="'.WT_I18N::translate('Add a new media object').'" alt="'.WT_I18N::translate('Add a new media object').'">';
				$html .= WT_I18N::translate('Add a new media object');
				$html .= '</a></span>';
				// Link to an existing item
				$html .= '<span><a href="#" onclick="window.open(\'inverselink.php?linktoid='.$controller->record->getXref().'&linkto=person\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=300,width=450\');">';
				$html .= '<img src="'.WT_CSS_URL.'images/image_link.png" id="head_icon" class="icon" title="'.WT_I18N::translate('Link to an existing media object').'" alt="'.WT_I18N::translate('Link to an existing media object').'">';
				$html .= WT_I18N::translate('Link to an existing media object');
				$html .= '</a></span>';
			}
			if (WT_USER_GEDCOM_ADMIN && $this->get_media()) {
				// Popup Reorder Media
				$html .= '<span><a href="#" onclick="reorder_media(\''.$controller->record->getXref().'\')">';
				$html .= '<img src="'.WT_CSS_URL.'images/images.png" id="head_icon" class="icon" title="'.WT_I18N::translate('Re-order media').'" alt="'.WT_I18N::translate('Re-order media').'">';
				$html .= WT_I18N::translate('Re-order media');
				$html .= '</a></span>';
			}
			$html .= '</td></tr></table>';
		}

		// Used when sorting media on album tab page
		$html .= '<table class="facts_table"><tr><td class="facts_value">'; // one-cell table - for presentation only
		$html .= '<ul class="album-list">';
		foreach ($this->get_media() as $media) {
			//View Edit Menu ----------------------------------

			//Get media item Notes
			$haystack = $media->getGedcom();
			$needle   = '1 NOTE';
			$before   = substr($haystack, 0, strpos($haystack, $needle));
			$after    = substr(strstr($haystack, $needle), strlen($needle));
			$notes    = print_fact_notes($before . $needle . $after, 1, true);

			// Prepare Below Thumbnail  menu ----------------------------------------------------
			$menu = new WT_Menu('<div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap">' . $media->getFullName() . '</div>');
			$menu->addClass('', 'submenu');

			// View Notes
			if (strpos($media->getGedcom(), "\n1 NOTE")) {
				$submenu = new WT_Menu(WT_I18N::translate('View notes'));
				// Notes Tooltip ----------------------------------------------------
				$submenu->addOnclick("modalNotes('". WT_Filter::escapeJs($notes) . "','". WT_I18N::translate('View notes') . "'); return false;");
				$submenu->addClass("submenuitem");
				$menu->addSubMenu($submenu);
			}
			//View Details
			$submenu = new WT_Menu(WT_I18N::translate('View details'), $media->getHtmlUrl());
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
					$submenu = new WT_Menu($source->getFullName(), $source->getHtmlUrl());
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
				$submenu->addOnclick("return window.open('addmedia.php?action=editmedia&amp;pid=".$media->getXref()."', '_blank', edit_window_specs);");
				$submenu->addClass("submenuitem");
				$menu->addSubMenu($submenu);
				if (Auth::isAdmin()) {
					// Manage Links
					if (array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
						$submenu = new WT_Menu(WT_I18N::translate('Manage links'));
						$submenu->addOnclick("return window.open('inverselink.php?mediaid=".$media->getXref()."&amp;linkto=manage', '_blank', find_window_specs);");
						$submenu->addClass("submenuitem");
						$menu->addSubMenu($submenu);
					} else {
						$submenu = new WT_Menu(WT_I18N::translate('Set link'), '#', null, 'right', 'right');
						$submenu->addClass('submenuitem', 'submenu');

						$ssubmenu = new WT_Menu(WT_I18N::translate('To individual'));
						$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid=".$media->getXref()."&amp;linkto=person', '_blank', find_window_specs);");
						$ssubmenu->addClass('submenuitem', 'submenu');
						$submenu->addSubMenu($ssubmenu);

						$ssubmenu = new WT_Menu(WT_I18N::translate('To family'));
						$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid=".$media->getXref()."&amp;linkto=family', '_blank', find_window_specs);");
						$ssubmenu->addClass('submenuitem', 'submenu');
						$submenu->addSubMenu($ssubmenu);

						$ssubmenu = new WT_Menu(WT_I18N::translate('To source'));
						$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid=".$media->getXref()."&amp;linkto=source', '_blank', find_window_specs);");
						$ssubmenu->addClass('submenuitem', 'submenu');
						$submenu->addSubMenu($ssubmenu);

						$menu->addSubMenu($submenu);
					}
					// Unlink media
					$submenu = new WT_Menu(WT_I18N::translate('Unlink media'));
					$submenu->addOnclick("return unlink_media('" . WT_I18N::translate('Are you sure you want to remove links to this media object?') . "', '" . $controller->record->getXref() . "', '" . $media->getXref() . "');");
					$submenu->addClass("submenuitem");
					$menu->addSubMenu($submenu);
				}
			}
			$html .= '<li class="album-list-item">';
			$html .= '<div class="album-image">' . $media->displayImage() . '</div>';
			$html .= '<div class="album-title">' . $menu->getMenu() . '</div>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</td></tr></table>';
		return $html;
	}

	// Get all facts containing media links for this person and their spouse-family records
	private function get_media() {
		global $controller;

		if ($this->media_list === null) {
			// Use facts from this individual and all their spouses
			$facts = $controller->record->getFacts();
			foreach ($controller->record->getSpouseFamilies() as $family) {
				foreach ($family->getFacts() as $fact) {
					$facts[] = $fact;
				}
			}
			// Use all media from each fact
			$this->media_list = array();
			foreach ($facts as $fact) {
				if (!$fact->isPendingDeletion()) { // Don't show pending edits, as the user just sees duplicates
					preg_match_all('/(?:^1|\n\d) OBJE @(' . WT_REGEX_XREF . ')@/', $fact->getGedcom(), $matches);
					foreach ($matches[1] as $match) {
						$media = WT_Media::getInstance($match);
						if ($media && $media->canShow()) {
							$this->media_list[] = $media;
						}
					}
				}
			}
			// If a media object is linked twice, only show it once
			$this->media_list = array_unique($this->media_list);
			// Sort these using _WT_OBJE_SORT
			$wt_obje_sort = array();
			foreach ($controller->record->getFacts('_WT_OBJE_SORT') as $fact) {
				$wt_obje_sort[] = trim($fact->getValue(), '@');
			}
			usort($this->media_list, function(WT_Media $x, WT_Media $y) use ($wt_obje_sort) {
				return array_search($x->getXref(), $wt_obje_sort) - array_search($y->getXref(), $wt_obje_sort);
			});
		}
		return $this->media_list;
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
