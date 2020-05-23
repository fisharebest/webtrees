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

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Flysystem\Adapter\ChrootAdapter;
use Fisharebest\Webtrees\Exceptions\HttpServerErrorException;
use Fisharebest\Webtrees\Http\RequestHandlers\ControlPanel;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

use function assert;
use function basename;
use function date;
use function e;
use function fclose;
use function fopen;
use function fseek;
use function intdiv;
use function microtime;
use function redirect;
use function response;
use function route;
use function version_compare;
use function view;

/**
 * Controller for upgrading to a new version of webtrees.
 */
class UpgradeController extends AbstractAdminController
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

    /** @var GedcomExportService */
    private $gedcom_export_service;

    /** @var UpgradeService */
    private $upgrade_service;

    /** @var TreeService */
    private $tree_service;

    /**
     * UpgradeController constructor.
     *
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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function wizard(ServerRequestInterface $request): ResponseInterface
    {
        $continue = $request->getQueryParams()['continue'] ?? '';

        $title = I18N::translate('Upgrade wizard');

        if ($continue === '1') {
            return $this->viewResponse('admin/upgrade/steps', [
                'steps' => $this->wizardSteps(),
                'title' => $title,
            ]);
        }

        return $this->viewResponse('admin/upgrade/wizard', [
            'current_version' => Webtrees::VERSION,
            'latest_version'  => $this->upgrade_service->latestVersion(),
            'title'           => $title,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function confirm(ServerRequestInterface $request): ResponseInterface
    {
        return redirect(route('upgrade', ['continue' => 1]));
    }

    /**
     * Perform one step of the wizard
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function step(ServerRequestInterface $request): ResponseInterface
    {
        $root_filesystem = $request->getAttribute('filesystem.root');
        assert($root_filesystem instanceof FilesystemInterface);

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        // Somewhere to unpack a .ZIP file
        $temporary_filesystem = new Filesystem(new ChrootAdapter($root_filesystem, self::UPGRADE_FOLDER));

        $zip_file   = Webtrees::ROOT_DIR . self::ZIP_FILENAME;
        $zip_folder = Webtrees::ROOT_DIR . self::UPGRADE_FOLDER;


        $step = $request->getQueryParams()['step'] ?? self::STEP_CHECK;

        switch ($step) {
            case self::STEP_CHECK:
                return $this->wizardStepCheck();

            case self::STEP_PREPARE:
                return $this->wizardStepPrepare($root_filesystem);

            case self::STEP_PENDING:
                return $this->wizardStepPending();

            case self::STEP_EXPORT:
                $tree_name = $request->getQueryParams()['tree'] ?? '';
                $tree      = $this->tree_service->all()[$tree_name];
                assert($tree instanceof Tree);

                return $this->wizardStepExport($tree, $data_filesystem);

            case self::STEP_DOWNLOAD:
                return $this->wizardStepDownload($root_filesystem);

            case self::STEP_UNZIP:
                return $this->wizardStepUnzip($zip_file, $zip_folder);

            case self::STEP_COPY:
                return $this->wizardStepCopyAndCleanUp($zip_file, $root_filesystem, $temporary_filesystem);

            default:
                return response('', StatusCodeInterface::STATUS_NO_CONTENT);
        }
    }

    /**
     * @return string[]
     */
    private function wizardSteps(): array
    {
        $download_url = $this->upgrade_service->downloadUrl();

        $export_steps = [];

        foreach ($this->tree_service->all() as $tree) {
            $route = route('upgrade', [
                'step' => self::STEP_EXPORT,
                'tree' => $tree->name(),
            ]);

            $export_steps[$route] = I18N::translate('Export all the family trees to GEDCOM files…') . ' ' . e($tree->title());
        }

        return [
                route('upgrade', ['step' => self::STEP_CHECK])   => I18N::translate('Upgrade wizard'),
                route('upgrade', ['step' => self::STEP_PREPARE]) => I18N::translate('Create a temporary folder…'),
                route('upgrade', ['step' => self::STEP_PENDING]) => I18N::translate('Check for pending changes…'),
            ] + $export_steps + [
                route('upgrade', ['step' => self::STEP_DOWNLOAD]) => I18N::translate('Download %s…', e($download_url)),
                route('upgrade', ['step' => self::STEP_UNZIP])    => I18N::translate('Unzip %s to a temporary folder…', e(basename($download_url))),
                route('upgrade', ['step' => self::STEP_COPY])     => I18N::translate('Copy files…'),
            ];
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
     * @param FilesystemInterface $root_filesystem
     *
     * @return ResponseInterface
     */
    private function wizardStepPrepare(FilesystemInterface $root_filesystem): ResponseInterface
    {
        $root_filesystem->deleteDir(self::UPGRADE_FOLDER);
        $root_filesystem->createDir(self::UPGRADE_FOLDER);

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
            throw new HttpServerErrorException(I18N::translate('You should accept or reject all pending changes before upgrading.'));
        }

        return response(view('components/alert-success', [
            'alert' => I18N::translate('There are no pending changes.'),
        ]));
    }

    /**
     * @param Tree                $tree
     * @param FilesystemInterface $data_filesystem
     *
     * @return ResponseInterface
     */
    private function wizardStepExport(Tree $tree, FilesystemInterface $data_filesystem): ResponseInterface
    {
        // We store the data in PHP temporary storage.
        $stream = fopen('php://temp', 'wb+');

        if ($stream === false) {
            throw new RuntimeException('Failed to create temporary stream');
        }

        $filename = $tree->name() . date('-Y-m-d') . '.ged';

        $this->gedcom_export_service->export($tree, $stream);

        fseek($stream, 0);
        $data_filesystem->putStream($tree->name() . date('-Y-m-d') . '.ged', $stream);
        fclose($stream);

        return response(view('components/alert-success', [
            'alert' => I18N::translate('The family tree has been exported to %s.', e($filename)),
        ]));
    }

    /**
     * @param FilesystemInterface $root_filesystem
     *
     * @return ResponseInterface
     */
    private function wizardStepDownload(FilesystemInterface $root_filesystem): ResponseInterface
    {
        $start_time   = microtime(true);
        $download_url = $this->upgrade_service->downloadUrl();

        try {
            $bytes = $this->upgrade_service->downloadFile($download_url, $root_filesystem, self::ZIP_FILENAME);
        } catch (Throwable $exception) {
            throw new HttpServerErrorException($exception->getMessage());
        }

        $kb       = I18N::number(intdiv($bytes + 1023, 1024));
        $end_time = microtime(true);
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
        $start_time = microtime(true);
        $this->upgrade_service->extractWebtreesZip($zip_file, $zip_folder);
        $count    = $this->upgrade_service->webtreesZipContents($zip_file)->count();
        $end_time = microtime(true);
        $seconds  = I18N::number($end_time - $start_time, 2);

        /* I18N: …from the .ZIP file, %2$s is a (fractional) number of seconds */
        $alert = I18N::plural('%1$s file was extracted in %2$s seconds.', '%1$s files were extracted in %2$s seconds.', $count, I18N::number($count), $seconds);

        return response(view('components/alert-success', [
            'alert' => $alert,
        ]));
    }

    /**
     * @param string              $zip_file
     * @param FilesystemInterface $root_filesystem
     * @param FilesystemInterface $temporary_filesystem
     *
     * @return ResponseInterface
     */
    private function wizardStepCopyAndCleanUp(
        string $zip_file,
        FilesystemInterface $root_filesystem,
        FilesystemInterface $temporary_filesystem
    ): ResponseInterface {
        $source_filesystem = new Filesystem(new ChrootAdapter($temporary_filesystem, self::ZIP_FILE_PREFIX));

        $this->upgrade_service->startMaintenanceMode();
        $this->upgrade_service->moveFiles($source_filesystem, $root_filesystem);
        $this->upgrade_service->endMaintenanceMode();

        // While we have time, clean up any old files.
        $files_to_keep    = $this->upgrade_service->webtreesZipContents($zip_file);
        $folders_to_clean = new Collection(self::FOLDERS_TO_CLEAN);

        $this->upgrade_service->cleanFiles($root_filesystem, $folders_to_clean, $files_to_keep);

        $url    = route(ControlPanel::class);
        $alert  =  I18N::translate('The upgrade is complete.');
        $button = '<a href="' . e($url) . '" class="btn btn-primary">' . I18N::translate('continue') . '</a>';

        return response(view('components/alert-success', [
            'alert' => $alert . ' ' . $button,
        ]));
    }
}
