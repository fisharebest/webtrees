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
namespace Fisharebest\Webtrees\Controller;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;

/**
 * Controller for full-page, themed HTML responses
 */
class PageController extends BaseController {
	/** @var string Most pages are not intended for robots */
	private $meta_robots = 'noindex,nofollow';

	/** @var string <head><title> $page_title </title></head> */
	private $page_title = WT_WEBTREES;

	/**
	 * What should this page show in the browser’s title bar?
	 *
	 * @param string  $page_title
	 *
	 * @return $this
	 */
	public function setPageTitle($page_title) {
		$this->page_title = $page_title;

		return $this;
	}

	/**
	 * Some pages will want to display this as <h1> $page_title </h1>
	 *
	 * @return string
	 */
	public function getPageTitle() {
		return $this->page_title;
	}

	/**
	 * Should robots index this page?
	 *
	 * @param string $meta_robots
	 *
	 * @return $this
	 */
	public function setMetaRobots($meta_robots) {
		$this->meta_robots = $meta_robots;

		return $this;
	}

	/**
	 * Should robots index this page?
	 *
	 * @return string
	 */
	public function getMetaRobots() {
		return $this->meta_robots;
	}

	/**
	 * Restrict access
	 *
	 * @param bool $condition
	 *
	 * @return $this
	 */
	public function restrictAccess($condition) {
		if ($condition !== true) {
			header('Location: ' . route('login', ['url' => Functions::getQueryUrl()]));
			exit;
		}

		return $this;
	}

	/**
	 * Print the page footer, using the theme
	 */
	public function pageFooter() {
		echo
			Theme::theme()->footerContainer() .
			'<script src="' . e(WT_ASSETS_URL . 'js/vendor.js') . '"></script>' .
			'<script src="' . e(WT_ASSETS_URL . 'js/webtrees.js') . '"></script>' .
			$this->getJavascript() .
			Theme::theme()->hookFooterExtraJavascript() .
			//DebugBar::renderHead() .
			//DebugBar::render() .
			'</body>' .
			'</html>';
	}

	/**
	 * Print the page header, using the theme
	 *
	 * @return $this
	 */
	public function pageHeader() {
		Theme::theme()->sendHeaders();
		echo Theme::theme()->doctype();
		echo Theme::theme()->html();
		echo Theme::theme()->head($this);
		echo Theme::theme()->bodyHeader();
		// We've displayed the header - display the footer automatically
		register_shutdown_function([$this, 'pageFooter']);

		return $this;
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return Individual
	 */
	public function getSignificantIndividual() {
		static $individual; // Only query the DB once.

		if (!$individual && $this->tree()->getUserPreference(Auth::user(), 'rootid')) {
			$individual = Individual::getInstance($this->tree()->getUserPreference(Auth::user(), 'rootid'), $this->tree());
		}
		if (!$individual && $this->tree()->getUserPreference(Auth::user(), 'gedcomid')) {
			$individual = Individual::getInstance($this->tree()->getUserPreference(Auth::user(), 'gedcomid'), $this->tree());
		}
		if (!$individual) {
			$individual = Individual::getInstance($this->tree()->getPreference('PEDIGREE_ROOT_ID'), $this->tree());
		}
		if (!$individual) {
			$individual = Individual::getInstance(
				Database::prepare(
					"SELECT MIN(i_id) FROM `##individuals` WHERE i_file=?"
				)->execute([$this->tree()->getTreeId()])->fetchOne(),
				$this->tree()
			);
		}
		if (!$individual) {
			// always return a record
			$individual = new Individual('I', '0 @I@ INDI', null, $this->tree());
		}

		return $individual;
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return Family
	 */
	public function getSignificantFamily() {
		$individual = $this->getSignificantIndividual();
		if ($individual) {
			foreach ($individual->getChildFamilies() as $family) {
				return $family;
			}
			foreach ($individual->getSpouseFamilies() as $family) {
				return $family;
			}
		}

		// always return a record
		return new Family('F', '0 @F@ FAM', null, $individual->getTree());
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return string
	 */
	public function getSignificantSurname() {
		return '';
	}
}
