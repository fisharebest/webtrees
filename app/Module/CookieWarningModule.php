<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Site;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PoweredByWebtreesModule - show a cookie warning, to comply with the GDPR.
 */
class CookieWarningModule extends AbstractModule implements ModuleFooterInterface
{
    use ModuleFooterTrait;

    /** @var Request */
    protected $request;

    // We only need to show a warning if we are using tracking
    protected const TRACKING_MODULES = [
        GoogleAnalyticsModule::class,
        MatomoAnalyticsModule::class,
        StatcounterModule::class,
    ];
    /**
     * @var ModuleService
     */
    private $module_service;

    /**
     * Dependency injection.
     *
     * @param Request       $request
     * @param ModuleService $module_service
     */
    public function __construct(Request $request, ModuleService $module_service)
    {
        $this->request = $request;
        $this->module_service = $module_service;
    }

    /**
     * How should this module be labelled on tabs, footers, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Cookie warning');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Cookie warning” module */
        return I18N::translate('Tell visitors why this site uses cookies.');
    }

    /**
     * The default position for this footer.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultFooterOrder(): int
    {
        return 4;
    }

    /**
     * A footer, to be added at the bottom of every page.
     *
     * @return string
     */
    public function getFooter(): string
    {
        if ($this->isCookieWarningAcknowledged()) {
            return '';
        }

        if ($this->siteUsesAnalyticss()) {
            return view('modules/cookie-warning/footer');
        }
        return '';
    }

    /**
     * @return bool
     */
    protected function isCookieWarningAcknowledged(): bool
    {
        $cookies = $this->request->cookies;

        return $cookies instanceof ParameterBag && $cookies->get('cookie', '') !== '';
    }

    /**
     * @return bool
     */
    protected function siteUsesAnalyticss(): bool
    {
        // If the browser sets the DNT header, then we won't use analytics.
        if ($this->request->server->get('HTTP_DNT') === '1') {
            return false;
        }

        foreach (self::TRACKING_MODULES as $class) {
            $module = $this->module_service->findByClass($class);

            if ($module instanceof ModuleAnalyticsInterface) {
                if ($module->analyticsCanShow()) {
                    return true;
                }
            }
        }

        return false;
    }
}
