<?php
// Controller for full-page, themed HTML responses
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Controller_Page extends WT_Controller_Base {
	// Page header information
	const     DOCTYPE       ='<!DOCTYPE html>';  // HTML5
	private   $canonical_url='';
	private   $meta_robots  ='noindex,nofollow'; // Most pages are not intended for robots
	private   $page_title   =WT_WEBTREES;        // <head><title> $page_title </title></head>

	// Startup activity
	public function __construct() {
		parent::__construct();
		// Every page uses these scripts
		$this
			->addExternalJavascript(WT_JQUERY_URL)
			->addExternalJavascript(WT_JQUERYUI_URL)
			->addExternalJavascript(WT_WEBTREES_JS_URL);
	}

	// Shutdown activity
	public function __destruct() {
		// If we printed a header, automatically print a footer
		if ($this->page_header) {
			$this->pageFooter();
		}
	}

	// What should this page show in the browser’s title bar?
	public function setPageTitle($page_title) {
		$this->page_title=$page_title;
		return $this;
	}
	// Some pages will want to display this as <h2> $page_title </h2>
	public function getPageTitle() {
		return $this->page_title;
	}

	// What is the preferred URL for this page?
	public function setCanonicalUrl($canonical_url) {
		$this->canonical_url=$canonical_url;
		return $this;
	}

	// Should robots index this page?
	public function setMetaRobots($meta_robots) {
		$this->meta_robots=$meta_robots;
		return $this;
	}

	// Restrict access
	public function requireAdminLogin() {
		require_once WT_ROOT.'includes/functions/functions.php'; // for get_query_url
		if (!WT_USER_IS_ADMIN) {
			header('Location: '.WT_LOGIN_URL.'?url='.rawurlencode(get_query_url()));
			exit;
		}
		return $this;
	}

	// Restrict access
	public function requireManagerLogin($ged_id=WT_GED_ID) {
		require_once WT_ROOT.'includes/functions/functions.php'; // for get_query_url
		if (
			$ged_id==WT_GED_ID && !WT_USER_GEDCOM_ADMIN ||
			$ged_id!=WT_GED_ID && userGedcomAdmin(WT_USER_ID, $gedcom_id)
		) {
			header('Location: '.WT_LOGIN_URL.'?url='.rawurlencode(get_query_url()));
			exit;
		}
		return $this;
	}

	// Restrict access
	public function requireAcceptLogin() {
		require_once WT_ROOT.'includes/functions/functions.php'; // for get_query_url
		if (!WT_USER_CAN_ACCEPT) {
			header('Location: '.WT_LOGIN_URL.'?url='.rawurlencode(get_query_url()));
			exit;
		}
		return $this;
	}

	// Restrict access
	public function requireEditorLogin() {
		require_once WT_ROOT.'includes/functions/functions.php'; // for get_query_url
		if (!WT_USER_CAN_EDIT) {
			header('Location: '.WT_LOGIN_URL.'?url='.rawurlencode(get_query_url()));
			exit;
		}
		return $this;
	}

	// Restrict access
	public function requireMemberLogin() {
		require_once WT_ROOT.'includes/functions/functions.php'; // for get_query_url
		if (!WT_USER_ID) {
			header('Location: '.WT_LOGIN_URL.'?url='.rawurlencode(get_query_url()));
			exit;
		}
		return $this;
	}

	// Print the page header, using the theme
	public function pageHeader() {
		// Import global variables into the local scope, for the theme’s header.php
		global $SEARCH_SPIDER, $TEXT_DIRECTION, $REQUIRE_AUTHENTICATION, $headerfile, $view;

		// The title often includes the names of records, which may have markup
		// that cannot be used in the page title.
		$title=html_entity_decode(strip_tags($this->page_title), ENT_QUOTES, 'UTF-8');

		// Initialise variables for the theme’s header.php
		$LINK_CANONICAL   = $this->canonical_url;
		$META_ROBOTS      = $this->meta_robots;
		$META_DESCRIPTION = WT_GED_ID ? get_gedcom_setting(WT_GED_ID, 'META_DESCRIPTION') : '';
		if (!$META_DESCRIPTION) {
			$META_DESCRIPTION = WT_TREE_TITLE;
		}
		$META_GENERATOR = WT_WEBTREES . ' ' . WT_VERSION . ' - ' . WT_WEBTREES_URL;
		$META_TITLE     = WT_GED_ID ? get_gedcom_setting(WT_GED_ID, 'META_TITLE') : '';
		if ($META_TITLE) {
			$title .= ' - ' . $META_TITLE;
		}

		// This javascript needs to be loaded in the header, *before* the CSS.
		// All other javascript should be defered until the end of the page
		$javascript = '<script src="' . WT_MODERNIZR_URL . '"></script>';

		// Give Javascript access to some PHP constants
		$this->addInlineJavascript('
			var WT_STATIC_URL  = "' . WT_Filter::escapeJs(WT_STATIC_URL)             . '";
			var WT_THEME_DIR   = "' . WT_Filter::escapeJs(WT_THEME_DIR)              . '";
			var WT_MODULES_DIR = "' . WT_Filter::escapeJs(WT_MODULES_DIR)            . '";
			var WT_GEDCOM      = "' . WT_Filter::escapeJs(WT_GEDCOM)                 . '";
			var WT_GED_ID      = "' . WT_Filter::escapeJs(WT_GED_ID)                 . '";
			var WT_USER_ID     = "' . WT_Filter::escapeJs(WT_USER_ID)                . '";
			var textDirection  = "' . WT_Filter::escapeJs($TEXT_DIRECTION)           . '";
			var WT_SCRIPT_NAME = "' . WT_Filter::escapeJs(WT_SCRIPT_NAME)            . '";
			var WT_LOCALE      = "' . WT_Filter::escapeJs(WT_LOCALE)                 . '";
			var WT_CSRF_TOKEN  = "' . WT_Filter::escapeJs(WT_Filter::getCsrfToken()) . '";
		', self::JS_PRIORITY_HIGH);

		// Temporary fix for access to main menu hover elements on android/blackberry touch devices
		$this->addInlineJavascript('
			if(navigator.userAgent.match(/Android|PlayBook/i)) {
				jQuery("#main-menu > li > a").attr("href", "#");
				jQuery("a.icon_arrow").attr("href", "#");
			}
		');

		header('Content-Type: text/html; charset=UTF-8');
		require WT_ROOT.$headerfile;

		// Flush the output, so the browser can render the header and load javascript
		// while we are preparing data for the page
		if (ini_get('output_buffering')) {
			ob_flush();
		}
		flush();

		// Once we've displayed the header, we should no longer write session data.
		Zend_Session::writeClose();

		// We've displayed the header - display the footer automatically
		$this->page_header=true;
		return $this;
	}

	// Print the page footer, using the theme
	protected function pageFooter() {
		global $footerfile, $TEXT_DIRECTION, $view;

		if (WT_GED_ID) {
			require WT_ROOT.$footerfile;
		}

		if (WT_DEBUG_SQL) {
			echo WT_DB::getQueryLog();
		}
		echo $this->getJavascript();
		echo '</body></html>';

		return $this;
	}

	// Get significant information from this page, to allow other pages such as
	// charts and reports to initialise with the same records
	public function getSignificantIndividual() {
		static $individual; // Only query the DB once.

		if (!$individual && WT_USER_ROOT_ID) {
			$individual=WT_Individual::getInstance(WT_USER_ROOT_ID);
		}
		if (!$individual && WT_USER_GEDCOM_ID) {
			$individual=WT_Individual::getInstance(WT_USER_GEDCOM_ID);
		}
		if (!$individual) {
			$individual=WT_Individual::getInstance(get_gedcom_setting(WT_GED_ID, 'PEDIGREE_ROOT_ID'));
		}
		if (!$individual) {
			$individual=WT_Individual::getInstance(
				WT_DB::prepare(
					"SELECT MIN(i_id) FROM `##individuals` WHERE i_file=?"
				)->execute(array(WT_GED_ID))->fetchOne()
			);
		}
		if (!$individual) {
			// always return a record
			$individual=new WT_Individual('I', '0 @I@ INDI', null, WT_GED_ID);
		}
		return $individual;
	}
	public function getSignificantFamily() {
		$individual=$this->getSignificantIndividual();
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
	public function getSignificantSurname() {
		return '';
	}
}
