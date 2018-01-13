<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;

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
	public function hasTabContent(Individual $individual) {
		return $individual->canEdit() || $this->getMedia($individual);
	}

	/**
	 * A greyed out tab has no actual content, but may perhaps have
	 * options to create content.
	 *
	 * @return bool
	 */
	public function isGrayedOut(Individual $individual) {
		return !$this->getMedia($individual);
	}

	/**
	 * Generate the HTML content of this tab.
	 *
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public function getTabContent(Individual $individual) {
		return view('tabs/album', [
			'media_list' => $this->getMedia($individual)
		]);
	}

	/**
	 * Get all facts containing media links for this person and their spouse-family records
	 *
	 * @param Individual $individual
	 *
	 * @return Media[]
	 */
	private function getMedia(Individual $individual) {
		if ($this->media_list === null) {
			// Use facts from this individual and all their spouses
			$facts = $individual->getFacts();
			foreach ($individual->getSpouseFamilies() as $family) {
				foreach ($family->getFacts() as $fact) {
					$facts[] = $fact;
				}
			}
			// Use all media from each fact
			$this->media_list = [];
			foreach ($facts as $fact) {
				// Don't show pending edits, as the user just sees duplicates
				if (!$fact->isPendingDeletion()) {
					preg_match_all('/(?:^1|\n\d) OBJE @(' . WT_REGEX_XREF . ')@/', $fact->getGedcom(), $matches);
					foreach ($matches[1] as $match) {
						$media = Media::getInstance($match, $individual->getTree());
						if ($media && $media->canShow()) {
							$this->media_list[] = $media;
						}
					}
				}
			}
			// If a media object is linked twice, only show it once
			$this->media_list = array_unique($this->media_list);
			// Sort these using _WT_OBJE_SORT
			$wt_obje_sort = [];
			foreach ($individual->getFacts('_WT_OBJE_SORT') as $fact) {
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
		return false;
	}
}
