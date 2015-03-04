<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class AlbumModule
 */
class AlbumModule extends Module implements ModuleTabInterface {
	private $media_list;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Album');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Album” module */ I18N::translate('An alternative to the “media” tab, and an enhanced image viewer.');
	}

	/** {@inheritdoc} */
	public function defaultTabOrder() {
		return 60;
	}

	/** {@inheritdoc} */
	public function hasTabContent() {
		return WT_USER_CAN_EDIT || $this->getMedia();
	}


	/** {@inheritdoc} */
	public function isGrayedOut() {
		return !$this->getMedia();
	}

	/** {@inheritdoc} */
	public function getTabContent() {
		global $WT_TREE, $controller;

		$html = '<div id="' . $this->getName() . '_content">';
		//Show Lightbox-Album header Links
		if (WT_USER_CAN_EDIT) {
			$html .= '<table class="facts_table"><tr><td class="descriptionbox rela">';
			// Add a new media object
			if ($WT_TREE->getPreference('MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
				$html .= '<span><a href="#" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid=' . $controller->record->getXref() . '\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=780,width=600\');return false;">';
				$html .= '<img src="' . Theme::theme()->assetUrl() . 'images/image_add.png" id="head_icon" class="icon" title="' . I18N::translate('Add a new media object') . '" alt="' . I18N::translate('Add a new media object') . '">';
				$html .= I18N::translate('Add a new media object');
				$html .= '</a></span>';
				// Link to an existing item
				$html .= '<span><a href="#" onclick="window.open(\'inverselink.php?linktoid=' . $controller->record->getXref() . '&linkto=person\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=300,width=450\');">';
				$html .= '<img src="' . Theme::theme()->assetUrl() . 'images/image_link.png" id="head_icon" class="icon" title="' . I18N::translate('Link to an existing media object') . '" alt="' . I18N::translate('Link to an existing media object') . '">';
				$html .= I18N::translate('Link to an existing media object');
				$html .= '</a></span>';
			}
			if (WT_USER_GEDCOM_ADMIN && $this->getMedia()) {
				// Popup Reorder Media
				$html .= '<span><a href="#" onclick="reorder_media(\'' . $controller->record->getXref() . '\')">';
				$html .= '<img src="' . Theme::theme()->assetUrl() . 'images/images.png" id="head_icon" class="icon" title="' . I18N::translate('Re-order media') . '" alt="' . I18N::translate('Re-order media') . '">';
				$html .= I18N::translate('Re-order media');
				$html .= '</a></span>';
			}
			$html .= '</td></tr></table>';
		}

		// Used when sorting media on album tab page
		$html .= '<table class="facts_table"><tr><td class="facts_value">'; // one-cell table - for presentation only
		$html .= '<ul class="album-list">';
		foreach ($this->getMedia() as $media) {
			//View Edit Menu ----------------------------------

			//Get media item Notes
			$haystack = $media->getGedcom();
			$needle   = '1 NOTE';
			$before   = substr($haystack, 0, strpos($haystack, $needle));
			$after    = substr(strstr($haystack, $needle), strlen($needle));
			$notes    = print_fact_notes($before . $needle . $after, 1, true);

			// Prepare Below Thumbnail  menu ----------------------------------------------------
			$menu = new Menu('<div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap">' . $media->getFullName() . '</div>');
			$menu->addClass('', 'submenu');

			// View Notes
			if (strpos($media->getGedcom(), "\n1 NOTE")) {
				$submenu = new Menu(I18N::translate('View notes'));
				// Notes Tooltip ----------------------------------------------------
				$submenu->setOnclick("modalNotes('" . Filter::escapeJs($notes) . "','" . I18N::translate('View notes') . "'); return false;");
				$submenu->addClass("submenuitem");
				$menu->addSubmenu($submenu);
			}
			//View Details
			$submenu = new Menu(I18N::translate('View details'), $media->getHtmlUrl());
			$submenu->addClass("submenuitem");
			$menu->addSubmenu($submenu);

			//View Sources
			foreach ($media->getFacts('SOUR') as $source_fact) {
				$source = $source_fact->getTarget();
				if ($source && $source->canShow()) {
					$submenu = new Menu(I18N::translate('Source') . ' – ' . $source->getFullName(), $source->getHtmlUrl());
					$submenu->addClass('submenuitem');
					$menu->addSubmenu($submenu);
				}
			}

			if (WT_USER_CAN_EDIT) {
				// Edit Media
				$submenu = new Menu(I18N::translate('Edit media'));
				$submenu->setOnclick("return window.open('addmedia.php?action=editmedia&amp;pid=" . $media->getXref() . "', '_blank', edit_window_specs);");
				$submenu->addClass("submenuitem");
				$menu->addSubmenu($submenu);
				if (Auth::isAdmin()) {
					if (Module::getModuleByName('GEDFact_assistant')) {
						$submenu = new Menu(I18N::translate('Manage links'));
						$submenu->setOnclick("return window.open('inverselink.php?mediaid=" . $media->getXref() . "&amp;linkto=manage', '_blank', find_window_specs);");
						$submenu->addClass("submenuitem");
						$menu->addSubmenu($submenu);
					} else {
						$submenu = new Menu(I18N::translate('Link this media object to an individual'), '#', 'menu-obje-link-indi');
						$submenu->setOnclick("return ilinkitem('" . $media->getXref() . "','person');");
						$submenu->addClass('submenuitem');
						$menu->addSubmenu($submenu);

						$submenu = new Menu(I18N::translate('Link this media object to a family'), '#', 'menu-obje-link-fam');
						$submenu->setOnclick("return ilinkitem('" . $media->getXref() . "','family');");
						$submenu->addClass('submenuitem');
						$menu->addSubmenu($submenu);

						$submenu = new Menu(I18N::translate('Link this media object to a source'), '#', 'menu-obje-link-sour');
						$submenu->setOnclick("return ilinkitem('" . $media->getXref() . "','source');");
						$submenu->addClass('submenuitem');
						$menu->addSubmenu($submenu);
					}
					$submenu = new Menu(I18N::translate('Unlink media'));
					$submenu->setOnclick("return unlink_media('" . I18N::translate('Are you sure you want to remove links to this media object?') . "', '" . $controller->record->getXref() . "', '" . $media->getXref() . "');");
					$submenu->addClass("submenuitem");
					$menu->addSubmenu($submenu);
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

	/**
	 * Get all facts containing media links for this person and their spouse-family records
	 *
	 * @return Media[]
	 */
	private function getMedia() {
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
				// Don't show pending edits, as the user just sees duplicates
				if (!$fact->isPendingDeletion()) {
					preg_match_all('/(?:^1|\n\d) OBJE @(' . WT_REGEX_XREF . ')@/', $fact->getGedcom(), $matches);
					foreach ($matches[1] as $match) {
						$media = Media::getInstance($match);
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
			usort($this->media_list, function(Media $x, Media $y) use ($wt_obje_sort) {
				return array_search($x->getXref(), $wt_obje_sort) - array_search($y->getXref(), $wt_obje_sort);
			});
		}
		return $this->media_list;
	}

	/** {@inheritdoc} */
	public function canLoadAjax() {
		return !Auth::isSearchEngine(); // Search engines cannot use AJAX
	}

	/** {@inheritdoc} */
	public function getPreLoadContent() {
		return '';
	}
}
