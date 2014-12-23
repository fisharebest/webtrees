<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
use WT\Theme;

/**
 * Class WT_Controller_Page Controller for full-page, themed HTML responses
 */
class WT_Controller_Page extends WT_Controller_Base {
	// Page header information
	private $canonical_url = '';
	private $meta_robots = 'noindex,nofollow'; // Most pages are not intended for robots
	private $page_title = WT_WEBTREES;        // <head><title> $page_title </title></head>

	/**
	 * Startup activity
	 */
	public function __construct() {
		parent::__construct();
		// Every page uses these scripts
		$this
			->addExternalJavascript(WT_JQUERY_URL)
			->addExternalJavascript(WT_JQUERYUI_URL)
			->addExternalJavascript(WT_WEBTREES_JS_URL);
	}

	/**
	 * Shutdown activity
	 */
	public function __destruct() {
		// If we printed a header, automatically print a footer
		if ($this->page_header) {
			echo $this->pageFooter();
		}
	}

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
	 * Some pages will want to display this as <h2> $page_title </h2>
	 *
	 * @return string
	 */
	public function getPageTitle() {
		return $this->page_title;
	}

	/**
	 * What is the preferred URL for this page?
	 *
	 * @param $canonical_url
	 *
	 * @return $this
	 */
	public function setCanonicalUrl($canonical_url) {
		$this->canonical_url = $canonical_url;

		return $this;
	}

	/**
	 * What is the preferred URL for this page?
	 *
	 * @return string
	 */
	public function getCanonicalUrl() {
		return $this->canonical_url;
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
	 * @param boolean $condition
	 *
	 * @return $this
	 */
	public function restrictAccess($condition) {
		if ($condition !== true) {
			header('Location: ' . WT_LOGIN_URL . '?url=' . rawurlencode(get_query_url()));
			exit;
		}

		return $this;
	}

	/**
	 * Print the page footer, using the theme
	 *
	 * @return string
	 */
	protected function pageFooter() {
		global $start_time;

		return
			Theme::theme()->footerContainer() .
			$this->getJavascript() .
			Theme::theme()->hookFooterExtraJavascript() .
			'</body>' .
			'</html>' . PHP_EOL .
			'<!-- webtrees: ' .  WT_VERSION . ' -->' .
			'<!-- Execution time: ' .  WT_I18N::number(microtime(true) - $start_time, 3) . ' seconds -->' .
			'<!-- Memory: ' .  WT_I18N::number(memory_get_peak_usage(true)/1024) . ' KB -->' .
			'<!-- SQL queries: ' .  WT_I18N::number(WT_DB::getQueryCount()) . ' -->';
	}

	/**
	 * Print the page header, using the theme
	 *
	 * @param string $view 'simple' or ''
	 *
	 * @return $this
	 */
	public function pageHeader($view = '') {
		global $TEXT_DIRECTION;

		// Give Javascript access to some PHP constants
		$this->addInlineJavascript('
			var WT_STATIC_URL  = "' . WT_Filter::escapeJs(WT_STATIC_URL) . '";
			var WT_MODULES_DIR = "' . WT_Filter::escapeJs(WT_MODULES_DIR) . '";
			var WT_GEDCOM      = "' . WT_Filter::escapeJs(WT_GEDCOM) . '";
			var WT_GED_ID      = "' . WT_Filter::escapeJs(WT_GED_ID) . '";
			var textDirection  = "' . WT_Filter::escapeJs($TEXT_DIRECTION) . '";
			var WT_SCRIPT_NAME = "' . WT_Filter::escapeJs(WT_SCRIPT_NAME) . '";
			var WT_LOCALE      = "' . WT_Filter::escapeJs(WT_LOCALE) . '";
			var WT_CSRF_TOKEN  = "' . WT_Filter::escapeJs(WT_Filter::getCsrfToken()) . '";
		', self::JS_PRIORITY_HIGH);

		// Temporary fix for access to main menu hover elements on android/blackberry touch devices
		$this->addInlineJavascript('
			if(navigator.userAgent.match(/Android|PlayBook/i)) {
				jQuery(".primary-menu > li > a").attr("href", "#");
			}
		');

		Theme::theme()->sendHeaders();
		echo Theme::theme()->doctype();
		echo Theme::theme()->html();
		echo Theme::theme()->head($this);

		switch ($view) {
		case 'simple':
			echo Theme::theme()->bodyHeaderPopupWindow();
			break;
		default:
			echo Theme::theme()->bodyHeader();
			break;
		}

		// Flush the output, so the browser can render the header and load javascript
		// while we are preparing data for the page
		if (ini_get('output_buffering')) {
			ob_flush();
		}
		flush();

		// Once we've displayed the header, we should no longer write session data.
		Zend_Session::writeClose();

		// We've displayed the header - display the footer automatically
		$this->page_header = true;

		return $this;
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return WT_Individual
	 */
	public function getSignificantIndividual() {
		global $WT_TREE;

		static $individual; // Only query the DB once.

		if (!$individual && WT_USER_ROOT_ID) {
			$individual = WT_Individual::getInstance(WT_USER_ROOT_ID);
		}
		if (!$individual && WT_USER_GEDCOM_ID) {
			$individual = WT_Individual::getInstance(WT_USER_GEDCOM_ID);
		}
		if (!$individual) {
			$individual = WT_Individual::getInstance($WT_TREE->getPreference('PEDIGREE_ROOT_ID'));
		}
		if (!$individual) {
			$individual = WT_Individual::getInstance(
				WT_DB::prepare(
					"SELECT MIN(i_id) FROM `##individuals` WHERE i_file=?"
				)->execute(array(WT_GED_ID))->fetchOne()
			);
		}
		if (!$individual) {
			// always return a record
			$individual = new WT_Individual('I', '0 @I@ INDI', null, WT_GED_ID);
		}

		return $individual;
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return WT_Family
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
		return new WT_Family('F', '0 @F@ FAM', null, WT_GED_ID);
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
