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
namespace Fisharebest\Webtrees\Theme;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\HitCounter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\AncestorsChartModule;
use Fisharebest\Webtrees\Module\CompactTreeChartModule;
use Fisharebest\Webtrees\Module\DescendancyChartModule;
use Fisharebest\Webtrees\Module\FamilyBookChartModule;
use Fisharebest\Webtrees\Module\FamilyTreeFavoritesModule;
use Fisharebest\Webtrees\Module\FanChartModule;
use Fisharebest\Webtrees\Module\GoogleMapsModule;
use Fisharebest\Webtrees\Module\HourglassChartModule;
use Fisharebest\Webtrees\Module\InteractiveTreeModule;
use Fisharebest\Webtrees\Module\LifespansChartModule;
use Fisharebest\Webtrees\Module\PedigreeChartModule;
use Fisharebest\Webtrees\Module\RelationshipsChartModule;
use Fisharebest\Webtrees\Module\StatisticsChartModule;
use Fisharebest\Webtrees\Module\TimelineChartModule;
use Fisharebest\Webtrees\Module\UserFavoritesModule;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;

/**
 * Common functions for all themes.
 */
abstract class AbstractTheme {
	/** @var Tree The current tree */
	protected $tree;

	/** @var string An escaped version of the "ged=XXX" URL parameter */
	protected $tree_url;

	/** @var int The number of times this page has been shown */
	protected $page_views;

	/**
	 * Custom themes should place their initialization code in the function hookAfterInit(), not in
	 * the constructor, as all themes get constructed - whether they are used or not.
	 */
	final public function __construct() {
	}

	/**
	 * Create accessibility links for the header.
	 *
	 * "Skip to content" allows keyboard only users to navigate over the headers without
	 * pressing TAB many times.
	 *
	 * @return string
	 */
	protected function accessibilityLinks() {
		return
			'<div class="accessibility-links">' .
			'<a class="sr-only sr-only-focusable btn btn-info btn-sm" href="#content">' .
			/* I18N: Skip over the headers and menus, to the main content of the page */ I18N::translate('Skip to content') .
			'</a>' .
			'</div>';
	}

	/**
	 * Create scripts for analytics and tracking.
	 *
	 * @return string
	 */
	protected function analytics() {
		if ($this->themeId() === '_administration' || !empty($_SERVER['HTTP_DNT'])) {
			return '';
		} else {
			return
				$this->analyticsBingWebmaster(
					Site::getPreference('BING_WEBMASTER_ID')
				) .
				$this->analyticsGoogleWebmaster(
					Site::getPreference('GOOGLE_WEBMASTER_ID')
				) .
				$this->analyticsGoogleTracker(
					Site::getPreference('GOOGLE_ANALYTICS_ID')
				) .
				$this->analyticsPiwikTracker(
					Site::getPreference('PIWIK_URL'),
					Site::getPreference('PIWIK_SITE_ID')
				) .
				$this->analyticsStatcounterTracker(
					Site::getPreference('STATCOUNTER_PROJECT_ID'),
					Site::getPreference('STATCOUNTER_SECURITY_ID')
				);
		}
	}

	/**
	 * Create the verification code for Google Webmaster Tools.
	 *
	 * @param string $verification_id
	 *
	 * @return string
	 */
	protected function analyticsBingWebmaster($verification_id) {
		// Only need to add this to the home page.
		if (WT_SCRIPT_NAME === 'index.php' && $verification_id) {
			return '<meta name="msvalidate.01" content="' . $verification_id . '">';
		} else {
			return '';
		}
	}

	/**
	 * Create the verification code for Google Webmaster Tools.
	 *
	 * @param string $verification_id
	 *
	 * @return string
	 */
	protected function analyticsGoogleWebmaster($verification_id) {
		// Only need to add this to the home page.
		if (WT_SCRIPT_NAME === 'index.php' && $verification_id) {
			return '<meta name="google-site-verification" content="' . $verification_id . '">';
		} else {
			return '';
		}
	}

	/**
	 * Create the tracking code for Google Analytics.
	 *
	 * See https://developers.google.com/analytics/devguides/collection/analyticsjs/advanced
	 *
	 * @param string $analytics_id
	 *
	 * @return string
	 */
	protected function analyticsGoogleTracker($analytics_id) {
		if ($analytics_id) {
			// Add extra dimensions (i.e. filtering categories)
			$dimensions = (object) array(
				'dimension1' => $this->tree ? $this->tree->getName() : '-',
				'dimension2' => $this->tree ? Auth::accessLevel($this->tree) : '-',
			);

			return
				'<script async src="https://www.google-analytics.com/analytics.js"></script>' .
				'<script>' .
				'window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;' .
				'ga("create","' . $analytics_id . '","auto");' .
				'ga("send", "pageview", ' . json_encode($dimensions) . ');' .
				'</script>';
		} else {
			return '';
		}
	}

	/**
	 * Create the tracking code for Piwik Analytics.
	 *
	 * @param string $url     - The domain/path to Piwik
	 * @param string $site_id - The Piwik site identifier
	 *
	 * @return string
	 */
	protected function analyticsPiwikTracker($url, $site_id) {
		$url = preg_replace(array('/^https?:\/\//', '/\/$/'), '', $url);

		if ($url && $site_id) {
			return
				'<script>' .
				'var _paq=_paq||[];' .
				'(function(){var u=(("https:"==document.location.protocol)?"https://' . $url . '/":"http://' . $url . '/");' .
				'_paq.push(["setSiteId",' . $site_id . ']);' .
				'_paq.push(["setTrackerUrl",u+"piwik.php"]);' .
				'_paq.push(["trackPageView"]);' .
				'_paq.push(["enableLinkTracking"]);' .
				'var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.defer=true;g.async=true;g.src=u+"piwik.js";' .
				's.parentNode.insertBefore(g,s);})();' .
				'</script>';
		} else {
			return '';
		}
	}

	/**
	 * Create the tracking code for Statcounter.
	 *
	 * @param string $project_id  - The statcounter project ID
	 * @param string $security_id - The statcounter security ID
	 *
	 * @return string
	 */
	protected function analyticsStatcounterTracker($project_id, $security_id) {
		if ($project_id && $security_id) {
			return
				'<script>' .
				'var sc_project=' . (int) $project_id . ',sc_invisible=1,sc_security="' . $security_id .
				'",scJsHost = (("https:"===document.location.protocol)?"https://secure.":"http://www.");' .
				'document.write("<sc"+"ript src=\'"+scJsHost+"statcounter.com/counter/counter.js\'></"+"script>");' .
				'</script>';
		} else {
			return '';
		}
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
			'<main id="content">' .
			$this->flashMessagesContainer(FlashMessages::getMessages());
	}

	/**
	 * Create the top of the <body> (for popup windows).
	 *
	 * @return string
	 */
	public function bodyHeaderPopupWindow() {
		return
			'<body class="container container-popup">' .
			'<main id="content">' .
			$this->flashMessagesContainer(FlashMessages::getMessages());
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
			return '<a href="mailto:' . Filter::escapeHtml($user->getEmail()) . '">' . $user->getRealNameHtml() . '</a>';
		default:
			return "<a href='#' onclick='message(\"" . Filter::escapeHtml($user->getUserName()) . "\", \"" . $method . "\", \"" . WT_BASE_URL . Filter::escapeHtml(Functions::getQueryUrl()) . "\", \"\");return false;'>" . $user->getRealNameHtml() . '</a>';
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
		return I18N::translate('For technical support or genealogy questions, please contact') . ' ' . $this->contactLink($user);
	}

	/**
	 * Create contact link for genealogy support.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	protected function contactLinkGenealogy(User $user) {
		return I18N::translate('For help with genealogy questions contact') . ' ' . $this->contactLink($user);
	}

	/**
	 * Create contact link for technical support.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	protected function contactLinkTechnical(User $user) {
		return I18N::translate('For technical support and information contact') . ' ' . $this->contactLink($user);
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
	 * Create a cookie warning.
	 *
	 * @return string
	 */
	public function cookieWarning() {
		if (
			empty($_SERVER['HTTP_DNT']) &&
			empty($_COOKIE['cookie']) &&
			(Site::getPreference('GOOGLE_ANALYTICS_ID') || Site::getPreference('PIWIK_SITE_ID') || Site::getPreference('STATCOUNTER_PROJECT_ID'))
		) {
			return
				'<div class="cookie-warning">' .
				I18N::translate('Cookies') . ' - ' .
				I18N::translate('This website uses cookies to learn about visitor behaviour.') . ' ' .
				'<button onclick="document.cookie=\'cookie=1\'; this.parentNode.classList.add(\'hidden\');">' . I18N::translate('continue') . '</button>' .
				'</div>';
		} else {
			return '';
		}
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
		return
			'<link rel="icon" href="' . $this->assetUrl() . 'favicon.png" type="image/png">' .
			'<link rel="icon" type="image/png" href="' . $this->assetUrl() . 'favicon192.png" sizes="192x192">' .
			'<link rel="apple-touch-icon" sizes="180x180" href="' . $this->assetUrl() . 'favicon180.png">';
	}

	/**
	 * Add markup to a flash message.
	 *
	 * @param \stdClass $message
	 *
	 * @return string
	 */
	protected function flashMessageContainer(\stdClass $message) {
		return $this->htmlAlert($message->text, $message->status, true);
	}

	/**
	 * Create a container for messages that are "flashed" to the session
	 * on one request, and displayed on another. If there are many messages,
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
	 * Close the main content and create the <footer> tag.
	 *
	 * @return string
	 */
	public function footerContainer() {
		return '</main><footer>' . $this->footerContent() . '</footer>';
	}

	/**
	 * Close the main content.
	 * Note that popup windows are deprecated
	 *
	 * @return string
	 */
	public function footerContainerPopupWindow() {
		return '</main>';
	}

	/**
	 * Create the contents of the <footer> tag.
	 *
	 * @return string
	 */
	protected function footerContent() {
		return
			$this->formatContactLinks() .
			$this->logoPoweredBy() .
			$this->formatPageViews($this->page_views) .
			$this->cookieWarning();
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
	 * Add markup to the hit counter.
	 *
	 * @param int $count
	 *
	 * @return string
	 */
	protected function formatPageViews($count) {
		if ($count > 0) {
			return
				'<div class="page-views">' .
				I18N::plural('This page has been viewed %s time.', 'This page has been viewed %s times.', $count,
					'<span class="odometer">' . I18N::digits($count) . '</span>') .
				'</div>';
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
	 * Create a quick search form for the header.
	 *
	 * @return string
	 */
	protected function formQuickSearch() {
		if ($this->tree) {
			return
				'<form action="search.php" class="header-search" role="search">' .
				'<input type="hidden" name="action" value="header">' .
				'<input type="hidden" name="ged" value="' . $this->tree->getNameHtml() . '">' .
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
			'<input type="search" name="query" size="15" placeholder="' . I18N::translate('Search') . '">' .
			'<input type="image" src="' . $this->assetUrl() . 'images/go.png" alt="' . I18N::translate('Search') . '">';
	}

	/**
	 * Add markup to the tree title.
	 *
	 * @return string
	 */
	protected function formatTreeTitle() {
		if ($this->tree) {
			return '<h1 class="header-title">' . $this->tree->getTitleHtml() . '</h1>';
		} else {
			return '';
		}
	}

	/**
	 * Add markup to the secondary menu.
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
	 * Add markup to an item in the secondary menu.
	 *
	 * @param Menu $menu
	 *
	 * @return string
	 */
	protected function formatSecondaryMenuItem(Menu $menu) {
		return $menu->getMenuAsList();
	}

	/**
	 * Create the <head> tag.
	 *
	 * @param PageController $controller The current controller
	 *
	 * @return string
	 */
	public function head(PageController $controller) {
		// Record this now. By the time we render the footer, $controller no longer exists.
		$this->page_views = $this->pageViews($controller);

		return
			'<head>' .
			$this->headContents($controller) .
			$this->hookHeaderExtraContent() .
			$this->analytics() .
			'</head>';
	}

	/**
	 * Create the contents of the <head> tag.
	 *
	 * @param PageController $controller The current controller
	 *
	 * @return string
	 */
	protected function headContents(PageController $controller) {
		// The title often includes the names of records, which may include HTML markup.
		$title = Filter::unescapeHtml($controller->getPageTitle());

		// If an extra (site) title is specified, append it.
		if ($this->tree && $this->tree->getPreference('META_TITLE')) {
			$title .= ' – ' . $this->tree->getPreference('META_TITLE');
		}

		$html =
			// modernizr.js and respond.js need to be loaded before the <body> to avoid FOUC
			'<!--[if IE 8]><script src="' . WT_MODERNIZR_JS_URL . '"></script><![endif]-->' .
			'<!--[if IE 8]><script src="' . WT_RESPOND_JS_URL . '"></script><![endif]-->' .
			$this->metaCharset() .
			$this->title($title) .
			$this->favicon() .
			$this->metaViewport() .
			$this->metaRobots($controller->getMetaRobots()) .
			$this->metaUaCompatible() .
			$this->metaGenerator(WT_WEBTREES . ' ' . WT_VERSION . ' - ' . WT_WEBTREES_URL);

		if ($this->tree) {
			$html .= $this->metaDescription($this->tree->getPreference('META_DESCRIPTION'));
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
			//$this->accessibilityLinks() .
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
			$this->flashMessagesContainer(FlashMessages::getMessages()) .
			'<div id="content">';
	}

	/**
	 * Allow themes to do things after initialization (since they cannot use
	 * the constructor).
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
		return '<html ' . I18N::htmlAttributes() . '>';
	}

	/**
	 * Add HTML markup to create an alert
	 *
	 * @param string $html        The content of the alert
	 * @param string $level       One of 'success', 'info', 'warning', 'danger'
	 * @param bool   $dismissible If true, add a close button.
	 *
	 * @return string
	 */
	public function htmlAlert($html, $level, $dismissible) {
		if ($dismissible) {
			return
				'<div class="alert alert-' . $level . ' alert-dismissible" role="alert">' .
				'<button type="button" class="close" data-dismiss="alert" aria-label="' . I18N::translate('close') . '">' .
				'<span aria-hidden="true">&times;</span>' .
				'</button>' .
				$html .
				'</div>';
		} else {
			return
				'<div class="alert alert-' . $level . '" role="alert">' .
				$html .
				'</div>';
		}
	}

	/**
	 * Display an icon for this fact.
	 *
	 * @param Fact $fact
	 *
	 * @return string
	 */
	public function icon(Fact $fact) {
		$icon = 'images/facts/' . $fact->getTag() . '.png';
		$dir  = substr($this->assetUrl(), strlen(WT_STATIC_URL));
		if (file_exists($dir . $icon)) {
			return '<img src="' . $this->assetUrl() . $icon . '" title="' . GedcomTag::getLabel($fact->getTag()) . '">';
		} elseif (file_exists($dir . 'images/facts/NULL.png')) {
			// Spacer image - for alignment - until we move to a sprite.
			return '<img src="' . Theme::theme()->assetUrl() . 'images/facts/NULL.png">';
		} else {
			return '';
		}
	}

	/**
	 * Display an individual in a box - for charts, etc.
	 *
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public function individualBox(Individual $individual) {
		$personBoxClass = array_search($individual->getSex(), array('person_box' => 'M', 'person_boxF' => 'F', 'person_boxNN' => 'U'));
		if ($individual->canShow() && $individual->getTree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
			$thumbnail = $individual->displayImage();
		} else {
			$thumbnail = '';
		}

		$content = '<span class="namedef name1">' . $individual->getFullName() . '</span>';
		$icons   = '';
		if ($individual->canShowName()) {
			$content =
				'<a href="' . $individual->getHtmlUrl() . '">' . $content . '</a>' .
				'<div class="namedef name1">' . $individual->getAddName() . '</div>';
			$icons   =
				'<div class="noprint icons">' .
				'<span class="iconz icon-zoomin" title="' . I18N::translate('Zoom in/out on this box.') . '"></span>' .
				'<div class="itr"><i class="icon-pedigree"></i><div class="popup">' .
				'<ul class="' . $personBoxClass . '">' . implode('', $this->individualBoxMenu($individual)) . '</ul>' .
				'</div>' .
				'</div>' .
				'</div>';
		}

		return
			'<div data-pid="' . $individual->getXref() . '" class="person_box_template ' . $personBoxClass . ' box-style1" style="width: ' . $this->parameter('chart-box-x') . 'px; min-height: ' . $this->parameter('chart-box-y') . 'px">' .
			$icons .
			'<div class="chart_textbox" style="max-height:' . $this->parameter('chart-box-y') . 'px;">' .
			$thumbnail .
			$content .
			'<div class="inout2 details1">' . $this->individualBoxFacts($individual) . '</div>' .
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
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public function individualBoxLarge(Individual $individual) {
		$personBoxClass = array_search($individual->getSex(), array('person_box' => 'M', 'person_boxF' => 'F', 'person_boxNN' => 'U'));
		if ($individual->getTree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
			$thumbnail = $individual->displayImage();
		} else {
			$thumbnail = '';
		}

		$content = '<span class="namedef name1">' . $individual->getFullName() . '</span>';
		$icons   = '';
		if ($individual->canShowName()) {
			$content =
				'<a href="' . $individual->getHtmlUrl() . '">' . $content . '</a>' .
				'<div class="namedef name2">' . $individual->getAddName() . '</div>';
			$icons   =
				'<div class="noprint icons">' .
				'<span class="iconz icon-zoomin" title="' . I18N::translate('Zoom in/out on this box.') . '"></span>' .
				'<div class="itr"><i class="icon-pedigree"></i><div class="popup">' .
				'<ul class="' . $personBoxClass . '">' . implode('', $this->individualBoxMenu($individual)) . '</ul>' .
				'</div>' .
				'</div>' .
				'</div>';
		}

		return
			'<div data-pid="' . $individual->getXref() . '" class="person_box_template ' . $personBoxClass . ' box-style2">' .
			$icons .
			'<div class="chart_textbox" style="max-height:' . $this->parameter('chart-box-y') . 'px;">' .
			$thumbnail .
			$content .
			'<div class="inout2 details2">' . $this->individualBoxFacts($individual) . '</div>' .
			'</div>' .
			'<div class="inout"></div>' .
			'</div>';
	}

	/**
	 * Display an individual in a box - for charts, etc.
	 *
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public function individualBoxSmall(Individual $individual) {
		$personBoxClass = array_search($individual->getSex(), array('person_box' => 'M', 'person_boxF' => 'F', 'person_boxNN' => 'U'));
		if ($individual->getTree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
			$thumbnail = $individual->displayImage();
		} else {
			$thumbnail = '';
		}

		return
			'<div data-pid="' . $individual->getXref() . '" class="person_box_template ' . $personBoxClass . ' iconz box-style0" style="width: ' . $this->parameter('compact-chart-box-x') . 'px; min-height: ' . $this->parameter('compact-chart-box-y') . 'px">' .
			'<div class="compact_view">' .
			$thumbnail .
			'<a href="' . $individual->getHtmlUrl() . '">' .
			'<span class="namedef name0">' . $individual->getFullName() . '</span>' .
			'</a>' .
			'<div class="inout2 details0">' . $individual->getLifeSpan() . '</div>' .
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
	 * @param Individual $individual
	 *
	 * @return string
	 */
	protected function individualBoxFacts(Individual $individual) {
		$html = '';

		$opt_tags = preg_split('/\W/', $individual->getTree()->getPreference('CHART_BOX_TAGS'), 0, PREG_SPLIT_NO_EMPTY);
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
					unset($opt_tags[$key]);
				}
			}
		}
		// Show DEAT or equivalent event
		foreach (explode('|', WT_EVENTS_DEAT) as $deattag) {
			$event = $individual->getFirstFact($deattag);
			if ($event) {
				$html .= $event->summary();
				if (in_array($deattag, $opt_tags)) {
					unset($opt_tags[array_search($deattag, $opt_tags)]);
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
	 * @param Individual $individual
	 *
	 * @return string
	 */
	protected function individualBoxLdsSummary(Individual $individual) {
		if ($individual->getTree()->getPreference('SHOW_LDS_AT_GLANCE')) {
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
	 * @param Individual $individual
	 *
	 * @return Menu[]
	 */
	public function individualBoxMenu(Individual $individual) {
		$menus = array_merge(
			$this->individualBoxMenuCharts($individual),
			$this->individualBoxMenuFamilyLinks($individual)
		);

		return $menus;
	}

	/**
	 * Chart links, to show in chart boxes;
	 *
	 * @param Individual $individual
	 *
	 * @return Menu[]
	 */
	protected function individualBoxMenuCharts(Individual $individual) {
		$menus = array();
		foreach (Module::getActiveCharts($this->tree) as $chart) {
			$menu = $chart->getBoxChartMenu($individual);
			if ($menu) {
				$menus[] = $menu;
			}
		}

		usort($menus, function (Menu $x, Menu $y) {
			return I18N::strcasecmp($x->getLabel(), $y->getLabel());
		});

		return $menus;
	}

	/**
	 * Family links, to show in chart boxes.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu[]
	 */
	protected function individualBoxMenuFamilyLinks(Individual $individual) {
		$menus = array();

		foreach ($individual->getSpouseFamilies() as $family) {
			$menus[] = new Menu('<strong>' . I18N::translate('Family with spouse') . '</strong>', $family->getHtmlUrl());
			$spouse  = $family->getSpouse($individual);
			if ($spouse && $spouse->canShowName()) {
				$menus[] = new Menu($spouse->getFullName(), $spouse->getHtmlUrl());
			}
			foreach ($family->getChildren() as $child) {
				if ($child->canShowName()) {
					$menus[] = new Menu($child->getFullName(), $child->getHtmlUrl());
				}
			}
		}

		return $menus;
	}

	/**
	 * Create part of an individual box
	 *
	 * @param Individual $individual
	 *
	 * @return string
	 */
	protected function individualBoxSexSymbol(Individual $individual) {
		if ($individual->getTree()->getPreference('PEDIGREE_SHOW_GENDER')) {
			return $individual->sexImage('large');
		} else {
			return '';
		}
	}

	/**
	 * Initialise the theme. We cannot pass these in a constructor, as the construction
	 * happens in a theme file, and we need to be able to change it.
	 *
	 * @param Tree|null $tree The current tree (if there is one).
	 */
	final public function init(Tree $tree = null) {
		$this->tree     = $tree;
		$this->tree_url = $tree ? 'ged=' . $tree->getNameUrl() : '';

		$this->hookAfterInit();
	}

	/**
	 * A large webtrees logo, for the header.
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
	 * A menu for the day/month/year calendar views.
	 *
	 * @return Menu
	 */
	protected function menuCalendar() {
		return new Menu(I18N::translate('Calendar'), '#', 'menu-calendar', array('rel' => 'nofollow'), array(
			// Day view
			new Menu(I18N::translate('Day'), 'calendar.php?' . $this->tree_url . '&amp;view=day', 'menu-calendar-day', array('rel' => 'nofollow')),
			// Month view
			new Menu(I18N::translate('Month'), 'calendar.php?' . $this->tree_url . '&amp;view=month', 'menu-calendar-month', array('rel' => 'nofollow')),
			//Year view
			new Menu(I18N::translate('Year'), 'calendar.php?' . $this->tree_url . '&amp;view=year', 'menu-calendar-year', array('rel' => 'nofollow')),
		));
	}

	/**
	 * Generate a menu item to change the blocks on the current (index.php) page.
	 *
	 * @return Menu|null
	 */
	protected function menuChangeBlocks() {
		if (WT_SCRIPT_NAME === 'index.php' && Auth::check() && Filter::get('ctype', 'gedcom|user', 'user') === 'user') {
			return new Menu(I18N::translate('Customize this page'), 'index_edit.php?user_id=' . Auth::id(), 'menu-change-blocks');
		} elseif (WT_SCRIPT_NAME === 'index.php' && Auth::isManager($this->tree)) {
			return new Menu(I18N::translate('Customize this page'), 'index_edit.php?gedcom_id=' . $this->tree->getTreeId(), 'menu-change-blocks');
		} else {
			return null;
		}
	}

	/**
	 * Generate a menu for each of the different charts.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 */
	protected function menuChart(Individual $individual) {
		$submenus = array();
		foreach (Module::getActiveCharts($this->tree) as $chart) {
			$menu = $chart->getChartMenu($individual);
			if ($menu) {
				$submenus[] = $menu;
			}
		}

		if ($submenus) {
			usort($submenus, function (Menu $x, Menu $y) {
				return I18N::strcasecmp($x->getLabel(), $y->getLabel());
			});

			return new Menu(I18N::translate('Charts'), '#', 'menu-chart', array('rel' => 'nofollow'), $submenus);
		} else {
			return null;
		}
	}

	/**
	 * Generate a menu item for the ancestors chart.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartAncestors(Individual $individual) {
		$chart = new AncestorsChartModule(WT_ROOT . WT_MODULES_DIR . 'ancestors_chart');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the compact tree.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartCompact(Individual $individual) {
		$chart = new CompactTreeChartModule(WT_ROOT . WT_MODULES_DIR . 'compact_tree_chart');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the descendants chart.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartDescendants(Individual $individual) {
		$chart = new DescendancyChartModule(WT_ROOT . WT_MODULES_DIR . 'descendancy_chart');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the family-book chart.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartFamilyBook(Individual $individual) {
		$chart = new FamilyBookChartModule(WT_ROOT . WT_MODULES_DIR . 'family_book_chart');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the fan chart.
	 *
	 * We can only do this if the GD2 library is installed with TrueType support.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartFanChart(Individual $individual) {
		$chart = new FanChartModule(WT_ROOT . WT_MODULES_DIR . 'fan_chart');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the interactive tree.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartInteractiveTree(Individual $individual) {
		$chart = new InteractiveTreeModule(WT_ROOT . WT_MODULES_DIR . 'tree');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the hourglass chart.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartHourglass(Individual $individual) {
		$chart = new HourglassChartModule(WT_ROOT . WT_MODULES_DIR . 'hourglass_chart');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the lifepsan chart.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartLifespan(Individual $individual) {
		$chart = new LifespansChartModule(WT_ROOT . WT_MODULES_DIR . 'lifespans_chart');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the pedigree chart.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartPedigree(Individual $individual) {
		$chart = new PedigreeChartModule(WT_ROOT . WT_MODULES_DIR . 'pedigree_chart');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the pedigree map.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartPedigreeMap(Individual $individual) {
		$chart = new GoogleMapsModule(WT_ROOT . WT_MODULES_DIR . 'googlemap');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the relationship chart.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartRelationship(Individual $individual) {
		$chart = new RelationshipsChartModule(WT_ROOT . WT_MODULES_DIR . 'relationships_chart');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the statistics charts.
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartStatistics() {
		$chart = new StatisticsChartModule(WT_ROOT . WT_MODULES_DIR . 'statistics_chart');

		return $chart->getChartMenu(null);
	}

	/**
	 * Generate a menu item for the timeline chart.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 *
	 * @deprecated
	 */
	protected function menuChartTimeline(Individual $individual) {
		$chart = new TimelineChartModule(WT_ROOT . WT_MODULES_DIR . 'timeline_chart');

		return $chart->getChartMenu($individual);
	}

	/**
	 * Generate a menu item for the control panel.
	 *
	 * @return Menu|null
	 */
	protected function menuControlPanel() {
		if (Auth::isManager($this->tree)) {
			return new Menu(I18N::translate('Control panel'), 'admin.php', 'menu-admin');
		} else {
			return null;
		}
	}

	/**
	 * Favorites menu.
	 *
	 * @return Menu|null
	 */
	protected function menuFavorites() {
		global $controller;

		$show_user_favorites = $this->tree && Module::getModuleByName('user_favorites') && Auth::check();
		$show_tree_favorites = $this->tree && Module::getModuleByName('gedcom_favorites');

		if ($show_user_favorites && $show_tree_favorites) {
			$favorites = array_merge(
				FamilyTreeFavoritesModule::getFavorites($this->tree->getTreeId()),
				UserFavoritesModule::getFavorites(Auth::id())
			);
		} elseif ($show_user_favorites) {
			$favorites = UserFavoritesModule::getFavorites(Auth::id());
		} elseif ($show_tree_favorites) {
			$favorites = FamilyTreeFavoritesModule::getFavorites($this->tree->getTreeId());
		} else {
			return null;
		}

		$menu = new Menu(I18N::translate('Favorites'), '#', 'menu-favorites');

		foreach ($favorites as $favorite) {
			switch ($favorite['type']) {
			case 'URL':
				$submenu = new Menu($favorite['title'], $favorite['url']);
				$menu->addSubmenu($submenu);
				break;
			case 'INDI':
			case 'FAM':
			case 'SOUR':
			case 'OBJE':
			case 'NOTE':
				$obj = GedcomRecord::getInstance($favorite['gid'], $this->tree);
				if ($obj && $obj->canShowName()) {
					$menu->addSubmenu(new Menu($obj->getFullName(), $obj->getHtmlUrl()));
				}
				break;
			}
		}

		if ($show_user_favorites && isset($controller->record) && $controller->record instanceof GedcomRecord) {
			$menu->addSubmenu(new Menu(I18N::translate('Add to favorites'), '#', '', array(
				'onclick' => 'jQuery.post("module.php?mod=user_favorites&mod_action=menu-add-favorite", {xref:"' . $controller->record->getXref() . '"},function(){location.reload();})',
			)));
		}

		return $menu;
	}

	/**
	 * A menu for the home (family tree) pages.
	 *
	 * @return Menu
	 */
	protected function menuHomePage() {
		if (count(Tree::getAll()) === 1 || Site::getPreference('ALLOW_CHANGE_GEDCOM') === '0') {
			return new Menu(I18N::translate('Family tree'), 'index.php?ctype=gedcom&amp;' . $this->tree_url, 'menu-tree');
		} else {
			$submenus = array();
			foreach (Tree::getAll() as $tree) {
				if ($tree == $this->tree) {
					$active = 'active ';
				} else {
					$active = '';
				}
				$submenus[] = new Menu($tree->getTitleHtml(), 'index.php?ctype=gedcom&amp;ged=' . $tree->getNameUrl(), $active . 'menu-tree-' . $tree->getTreeId());
			}

			return new Menu(I18N::translate('Family trees'), '#', 'menu-tree', array(), $submenus);
		}
	}

	/**
	 * A menu to show a list of available languages.
	 *
	 * @return Menu|null
	 */
	protected function menuLanguages() {
		$menu = new Menu(I18N::translate('Language'), '#', 'menu-language');

		foreach (I18N::activeLocales() as $locale) {
			$language_tag = $locale->languageTag();
			$class        = 'menu-language-' . $language_tag . (WT_LOCALE === $language_tag ? ' active' : '');
			$menu->addSubmenu(new Menu($locale->endonym(), '#', $class, array(
				'onclick'       => 'return false;',
				'data-language' => $language_tag,
			)));
		}

		if (count($menu->getSubmenus()) > 1) {
			return $menu;
		} else {
			return null;
		}
	}

	/**
	 * Create a menu to show lists of individuals, families, sources, etc.
	 *
	 * @param string $surname The significant surname on the page
	 *
	 * @return Menu
	 */
	protected function menuLists($surname) {
		// Do not show empty lists
		$row = Database::prepare(
			"SELECT SQL_CACHE" .
			" EXISTS(SELECT 1 FROM `##sources` WHERE s_file = ?) AS sour," .
			" EXISTS(SELECT 1 FROM `##other` WHERE o_file = ? AND o_type='REPO') AS repo," .
			" EXISTS(SELECT 1 FROM `##other` WHERE o_file = ? AND o_type='NOTE') AS note," .
			" EXISTS(SELECT 1 FROM `##media` WHERE m_file = ?) AS obje"
		)->execute(array(
			$this->tree->getTreeId(),
			$this->tree->getTreeId(),
			$this->tree->getTreeId(),
			$this->tree->getTreeId(),
		))->fetchOneRow();

		$submenus = array(
			$this->menuListsIndividuals($surname),
			$this->menuListsFamilies($surname),
			$this->menuListsBranches($surname),
			$this->menuListsPlaces(),
		);
		if ($row->obje) {
			$submenus[] = $this->menuListsMedia();
		}
		if ($row->repo) {
			$submenus[] = $this->menuListsRepositories();
		}
		if ($row->sour) {
			$submenus[] = $this->menuListsSources();
		}
		if ($row->note) {
			$submenus[] = $this->menuListsNotes();
		}

		uasort($submenus, function (Menu $x, Menu $y) {
			return I18N::strcasecmp($x->getLabel(), $y->getLabel());
		});

		return new Menu(I18N::translate('Lists'), '#', 'menu-list', array(), $submenus);
	}

	/**
	 * A menu for the list of branches
	 *
	 * @param string $surname The significant surname on the page
	 *
	 * @return Menu
	 */
	protected function menuListsBranches($surname) {
		return new Menu(I18N::translate('Branches'), 'branches.php?ged=' . $this->tree->getNameUrl() . '&amp;surname=' . rawurlencode($surname), 'menu-branches', array('rel' => 'nofollow'));
	}

	/**
	 * A menu for the list of families
	 *
	 * @param string $surname The significant surname on the page
	 *
	 * @return Menu
	 */
	protected function menuListsFamilies($surname) {
		return new Menu(I18N::translate('Families'), 'famlist.php?ged=' . $this->tree->getNameUrl() . '&amp;surname=' . rawurlencode($surname), 'menu-list-fam', array('rel' => 'nofollow'));
	}

	/**
	 * A menu for the list of individuals
	 *
	 * @param string $surname The significant surname on the page
	 *
	 * @return Menu
	 */
	protected function menuListsIndividuals($surname) {
		return new Menu(I18N::translate('Individuals'), 'indilist.php?ged=' . $this->tree->getNameUrl() . '&amp;surname=' . rawurlencode($surname), 'menu-list-indi');
	}

	/**
	 * A menu for the list of media objects
	 *
	 * @return Menu
	 */
	protected function menuListsMedia() {
		return new Menu(I18N::translate('Media objects'), 'medialist.php?' . $this->tree_url, 'menu-list-obje', array('rel' => 'nofollow'));
	}

	/**
	 * A menu for the list of notes
	 *
	 * @return Menu
	 */
	protected function menuListsNotes() {
		return new Menu(I18N::translate('Shared notes'), 'notelist.php?' . $this->tree_url, 'menu-list-note', array('rel' => 'nofollow'));
	}

	/**
	 * A menu for the list of individuals
	 *
	 * @return Menu
	 */
	protected function menuListsPlaces() {
		return new Menu(I18N::translate('Place hierarchy'), 'placelist.php?ged=' . $this->tree->getNameUrl(), 'menu-list-plac', array('rel' => 'nofollow'));
	}

	/**
	 * A menu for the list of repositories
	 *
	 * @return Menu
	 */
	protected function menuListsRepositories() {
		return new Menu(I18N::translate('Repositories'), 'repolist.php?' . $this->tree_url, 'menu-list-repo', array('rel' => 'nofollow'));
	}

	/**
	 * A menu for the list of sources
	 *
	 * @return Menu
	 */
	protected function menuListsSources() {
		return new Menu(I18N::translate('Sources'), 'sourcelist.php?' . $this->tree_url, 'menu-list-sour', array('rel' => 'nofollow'));
	}

	/**
	 * A login menu option (or null if we are already logged in).
	 *
	 * @return Menu|null
	 */
	protected function menuLogin() {
		if (Auth::check() || WT_SCRIPT_NAME === 'login.php') {
			return null;
		} else {
			return new Menu(I18N::translate('Sign in'), WT_LOGIN_URL . '?url=' . rawurlencode(Functions::getQueryUrl()), 'menu-login', array('rel' => 'nofollow'));
		}
	}

	/**
	 * A logout menu option (or null if we are already logged out).
	 *
	 * @return Menu|null
	 */
	protected function menuLogout() {
		if (Auth::check()) {
			return new Menu(I18N::translate('Sign out'), 'logout.php', 'menu-logout');
		} else {
			return null;
		}
	}

	/**
	 * Get the additional menus created by each of the modules
	 *
	 * @return Menu[]
	 */
	protected function menuModules() {
		$menus = array();
		foreach (Module::getActiveMenus($this->tree) as $module) {
			$menus[] = $module->getMenu();
		}

		return array_filter($menus);
	}

	/**
	 * A link to allow users to edit their account settings (edituser.php).
	 *
	 * @return Menu|null
	 */
	protected function menuMyAccount() {
		if (Auth::check()) {
			return new Menu(I18N::translate('My account'), 'edituser.php');
		} else {
			return null;
		}
	}

	/**
	 * A link to the user's individual record (individual.php).
	 *
	 * @return Menu|null
	 */
	protected function menuMyIndividualRecord() {
		$gedcomid = $this->tree->getUserPreference(Auth::user(), 'gedcomid');

		if ($gedcomid) {
			return new Menu(I18N::translate('My individual record'), 'individual.php?pid=' . $gedcomid . '&amp;' . $this->tree_url, 'menu-myrecord');
		} else {
			return null;
		}
	}

	/**
	 * A link to the user's personal home page.
	 *
	 * @return Menu
	 */
	protected function menuMyPage() {
		return new Menu(I18N::translate('My page'), 'index.php?ctype=user&amp;' . $this->tree_url, 'menu-mypage');
	}

	/**
	 * A menu for the user's personal pages.
	 *
	 * @return Menu|null
	 */
	protected function menuMyPages() {
		if (Auth::id()) {
			return new Menu(I18N::translate('My pages'), '#', 'menu-mymenu', array(), array_filter(array(
				$this->menuMyPage(),
				$this->menuMyIndividualRecord(),
				$this->menuMyPedigree(),
				$this->menuMyAccount(),
				$this->menuChangeBlocks(),
				$this->menuControlPanel(),
			)));
		} else {
			return null;
		}
	}

	/**
	 * A link to the user's individual record.
	 *
	 * @return Menu|null
	 */
	protected function menuMyPedigree() {
		$gedcomid = $this->tree->getUserPreference(Auth::user(), 'gedcomid');

		if ($gedcomid && Module::isActiveChart($this->tree, 'pedigree_chart')) {
			$showFull   = $this->tree->getPreference('PEDIGREE_FULL_DETAILS') ? 1 : 0;
			$showLayout = $this->tree->getPreference('PEDIGREE_LAYOUT') ? 1 : 0;

			return new Menu(
				I18N::translate('My pedigree'),
				'pedigree.php?' . $this->tree_url . '&amp;rootid=' . $gedcomid . '&amp;show_full=' . $showFull . '&amp;talloffset=' . $showLayout,
				'menu-mypedigree'
			);
		} else {
			return null;
		}
	}

	/**
	 * Create a pending changes menu.
	 *
	 * @return Menu|null
	 */
	protected function menuPendingChanges() {
		if ($this->pendingChangesExist()) {
			$menu = new Menu(I18N::translate('Pending changes'), '#', 'menu-pending', array('onclick' => 'window.open("edit_changes.php", "_blank", chan_window_specs); return false;'));

			return $menu;
		} else {
			return null;
		}
	}

	/**
	 * A menu with a list of reports.
	 *
	 * @return Menu|null
	 */
	protected function menuReports() {
		$submenus = array();
		foreach (Module::getActiveReports($this->tree) as $report) {
			$submenus[] = $report->getReportMenu();
		}

		if ($submenus) {
			return new Menu(I18N::translate('Reports'), '#', 'menu-report', array('rel' => 'nofollow'), $submenus);
		} else {
			return null;
		}
	}

	/**
	 * Create the search menu
	 *
	 * @return Menu
	 */
	protected function menuSearch() {
		//-- main search menu item
		$menu = new Menu(I18N::translate('Search'), '#', 'menu-search', array('rel' => 'nofollow'));
		//-- search_general sub menu
		$menu->addSubmenu(new Menu(I18N::translate('General search'), 'search.php?' . $this->tree_url, 'menu-search-general', array('rel' => 'nofollow')));
		//-- search_soundex sub menu
		$menu->addSubmenu(new Menu(/* I18N: search using “sounds like”, rather than exact spelling */
			I18N::translate('Phonetic search'), 'search.php?' . $this->tree_url . '&amp;action=soundex', 'menu-search-soundex', array('rel' => 'nofollow')));
		//-- advanced search
		$menu->addSubmenu(new Menu(I18N::translate('Advanced search'), 'search_advanced.php?' . $this->tree_url, 'menu-search-advanced', array('rel' => 'nofollow')));
		//-- search_replace sub menu
		if (Auth::isEditor($this->tree)) {
			$menu->addSubmenu(new Menu(I18N::translate('Search and replace'), 'search.php?' . $this->tree_url . '&amp;action=replace', 'menu-search-replace'));
		}

		return $menu;
	}

	/**
	 * Themes menu.
	 *
	 * @return Menu|null
	 */
	public function menuThemes() {
		if ($this->tree && Site::getPreference('ALLOW_USER_THEMES') && $this->tree->getPreference('ALLOW_THEME_DROPDOWN')) {
			$submenus = array();
			foreach (Theme::installedThemes() as $theme) {
				$class      = 'menu-theme-' . $theme->themeId() . ($theme === $this ? ' active' : '');
				$submenus[] = new Menu($theme->themeName(), '#', $class, array(
					'onclick'    => 'return false;',
					'data-theme' => $theme->themeId(),
				));
			}

			usort($submenus, function (Menu $x, Menu $y) {
				return I18N::strcasecmp($x->getLabel(), $y->getLabel());
			});

			$menu = new Menu(I18N::translate('Theme'), '#', 'menu-theme', array(), $submenus);

			return $menu;
		} else {
			return null;
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
	 * How many times has the current page been shown?
	 *
	 * @param  PageController $controller
	 *
	 * @return int Number of views, or zero for pages that aren't logged.
	 */
	protected function pageViews(PageController $controller) {
		if ($this->tree && $this->tree->getPreference('SHOW_COUNTER')) {
			if (isset($controller->record) && $controller->record instanceof GedcomRecord) {
				return HitCounter::countHit($this->tree, WT_SCRIPT_NAME, $controller->record->getXref());
			} elseif (isset($controller->root) && $controller->root instanceof GedcomRecord) {
				return HitCounter::countHit($this->tree, WT_SCRIPT_NAME, $controller->root->getXref());
			} elseif (WT_SCRIPT_NAME === 'index.php') {
				if (Auth::check() && Filter::get('ctype') !== 'gedcom') {
					return HitCounter::countHit($this->tree, WT_SCRIPT_NAME, 'user:' . Auth::id());
				} else {
					return HitCounter::countHit($this->tree, WT_SCRIPT_NAME, 'gedcom:' . $this->tree->getTreeId());
				}
			}
		}

		return 0;
	}

	/**
	 * Misecellaneous dimensions, fonts, styles, etc.
	 *
	 * @param string $parameter_name
	 *
	 * @return string|int|float
	 */
	public function parameter($parameter_name) {
		$parameters = array(
			'chart-background-f'             => 'dddddd',
			'chart-background-m'             => 'cccccc',
			'chart-background-u'             => 'eeeeee',
			'chart-box-x'                    => 250,
			'chart-box-y'                    => 80,
			'chart-descendancy-indent'       => 15,
			'chart-font-color'               => '000000',
			'chart-font-name'                => WT_ROOT . 'packages/dejavu-fonts-ttf-2.35/ttf/DejaVuSans.ttf',
			'chart-font-size'                => 7,
			'chart-spacing-x'                => 5,
			'chart-spacing-y'                => 10,
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
			'image-dline'                    => $this->assetUrl() . 'images/dline.png',
			'image-dline2'                   => $this->assetUrl() . 'images/dline2.png',
			'image-hline'                    => $this->assetUrl() . 'images/hline.png',
			'image-spacer'                   => $this->assetUrl() . 'images/spacer.png',
			'image-vline'                    => $this->assetUrl() . 'images/vline.png',
			'image-minus'                    => $this->assetUrl() . 'images/minus.png',
			'image-plus'                     => $this->assetUrl() . 'images/plus.png',
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
		return $this->tree && $this->tree->hasPendingEdit() && Auth::isModerator($this->tree);
	}

	/**
	 * Create a pending changes link. Some themes prefer an alert/banner to a menu.
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
		return I18N::translate('There are pending changes for you to moderate.');
	}

	/**
	 * Generate a list of items for the main menu.
	 *
	 * @return Menu[]
	 */
	protected function primaryMenu() {
		global $controller;

		if ($this->tree) {
			$individual = $controller->getSignificantIndividual();

			return array_filter(array_merge(array(
				$this->menuHomePage(),
				$this->menuChart($individual),
				$this->menuLists($controller->getSignificantSurname()),
				$this->menuCalendar(),
				$this->menuReports(),
				$this->menuSearch(),
			), $this->menuModules()));
		} else {
			// No public trees? No genealogy menu!
			return array();
		}
	}

	/**
	 * Add markup to the primary menu.
	 *
	 * @param Menu[] $menus
	 *
	 * @return string
	 */
	protected function primaryMenuContainer(array $menus) {
		return '<nav><ul class="primary-menu">' . $this->primaryMenuContent($menus) . '</ul></nav>';
	}

	/**
	 * Create the primary menu.
	 *
	 * @param Menu[] $menus
	 *
	 * @return string
	 */
	protected function primaryMenuContent(array $menus) {
		return implode('', array_map(function (Menu $menu) {
			return $menu->getMenuAsList();
		}, $menus));
	}

	/**
	 * Generate a list of items for the user menu.
	 *
	 * @return Menu[]
	 */
	protected function secondaryMenu() {
		return array_filter(array(
			$this->menuPendingChanges(),
			$this->menuMyPages(),
			$this->menuFavorites(),
			$this->menuThemes(),
			$this->menuLanguages(),
			$this->menuLogin(),
			$this->menuLogout(),
		));
	}

	/**
	 * Add markup to the secondary menu.
	 *
	 * @param Menu[] $menus
	 *
	 * @return string
	 */
	protected function secondaryMenuContainer(array $menus) {
		return '<ul class="nav nav-pills secondary-menu">' . $this->secondaryMenuContent($menus) . '</ul>';
	}

	/**
	 * Format the secondary menu.
	 *
	 * @param Menu[] $menus
	 *
	 * @return string
	 */
	protected function secondaryMenuContent(array $menus) {
		return implode('', array_map(function (Menu $menu) {
			return $menu->getMenuAsList();
		}, $menus));
	}

	/**
	 * Send any HTTP headers.
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
		$stylesheets = array(
			WT_BOOTSTRAP_CSS_URL,
			WT_FONT_AWESOME_CSS_URL,
			WT_FONT_AWESOME_RTL_CSS_URL,
		);

		if (I18N::direction() === 'rtl') {
			$stylesheets[] = WT_BOOTSTRAP_RTL_CSS_URL;
		}

		return $stylesheets;
	}

	/**
	 * Create the <title> tag.
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	protected function title($title) {
		return '<title>' . Filter::escapeHtml($title) . '</title>';
	}
}
