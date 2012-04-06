<?php
// Base controller for all other controllers
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

class WT_Controller_Base {
	// Page header information
	const     DOCTYPE       ='<!DOCTYPE html>';  // HTML5
	private   $canonical_url='';
	private   $meta_robots  ='noindex,nofollow'; // Most pages are not intended for robots
	protected $page_header  =false;              // Have we printed a page header?
	private   $page_title   =WT_WEBTREES;        // <head><title> $page_title </title></head>

	// The controller accumulates JavaScript (inline and external), and renders it in the footer
	const JS_PRIORITY_HIGH   = 0;
	const JS_PRIORITY_NORMAL = 1;
	const JS_PRIORITY_LOW    = 2;
	private $inline_javascript=array(
		self::JS_PRIORITY_HIGH  =>array(),
		self::JS_PRIORITY_NORMAL=>array(),
		self::JS_PRIORITY_LOW   =>array(),
	);
	private $external_javascript=array();

	// Startup activity
	public function __construct() {
		// Every page uses jQuery and jQueryUI
		//$this->addExternalJavaScript(WT_JQUERY_URL);
		//$this->addExternalJavaScript(WT_JQUERYUI_URL);
	}

	// Shutdown activity
	public function __destruct() {
		// If we printed a header, automatically print a footer
		if ($this->page_header) {
			$this->pageFooter();
		}
	}

	// What should this page show in the browser's title bar?
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

	// Make a list of external JavaScript, so we can render them in the footer
	public function addExternalJavaScript($script_name) {
		$this->external_javascript[$script_name]=true;
		return $this;
	}

	// Make a list of inline JavaScript, so we can render them in the footer
	// NOTE: there is no need to use "jQuery(document).ready(function(){...})", etc.
	// as this JavaScript won't be inserted until the very end of the page.
	public function addInlineJavaScript($script, $priority=self::JS_PRIORITY_NORMAL) {
		if (WT_DEBUG) {
			/* Show where the JS was added */
			$backtrace=debug_backtrace();
			$script='/* '.$backtrace[0]['file'].':'.$backtrace[0]['line'].' */'.PHP_EOL.$script;
		}
		$tmp=&$this->inline_javascript[$priority];
		$tmp[]=$script;
		return $this;
	}

	// We've collected up JavaScript fragments while rendering the page.
	// Now display them.
	public function getJavaScript() {
		// Load external libraries first
		$html='';
		foreach (array_keys($this->external_javascript) as $script_name) {
			$html.=PHP_EOL.'<script type="text/javascript" src="'.htmlspecialchars($script_name).'"></script>';
		}
		// Process the scripts, in priority order
		if ($this->inline_javascript) {
			$html.='<script>';
			foreach ($this->inline_javascript as $scripts) {
				foreach ($scripts as $script) {
					$html.=$script.PHP_EOL;
				}
			}
			$html.='</script>';
		}

		$this->inline_javascript=array(
			self::JS_PRIORITY_HIGH  =>array(),
			self::JS_PRIORITY_NORMAL=>array(),
			self::JS_PRIORITY_LOW   =>array(),
		);
		$this->external_javascript=array();

		return $html;
	}

	// Print the page header, using the theme
	public function pageHeader() {
		// Import global variables into the local scope, for the theme's header.php
		global $BROWSERTYPE, $SEARCH_SPIDER, $TEXT_DIRECTION, $REQUIRE_AUTHENTICATION;
		global $stylesheet, $headerfile, $view;

		// The title often includes the names of records, which may have markup
		// that cannot be used in the page title.
		$title=html_entity_decode(strip_tags($this->page_title), ENT_QUOTES, 'UTF-8');

		// Initialise variables for the theme's header.php
		$LINK_CANONICAL  =$this->canonical_url;
		$META_ROBOTS     =$this->meta_robots;
		$GEDCOM_TITLE    =get_gedcom_setting(WT_GED_ID, 'title');
		$META_DESCRIPTION=get_gedcom_setting(WT_GED_ID, 'META_DESCRIPTION', $GEDCOM_TITLE);
		$META_GENERATOR  =WT_WEBTREES.'-'.WT_VERSION_TEXT.' - '.WT_WEBTREES_URL;
		$META_TITLE      =get_gedcom_setting(WT_GED_ID, 'META_TITLE');
		if ($META_TITLE) {
			$title.=' - '.$META_TITLE;
		}

		$javascript=
			'<!--[if lt IE 9]><script src="'.WT_STATIC_URL.'js/html5.js"></script><![endif]-->
			<script type="text/javascript" src="'.WT_JQUERY_URL.'"></script>
			<script type="text/javascript" src="'.WT_JQUERYUI_URL.'"></script>
			<script type="text/javascript" src="'.WT_STATIC_URL.'js/jquery/jquery.jeditable.min.js"></script>
			<script>
			// Give JavaScript access to some PHP constants
			var WT_STATIC_URL  = "'.WT_STATIC_URL.'";
			var WT_THEME_DIR   = "'.WT_THEME_DIR.'";
			var WT_MODULES_DIR = "'.WT_MODULES_DIR.'";
			var WT_GEDCOM      = "'.WT_GEDCOM.'";
			var WT_GED_ID      = "'.WT_GED_ID.'";
			var WT_USER_ID     = "'.WT_USER_ID.'";
			var textDirection  = "'.$TEXT_DIRECTION.'";
			var browserType    = "'.$BROWSERTYPE.'";
			var WT_SCRIPT_NAME = "'.WT_SCRIPT_NAME.'";
			var WT_LOCALE      = "'.WT_LOCALE.'";
			var accesstime     = '.WT_DB::prepare("SELECT UNIX_TIMESTAMP(NOW())")->fetchOne().';
	
		function delete_record(pid, linenum, mediaid) {
			if (!mediaid) mediaid="";
			if (confirm(\''.WT_I18N::translate('Are you sure you want to delete this fact?').'\')) {
				window.open(\'edit_interface.php?action=delete&pid=\'+pid+\'&linenum=\'+linenum+\'&mediaid=\'+mediaid, \'_blank\', edit_window_specs);
			}
			return false;
		}
		</script>
		<script src="'.WT_STATIC_URL.'js/webtrees.js" type="text/javascript"></script>';
		
		header('Content-Type: text/html; charset=UTF-8');
		require WT_ROOT.$headerfile;

		// Flush the output, so the browser can render the header and load javascript
		// while we are preparing data for the page
		flush();
		if (ini_get('output_buffering')) {
			ob_flush();
		}

		// Once we've displayed the header, we should no longer write session data.
		Zend_Session::writeClose();

		// Allow the browser to format the header/menus while we generate the page
		flush();

		// We've displayed the header - display the footer automatically
		$this->page_header=true;
		return $this;
	}

	// Print the page footer, using the theme
	protected function pageFooter() {
		global $footerfile, $TEXT_DIRECTION, $view;

		require WT_ROOT.$footerfile;

		if (WT_DEBUG_SQL) {
			echo WT_DB::getQueryLog();
		}
		echo $this->getJavaScript();
		echo '</body></html>';

		return $this;
	}

	// Get significant information from this page, to allow other pages such as
	// charts and reports to initialise with the same records
	public function getSignificantIndividual() {
		static $individual; // Only query the DB once.

		if (!$individual && WT_USER_ROOT_ID) {
			$individual=WT_Person::getInstance(WT_USER_ROOT_ID);
		}
		if (!$individual && WT_USER_GEDCOM_ID) {
			$individual=WT_Person::getInstance(WT_USER_GEDCOM_ID);
		}
		if (!$individual) {
			$individual=WT_Person::getInstance(get_gedcom_setting(WT_GED_ID, 'PEDIGREE_ROOT_ID'));
		}
		if (!$individual) {
			$individual=WT_Person::getInstance(
				WT_DB::prepare(
					"SELECT MIN(i_id) FROM `##individuals` WHERE i_file=?"
				)->execute(array(WT_GED_ID))->fetchOne()
			);
		}
		if (!$individual) {
			// always return a record
			$individual=new WT_Person('0 @I@ INDI');
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
		return new WT_Family('0 @F@ FAM');
	}
	public function getSignificantSurname() {
		return '';
	}
}
