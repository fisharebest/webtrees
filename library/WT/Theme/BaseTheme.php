<?php
// webtrees: Web based Family History software
// Copyright (C) 2015 webtrees development team.
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

namespace WT\Theme;

use gedcom_favorites_WT_Module;
use user_favorites_WT_Module;
use WT\Auth;
use WT\Theme;
use WT\User;
use WT_Controller_Page;
use WT_DB;
use WT_Fact;
use WT_FlashMessages;
use WT_Gedcom_Tag;
use WT_GedcomRecord;
use WT_I18N;
use WT_Filter;
use WT_Individual;
use WT_Menu;
use WT_Module;
use WT_Site;
use WT_Tree;
use Zend_Session_Namespace;

/**
 * Class Base - Common functions and interfaces for all themes.
 */
abstract class BaseTheme {
	/** @var Zend_Session_Namespace */
	protected $session;

	/** @var WT_Tree The current tree */
	protected $tree;

	/** @var string An escaped version of the "ged=XXX" URL parameter */
	protected $tree_url;

	/** @var boolean Are we showing a page to a search engine? */
	protected $search_engine;

	/**
	 * Custom themes should place their initialization code in the function hookAfterInit(), not in
	 * the constructor, as all themes get constructed - whether they are used or not.
	 */
	final public function __construct() {
	}

	/**
	 * Create the top of the <body>.
	 *
	 * @return string
	 */
	public function bodyHeader() {
		return
			'<body class="container">' .
			'<header>' .
			$this->headerContent() .
			$this->primaryMenuContainer($this->primaryMenu()) .
			'</header>' .
			'<main id="content" role="main">' .
			$this->flashMessagesContainer(WT_FlashMessages::getMessages());
	}

	/**
	 * Create the top of the <body> (for popup windows).
	 *
	 * @return string
	 */
	public function bodyHeaderPopupWindow() {
		return
			'<body>' .
			'<main id="content" role="main">' .
			$this->flashMessagesContainer(WT_FlashMessages::getMessages());
	}

	/**
	 * Create a contact link for a user.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	public function contactLink(User $user) {
		$method = $user->getPreference('contactmethod');

		switch ($method) {
		case 'none':
			return '';
		case 'mailto':
			return '<a href="mailto:' . WT_Filter::escapeHtml($user->getEmail()) . '">' . WT_Filter::escapeHtml($user->getRealName()) . '</a>';
		default:
			return "<a href='#' onclick='message(\"" . WT_Filter::escapeJs($user->getUserName()) . "\", \"" . $method . "\", \"" . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_Filter::escapeJs(get_query_url()) . "\", \"\");return false;'>" . WT_Filter::escapeHtml($user->getRealName()) . '</a>';
		}
	}

	/**
	 * Create contact link for both technical and genealogy support.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	protected function contactLinkEverything(User $user) {
		return WT_I18N::translate('For technical support or genealogy questions, please contact') . ' ' . $this->contactLink($user);
	}

	/**
	 * Create contact link for genealogy support.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	protected function contactLinkGenealogy(User $user) {
		return WT_I18N::translate('For help with genealogy questions contact') . ' ' . $this->contactLink($user);
	}

	/**
	 * Create contact link for technical support.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	protected function contactLinkTechnical(User $user) {
		return WT_I18N::translate('For technical support and information contact') . ' ' . $this->contactLink($user);
	}

	/**
	 * Create contact links for the page footer.
	 *
	 * @return string
	 */
	protected function contactLinks() {
		$contact_user   = User::find($this->tree->getPreference('CONTACT_USER_ID'));
		$webmaster_user = User::find($this->tree->getPreference('WEBMASTER_USER_ID'));

		if ($contact_user && $contact_user === $webmaster_user) {
			return $this->contactLinkEverything($contact_user);
		} elseif ($contact_user && $webmaster_user) {
			return $this->contactLinkGenealogy($contact_user) . '<br>' . $this->contactLinkTechnical($webmaster_user);
		} elseif ($contact_user) {
			return $this->contactLinkGenealogy($contact_user);
		} elseif ($webmaster_user) {
			return $this->contactLinkTechnical($webmaster_user);
		} else {
			return '';
		}
	}

	/**
	 * Where are our CSS assets?
	 *
	 * @return string A relative path, such as "themes/foo/"
	 */
	public function cssUrl() {
		return '';
	}

	/**
	 * Create the <DOCTYPE> tag.
	 *
	 * @return string
	 */
	public function doctype() {
		return '<!DOCTYPE html>';
	}

	/**
	 * HTML link to a "favorites icon".
	 *
	 * @return string
	 */
	protected function favicon() {
		return '';
	}

	/**
	 * Add markup to a flash message.
	 *
	 * @param \stdClass $message
	 *
	 * @return string
	 */
	protected function flashMessageContainer(\stdClass $message) {
		return
			'<div class="alert alert-' . $message->status . ' alert-dismissible" role="alert">' .
			'<button type="button" class="close" data-dismiss="alert" aria-label="' . /* I18N: button label */ WT_I18N::translate('close') . '">' .
			'<span aria-hidden="true">&times;</span>' .
			'</button>' .
			$message->text .
			'</div>';
	}

	/**
	 * Create a container for messages that are "flashed" to the session
	 * on one request, and displayed on another.  If there are many messages,
	 * the container may need a max-height and scroll-bar.
	 *
	 * @param \stdClass[] $messages
	 *
	 * @return string
	 */
	protected function flashMessagesContainer(array $messages) {
		$html = '';
		foreach ($messages as $message) {
			$html .= $this->flashMessageContainer($message);
		}

		if ($html) {
			return '<div class="flash-messages">' . $html . '</div>';
		} else {
			return '';
		}
	}

	/**
	 * Create the <footer> tag.
	 *
	 * @return string
	 */
	public function footerContainer() {
		return
			'</main>' .
			'<footer>' . $this->footerContent() . '</footer>';
	}

	/**
	 * Create the contents of the <footer> tag.
	 *
	 * @return string
	 */
	protected function footerContent() {
		return
			$this->formatContactLinks() .
			$this->logoPoweredBy();
	}

	/**
	 * Format the contents of a variable-height home-page block.
	 *
	 * @param string $id
	 * @param string $title
	 * @param string $class
	 * @param string $content
	 *
	 * @return string
	 */
	public function formatBlock($id, $title, $class, $content) {
		return
			'<div id="' . $id . '" class="block" >' .
			'<div class="blockheader">' . $title . '</div>' .
			'<div class="blockcontent ' . $class . '">' . $content . '</div>' .
			'</div>';
	}

	/**
	 * Add markup to the contact links.
	 *
	 * @return string
	 */
	protected function formatContactLinks() {
		if ($this->tree) {
			return '<div class="contact-links">' . $this->contactLinks() . '</div>';
		} else {
			return '';
		}
	}

	/**
	 * Create a pending changes link for the page footer.
	 *
	 * @return string
	 */
	protected function formatPendingChangesLink() {
		if ($this->pendingChangesExist()) {
			return '<div class="pending-changes-link">' . $this->pendingChangesLink() . '</div>';
		} else {
			return '';
		}
	}

	/**
	 * Add markup to the tree title.
	 *
	 * @return string
	 */
	protected function formatTreeTitle() {
		if ($this->tree) {
			return '<h1 class="header-title">' . $this->tree->tree_title_html . '</h1>';
		} else {
			return '';
		}
	}

	/**
	 * Add markup to the user menu.
	 *
	 * @return string
	 */
	protected function formatSecondaryMenu() {
		return
			'<ul class="secondary-menu">' .
			implode('', $this->secondaryMenu()) .
			'</ul>';
	}

	/**
	 * Add markup to an item in the user menu.
	 *
	 * @param WT_Menu $menu
	 *
	 * @return string
	 */
	protected function formatUserMenuItem(WT_Menu $menu) {
		return $menu->getMenuAsList();
	}

	/**
	 * Create a quick search form for the header.
	 *
	 * @return string
	 */
	protected function formQuickSearch() {
		if ($this->tree) {
			return
				'<form action="search.php" class="header-search" method="post" role="search">' .
				'<input type="hidden" name="action" value="general">' .
				'<input type="hidden" name="ged" value="' . $this->tree->tree_name_html . '">' .
				'<input type="hidden" name="topsearch" value="yes">' .
				$this->formQuickSearchFields() .
				'</form>';
		} else {
			return '';
		}
	}

	/**
	 * Create a search field and submit button for the quick search form in the header.
	 *
	 * @return string
	 */
	protected function formQuickSearchFields() {
		return
			'<input type="search" name="query" size="15" placeholder="' . WT_I18N::translate('Search') . '">' .
			'<input type="image" src="' . Theme::theme()->parameter('image-search') . '" alt="' . WT_I18N::translate('Search') . '">';
	}

	/**
	 * Create the <head> tag.
	 *
	 * @param WT_Controller_Page $controller The current controller
	 *
	 * @return string
	 */
	public function head(WT_Controller_Page $controller) {
		return
			'<head>' .
			$this->headContents($controller) .
			$this->hookHeaderExtraContent() .
			'</head>';
	}

	/**
	 * Create the contents of the <head> tag.
	 *
	 * @param WT_Controller_Page $controller The current controller
	 *
	 * @return string
	 */
	protected function headContents(WT_Controller_Page $controller) {
		// The title often includes the names of records, which may include HTML markup.
		$title = WT_Filter::unescapeHtml($controller->getPageTitle());

		// If an extra (site) title is specified, append it.
		if ($this->tree && $this->tree->getPreference('META_TITLE')) {
			$title .= ' - ' . WT_Filter::escapeHtml($this->tree->getPreference('META_TITLE'));
		}

		$html =
			// Modernizr needs to be loaded before the <body> to avoid FOUC in IE8
			'<!--[if IE 8]><script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script><![endif]-->' .
			$this->metaCharset() .
			$this->title($title) .
			$this->metaViewport() .
			$this->metaRobots($controller->getMetaRobots()) .
			$this->metaUaCompatible() .
			$this->metaGenerator(WT_WEBTREES . ' ' . WT_VERSION . ' - ' . WT_WEBTREES_URL) .
			$this->metaCanonicalUrl($controller->getCanonicalUrl());

		if ($this->tree) {
			$html .= $this->metaDescription($this->tree->getPreference('META_DESCRIPTION', html_entity_decode(strip_tags($this->tree->tree_title_html), ENT_QUOTES)));
		}

		// CSS files
		foreach ($this->stylesheets() as $css) {
			$html .= '<link rel="stylesheet" type="text/css" href="' . $css . '">';
		}

		return $html;
	}

	/**
	 * Create the contents of the <header> tag.
	 *
	 * @return string
	 */
	protected function headerContent() {
		return
			$this->logoHeader() .
			$this->secondaryMenuContainer($this->secondaryMenu()) .
			$this->formatTreeTitle() .
			$this->formQuickSearch();
	}

	/**
	 * Create the <header> tag for a popup window.
	 *
	 * @return string
	 */
	protected function headerSimple() {
		return
			$this->flashMessagesContainer(WT_FlashMessages::getMessages()) .
			'<div id="content">';
	}

	/**
	 * Allow themes to do things after initialization (since they cannot use
	 * the constructor).
	 *
	 * @return void
	 */
	public function hookAfterInit() {
	}

	/**
	 * Allow themes to add extra scripts to the page footer.
	 *
	 * @return string
	 */
	public function hookFooterExtraJavascript() {
		return '';
	}

	/**
	 * Allow themes to add extra content to the page header.
	 * Typically this will be additional CSS.
	 *
	 * @return string
	 */
	public function hookHeaderExtraContent() {
		return '';
	}

	/**
	 * Create the <html> tag.
	 *
	 * @return string
	 */
	public function html() {
		return '<html ' . WT_I18N::html_markup() . '>';
	}

	/**
	 * Display an icon for this fact.
	 *
	 * @param WT_Fact $fact
	 *
	 * @return string
	 */
	public function icon(WT_Fact $fact) {
		$icon = 'images/facts/' . $fact->getTag() . '.png';
		$dir  = substr($this->cssUrl(), strlen(WT_STATIC_URL));
		if (file_exists($dir . $icon)) {
			return '<img src="' . $this->cssUrl() . $icon . '" title="' . WT_Gedcom_Tag::getLabel($fact->getTag()) . '">';
		} elseif (file_exists($dir . 'images/facts/NULL.png')) {
			// Spacer image - for alignment - until we move to a sprite.
			return '<img src="' . Theme::theme()->cssUrl() . 'images/facts/NULL.png">';
		} else {
			return '';
		}
	}

	/**
	 * Display an individual in a box - for charts, etc.
	 *
	 * @param WT_Individual $individual
	 *
	 * @return string
	 */
	public function individualBox(WT_Individual $individual) {
		$personBoxClass = array_search($individual->getSex(), array('person_box' => 'M', 'person_boxF' => 'F', 'person_boxNN' => 'U'));
		if ($this->tree->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
			$thumbnail = $individual->displayImage();
		} else {
			$thumbnail = '';
		}

		return
			'<div data-pid="' . $individual->getXref() . '" class="person_box_template ' . $personBoxClass . ' box-style1" style="width: ' . $this->parameter('chart-box-x') . 'px; min-height: ' . $this->parameter('chart-box-y') . 'px">' .
			'<div class="noprint icons">' .
			'<span class="iconz icon-zoomin" title="' . WT_I18N::translate('Zoom in/out on this box.') . '"></span>' .
			'<div class="itr"><i class="icon-pedigree"></i><div class="popup">' .
			'<ul class="' . $personBoxClass . '">' . implode('', $this->individualBoxMenu($individual)) . '</ul>' .
			'</div>' .
			'</div>' .
			'</div>' .
			'<div class="chart_textbox" style="max-height:' . $this->parameter('chart-box-y') . 'px;">' .
			$thumbnail .
			'<a href="' . $individual->getHtmlUrl() . '">'.
			'<span class="namedef name1">' . $individual->getFullName() . '</span>' .
			'</a>' .
			'<div class="namedef name1">' . $individual->getAddName() . '</div>' .
			'<div class="inout2 details1">' . $this->individualBoxFacts($individual) .'</div>' .
			'</div>' .
			'<div class="inout"></div>' .
			'</div>';
	}

	/**
	 * Display an empty box - for a missing individual in a chart.
	 *
	 * @return string
	 */
	public function individualBoxEmpty() {
		return '<div class="person_box_template person_boxNN box-style1" style="width: ' . $this->parameter('chart-box-x') . 'px; min-height: ' . $this->parameter('chart-box-y') . 'px"></div>';
	}

	/**
	 * Display an individual in a box - for charts, etc.
	 *
	 * @param WT_Individual $individual
	 *
	 * @return string
	 */
	public function individualBoxLarge(WT_Individual $individual) {
		$personBoxClass = array_search($individual->getSex(), array('person_box' => 'M', 'person_boxF' => 'F', 'person_boxNN' => 'U'));
		if ($this->tree->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
			$thumbnail = $individual->displayImage();
		} else {
			$thumbnail = '';
		}

		return
			'<div data-pid="' . $individual->getXref() . '" class="person_box_template ' . $personBoxClass . ' box-style2">' .
			'<div class="noprint icons">' .
			'<span class="iconz icon-zoomin" title="' . WT_I18N::translate('Zoom in/out on this box.') . '"></span>' .
			'<div class="itr"><i class="icon-pedigree"></i><div class="popup">' .
			'<ul class="' . $personBoxClass . '">' . implode('', $this->individualBoxMenu($individual)) . '</ul>' .
			'</div>' .
			'</div>' .
			'</div>' .
			'<div class="chart_textbox" style="max-height:' . $this->parameter('chart-box-y') . 'px;">' .
			$thumbnail .
			'<a href="' . $individual->getHtmlUrl() . '">'.
			'<span class="namedef name2">' . $individual->getFullName() . '</span>' .
			'</a>' .
			'<div class="namedef name2">' . $individual->getAddName() . '</div>' .
			'<div class="inout2 details2">' . $this->individualBoxFacts($individual) .'</div>' .
			'</div>' .
			'<div class="inout"></div>' .
			'</div>';
	}

	/**
	 * Display an individual in a box - for charts, etc.
	 *
	 * @param WT_Individual $individual
	 *
	 * @return string
	 */
	public function individualBoxSmall(WT_Individual $individual) {
		$personBoxClass = array_search($individual->getSex(), array('person_box' => 'M', 'person_boxF' => 'F', 'person_boxNN' => 'U'));
		if ($this->tree->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
			$thumbnail = $individual->displayImage();
		} else {
			$thumbnail = '';
		}


		return
			'<div data-pid="' . $individual->getXref() . '" class="person_box_template ' . $personBoxClass . ' box-style0" style="width: ' . $this->parameter('compact-chart-box-x') . 'px; min-height: ' . $this->parameter('compact-chart-box-y') . 'px">' .
			'<div class="compact_view">' .
			$thumbnail .
			'<a href="' . $individual->getHtmlUrl() . '">'.
			'<span class="namedef name0">' . $individual->getFullName() . '</span>' .
			'</a>' .
			'<div class="inout2 details0">' . $individual->getLifeSpan() .'</div>' .
			'</div>' .
			'<div class="inout"></div>' .
			'</div>';
	}

	/**
	 * Display an individual in a box - for charts, etc.
	 *
	 * @return string
	 */
	public function individualBoxSmallEmpty() {
		return '<div class="person_box_template person_boxNN box-style1" style="width: ' . $this->parameter('compact-chart-box-x') . 'px; min-height: ' . $this->parameter('compact-chart-box-y') . 'px"></div>';
	}

	/**
	 * Generate the facts, for display in charts.
	 *
	 * @param WT_Individual $individual
	 *
	 * @return string
	 */
	protected function individualBoxFacts(WT_Individual $individual) {
		$html = '';

		$opt_tags = preg_split('/\W/', $this->tree->getPreference('CHART_BOX_TAGS'), 0, PREG_SPLIT_NO_EMPTY);
		// Show BIRT or equivalent event
		foreach (explode('|', WT_EVENTS_BIRT) as $birttag) {
			if (!in_array($birttag, $opt_tags)) {
				$event = $individual->getFirstFact($birttag);
				if ($event) {
					$html .= $event->summary();
					break;
				}
			}
		}
		// Show optional events (before death)
		foreach ($opt_tags as $key => $tag) {
			if (!preg_match('/^(' . WT_EVENTS_DEAT . ')$/', $tag)) {
				$event = $individual->getFirstFact($tag);
				if (!is_null($event)) {
					$html .= $event->summary();
					unset ($opt_tags[$key]);
				}
			}
		}
		// Show DEAT or equivalent event
		foreach (explode('|', WT_EVENTS_DEAT) as $deattag) {
			$event = $individual->getFirstFact($deattag);
			if ($event) {
				$html .= $event->summary();
				if (in_array($deattag, $opt_tags)) {
					unset ($opt_tags[array_search($deattag, $opt_tags)]);
				}
				break;
			}
		}
		// Show remaining optional events (after death)
		foreach ($opt_tags as $tag) {
			$event = $individual->getFirstFact($tag);
			if ($event) {
				$html .= $event->summary();
			}
		}

		return $html;
	}

	/**
	 * Generate the LDS summary, for display in charts.
	 *
	 * @param WT_Individual $individual
	 *
	 * @return string
	 */
	protected function individualBoxLdsSummary(WT_Individual $individual) {
		if ($this->tree->getPreference('SHOW_LDS_AT_GLANCE')) {
			$BAPL = $individual->getFacts('BAPL') ? 'B' : '_';
			$ENDL = $individual->getFacts('ENDL') ? 'E' : '_';
			$SLGC = $individual->getFacts('SLGC') ? 'C' : '_';
			$SLGS = '_';

			foreach ($individual->getSpouseFamilies() as $family) {
				if ($family->getFacts('SLGS')) {
					$SLGS = '';
				}
			}

			return $BAPL . $ENDL . $SLGS . $SLGC;
		} else {
			return '';
		}
	}

	/**
	 * Links, to show in chart boxes;
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu[]
	 */
	protected function individualBoxMenu(WT_Individual $individual) {
		$menus = array_merge(
			$this->individualBoxMenuCharts($individual),
			$this->individualBoxMenuFamilyLinks($individual)
		);

		return $menus;
	}

	/**
	 * Chart links, to show in chart boxes;
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu[]
	 */
	protected function individualBoxMenuCharts(WT_Individual $individual) {
		$menus = array_filter(array(
			$this->menuChartAncestors($individual),
			$this->menuChartCompact($individual),
			$this->menuChartDescendants($individual),
			$this->menuChartFanChart($individual),
			$this->menuChartHourglass($individual),
			$this->menuChartInteractiveTree($individual),
			$this->menuChartPedigree($individual),
			$this->menuChartPedigreeMap($individual),
			$this->menuChartRelationship($individual),
			$this->menuChartTimeline($individual),
		));

		usort($menus, function (WT_Menu $x, WT_Menu $y) {
			return WT_I18N::strcasecmp($x->getLabel(), $y->getLabel());
		});

		return $menus;
	}

	/**
	 * Family links, to show in chart boxes.
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu[]
	 */
	protected function individualBoxMenuFamilyLinks(WT_Individual $individual) {
		$menus = array();

		foreach ($individual->getSpouseFamilies() as $family) {
			$menus[] = new WT_Menu('<strong>' . WT_I18N::translate('Family with spouse') . '</strong>', $family->getHtmlUrl());
			$spouse = $family->getSpouse($individual);
			if ($spouse && $spouse->canShowName()) {
				$menus[] = new WT_Menu($spouse->getFullName(), $spouse->getHtmlUrl());
			}
			foreach ($family->getChildren() as $child) {
				if ($child->canShowName()) {
					$menus[] = new WT_Menu($child->getFullName(), $child->getHtmlUrl());
				}
			}
		}

		return $menus;
	}

	/**
	 * Create part of an individual box
	 *
	 * @param WT_Individual $individual
	 *
	 * @return string
	 */
	protected function individualBoxSexSymbol(WT_Individual $individual) {
		if ($this->tree->getPreference('PEDIGREE_SHOW_GENDER')) {
			return $individual->sexImage('large');
		} else {
			return '';
		}
	}

	/**
	 * Initialise the theme.  We cannot pass these in a constructor, as the construction
	 * happens in a theme file, and we need to be able to change it.
	 *
	 * @param Zend_Session_Namespace $session
	 * @param bool                   $search_engine
	 * @param WT_Tree|null           $tree The current tree (if there is one).
	 *
	 * @return void
	 */
	final public function init(Zend_Session_Namespace $session, $search_engine, WT_Tree $tree = null) {
		$this->tree          = $tree;
		$this->tree_url      = $tree ? 'ged=' . WT_Filter::escapeUrl($tree->tree_name) : null;
		$this->session       = $session;
		$this->search_engine = $search_engine;

		$this->hookAfterInit();
	}

	/**
	 * Are we generating a page for a robot (instead of a human being).
	 *
	 * @return boolean
	 */
	protected function isSearchEngine() {
		return $this->search_engine;
	}

	/**
	 * A large webtrees logo, for the footer.
	 *
	 * @return string
	 */
	protected function logoHeader() {
		return '<div class="header-logo"></div>';
	}

	/**
	 * A small "powered by webtrees" logo for the footer.
	 *
	 * @return string
	 */
	protected function logoPoweredBy() {
		return '<a href="' . WT_WEBTREES_URL . '" class="powered-by-webtrees" title="' . WT_WEBTREES_URL . '"></a>';
	}

	/**
	 * @return WT_Menu
	 */
	protected function menuCalendar() {
		if ($this->isSearchEngine()) {
			return new WT_Menu(WT_I18N::translate('Calendar'), '#', 'menu-calendar');
		}

		// Default action is the day view.
		$menu = new WT_Menu(WT_I18N::translate('Calendar'), 'calendar.php?' . $this->tree_url, 'menu-calendar');

		// Day view
		$submenu = new WT_Menu(WT_I18N::translate('Day'), 'calendar.php?' . $this->tree_url, 'menu-calendar-day');
		$menu->addSubmenu($submenu);

		// Month view
		$submenu = new WT_Menu(WT_I18N::translate('Month'), 'calendar.php?' . $this->tree_url . '&amp;action=calendar', 'menu-calendar-month');
		$menu->addSubmenu($submenu);

		//Year view
		$submenu = new WT_Menu(WT_I18N::translate('Year'), 'calendar.php?' . $this->tree_url . '&amp;action=year', 'menu-calendar-year');
		$menu->addSubmenu($submenu);

		return $menu;
	}

	/**
	 * Generate a menu for each of the different charts.
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	protected function menuChart(WT_Individual $individual) {
		if ($this->tree && !$this->isSearchEngine()) {
			// The top level menu is the pedigree chart
			$menu = $this->menuChartPedigree($individual);
			$menu->setLabel(WT_I18N::translate('Charts'));
			$menu->setId('menu-chart');

			$submenus = array_filter(array(
				$this->menuChartAncestors($individual),
				$this->menuChartCompact($individual),
				$this->menuChartDescendants($individual),
				$this->menuChartFamilyBook($individual),
				$this->menuChartFanChart($individual),
				$this->menuChartHourglass($individual),
				$this->menuChartInteractiveTree($individual),
				$this->menuChartLifespan($individual),
				$this->menuChartPedigree($individual),
				$this->menuChartPedigreeMap($individual),
				$this->menuChartRelationship($individual),
				$this->menuChartStatistics(),
				$this->menuChartTimeline($individual),
			));

			usort($submenus, function (WT_Menu $x, WT_Menu $y) {
				return WT_I18N::strcasecmp($x->getLabel(), $y->getLabel());
			});

			$menu->setSubmenus($submenus);

			return $menu;
		} else {
			return new WT_Menu(WT_I18N::translate('Charts'), '#', 'menu-chart');
		}
	}

	/**
	 * Generate a menu item for the ancestors chart (ancestry.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	protected function menuChartAncestors(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Ancestors'), 'ancestry.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-pedigree');
	}

	/**
	 * Generate a menu item for the compact tree (compact.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	protected function menuChartCompact(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Compact tree'), 'compact.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-compact');
	}

	/**
	 * Generate a menu item for the descendants chart (descendancy.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	protected function menuChartDescendants(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Descendants'), 'descendancy.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-descendants');
	}

	/**
	 * Generate a menu item for the family-book chart (familybook.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	protected function menuChartFamilyBook(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Family book'), 'familybook.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-familybook');
	}

	/**
	 * Generate a menu item for the fan chart (fanchart.php).
	 *
	 * We can only do this if the GD2 library is installed with TrueType support.
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu|null
	 */
	protected function menuChartFanChart(WT_Individual $individual) {
		if (function_exists('imagettftext')) {
			return new WT_Menu(WT_I18N::translate('Fan chart'), 'fanchart.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-fanchart');
		} else {
			return null;
		}
	}

	/**
	 * Generate a menu item for the pedigree map (googlemap module).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu|null
	 */
	protected function menuChartInteractiveTree(WT_Individual $individual) {
		if (array_key_exists('tree', WT_Module::getActiveModules())) {
			return new WT_Menu(WT_I18N::translate('Interactive tree'), 'module.php?mod=tree&amp;mod_action=treeview&amp;' . $this->tree_url . '&amp;rootid=' . $individual->getXref(), 'menu-chart-tree');
		} else {
			return null;
		}
	}

	/**
	 * Generate a menu item for the hourglass chart (hourglass.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	protected function menuChartHourglass(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Hourglass chart'), 'hourglass.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-hourglass');
	}

	/**
	 * Generate a menu item for the lifepsan chart (lifespan.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	protected function menuChartLifespan(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Lifespans'), 'lifespan.php?pids%5B%5D=' . $individual->getXref() . '&amp;addFamily=1&amp;' . $this->tree_url, 'menu-chart-lifespan');
	}

	/**
	 * Generate a menu item for the pedigree chart (pedigree.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	protected function menuChartPedigree(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Pedigree'), 'pedigree.php?rootid=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-pedigree');
	}

	/**
	 * Generate a menu item for the pedigree map (googlemap module).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu|null
	 */
	protected function menuChartPedigreeMap(WT_Individual $individual) {
		if (array_key_exists('googlemap', WT_Module::getActiveModules())) {
			return new WT_Menu(WT_I18N::translate('Pedigree map'), 'module.php?' . $this->tree_url . '&amp;mod=googlemap&amp;mod_action=pedigree_map&amp;rootid=' . $individual->getXref(), 'menu-chart-pedigree_map');
		} else {
			return null;
		}
	}

	/**
	 * Generate a menu item for the relationship chart (relationship.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	protected function menuChartRelationship(WT_Individual $individual) {
		if (WT_USER_GEDCOM_ID && $individual->getXref()) {
			return new WT_Menu(WT_I18N::translate('Relationship to me'), 'relationship.php?pid1=' . WT_USER_GEDCOM_ID . '&amp;pid2=' . $individual->getXref() . '&amp;ged=' . $this->tree_url, 'menu-chart-relationship');
		} else {
			return new WT_Menu(WT_I18N::translate('Relationships'), 'relationship.php?pid1=' . $individual->getXref() . '&amp;ged=' . $this->tree_url, 'menu-chart-relationship');
		}
	}

	/**
	 * Generate a menu item for the statistics charts (statistics.php).
	 *
	 * @return WT_Menu
	 */
	protected function menuChartStatistics() {
		return new WT_Menu(WT_I18N::translate('Statistics'), 'statistics.php?' . $this->tree_url, 'menu-chart-statistics');
	}

	/**
	 * Generate a menu item for the timeline chart (timeline.php).
	 *
	 * @param WT_Individual $individual
	 *
	 * @return WT_Menu
	 */
	protected function menuChartTimeline(WT_Individual $individual) {
		return new WT_Menu(WT_I18N::translate('Timeline'), 'timeline.php?pids%5B%5D=' . $individual->getXref() . '&amp;' . $this->tree_url, 'menu-chart-timeline');
	}

	/**
	 * Favorites menu.
	 *
	 * @return WT_Menu|null
	 */
	protected function menuFavorites() {
		global $controller;

		$show_user_favorites = $this->tree && array_key_exists('user_favorites', WT_Module::getActiveModules()) && Auth::check();
		$show_tree_favorites = $this->tree && array_key_exists('gedcom_favorites', WT_Module::getActiveModules());

		if ($show_user_favorites && $show_tree_favorites) {
			$favorites = array_merge(
				gedcom_favorites_WT_Module::getFavorites(WT_GED_ID),
				user_favorites_WT_Module::getFavorites(Auth::id())
			);
		} elseif ($show_user_favorites) {
			$favorites = user_favorites_WT_Module::getFavorites(Auth::id());
		} elseif ($show_tree_favorites) {
			$favorites = gedcom_favorites_WT_Module::getFavorites(WT_GED_ID);
		} else {
			return null;
		}

		$menu = new WT_Menu(WT_I18N::translate('Favorites'), '#', 'menu-favorites');

		foreach ($favorites as $favorite) {
			switch ($favorite['type']) {
			case 'URL':
				$submenu = new WT_Menu($favorite['title'], $favorite['url']);
				$menu->addSubmenu($submenu);
				break;
			case 'INDI':
			case 'FAM':
			case 'SOUR':
			case 'OBJE':
			case 'NOTE':
				$obj = WT_GedcomRecord::getInstance($favorite['gid']);
				if ($obj && $obj->canShowName()) {
					$submenu = new WT_Menu($obj->getFullName(), $obj->getHtmlUrl());
					$menu->addSubmenu($submenu);
				}
				break;
			}
		}

		if ($show_user_favorites) {
			if (isset($controller->record) && $controller->record instanceof WT_GedcomRecord) {
				$submenu = new WT_Menu(WT_I18N::translate('Add to favorites'), '#');
				$submenu->setOnclick("jQuery.post('module.php?mod=user_favorites&amp;mod_action=menu-add-favorite',{xref:'" . $controller->record->getXref() . "'},function(){location.reload();})");
				$menu->addSubmenu($submenu);
			}
		}

		return $menu;
	}

	/**
	 * @return WT_Menu
	 */
	protected function menuHomePage() {
		$menu                = new WT_Menu(WT_I18N::translate('Home page'), 'index.php?ctype=gedcom&amp;' . $this->tree_url, 'menu-tree');
		$ALLOW_CHANGE_GEDCOM = WT_Site::getPreference('ALLOW_CHANGE_GEDCOM') && count(WT_Tree::getAll()) > 1;
		foreach (WT_Tree::getAll() as $tree) {
			if ($tree->tree_id === WT_GED_ID || $ALLOW_CHANGE_GEDCOM) {
				$submenu = new WT_Menu(
					$tree->tree_title_html,
					'index.php?ctype=gedcom&amp;ged=' . $tree->tree_name_url,
					'menu-tree-' . $tree->tree_id // Cannot use name - it must be a CSS identifier
				);
				$menu->addSubmenu($submenu);
			}
		}

		return $menu;
	}

	/**
	 * A menu to show a list of available languages.
	 *
	 * @return WT_Menu|null
	 */
	protected function menuLanguages() {
		$menu = new WT_Menu(WT_I18N::translate('Language'), '#', 'menu-language');

		foreach (WT_I18N::installed_languages() as $lang => $name) {
			$submenu = new WT_Menu($name, get_query_url(array('lang' => $lang), '&amp;'), 'menu-language-' . $lang);
			if (WT_LOCALE === $lang) {
				$submenu->addClass('', '', 'active');
			}
			$menu->addSubmenu($submenu);
		}

		if (count($menu->getSubmenus()) > 1 && !$this->isSearchEngine()) {
			return $menu;
		} else {
			return null;
		}
	}

	/**
	 * Create a menu to show lists of individuals, families, sources, etc.
	 *
	 * @return WT_Menu|null
	 */
	protected function menuLists() {
		global $controller;

		// The top level menu shows the individual list
		$menu = new WT_Menu(WT_I18N::translate('Lists'), 'indilist.php?' . $this->tree_url, 'menu-list');

		// Do not show empty lists
		$row = WT_DB::prepare(
			"SELECT SQL_CACHE" .
			" EXISTS(SELECT 1 FROM `##sources` WHERE s_file=?                  ) AS sour," .
			" EXISTS(SELECT 1 FROM `##other`   WHERE o_file=? AND o_type='REPO') AS repo," .
			" EXISTS(SELECT 1 FROM `##other`   WHERE o_file=? AND o_type='NOTE') AS note," .
			" EXISTS(SELECT 1 FROM `##media`   WHERE m_file=?                  ) AS obje"
		)->execute(array(WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID))->fetchOneRow();

		// Build a list of submenu items and then sort it in localized name order
		$surname_url = '&surname=' . rawurlencode($controller->getSignificantSurname()) . '&amp;' . $this->tree_url;

		$menulist = array(
			new WT_Menu(WT_I18N::translate('Individuals'), 'indilist.php?' . $this->tree_url . $surname_url, 'menu-list-indi'),
		);

		if (!$this->isSearchEngine()) {
			$menulist[] = new WT_Menu(WT_I18N::translate('Families'), 'famlist.php?' . $this->tree_url . $surname_url, 'menu-list-fam');
			$menulist[] = new WT_Menu(WT_I18N::translate('Branches'), 'branches.php?' . $this->tree_url . $surname_url, 'menu-branches');
			$menulist[] = new WT_Menu(WT_I18N::translate('Place hierarchy'), 'placelist.php?' . $this->tree_url, 'menu-list-plac');
			if ($row->obje) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Media objects'), 'medialist.php?' . $this->tree_url, 'menu-list-obje');
			}
			if ($row->repo) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Repositories'), 'repolist.php?' . $this->tree_url, 'menu-list-repo');
			}
			if ($row->sour) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Sources'), 'sourcelist.php?' . $this->tree_url, 'menu-list-sour');
			}
			if ($row->note) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Shared notes'), 'notelist.php?' . $this->tree_url, 'menu-list-note');
			}
		}
		uasort($menulist, function (WT_Menu $x, WT_Menu $y) {
				return WT_I18N::strcasecmp($x->getLabel(), $y->getLabel());
			});

		$menu->setSubmenus($menulist);

		return $menu;
	}

	/**
	 * A login menu option (or null if we are already logged in).
	 *
	 * @return WT_Menu|null
	 */
	protected function menuLogin() {
		if (Auth::check() || $this->isSearchEngine() || WT_SCRIPT_NAME === 'login.php') {
			return null;
		} else {
			return new WT_Menu(WT_I18N::translate('Login'), WT_LOGIN_URL . '?url=' . rawurlencode(get_query_url()));
		}
	}

	/**
	 * A logout menu option (or null if we are already logged out).
	 *
	 * @return WT_Menu|null
	 */
	protected function menuLogout() {
		if (Auth::check()) {
			return new WT_Menu(WT_I18N::translate('Logout'), 'logout.php');
		} else {
			return null;
		}
	}

	/**
	 * Get the additional menus created by each of the modules
	 *
	 * @return WT_Menu[]
	 */
	protected function menuModules() {
		$menus = array();
		foreach (WT_Module::getActiveMenus() as $module) {
			$menu = $module->getMenu();
			if ($menu) {
				$menus[] = $menu;
			}
		}

		return $menus;
	}

	/**
	 * A link to allow users to edit their account settings.
	 *
	 * @return WT_Menu|null
	 */
	protected function menuMyAccount() {
		if (Auth::check()) {
			return new WT_Menu(WT_Filter::escapeHtml(Auth::user()->getRealName()), 'edituser.php');
		} else {
			return null;
		}
	}

	/**
	 * @return WT_Menu|null
	 */
	protected function menuMyMenu() {
		$showFull   = $this->tree->getPreference('PEDIGREE_FULL_DETAILS') ? 1 : 0;
		$showLayout = $this->tree->getPreference('PEDIGREE_LAYOUT') ? 1 : 0;

		if (!Auth::id()) {
			return null;
		}

		$menu = new WT_Menu(WT_I18N::translate('My page'), 'index.php?ctype=user&amp;' . $this->tree_url, 'menu-mymenu');

		$menu->addSubmenu($this->menuMyPage());

		if (Auth::user()->getPreference('editaccount')) {
			$menu->addSubmenu(new WT_Menu(WT_I18N::translate('My account'), 'edituser.php', 'menu-myaccount'));
		}

		if (WT_USER_GEDCOM_ID) {
			$menu->addSubmenu(new WT_Menu(
				WT_I18N::translate('My pedigree'),
				'pedigree.php?' . $this->tree_url . '&amp;rootid=' . WT_USER_GEDCOM_ID . "&amp;show_full={$showFull}&amp;talloffset={$showLayout}",
				'menu-mypedigree'
			));

			$menu->addSubmenu(new WT_Menu(
				WT_I18N::translate('My individual record'), 'individual.php?pid=' . WT_USER_GEDCOM_ID . '&amp;' . $this->tree_url, 'menu-myrecord'
			));
		}

		if (WT_USER_GEDCOM_ADMIN) {
			$menu->addSubmenu(new WT_Menu(WT_I18N::translate('Administration'), 'admin.php', 'menu-admin'));
		}

		return $menu;
	}

	/**
	 * A link to the user's personal home page.
	 *
	 * @return WT_Menu|null
	 */
	protected function menuMyPage()
	{
		return new WT_Menu(WT_I18N::translate('My page'), 'index.php?ctype=user&amp;' . $this->tree_url, 'menu-mypage');
	}

		/**
		 * Create a pending changes menu.
		 *
		 * @return WT_Menu|null
		 */
		protected function menuPendingChanges() {
		if ($this->pendingChangesExist()) {
			$menu = new WT_Menu(WT_I18N::translate('Pending changes'), '#', 'menu-pending');
			$menu->setOnclick('window.open(\'edit_changes.php\', \'_blank\', chan_window_specs); return false;');

			return $menu;
		} else {
			return null;
		}
	}

	/**
	 * @return WT_Menu|null
	 */
	protected function menuReports() {
		$active_reports = WT_Module::getActiveReports();

		if ($this->isSearchEngine() || !$active_reports) {
			return new WT_Menu(WT_I18N::translate('Reports'), '#', 'menu-report');
		}

		$menu = new WT_Menu(WT_I18N::translate('Reports'), 'reportengine.php?' . $this->tree_url, 'menu-report');

		$sub_menu = false;
		foreach ($active_reports as $report) {
			foreach ($report->getReportMenus() as $submenu) {
				$menu->addSubmenu($submenu);
				$sub_menu = true;
			}
		}

		if ($sub_menu && !$this->isSearchEngine()) {
			return $menu;
		} else {
			return null;
		}
	}

	/**
	 * Create the search menu
	 *
	 * @return WT_Menu
	 */
	protected function menuSearch() {
		if ($this->isSearchEngine()) {
			return new WT_Menu(WT_I18N::translate('Search'), '#', 'menu-search');
		}
		//-- main search menu item
		$menu = new WT_Menu(WT_I18N::translate('Search'), 'search.php?' . $this->tree_url, 'menu-search');
		//-- search_general sub menu
		$submenu = new WT_Menu(WT_I18N::translate('General search'), 'search.php?' . $this->tree_url, 'menu-search-general');
		$menu->addSubmenu($submenu);
		//-- search_soundex sub menu
		$submenu = new WT_Menu(/* I18N: search using “sounds like”, rather than exact spelling */
			WT_I18N::translate('Phonetic search'), 'search.php?' . $this->tree_url . '&amp;action=soundex', 'menu-search-soundex');
		$menu->addSubmenu($submenu);
		//-- advanced search
		$submenu = new WT_Menu(WT_I18N::translate('Advanced search'), 'search_advanced.php?' . $this->tree_url, 'menu-search-advanced');
		$menu->addSubmenu($submenu);
		//-- search_replace sub menu
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Search and replace'), 'search.php?' . $this->tree_url . '&amp;action=replace', 'menu-search-replace');
			$menu->addSubmenu($submenu);
		}

		return $menu;
	}

	/**
	 * Themes menu.
	 *
	 * @return WT_Menu|null
	 */
	public function menuThemes() {
		if ($this->tree && !$this->isSearchEngine() && WT_Site::getPreference('ALLOW_USER_THEMES') && $this->tree->getPreference('ALLOW_THEME_DROPDOWN')) {
			$menu = new WT_Menu(WT_I18N::translate('Theme'), '#', 'menu-theme');
			foreach (Theme::installedThemes() as $theme) {
				$submenu = new WT_Menu($theme->themeName(), get_query_url(array('theme' => $theme->themeId()), '&amp;'), 'menu-theme-' . $theme->themeId());
				if ($theme === $this) {
					$submenu->addClass('', '', 'active');
				}
				$menu->addSubmenu($submenu);
			}

			return $menu;
		} else {
			return null;
		}
	}

	/**
	 * Create the <link rel="canonical"> tag.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	protected function metaCanonicalUrl($url) {
		if ($url) {
			return '<link rel="canonical" href="' . $url . '">';
		} else {
			return '';
		}
	}

	/**
	 * Create the <meta charset=""> tag.
	 *
	 * @return string
	 */
	protected function metaCharset() {
		return '<meta charset="UTF-8">';
	}

	/**
	 * Create the <meta name="description"> tag.
	 *
	 * @param string $description
	 *
	 * @return string
	 */
	protected function metaDescription($description) {
		if ($description) {
			return '<meta name="description" content="' . $description . '">';
		} else {
			return '';
		}
	}

	/**
	 * Create the <meta name="generator"> tag.
	 *
	 * @param string $generator
	 *
	 * @return string
	 */
	protected function metaGenerator($generator) {
		if ($generator) {
			return '<meta name="generator" content="' . $generator . '">';
		} else {
			return '';
		}
	}

	/**
	 * Create the <meta name="robots"> tag.
	 *
	 * @param string $robots
	 *
	 * @return string
	 */
	protected function metaRobots($robots) {
		if ($robots) {
			return '<meta name="robots" content="' . $robots . '">';
		} else {
			return '';
		}
	}

	/**
	 * Create the <meta http-equiv="X-UA-Compatible"> tag.
	 *
	 * @return string
	 */
	protected function metaUaCompatible() {
		return '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
	}

	/**
	 * Create the <meta name="viewport" content="width=device-width, initial-scale=1"> tag.
	 *
	 * @return string
	 */
	protected function metaViewport() {
		return '<meta name="viewport" content="width=device-width, initial-scale=1">';
	}

	/**
	 * Misecellaneous dimensions, fonts, styles, etc.
	 *
	 * @param string $parameter_name
	 *
	 * @return string|integer|float
	 */
	public function parameter($parameter_name) {
		$parameters = array(
			'chart-background-f'             => 'dddddd',
			'chart-background-m'             => 'cccccc',
			'chart-background-u'             => 'eeeeee',
			'chart-box-x'                    => 250,
			'chart-box-y'                    => 80,
			'chart-descendancy-box-x'        => 260,
			'chart-descendancy-box-y'        => 80,
			'chart-descendancy-indent'       => 15,
			'chart-font-color'               => '000000',
			'chart-font-name'                => WT_ROOT . 'includes/fonts/DejaVuSans.ttf',
			'chart-font-size'                => 7,
			'chart-offset-x'                 => 10,
			'chart-offset-y'                 => 10,
			'chart-spacing-x'                => 1,
			'chart-spacing-y'                => 5,
			'compact-chart-box-x'            => 240,
			'compact-chart-box-y'            => 50,
			'distribution-chart-high-values' => '555555',
			'distribution-chart-low-values'  => 'cccccc',
			'distribution-chart-no-values'   => 'ffffff',
			'distribution-chart-x'           => 440,
			'distribution-chart-y'           => 220,
			'line-width'                     => 1.5,
			'shadow-blur'                    => 0,
			'shadow-color'                   => '',
			'shadow-offset-x'                => 0,
			'shadow-offset-y'                => 0,
			'stats-small-chart-x'            => 440,
			'stats-small-chart-y'            => 125,
			'stats-large-chart-x'            => 900,
			'image-dline'                    => $this->cssUrl() . 'images/dline.png',
			'image-dline2'                   => $this->cssUrl() . 'images/dline2.png',
			'image-hline'                    => $this->cssUrl() . 'images/hline.png',
			'image-spacer'                   => $this->cssUrl() . 'images/spacer.png',
			'image-vline'                    => $this->cssUrl() . 'images/vline.png',
			'image-add'                      => $this->cssUrl() . 'images/add.png',
			'image-button_family'            => $this->cssUrl() . 'images/buttons/family.png',
			'image-minus'                    => $this->cssUrl() . 'images/minus.png',
			'image-plus'                     => $this->cssUrl() . 'images/plus.png',
			'image-remove'                   => $this->cssUrl() . 'images/delete.png',
			'image-search'                   => $this->cssUrl() . 'images/go.png',
			'image-default_image_F'          => $this->cssUrl() . 'images/silhouette_female.png',
			'image-default_image_M'          => $this->cssUrl() . 'images/silhouette_male.png',
			'image-default_image_U'          => $this->cssUrl() . 'images/silhouette_unknown.png',
		);

		if (array_key_exists($parameter_name, $parameters)) {
			return $parameters[$parameter_name];
		} else {
			throw new \InvalidArgumentException($parameter_name);
		}
	}

	/**
	 * Are there any pending changes for us to approve?
	 *
	 * @return bool
	 */
	protected function pendingChangesExist() {
		return exists_pending_change(Auth::user(), $this->tree);
	}

	/**
	 * Create a pending changes link.
	 *
	 * @return string
	 */
	protected function pendingChangesLink() {
		return
			'<a href="#" onclick="window.open(\'edit_changes.php\', \'_blank\', chan_window_specs); return false;">' .
			$this->pendingChangesLinkText() .
			'</a>';
	}

	/**
	 * Text to use in the pending changes link.
	 *
	 * @return string
	 */
	protected function pendingChangesLinkText() {
		return WT_I18N::translate('There are pending changes for you to moderate.');
	}

	/**
	 * Generate a list of items for the main menu.
	 *
	 * @return WT_Menu[]
	 */
	protected function primaryMenu() {
		global $controller;

		if ($this->tree) {
			$individual = $controller->getSignificantIndividual();

			return array_filter(array_merge(array(
				$this->menuHomePage(),
				$this->menuMyMenu(),
				$this->menuChart($individual),
				$this->menuLists(),
				$this->menuCalendar(),
				$this->menuReports(),
				$this->menuSearch(),
			), $this->menuModules()));
		} else {
			// No public trees?  No genealogy menu!
			return array();
		}
	}

	/**
	 * Add markup to the primary menu.
	 *
	 * @param WT_Menu[] $menus
	 *
	 * @return string
	 */
	protected function primaryMenuContainer(array $menus) {
		$html = '';
		foreach ($menus as $menu) {
			$html .= $menu->getMenuAsList();
		}

		return '<nav><ul class="primary-menu">' . $html . '</ul></nav>';
	}

	/**
	 * Generate a list of items for the user menu.
	 *
	 * @return WT_Menu[]
	 */
	protected function secondaryMenu() {
		return array_filter(array(
			$this->menuPendingChanges(),
			$this->menuLogin(),
			$this->menuMyAccount(),
			$this->menuLogout(),
			$this->menuFavorites(),
			$this->menuLanguages(),
			$this->menuThemes(),
		));
	}

	/**
	 * Add markup to the secondary menu.
	 *
	 * @param WT_Menu[] $menus
	 *
	 * @return string
	 */
	protected function secondaryMenuContainer(array $menus) {
		$html = '';
		foreach ($menus as $menu) {
			$html .= $menu->getMenuAsList();
		}

		return '<ul class="nav nav-pills secondary-menu">' . $html . '</ul>';
	}

	/**
	 * Send any HTTP headers.
	 *
	 * @return void
	 */
	public function sendHeaders() {
		header('Content-Type: text/html; charset=UTF-8');
	}

	/**
	 * A list of CSS files to include for this page.
	 *
	 * @return string[]
	 */
	protected function stylesheets() {
		return array();
	}

	/**
	 * A fixed string to identify this theme, in settings, etc.
	 *
	 * @return string
	 */
	abstract public function themeId();

	/**
	 * What is this theme called?
	 *
	 * @return string
	 */
	abstract public function themeName();

	/**
	 * Create the <title> tag.
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	protected function title($title) {
		return '<title>' . WT_Filter::escapeHtml($title) . '</title>';
	}
}
