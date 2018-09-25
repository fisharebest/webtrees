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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Theme;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
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
use Fisharebest\Webtrees\Module\HourglassChartModule;
use Fisharebest\Webtrees\Module\InteractiveTreeModule;
use Fisharebest\Webtrees\Module\LifespansChartModule;
use Fisharebest\Webtrees\Module\PedigreeChartModule;
use Fisharebest\Webtrees\Module\RelationshipsChartModule;
use Fisharebest\Webtrees\Module\StatisticsChartModule;
use Fisharebest\Webtrees\Module\TimelineChartModule;
use Fisharebest\Webtrees\Module\UserFavoritesModule;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use stdClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * Common functions for all themes.
 */
abstract class AbstractTheme
{
    /**
     * Where are our CSS, JS and other assets?
     */
    const THEME_DIR  = '_common';
    const ASSET_DIR  = 'themes/' . self::THEME_DIR . '/css-2.0.0/';
    const STYLESHEET = self::ASSET_DIR . 'style.css';

    // Icons are created using <i class="..."></i>
    const ICONS = [
        // Icons for GEDCOM records
        'family'               => 'fas fa-users',
        'individual'           => 'far fa-user',
        'media'                => 'far fa-file-image',
        'note'                 => 'far fa-sticky-note',
        'repository'           => 'fas fa-university',
        'source'               => 'far fa-file-alt',
        'submission'           => 'fas fa-upload',
        'submitter'            => 'fas fa-user-plus',

        // Icons for sexes
        'F'                    => 'fas fa-venus',
        'M'                    => 'fas fa-mars',
        'U'                    => 'fas fa-genderless',

        // Icons for editing
        'add'                  => 'fas fa-plus',
        'config'               => 'far fa-cogs',
        'copy'                 => 'far fa-copy',
        'create'               => 'fas fa-plus',
        'delete'               => 'fas fa-trash-alt',
        'edit'                 => 'fas fa-pencil-alt',
        'link'                 => 'fas fa-link',
        'unlink'               => 'fas fa-unlink',

        // Icons for arrows
        'arrow-down'           => 'fas fa-arrow-down',
        'arrow-left'           => 'fas fa-arrow-left',
        'arrow-right'          => 'fas fa-arrow-right',
        'arrow-up'             => 'fas fa-arrow-up',

        // Status icons
        'error'                => 'fas fa-exclamation-triangle',
        'info'                 => 'fas fa-info-circle',
        'warning'              => 'fas fa-exclamation-circle',

        // Icons for file types
        'mime-application-pdf' => '',
        'mime-text-html'       => '',

        // Other icons
        'mail'                 => 'far fa-envelope',
        'help'                 => 'fas fa-info-circle',
        'search'               => 'fas fa-search',
    ];

    const PERSON_BOX_CLASSES = [
        'M' => 'person_box',
        'F' => 'person_boxF',
        'U' => 'person_boxNN',
    ];

    /** @var  Request */
    protected $request;

    /** @var Tree|null */
    protected $tree;

    /**
     * Custom themes should place their initialization code in the function hookAfterInit(), not in
     * the constructor, as all themes get constructed - whether they are used or not.
     */
    final public function __construct()
    {
    }

    /**
     * Create accessibility links for the header.
     *
     * "Skip to content" allows keyboard only users to navigate over the headers without
     * pressing TAB many times.
     *
     * @return string
     */
    public function accessibilityLinks(): string
    {
        return
            '<div class="wt-accessibility-links">' .
            '<a class="sr-only sr-only-focusable btn btn-info btn-sm" href="#content">' .
            /* I18N: Skip over the headers and menus, to the main content of the page */
            I18N::translate('Skip to content') .
            '</a>' .
            '</div>';
    }

    /**
     * Create scripts for analytics and tracking.
     *
     * @return string
     */
    public function analytics()
    {
        if (!empty($_SERVER['HTTP_DNT'])) {
            return '';
        }

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

    /**
     * Create the verification code for Google Webmaster Tools.
     *
     * @param string $verification_id
     *
     * @return string
     */
    public function analyticsBingWebmaster($verification_id): string
    {
        return '<meta name="msvalidate.01" content="' . $verification_id . '">';
    }

    /**
     * Create the verification code for Google Webmaster Tools.
     *
     * @param string $verification_id
     *
     * @return string
     */
    public function analyticsGoogleWebmaster($verification_id): string
    {
        return '<meta name="google-site-verification" content="' . $verification_id . '">';
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
    public function analyticsGoogleTracker($analytics_id)
    {
        if ($analytics_id) {
            // Add extra dimensions (i.e. filtering categories)
            $dimensions = (object) [
                'dimension1' => $this->tree ? $this->tree->getName() : '-',
                'dimension2' => $this->tree ? Auth::accessLevel($this->tree) : '-',
            ];

            return
                '<script async src="https://www.google-analytics.com/analytics.js"></script>' .
                '<script>' .
                'window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;' .
                'ga("create","' . $analytics_id . '","auto");' .
                'ga("send", "pageview", ' . json_encode($dimensions) . ');' .
                '</script>';
        }

        return '';
    }

    /**
     * Create the tracking code for Piwik Analytics.
     *
     * @param string $url     - The domain/path to Piwik
     * @param string $site_id - The Piwik site identifier
     *
     * @return string
     */
    public function analyticsPiwikTracker($url, $site_id)
    {
        $url = preg_replace([
            '/^https?:\/\//',
            '/\/$/',
        ], '', $url);

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
        }

        return '';
    }

    /**
     * Create the tracking code for Statcounter.
     *
     * @param string $project_id  - The statcounter project ID
     * @param string $security_id - The statcounter security ID
     *
     * @return string
     */
    public function analyticsStatcounterTracker($project_id, $security_id)
    {
        if ($project_id && $security_id) {
            return
                '<script>' .
                'var sc_project=' . (int) $project_id . ',sc_invisible=1,sc_security="' . $security_id .
                '",scJsHost = (("https:"===document.location.protocol)?"https://secure.":"http://www.");' .
                'document.write("<sc"+"ript src=\'"+scJsHost+"statcounter.com/counter/counter.js\'></"+"script>");' .
                '</script>';
        }

        return '';
    }

    /**
     * Where are our CSS, JS and other assets?
     *
     * @deprecated - use the constant directly
     *
     * @return string A relative path, such as "themes/foo/"
     */
    public function assetUrl(): string
    {
        return self::ASSET_DIR;
    }

    /**
     * Create a contact link for a user.
     *
     * @param User $user
     *
     * @return string
     */
    public function contactLink(User $user): string
    {
        $method = $user->getPreference('contactmethod');

        switch ($method) {
            case 'none':
                return '';
            case 'mailto':
                return '<a href="mailto:' . e($user->getEmail()) . '">' . e($user->getRealName()) . '</a>';
            default:
                $url = route(Auth::check() ? 'message' : 'contact', [
                    'ged' => $this->tree->getName(),
                    'to'  => $user->getUserName(),
                    'url' => $this->request->getRequestUri(),
                ]);

                return '<a href="' . e($url) . '">' . e($user->getRealName()) . '</a>';
        }
    }

    /**
     * Create contact link for both technical and genealogy support.
     *
     * @param User $user
     *
     * @return string
     */
    public function contactLinkEverything(User $user): string
    {
        return I18N::translate('For technical support or genealogy questions contact %s.', $this->contactLink($user));
    }

    /**
     * Create contact link for genealogy support.
     *
     * @param User $user
     *
     * @return string
     */
    public function contactLinkGenealogy(User $user): string
    {
        return I18N::translate('For help with genealogy questions contact %s.', $this->contactLink($user));
    }

    /**
     * Create contact link for technical support.
     *
     * @param User $user
     *
     * @return string
     */
    public function contactLinkTechnical(User $user): string
    {
        return I18N::translate('For technical support and information contact %s.', $this->contactLink($user));
    }

    /**
     * Create contact links for the page footer.
     *
     * @return string
     */
    public function contactLinks()
    {
        $contact_user   = User::find($this->tree->getPreference('CONTACT_USER_ID'));
        $webmaster_user = User::find($this->tree->getPreference('WEBMASTER_USER_ID'));

        if ($contact_user && $contact_user === $webmaster_user) {
            return $this->contactLinkEverything($contact_user);
        }

        if ($contact_user && $webmaster_user) {
            return $this->contactLinkGenealogy($contact_user) . '<br>' . $this->contactLinkTechnical($webmaster_user);
        }

        if ($contact_user) {
            return $this->contactLinkGenealogy($contact_user);
        }

        if ($webmaster_user) {
            return $this->contactLinkTechnical($webmaster_user);
        }

        return '';
    }

    /**
     * Create a cookie warning.
     *
     * @return string
     */
    public function cookieWarning()
    {
        if (
            empty($_SERVER['HTTP_DNT']) &&
            empty($_COOKIE['cookie']) &&
            (Site::getPreference('GOOGLE_ANALYTICS_ID') === '1' || Site::getPreference('PIWIK_SITE_ID') === '1' || Site::getPreference('STATCOUNTER_PROJECT_ID') === '1')
        ) {
            return
                '<div class="wt-cookie-warning">' .
                I18N::translate('Cookies') . ' - ' .
                I18N::translate('This website uses cookies to learn about visitor behaviour.') . ' ' .
                '<button onclick="document.cookie=\'cookie=1\'; this.parentNode.classList.add(\'hidden\');">' . I18N::translate('continue') . '</button>' .
                '</div>';
        }

        return '';
    }

    /**
     * Create the <DOCTYPE> tag.
     *
     * @return string
     */
    public function doctype(): string
    {
        return '<!DOCTYPE html>';
    }

    /**
     * HTML link to a "favorites icon".
     *
     * @return string
     */
    protected function favicon(): string
    {
        return
            '<link rel="icon" href="' . self::ASSET_DIR . 'favicon.png" type="image/png">' .
            '<link rel="icon" type="image/png" href="' . self::ASSET_DIR . 'favicon192.png" sizes="192x192">' .
            '<link rel="apple-touch-icon" sizes="180x180" href="' . self::ASSET_DIR . 'favicon180.png">';
    }

    /**
     * Add markup to a flash message.
     *
     * @param stdClass $message
     *
     * @return string
     */
    protected function flashMessageContainer(stdClass $message): string
    {
        return $this->htmlAlert($message->text, $message->status, true);
    }

    /**
     * Create a container for messages that are "flashed" to the session
     * on one request, and displayed on another. If there are many messages,
     * the container may need a max-height and scroll-bar.
     *
     * @param stdClass[] $messages
     *
     * @return string
     */
    protected function flashMessagesContainer(array $messages)
    {
        $html = '';
        foreach ($messages as $message) {
            $html .= $this->flashMessageContainer($message);
        }

        if ($html) {
            return '<div class="flash-messages">' . $html . '</div>';
        }

        return '';
    }

    /**
     * Add markup to the contact links.
     *
     * @return string
     */
    public function formatContactLinks()
    {
        if ($this->tree) {
            return '<div class="wt-contact-links">' . $this->contactLinks() . '</div>';
        }

        return '';
    }

    /**
     * Create a pending changes link for the page footer.
     *
     * @return string
     */
    public function formatPendingChangesLink()
    {
        if ($this->pendingChangesExist()) {
            return '<div class="pending-changes-link">' . $this->pendingChangesLink() . '</div>';
        }

        return '';
    }

    /**
     * Create a quick search form for the header.
     *
     * @return string
     */
    protected function formQuickSearch()
    {
        if ($this->tree) {
            return
                '<div class="col wt-header-search">' .
                '<form class="wt-header-search-form" role="search">' .
                '<input type="hidden" name="route" value="search-quick">' .
                '<input type="hidden" name="ged" value="' . e($this->tree->getName()) . '">' .
                $this->formQuickSearchFields() .
                '</form>' .
                '</div>';
        }

        return '';
    }

    /**
     * Create a search field and submit button for the quick search form in the header.
     *
     * @return string
     */
    protected function formQuickSearchFields(): string
    {
        return
            '<div class="input-group">' .
            '<label class="sr-only" for="quick-search">' . I18N::translate('Search') . '</label>' .
            '<input type="search" class="form-control wt-header-search-field" id="quick-search" name="query" size="15" placeholder="' . I18N::translate('Search') . '">' .
            '<span class="input-group-btn">' .
            '<button type="submit" class="btn btn-primary wt-header-search-button">' . view('icons/search') . '</button>' .
            '</span>' .
            '</div>';
    }

    /**
     * Add markup to the tree title.
     *
     * @return string
     */
    protected function formatTreeTitle()
    {
        if ($this->tree) {
            return '<h1 class="col wt-site-title">' . e($this->tree->getTitle()) . '</h1>';
        }

        return '';
    }

    /**
     * Add markup to the secondary menu.
     *
     * @return string
     */
    public function formatSecondaryMenu(): string
    {
        return
            '<ul class="nav wt-secondary-menu">' .
            implode('', array_map(function (Menu $menu): string {
                return $this->formatSecondaryMenuItem($menu);
            }, $this->secondaryMenu())) .
            '</ul>';
    }

    /**
     * Add markup to an item in the secondary menu.
     *
     * @param Menu $menu
     *
     * @return string
     */
    public function formatSecondaryMenuItem(Menu $menu): string
    {
        return $menu->bootstrap4();
    }

    /**
     * Create the contents of the <header> tag.
     *
     * @return string
     */
    protected function headerContent(): string
    {
        return
            $this->accessibilityLinks() .
            $this->logoHeader() .
            $this->formatTreeTitle() .
            $this->formQuickSearch() .
            $this->secondaryMenuContainer($this->secondaryMenu()) .
            $this->primaryMenuContainer($this->primaryMenu($this->tree->significantIndividual(Auth::user())));
    }

    /**
     * Allow themes to do things after initialization (since they cannot use
     * the constructor).
     *
     * @return void
     */
    public function hookAfterInit()
    {
    }

    /**
     * Allow themes to add extra content to the page header.
     * Typically this will be additional CSS.
     *
     * @return string
     */
    public function hookHeaderExtraContent(): string
    {
        return '';
    }

    /**
     * Create the <html> tag.
     *
     * @return string
     */
    public function html(): string
    {
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
    public function htmlAlert($html, $level, $dismissible)
    {
        if ($dismissible) {
            return
                '<div class="alert alert-' . $level . ' alert-dismissible" role="alert">' .
                '<button type="button" class="close" data-dismiss="alert" aria-label="' . I18N::translate('close') . '">' .
                '<span aria-hidden="true">&times;</span>' .
                '</button>' .
                $html .
                '</div>';
        }

        return
            '<div class="alert alert-' . $level . '" role="alert">' .
            $html .
            '</div>';
    }

    /**
     * Display an icon for this fact.
     *
     * @param Fact $fact
     *
     * @return string
     */
    public function icon(Fact $fact): string
    {
        $icon = 'images/facts/' . $fact->getTag() . '.png';
        if (file_exists(self::ASSET_DIR . $icon)) {
            return '<img src="' . self::ASSET_DIR . $icon . '" title="' . GedcomTag::getLabel($fact->getTag()) . '">';
        }

        if (file_exists(self::ASSET_DIR . 'images/facts/NULL.png')) {
            // Spacer image - for alignment - until we move to a sprite.
            return '<img src="' . Theme::theme()->assetUrl() . 'images/facts/NULL.png">';
        }

        return '';
    }

    /**
     * Decorative icons are used in addition to text.
     * They need additional markup to hide them from assistive technologies.
     *
     * Semantic icons are used in place of text.
     * They need additional markup to convey their meaning to assistive technologies.
     *
     * @link http://fontawesome.io/accessibility
     *
     * @param string $icon
     * @param string $text
     *
     * @return string
     */
    public function replacementIconFunction($icon, $text = '')
    {
        if (array_key_exists($icon, self::ICONS)) {
            if ($text === '') {
                // Decorative icon.  Hiden from assistive technology.
                return '<i class="' . self::ICONS[$icon] . '" aria-hidden="true"></i>';
            }

            // Semantic icon.  Label for assistive technology.
            return
                '<i class="' . self::ICONS[$icon] . '" title="' . $text . '"></i>' .
                '<span class="sr-only">' . $text . '</span>';
        }

        return $text;
    }


    /**
     * Display an individual in a box - for charts, etc.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBox(Individual $individual): string
    {
        $person_box_class = self::PERSON_BOX_CLASSES[$individual->getSex()];

        if ($individual->canShow() && $individual->getTree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
            $thumbnail = $individual->displayImage(40, 50, 'crop', []);
        } else {
            $thumbnail = '';
        }

        $content = '<span class="namedef name1">' . $individual->getFullName() . '</span>';
        $icons   = '';
        if ($individual->canShow()) {
            $content = '<a href="' . e($individual->url()) . '">' . $content . '</a>' .
                '<div class="namedef name1">' . $individual->getAddName() . '</div>';
            $icons   = '<div class="icons">' .
                '<span class="iconz icon-zoomin" title="' . I18N::translate('Zoom in/out on this box.') . '"></span>' .
                '<div class="itr"><i class="icon-pedigree"></i><div class="popup">' .
                '<ul class="' . $person_box_class . '">' . implode('', array_map(function (Menu $menu): string {
                    return $menu->bootstrap4();
                }, $this->individualBoxMenu($individual))) . '</ul>' .
                '</div>' .
                '</div>' .
                '</div>';
        }

        return
            '<div data-xref="' . e($individual->getXref()) . '" data-tree="' . e($individual->getTree()->getName()) . '" class="person_box_template ' . $person_box_class . ' box-style1" style="width: ' . $this->parameter('chart-box-x') . 'px; height: ' . $this->parameter('chart-box-y') . 'px">' .
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
    public function individualBoxEmpty(): string
    {
        return '<div class="person_box_template person_boxNN box-style1" style="width: ' . $this->parameter('chart-box-x') . 'px; min-height: ' . $this->parameter('chart-box-y') . 'px"></div>';
    }

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBoxLarge(Individual $individual): string
    {
        $person_box_class = self::PERSON_BOX_CLASSES[$individual->getSex()];

        if ($individual->getTree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
            $thumbnail = $individual->displayImage(40, 50, 'crop', []);
        } else {
            $thumbnail = '';
        }

        $content = '<span class="namedef name1">' . $individual->getFullName() . '</span>';
        $icons   = '';
        if ($individual->canShow()) {
            $content = '<a href="' . e($individual->url()) . '">' . $content . '</a>' .
                '<div class="namedef name2">' . $individual->getAddName() . '</div>';
            $icons   = '<div class="icons">' .
                '<span class="iconz icon-zoomin" title="' . I18N::translate('Zoom in/out on this box.') . '"></span>' .
                '<div class="itr"><i class="icon-pedigree"></i><div class="popup">' .
                '<ul class="' . $person_box_class . '">' . implode('', array_map(function (Menu $menu): string {
                    return $menu->bootstrap4();
                }, $this->individualBoxMenu($individual))) . '</ul>' .
                '</div>' .
                '</div>' .
                '</div>';
        }

        return
            '<div data-xref="' . e($individual->getXref()) . '" data-tree="' . e($individual->getTree()->getName()) . '" class="person_box_template ' . $person_box_class . ' box-style2">' .
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
    public function individualBoxSmall(Individual $individual): string
    {
        $person_box_class = self::PERSON_BOX_CLASSES[$individual->getSex()];

        if ($individual->getTree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
            $thumbnail = $individual->displayImage(40, 50, 'crop', []);
        } else {
            $thumbnail = '';
        }

        return
            '<div data-xref="' . $individual->getXref() . '" class="person_box_template ' . $person_box_class . ' iconz box-style0" style="width: ' . $this->parameter('compact-chart-box-x') . 'px; min-height: ' . $this->parameter('compact-chart-box-y') . 'px">' .
            '<div class="compact_view">' .
            $thumbnail .
            '<a href="' . e($individual->url()) . '">' .
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
    public function individualBoxSmallEmpty(): string
    {
        return '<div class="person_box_template person_boxNN box-style1" style="width: ' . $this->parameter('compact-chart-box-x') . 'px; min-height: ' . $this->parameter('compact-chart-box-y') . 'px"></div>';
    }

    /**
     * Generate the facts, for display in charts.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBoxFacts(Individual $individual): string
    {
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
                if ($event !== null) {
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
    public function individualBoxLdsSummary(Individual $individual)
    {
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
        }

        return '';
    }

    /**
     * Links, to show in chart boxes;
     *
     * @param Individual $individual
     *
     * @return Menu[]
     */
    public function individualBoxMenu(Individual $individual): array
    {
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
    public function individualBoxMenuCharts(Individual $individual): array
    {
        $menus = [];
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
    public function individualBoxMenuFamilyLinks(Individual $individual): array
    {
        $menus = [];

        foreach ($individual->getSpouseFamilies() as $family) {
            $menus[] = new Menu('<strong>' . I18N::translate('Family with spouse') . '</strong>', $family->url());
            $spouse  = $family->getSpouse($individual);
            if ($spouse && $spouse->canShowName()) {
                $menus[] = new Menu($spouse->getFullName(), $spouse->url());
            }
            foreach ($family->getChildren() as $child) {
                if ($child->canShowName()) {
                    $menus[] = new Menu($child->getFullName(), $child->url());
                }
            }
        }

        return $menus;
    }

    /**
     * Initialise the theme. We cannot pass these in a constructor, as the construction
     * happens in a theme file, and we need to be able to change it.
     *
     * @param Request   $request
     * @param Tree|null $tree The current tree (if there is one).
     *
     * @return void
     */
    final public function init(Request $request, Tree $tree = null)
    {
        $this->request = $request;
        $this->tree    = $tree;

        $this->hookAfterInit();
    }

    /**
     * A large webtrees logo, for the header.
     *
     * @return string
     */
    protected function logoHeader(): string
    {
        return '<div class="col wt-site-logo"></div>';
    }

    /**
     * A small "powered by webtrees" logo for the footer.
     *
     * @return string
     */
    public function logoPoweredBy(): string
    {
        return '<a href="' . WT_WEBTREES_URL . '" class="wt-powered-by-webtrees" title="' . WT_WEBTREES_URL . '" dir="ltr">' . WT_WEBTREES_URL . '</a>';
    }

    /**
     * A menu for the day/month/year calendar views.
     *
     * @return Menu
     */
    public function menuCalendar(): Menu
    {
        return new Menu(I18N::translate('Calendar'), '#', 'menu-calendar', ['rel' => 'nofollow'], [
            // Day view
            new Menu(I18N::translate('Day'), route('calendar', [
                'view' => 'day',
                'ged'  => $this->tree->getName(),
            ]), 'menu-calendar-day', ['rel' => 'nofollow']),
            // Month view
            new Menu(I18N::translate('Month'), route('calendar', [
                'view' => 'month',
                'ged'  => $this->tree->getName(),
            ]), 'menu-calendar-month', ['rel' => 'nofollow']),
            //Year view
            new Menu(I18N::translate('Year'), route('calendar', [
                'view' => 'year',
                'ged'  => $this->tree->getName(),
            ]), 'menu-calendar-year', ['rel' => 'nofollow']),
        ]);
    }

    /**
     * Generate a menu item to change the blocks on the current (index.php) page.
     *
     * @return Menu|null
     */
    public function menuChangeBlocks()
    {
        if (Auth::check() && $this->request->get('route') === 'user-page') {
            return new Menu(I18N::translate('Customize this page'), route('user-page-edit', ['ged' => $this->tree->getName()]), 'menu-change-blocks');
        }

        if (Auth::isManager($this->tree) && $this->request->get('route') === 'tree-page') {
            return new Menu(I18N::translate('Customize this page'), route('tree-page-edit', ['ged' => $this->tree->getName()]), 'menu-change-blocks');
        }

        return null;
    }

    /**
     * Generate a menu for each of the different charts.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function menuChart(Individual $individual)
    {
        $submenus = [];
        foreach (Module::getActiveCharts($this->tree) as $chart) {
            $menu = $chart->getChartMenu($individual);
            if ($menu) {
                $submenus[] = $menu;
            }
        }

        if (empty($submenus)) {
            return null;
        }

        usort($submenus, function (Menu $x, Menu $y) {
            return I18N::strcasecmp($x->getLabel(), $y->getLabel());
        });

        return new Menu(I18N::translate('Charts'), '#', 'menu-chart', ['rel' => 'nofollow'], $submenus);
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
    public function menuChartAncestors(Individual $individual)
    {
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
    public function menuChartCompact(Individual $individual)
    {
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
    public function menuChartDescendants(Individual $individual)
    {
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
    public function menuChartFamilyBook(Individual $individual)
    {
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
    public function menuChartFanChart(Individual $individual)
    {
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
    public function menuChartInteractiveTree(Individual $individual)
    {
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
    public function menuChartHourglass(Individual $individual)
    {
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
    public function menuChartLifespan(Individual $individual)
    {
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
    public function menuChartPedigree(Individual $individual)
    {
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
    public function menuChartPedigreeMap(Individual $individual)
    {
        return null;
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
    public function menuChartRelationship(Individual $individual)
    {
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
    public function menuChartStatistics()
    {
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
    public function menuChartTimeline(Individual $individual)
    {
        $chart = new TimelineChartModule(WT_ROOT . WT_MODULES_DIR . 'timeline_chart');

        return $chart->getChartMenu($individual);
    }

    /**
     * Generate a menu item for the control panel.
     *
     * @return Menu|null
     */
    public function menuControlPanel()
    {
        if (Auth::isAdmin()) {
            return new Menu(I18N::translate('Control panel'), route('admin-control-panel'), 'menu-admin');
        }

        if (Auth::isManager($this->tree)) {
            return new Menu(I18N::translate('Control panel'), route('admin-control-panel-manager'), 'menu-admin');
        }

        return null;
    }

    /**
     * Favorites menu.
     *
     * @return Menu|null
     */
    public function menuFavorites()
    {
        global $controller;

        /** @var UserFavoritesModule|null $user_favorites */
        $user_favorites = Module::getModuleByName('user_favorites');

        /** @var FamilyTreeFavoritesModule|null $tree_favorites */
        $tree_favorites = Module::getModuleByName('gedcom_favorites');

        $show_user_favorites = $this->tree !== null && $user_favorites !== null && Auth::check();
        $show_tree_favorites = $this->tree !== null && $tree_favorites !== null;

        if ($show_user_favorites && $show_tree_favorites) {
            $favorites = array_merge(
                $tree_favorites->getFavorites($this->tree),
                $user_favorites->getFavorites($this->tree, Auth::user())
            );
        } elseif ($show_user_favorites) {
            $favorites = $user_favorites->getFavorites($this->tree, Auth::user());
        } elseif ($show_tree_favorites) {
            $favorites = $tree_favorites->getFavorites($this->tree);
        } else {
            $favorites = [];
        }

        $submenus = [];
        $records  = [];
        foreach ($favorites as $favorite) {
            switch ($favorite->favorite_type) {
                case 'URL':
                    $submenus[] = new Menu(e($favorite->title), $favorite->url);
                    break;
                default:
                    $record = GedcomRecord::getInstance($favorite->xref, $this->tree);
                    if ($record && $record->canShowName()) {
                        $submenus[] = new Menu($record->getFullName(), $record->url());
                        $records[]  = $record;
                    }
                    break;
            }
        }

        // @TODO we no longer have a global $controller
        if ($show_user_favorites && isset($controller->record) && $controller->record instanceof GedcomRecord && !in_array($controller->record, $records)) {
            $url = route('module', [
                'module' => 'user_favorites',
                'action' => 'AddFavorite',
                'ged'    => $this->tree->getName(),
                'xref'   => $controller->record->getXref(),
            ]);

            $submenus[] = new Menu(I18N::translate('Add to favorites'), '#', '', [
                'data-url' => $url,
                'onclick'  => 'jQuery.post(this.dataset.url,function() {location.reload();})',
            ]);
        }

        if (empty($submenus)) {
            return null;
        }

        return new Menu(I18N::translate('Favorites'), '#', 'menu-favorites', [], $submenus);
    }

    /**
     * A menu for the home (family tree) pages.
     *
     * @return Menu
     */
    public function menuHomePage()
    {
        if (count(Tree::getAll()) === 1 || Site::getPreference('ALLOW_CHANGE_GEDCOM') !== '1') {
            return new Menu(I18N::translate('Family tree'), route('tree-page', ['ged' => $this->tree->getName()]), 'menu-tree');
        }

        $submenus = [];
        foreach (Tree::getAll() as $tree) {
            if ($tree == $this->tree) {
                $active = 'active ';
            } else {
                $active = '';
            }
            $submenus[] = new Menu(e($tree->getTitle()), route('tree-page', ['ged' => $tree->getName()]), $active . 'menu-tree-' . $tree->getTreeId());
        }

        return new Menu(I18N::translate('Family trees'), '#', 'menu-tree', [], $submenus);
    }

    /**
     * A menu to show a list of available languages.
     *
     * @return Menu|null
     */
    public function menuLanguages()
    {
        $menu = new Menu(I18N::translate('Language'), '#', 'menu-language');

        foreach (I18N::activeLocales() as $locale) {
            $language_tag = $locale->languageTag();
            $class        = 'menu-language-' . $language_tag . (WT_LOCALE === $language_tag ? ' active' : '');
            $menu->addSubmenu(new Menu($locale->endonym(), '#', $class, [
                'onclick'       => 'return false;',
                'data-language' => $language_tag,
            ]));
        }

        if (count($menu->getSubmenus()) > 1) {
            return $menu;
        }

        return null;
    }

    /**
     * Create a menu to show lists of individuals, families, sources, etc.
     *
     * @param string $surname The significant surname on the page
     *
     * @return Menu
     */
    public function menuLists($surname): Menu
    {
        // Do not show empty lists
        $row = Database::prepare(
            "SELECT" .
            " EXISTS(SELECT 1 FROM `##sources` WHERE s_file = ?) AS sour," .
            " EXISTS(SELECT 1 FROM `##other` WHERE o_file = ? AND o_type='REPO') AS repo," .
            " EXISTS(SELECT 1 FROM `##other` WHERE o_file = ? AND o_type='NOTE') AS note," .
            " EXISTS(SELECT 1 FROM `##media` WHERE m_file = ?) AS obje"
        )->execute([
            $this->tree->getTreeId(),
            $this->tree->getTreeId(),
            $this->tree->getTreeId(),
            $this->tree->getTreeId(),
        ])->fetchOneRow();

        $submenus = [
            $this->menuListsIndividuals($surname),
            $this->menuListsFamilies($surname),
            $this->menuListsBranches($surname),
            $this->menuListsPlaces(),
        ];
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

        return new Menu(I18N::translate('Lists'), '#', 'menu-list', [], $submenus);
    }

    /**
     * A menu for the list of branches
     *
     * @param string $surname The significant surname on the page
     *
     * @return Menu
     */
    public function menuListsBranches($surname): Menu
    {
        return new Menu(I18N::translate('Branches'), route('branches', [
            'ged'     => $this->tree->getName(),
            'surname' => $surname,
        ]), 'menu-branches', ['rel' => 'nofollow']);
    }

    /**
     * A menu for the list of families
     *
     * @param string $surname The significant surname on the page
     *
     * @return Menu
     */
    public function menuListsFamilies($surname): Menu
    {
        return new Menu(I18N::translate('Families'), route('family-list', [
            'ged'     => $this->tree->getName(),
            'surname' => $surname,
        ]), 'menu-list-indi');
    }

    /**
     * A menu for the list of individuals
     *
     * @param string $surname The significant surname on the page
     *
     * @return Menu
     */
    public function menuListsIndividuals($surname): Menu
    {
        return new Menu(I18N::translate('Individuals'), route('individual-list', [
            'ged'     => $this->tree->getName(),
            'surname' => $surname,
        ]), 'menu-list-indi');
    }

    /**
     * A menu for the list of media objects
     *
     * @return Menu
     */
    public function menuListsMedia(): Menu
    {
        return new Menu(I18N::translate('Media objects'), route('media-list', ['ged' => $this->tree->getName()]), 'menu-list-obje', ['rel' => 'nofollow']);
    }

    /**
     * A menu for the list of notes
     *
     * @return Menu
     */
    public function menuListsNotes(): Menu
    {
        return new Menu(I18N::translate('Shared notes'), route('note-list', ['ged' => $this->tree->getName()]), 'menu-list-note', ['rel' => 'nofollow']);
    }

    /**
     * A menu for the list of individuals
     *
     * @return Menu
     */
    protected function menuListsPlaces(): Menu
    {
        return new Menu(I18N::translate('Place hierarchy'), route('place-hierarchy', ['ged' => $this->tree->getName()]), 'menu-list-plac', ['rel' => 'nofollow']);
    }

    /**
     * A menu for the list of repositories
     *
     * @return Menu
     */
    public function menuListsRepositories(): Menu
    {
        return new Menu(I18N::translate('Repositories'), route('repository-list', ['ged' => $this->tree->getName()]), 'menu-list-repo', ['rel' => 'nofollow']);
    }

    /**
     * A menu for the list of sources
     *
     * @return Menu
     */
    public function menuListsSources(): Menu
    {
        return new Menu(I18N::translate('Sources'), route('source-list', ['ged' => $this->tree->getName()]), 'menu-list-sour', ['rel' => 'nofollow']);
    }

    /**
     * A login menu option (or null if we are already logged in).
     *
     * @return Menu|null
     */
    public function menuLogin()
    {
        if (Auth::check()) {
            return null;
        }

        // Return to this page after login...
        $url = Functions::getQueryUrl();
        // ...but switch from the tree-page to the user-page
        $url = str_replace('route=tree-page', 'route=user-page', $url);

        return new Menu(I18N::translate('Sign in'), route('login', ['url' => $url]), 'menu-login', ['rel' => 'nofollow']);
    }

    /**
     * A logout menu option (or null if we are already logged out).
     *
     * @return Menu|null
     */
    public function menuLogout()
    {
        if (Auth::check()) {
            return new Menu(I18N::translate('Sign out'), route('logout'), 'menu-logout');
        }

        return null;
    }

    /**
     * Get the additional menus created by each of the modules
     *
     * @return Menu[]
     */
    public function menuModules(): array
    {
        $menus = [];
        foreach (Module::getActiveMenus($this->tree) as $module) {
            $menus[] = $module->getMenu($this->tree);
        }

        return array_filter($menus);
    }

    /**
     * A link to allow users to edit their account settings.
     *
     * @return Menu|null
     */
    public function menuMyAccount()
    {
        if (Auth::check()) {
            return new Menu(I18N::translate('My account'), route('my-account'));
        }

        return null;
    }

    /**
     * A link to the user's individual record (individual.php).
     *
     * @return Menu|null
     */
    public function menuMyIndividualRecord()
    {
        $record = Individual::getInstance($this->tree->getUserPreference(Auth::user(), 'gedcomid'), $this->tree);

        if ($record) {
            return new Menu(I18N::translate('My individual record'), $record->url(), 'menu-myrecord');
        }

        return null;
    }

    /**
     * A link to the user's personal home page.
     *
     * @return Menu
     */
    public function menuMyPage(): Menu
    {
        return new Menu(I18N::translate('My page'), route('user-page', ['ged' => $this->tree->getName()]), 'menu-mypage');
    }

    /**
     * A menu for the user's personal pages.
     *
     * @return Menu|null
     */
    public function menuMyPages()
    {
        if (Auth::id() && $this->tree !== null) {
            return new Menu(I18N::translate('My pages'), '#', 'menu-mymenu', [], array_filter([
                $this->menuMyPage(),
                $this->menuMyIndividualRecord(),
                $this->menuMyPedigree(),
                $this->menuMyAccount(),
                $this->menuControlPanel(),
                $this->menuChangeBlocks(),
            ]));
        }

        return null;
    }

    /**
     * A link to the user's individual record.
     *
     * @return Menu|null
     */
    public function menuMyPedigree()
    {
        $gedcomid = $this->tree->getUserPreference(Auth::user(), 'gedcomid');

        if ($gedcomid !== '' && Module::isActiveChart($this->tree, 'pedigree_chart')) {
            return new Menu(
                I18N::translate('My pedigree'),
                route('pedigree', [
                    'xref' => $gedcomid,
                    'ged'  => $this->tree->getName(),
                ]),
                'menu-mypedigree'
            );
        }

        return null;
    }

    /**
     * Create a pending changes menu.
     *
     * @return Menu|null
     */
    public function menuPendingChanges()
    {
        if ($this->pendingChangesExist()) {
            $url = route('show-pending', [
                'ged' => $this->tree ? $this->tree->getName() : '',
                'url' => $this->request->getRequestUri(),
            ]);

            return new Menu(I18N::translate('Pending changes'), $url, 'menu-pending');
        }

        return null;
    }

    /**
     * A menu with a list of reports.
     *
     * @return Menu|null
     */
    public function menuReports()
    {
        $submenus = [];
        foreach (Module::getActiveReports($this->tree) as $report) {
            $submenus[] = $report->getReportMenu($this->tree);
        }

        if (empty($submenus)) {
            return null;
        }

        return new Menu(I18N::translate('Reports'), '#', 'menu-report', ['rel' => 'nofollow'], $submenus);
    }

    /**
     * Create the search menu.
     *
     * @return Menu
     */
    public function menuSearch(): Menu
    {
        return new Menu(I18N::translate('Search'), '#', 'menu-search', ['rel' => 'nofollow'], array_filter([
            $this->menuSearchGeneral(),
            $this->menuSearchPhonetic(),
            $this->menuSearchAdvanced(),
            $this->menuSearchAndReplace(),
        ]));
    }

    /**
     * Create the general search sub-menu.
     *
     * @return Menu
     */
    public function menuSearchGeneral(): Menu
    {
        return new Menu(I18N::translate('General search'), route('search-general', ['ged' => $this->tree->getName()]), 'menu-search-general', ['rel' => 'nofollow']);
    }

    /**
     * Create the phonetic search sub-menu.
     *
     * @return Menu
     */
    public function menuSearchPhonetic(): Menu
    {
        /* I18N: search using “sounds like”, rather than exact spelling */
        return new Menu(I18N::translate('Phonetic search'), route('search-phonetic', ['ged'    => $this->tree->getName(), 'action' => 'soundex',]), 'menu-search-soundex', ['rel' => 'nofollow']);
    }

    /**
     * Create the advanced search sub-menu.
     *
     * @return Menu
     */
    public function menuSearchAdvanced(): Menu
    {
        return new Menu(I18N::translate('Advanced search'), route('search-advanced', ['ged' => $this->tree->getName()]), 'menu-search-advanced', ['rel' => 'nofollow']);
    }

    /**
     * Create the advanced search sub-menu.
     *
     * @return Menu
     */
    public function menuSearchAndReplace()
    {
        if (Auth::isEditor($this->tree)) {
            return new Menu(I18N::translate('Search and replace'), route('search-replace', [
                'ged'    => $this->tree->getName(),
                'action' => 'replace',
            ]), 'menu-search-replace');
        }

        return null;
    }

    /**
     * Themes menu.
     *
     * @return Menu|null
     */
    public function menuThemes()
    {
        if ($this->tree !== null && Site::getPreference('ALLOW_USER_THEMES') === '1' && $this->tree->getPreference('ALLOW_THEME_DROPDOWN') === '1') {
            $submenus = [];
            foreach (Theme::installedThemes() as $theme) {
                $class      = 'menu-theme-' . $theme->themeId() . ($theme === $this ? ' active' : '');
                $submenus[] = new Menu($theme->themeName(), '#', $class, [
                    'onclick'    => 'return false;',
                    'data-theme' => $theme->themeId(),
                ]);
            }

            usort($submenus, function (Menu $x, Menu $y): int {
                return I18N::strcasecmp($x->getLabel(), $y->getLabel());
            });

            $menu = new Menu(I18N::translate('Theme'), '#', 'menu-theme', [], $submenus);

            return $menu;
        }

        return null;
    }

    /**
     * Create the <meta charset=""> tag.
     *
     * @return string
     */
    protected function metaCharset(): string
    {
        return '<meta charset="UTF-8">';
    }

    /**
     * Make the CSRF token available to Javascript.
     *
     * @return string
     */
    protected function metaCsrf(): string
    {
        return '<meta name="csrf" content="' . e(Session::getCsrfToken()) . '">';
    }

    /**
     * Create the <meta name="description"> tag.
     *
     * @param string $description
     *
     * @return string
     */
    protected function metaDescription($description)
    {
        if ($description) {
            return '<meta name="description" content="' . $description . '">';
        }

        return '';
    }

    /**
     * Create the <meta name="generator"> tag.
     *
     * @param string $generator
     *
     * @return string
     */
    protected function metaGenerator($generator)
    {
        if ($generator) {
            return '<meta name="generator" content="' . $generator . '">';
        }

        return '';
    }

    /**
     * Create the <meta name="robots"> tag.
     *
     * @param string $robots
     *
     * @return string
     */
    protected function metaRobots($robots)
    {
        if ($robots) {
            return '<meta name="robots" content="' . $robots . '">';
        }

        return '';
    }

    /**
     * Create the <meta name="viewport" content="width=device-width, initial-scale=1"> tag.
     *
     * @return string
     */
    protected function metaViewport(): string
    {
        return '<meta name="viewport" content="width=device-width, initial-scale=1">';
    }

    /**
     * Misecellaneous dimensions, fonts, styles, etc.
     *
     * @param string $parameter_name
     *
     * @return string|int|float
     */
    public function parameter($parameter_name)
    {
        $parameters = [
            'chart-background-f'             => 'dddddd',
            'chart-background-m'             => 'cccccc',
            'chart-background-u'             => 'eeeeee',
            'chart-box-x'                    => 250,
            'chart-box-y'                    => 80,
            'chart-font-color'               => '000000',
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
            'image-dline'                    => static::ASSET_DIR . 'images/dline.png',
            'image-dline2'                   => static::ASSET_DIR . 'images/dline2.png',
            'image-hline'                    => static::ASSET_DIR . 'images/hline.png',
            'image-spacer'                   => static::ASSET_DIR . 'images/spacer.png',
            'image-vline'                    => static::ASSET_DIR . 'images/vline.png',
            'image-minus'                    => static::ASSET_DIR . 'images/minus.png',
            'image-plus'                     => static::ASSET_DIR . 'images/plus.png',
        ];

        if (array_key_exists($parameter_name, $parameters)) {
            return $parameters[$parameter_name];
        }

        throw new \InvalidArgumentException($parameter_name);
    }

    /**
     * Are there any pending changes for us to approve?
     *
     * @return bool
     */
    public function pendingChangesExist(): bool
    {
        return $this->tree && $this->tree->hasPendingEdit() && Auth::isModerator($this->tree);
    }

    /**
     * Create a pending changes link. Some themes prefer an alert/banner to a menu.
     *
     * @return string
     */
    public function pendingChangesLink(): string
    {
        return '<a href="' . e(route('show-pending', ['ged' => $this->tree->getName()])) . '">' . $this->pendingChangesLinkText() . '</a>';
    }

    /**
     * Text to use in the pending changes link.
     *
     * @return string
     */
    public function pendingChangesLinkText(): string
    {
        return I18N::translate('There are pending changes for you to moderate.');
    }

    /**
     * Generate a list of items for the main menu.
     *
     * @param Individual $individual
     *
     * @return Menu[]
     */
    public function primaryMenu(Individual $individual): array
    {
        $surname = $individual->getAllNames()[0]['surn'];

        return array_filter(array_merge([
            $this->menuHomePage(),
            $this->menuChart($individual),
            $this->menuLists($surname),
            $this->menuCalendar(),
            $this->menuReports(),
            $this->menuSearch(),
        ], $this->menuModules()));
    }

    /**
     * Add markup to the primary menu.
     *
     * @param Menu[] $menus
     *
     * @return string
     */
    protected function primaryMenuContainer(array $menus): string
    {
        return '<nav class="col wt-primary-navigation"><ul class="nav wt-primary-menu">' . $this->primaryMenuContent($menus) . '</ul></nav>';
    }

    /**
     * Create the primary menu.
     *
     * @param Menu[] $menus
     *
     * @return string
     */
    public function primaryMenuContent(array $menus): string
    {
        return implode('', array_map(function (Menu $menu): string {
            return $menu->bootstrap4();
        }, $menus));
    }

    /**
     * Generate a list of items for the user menu.
     *
     * @return Menu[]
     */
    public function secondaryMenu(): array
    {
        return array_filter([
            $this->menuPendingChanges(),
            $this->menuMyPages(),
            $this->menuFavorites(),
            $this->menuThemes(),
            $this->menuLanguages(),
            $this->menuLogin(),
            $this->menuLogout(),
        ]);
    }

    /**
     * Add markup to the secondary menu.
     *
     * @param Menu[] $menus
     *
     * @return string
     */
    protected function secondaryMenuContainer(array $menus): string
    {
        return '<div class="col wt-secondary-navigation"><ul class="nav wt-secondary-menu">' . $this->secondaryMenuContent($menus) . '</ul></div>';
    }

    /**
     * Format the secondary menu.
     *
     * @param Menu[] $menus
     *
     * @return string
     */
    public function secondaryMenuContent(array $menus): string
    {
        return implode('', array_map(function (Menu $menu): string {
            return $menu->bootstrap4();
        }, $menus));
    }

    /**
     * A list of CSS files to include for this page.
     *
     * @return string[]
     */
    public function stylesheets(): array
    {
        return [
            self::STYLESHEET,
        ];
    }

    /**
     * A fixed string to identify this theme, in settings, etc.
     *
     * @return string
     */
    public function themeId(): string
    {
        return static::THEME_DIR;
    }

    /**
     * What is this theme called?
     *
     * @return string
     */
    abstract public function themeName(): string;

    /**
     * Create the <title> tag.
     *
     * @param string $title
     *
     * @return string
     */
    protected function title($title): string
    {
        return '<title>' . e($title) . '</title>';
    }
}
