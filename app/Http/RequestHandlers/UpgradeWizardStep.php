<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\Exceptions\HttpServerErrorException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function assert;
use function date;
use function e;
use function fclose;
use function intdiv;
use function response;
use function route;
use function version_compare;
use function view;

/**
 * Upgrade to a new version of webtrees.
 */
class UpgradeWizardStep implements RequestHandlerInterface
{
    // We make the upgrade in a number of small steps to keep within server time limits.
    private const STEP_CHECK    = 'Check';
    private const STEP_PREPARE  = 'Prepare';
    private const STEP_PENDING  = 'Pending';
    private const STEP_EXPORT   = 'Export';
    private const STEP_DOWNLOAD = 'Download';
    private const STEP_UNZIP    = 'Unzip';
    private const STEP_COPY     = 'Copy';

    // Where to store our temporary files.
    private const UPGRADE_FOLDER = 'data/tmp/upgrade/';

    // Where to store the downloaded ZIP archive.
    private const ZIP_FILENAME = 'data/tmp/webtrees.zip';

    // The ZIP archive stores everything inside this top-level folder.
    private const ZIP_FILE_PREFIX = 'webtrees';

    // Cruft can accumulate after upgrades.
    private const FOLDERS_TO_CLEAN = [
        'app',
        'resources',
        'vendor',
    ];

    private GedcomExportService $gedcom_export_service;

    private UpgradeService $upgrade_service;

    private TreeService $tree_service;

    /**
     * @param GedcomExportService $gedcom_export_service
     * @param TreeService         $tree_service
     * @param UpgradeService      $upgrade_service
     */
    public function __construct(
        GedcomExportService $gedcom_export_service,
        TreeService $tree_service,
        UpgradeService $upgrade_service
    ) {
        $this->gedcom_export_service = $gedcom_export_service;
        $this->tree_service          = $tree_service;
        $this->upgrade_service       = $upgrade_service;
    }

    /**
     * Perform one step of the wizard
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $zip_file   = Webtrees::ROOT_DIR . self::ZIP_FILENAME;
        $zip_folder = Webtrees::ROOT_DIR . self::UPGRADE_FOLDER;

        $step = Validator::queryParams($request)->string('step', self::STEP_CHECK);

        switch ($step) {
            case self::STEP_CHECK:
                return $this->wizardStepCheck();

            case self::STEP_PREPARE:
                return $this->wizardStepPrepare();

            case self::STEP_PENDING:
                return $this->wizardStepPending();

            case self::STEP_EXPORT:
                $tree_name = Validator::queryParams($request)->string('tree');
                $tree      = $this->tree_service->all()[$tree_name];
                assert($tree instanceof Tree);

                return $this->wizardStepExport($tree);

            case self::STEP_DOWNLOAD:
                return $this->wizardStepDownload();

            case self::STEP_UNZIP:
                return $this->wizardStepUnzip($zip_file, $zip_folder);

            case self::STEP_COPY:
                return $this->wizardStepCopyAndCleanUp($zip_file);

            default:
                return response('', StatusCodeInterface::STATUS_NO_CONTENT);
        }
    }

    /**
     * @return ResponseInterface
     */
    private function wizardStepCheck(): ResponseInterface
    {
        $latest_version = $this->upgrade_service->latestVersion();

        if ($latest_version === '') {
            throw new HttpServerErrorException(I18N::translate('No upgrade information is available.'));
        }

        if (version_compare(Webtrees::VERSION, $latest_version) >= 0) {
            $message = I18N::translate('This is the latest version of webtrees. No upgrade is available.');
            throw new HttpServerErrorException($message);
        }

        /* I18N: %s is a version number, such as 1.2.3 */
        $alert = I18N::translate('Upgrade to webtrees %s.', e($latest_version));

        return response(view('components/alert-success', [
            'alert' => $alert,
        ]));
    }

    /**
     * Make sure the temporary folder exists.
     *
     * @return ResponseInterface
     */
    private function wizardStepPrepare(): ResponseInterface
    {
        $root_filesystem = Registry::filesystem()->root();
        $root_filesystem->deleteDirectory(self::UPGRADE_FOLDER);
        $root_filesystem->createDirectory(self::UPGRADE_FOLDER);

        return response(view('components/alert-success', [
            'alert' => I18N::translate('The folder %s has been created.', e(self::UPGRADE_FOLDER)),
        ]));
    }

    /**
     * @return ResponseInterface
     */
    private function wizardStepPending(): ResponseInterface
    {
        $changes = DB::table('change')->where('status', '=', 'pending')->exists();

        if ($changes) {
            return response(view('components/alert-danger', [
                'alert' => I18N::translate('You should accept or reject all pending changes before upgrading.'),
            ]), StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        return response(view('components/alert-success', [
            'alert' => I18N::translate('There are no pending changes.'),
        ]));
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    private function wizardStepExport(Tree $tree): ResponseInterface
    {
        $data_filesystem = Registry::filesystem()->data();
        $filename        = $tree->name() . date('-Y-m-d') . '.ged';
        $stream          = $this->gedcom_export_service->export($tree);
        $data_filesystem->writeStream($filename, $stream);
        fclose($stream);

        return response(view('components/alert-success', [
            'alert' => I18N::translate('The family tree has been exported to %s.', e($filename)),
        ]));
    }

    /**
     * @return ResponseInterface
     */
    private function wizardStepDownload(): ResponseInterface
    {
        $root_filesystem = Registry::filesystem()->root();
        $start_time      = Registry::timeFactory()->now();
        $download_url    = $this->upgrade_service->downloadUrl();

        try {
            $bytes = $this->upgrade_service->downloadFile($download_url, $root_filesystem, self::ZIP_FILENAME);
        } catch (Throwable $exception) {
            throw new HttpServerErrorException($exception->getMessage());
        }

        $kb       = I18N::number(intdiv($bytes + 1023, 1024));
        $end_time = Registry::timeFactory()->now();
        $seconds  = I18N::number($end_time - $start_time, 2);

        return response(view('components/alert-success', [
            'alert' => I18N::translate('%1$s KB were downloaded in %2$s seconds.', $kb, $seconds),
        ]));
    }

    /**
     * For performance reasons, we use direct filesystem access for this step.
     *
     * @param string $zip_file
     * @param string $zip_folder
     *
     * @return ResponseInterface
     */
    private function wizardStepUnzip(string $zip_file, string $zip_folder): ResponseInterface
    {
        $start_time = Registry::timeFactory()->now();
        $this->upgrade_service->extractWebtreesZip($zip_file, $zip_folder);
        $count    = $this->upgrade_service->webtreesZipContents($zip_file)->count();
        $end_time = Registry::timeFactory()->now();
        $seconds  = I18N::number($end_time - $start_time, 2);

        /* I18N: …from the .ZIP file, %2$s is a (fractional) number of seconds */
        $alert = I18N::plural('%1$s file was extracted in %2$s seconds.', '%1$s files were extracted in %2$s seconds.', $count, I18N::number($count), $seconds);

        return response(view('components/alert-success', [
            'alert' => $alert,
        ]));
    }

    /**
     * @param string $zip_file
     *
     * @return ResponseInterface
     */
    private function wizardStepCopyAndCleanUp(string $zip_file): ResponseInterface
    {
        $source_filesystem = Registry::filesystem()->root(self::UPGRADE_FOLDER . self::ZIP_FILE_PREFIX);
        $root_filesystem   = Registry::filesystem()->root();

        $this->upgrade_service->startMaintenanceMode();
        $this->upgrade_service->moveFiles($source_filesystem, $root_filesystem);
        $this->upgrade_service->endMaintenanceMode();

        // While we have time, clean up any old files.
        $files_to_keep    = $this->upgrade_service->webtreesZipContents($zip_file);
        $folders_to_clean = new Collection(self::FOLDERS_TO_CLEAN);

        $this->upgrade_service->cleanFiles($root_filesystem, $folders_to_clean, $files_to_keep);

        $url    = route(ControlPanel::class);
        $alert  = I18N::translate('The upgrade is complete.');
        $button = '<a href="' . e($url) . '" class="btn btn-primary">' . I18N::translate('continue') . '</a>';

        return response(view('components/alert-success', [
            'alert' => $alert . ' ' . $button,
        ]));
    }
}
