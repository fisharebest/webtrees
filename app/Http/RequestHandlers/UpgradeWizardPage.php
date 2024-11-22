<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Validator;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function basename;
use function e;
use function route;
use function version_compare;

/**
 * Upgrade to a new version of webtrees.
 */
class UpgradeWizardPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    // We make the upgrade in a number of small steps to keep within server time limits.
    private const string STEP_CHECK    = 'Check';
    private const string STEP_PREPARE  = 'Prepare';
    private const string STEP_PENDING  = 'Pending';
    private const string STEP_EXPORT   = 'Export';
    private const string STEP_DOWNLOAD = 'Download';
    private const string STEP_UNZIP    = 'Unzip';
    private const string STEP_COPY     = 'Copy';

    private TreeService $tree_service;

    private UpgradeService $upgrade_service;

    /**
     * @param TreeService    $tree_service
     * @param UpgradeService $upgrade_service
     */
    public function __construct(TreeService $tree_service, UpgradeService $upgrade_service)
    {
        $this->tree_service    = $tree_service;
        $this->upgrade_service = $upgrade_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $continue = Validator::queryParams($request)->string('continue', '');

        $title = I18N::translate('Upgrade wizard');

        $latest_version = $this->upgrade_service->latestVersion();

        $upgrade_available = version_compare($latest_version, Webtrees::VERSION) > 0;

        if ($upgrade_available && $continue === '1') {
            return $this->viewResponse('admin/upgrade/steps', [
                'steps' => $this->wizardSteps(),
                'title' => $title,
            ]);
        }

        return $this->viewResponse('admin/upgrade/wizard', [
            'current_version' => Webtrees::VERSION,
            'latest_version'  => $latest_version,
            'title'           => $title,
        ]);
    }

    /**
     * @return array<string>
     */
    private function wizardSteps(): array
    {
        $download_url = $this->upgrade_service->downloadUrl();

        $export_steps = [];

        foreach ($this->tree_service->all() as $tree) {
            $route = route(UpgradeWizardStep::class, [
                'step' => self::STEP_EXPORT,
                'tree' => $tree->name(),
            ]);

            $export_steps[$route] = I18N::translate('Export all the family trees to GEDCOM files…') . ' ' . e($tree->title());
        }

        return [
                route(UpgradeWizardStep::class, ['step' => self::STEP_CHECK])   => I18N::translate('Upgrade wizard'),
                route(UpgradeWizardStep::class, ['step' => self::STEP_PREPARE]) => I18N::translate('Create a temporary folder…'),
                route(UpgradeWizardStep::class, ['step' => self::STEP_PENDING]) => I18N::translate('Check for pending changes…'),
            ] + $export_steps + [
                route(UpgradeWizardStep::class, ['step' => self::STEP_DOWNLOAD]) => I18N::translate('Download %s…', e($download_url)),
                route(UpgradeWizardStep::class, ['step' => self::STEP_UNZIP])    => I18N::translate('Unzip %s to a temporary folder…', e(basename($download_url))),
                route(UpgradeWizardStep::class, ['step' => self::STEP_COPY])     => I18N::translate('Copy files…'),
            ];
    }
}
