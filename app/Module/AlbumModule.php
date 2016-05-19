<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Theme;

/**
 * Class AlbumModule
 */
class AlbumModule extends AbstractModule implements ModuleTabInterface {
	/** @var Media[] List of media objects. */
	private $media_list;

	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Album');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “Album” module */ I18N::translate('An alternative to the “media” tab, and an enhanced image viewer.');
	}

	/**
	 * The user can re-arrange the tab order, but until they do, this
	 * is the order in which tabs are shown.
	 *
	 * @return int
	 */
	public function defaultTabOrder() {
		return 60;
	}

	/**
	 * Is this tab empty? If so, we don't always need to display it.
	 *
	 * @return bool
	 */
	public function hasTabContent() {
		global $WT_TREE;

		return Auth::isEditor($WT_TREE) || $this->getMedia();
	}

	/**
	 * A greyed out tab has no actual content, but may perhaps have
	 * options to create content.
	 *
	 * @return bool
	 */
	public function isGrayedOut() {
		return !$this->getMedia();
	}

	/**
	 * Generate the HTML content of this tab.
	 *
	 * @return string
	 */
	public function getTabContent() {
		global $WT_TREE, $controller;

		$html = '<div id="' . $this->getName() . '_content">';
		//Show Lightbox-Album header Links
		if (Auth::isEditor($WT_TREE)) {
			$html .= '<table class="facts_table"><tr class="noprint"><td class="descriptionbox rela">';
			// Add a media object
			if ($WT_TREE->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($WT_TREE)) {
				$html .= '<span><a href="#" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid=' . $controller->record->getXref() . '\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=780,width=600\');return false;">';
				$html .= '<img src="' . Theme::theme()->assetUrl() . 'images/image_add.png" id="head_icon" class="icon" title="' . I18N::translate('Add a media object') . '" alt="' . I18N::translate('Add a media object') . '">';
				$html .= I18N::translate('Add a media object');
				$html .= '</a></span>';
				// Link to an existing item
				$html .= '<span><a href="#" onclick="window.open(\'inverselink.php?linktoid=' . $controller->record->getXref() . '&linkto=person\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=300,width=450\');">';
				$html .= '<img src="' . Theme::theme()->assetUrl() . 'images/image_link.png" id="head_icon" class="icon" title="' . I18N::translate('Link to an existing media object') . '" alt="' . I18N::translate('Link to an existing media object') . '">';
				$html .= I18N::translate('Link to an existing media object');
				$html .= '</a></span>';
			}
			if (Auth::isManager($WT_TREE) && $this->getMedia()) {
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
			$notes    = FunctionsPrint::printFactNotes($before . $needle . $after, 1, true);

			// Prepare Below Thumbnail  menu ----------------------------------------------------
			$menu = new Menu('<div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap">' . $media->getFullName() . '</div>');
			$menu->addClass('', 'submenu');

			// View Notes
			if (strpos($media->getGedcom(), "\n1 NOTE")) {
				$submenu = new Menu(I18N::translate('View the notes'), '#', '', array(
					'onclick' => 'modalNotes("' . Filter::escapeJs($notes) . '","' . I18N::translate('View the notes') . '"); return false;',
				));
				$submenu->addClass("submenuitem");
				$menu->addSubmenu($submenu);
			}
			//View Details
			$submenu = new Menu(I18N::translate('View the details'), $media->getHtmlUrl());
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

			if (Auth::isEditor($media->getTree())) {
				// Edit Media
				$submenu = new Menu(I18N::translate('Edit the media object'), '#', '', array(
					'onclick' => 'return window.open("addmedia.php?action=editmedia&pid=' . $media->getXref() . '", "_blank", edit_window_specs);',
				));
				$submenu->addClass("submenuitem");
				$menu->addSubmenu($submenu);
				if (Auth::isAdmin()) {
					if (Module::getModuleByName('GEDFact_assistant')) {
						$submenu = new Menu(I18N::translate('Manage the links'), '#', '', array(
							'onclick' => 'return window.open("inverselink.php?mediaid=' . $media->getXref() . '&linkto=manage", "_blank", find_window_specs);',
						));
						$submenu->addClass("submenuitem");
						$menu->addSubmenu($submenu);
					} else {
						$submenu = new Menu(I18N::translate('Link this media object to an individual'), '#', 'menu-obje-link-indi', array(
							'onclick' => 'return ilinkitem("' . $media->getXref() . '","person");',
						));
						$submenu->addClass('submenuitem');
						$menu->addSubmenu($submenu);

						$submenu = new Menu(I18N::translate('Link this media object to a family'), '#', 'menu-obje-link-fam', array(
							'onclick' => 'return ilinkitem("' . $media->getXref() . '","family");',
						));
						$submenu->addClass('submenuitem');
						$menu->addSubmenu($submenu);

						$submenu = new Menu(I18N::translate('Link this media object to a source'), '#', 'menu-obje-link-sour', array(
							'onclick' => 'return ilinkitem("' . $media->getXref() . '","source");',
						));
						$submenu->addClass('submenuitem');
						$menu->addSubmenu($submenu);
					}
					$submenu = new Menu(I18N::translate('Unlink the media object'), '#', '', array(
						'onclick' => 'return unlink_media("' . I18N::translate('Are you sure you want to remove links to this media object?') . '", "' . $controller->record->getXref() . '", "' . $media->getXref() . '");',
					));
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
						$media = Media::getInstance($match, $controller->record->getTree());
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
			usort($this->media_list, function (Media $x, Media $y) use ($wt_obje_sort) {
				return array_search($x->getXref(), $wt_obje_sort) - array_search($y->getXref(), $wt_obje_sort);
			});
		}

		return $this->media_list;
	}

	/**
	 * Can this tab load asynchronously?
	 *
	 * @return bool
	 */
	public function canLoadAjax() {
		return !Auth::isSearchEngine(); // Search engines cannot use AJAX
	}

	/**
	 * Any content (e.g. Javascript) that needs to be rendered before the tabs.
	 *
	 * This function is probably not needed, as there are better ways to achieve this.
	 *
	 * @return string
	 */
	public function getPreLoadContent() {
		return '';
	}
}
