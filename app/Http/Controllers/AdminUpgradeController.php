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

namespace Fisharebest\Webtrees\Http\Controllers;

use Exception;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use GuzzleHttp\Client;
use Illuminate\Database\Capsule\Manager as DB;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use ZipArchive;

/**
 * Controller for upgrading to a new version of webtrees.
 */
class AdminUpgradeController extends AbstractBaseController
{
    // Icons for success and failure
    const SUCCESS = '<i class="fas fa-check" style="color:green"></i> ';
    const FAILURE = '<i class="fas fa-times" style="color:red"></i> ';

    // Options for fetching files using GuzzleHTTP
    const GUZZLE_OPTIONS = [
        'connect_timeout' => 25,
        'read_timeout'    => 25,
        'timeout'         => 55,
    ];

    const LOCK_FILE = 'data/offline.txt';

    /** @var string */
    protected $layout = 'layouts/administration';

    /** @var TimeoutService */
    private $timeout_service;

    /** @var UpgradeService */
    private $upgrade_service;

    /**
     * AdminUpgradeController constructor.
     *
     * @param TimeoutService $timeout_service
     * @param UpgradeService $upgrade_service
     */
    public function __construct(TimeoutService $timeout_service, UpgradeService $upgrade_service)
    {
        $this->timeout_service = $timeout_service;
        $this->upgrade_service = $upgrade_service;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function wizard(Request $request): Response
    {
        $continue = (bool) $request->get('continue');

        $title = I18N::translate('Upgrade wizard');

        if ($continue) {
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
     * Perform one step of the wizard
     *
     * @param Request   $request
     * @param Tree|null $tree
     *
     * @return Response
     */
    public function step(Request $request, Tree $tree = null): Response
    {
        $step = $request->get('step');

        switch ($step) {
            case 'Check':
                return $this->wizardStepCheck();
            case 'Pending':
                return $this->wizardStepPending();
            case 'Export':
                if ($tree instanceof Tree) {
                    return $this->wizardStepExport($tree);
                }

                return $this->success('The tree no longer exists.');
            case 'Download':
                return $this->wizardStepDownload();
            case 'Unzip':
                return $this->wizardStepUnzip();
            case 'Copy':
                return $this->wizardStepCopy();
            default:
                throw new NotFoundHttpException();
        }
    }

    /**
     * @return string[]
     */
    private function wizardSteps(): array
    {
        $download_url = $this->upgrade_service->downloadUrl();

        $export_steps = [];

        foreach (Tree::getAll() as $tree) {
            $route = route('upgrade', [
                'step' => 'Export',
                'ged'  => $tree->name(),
            ]);

            $export_steps[$route] = I18N::translate('Export all the family trees to GEDCOM files…') . ' ' . e($tree->title());
        }

        return [
                route('upgrade', ['step' => 'Check'])   => 'config.php',
                route('upgrade', ['step' => 'Pending']) => I18N::translate('Check for pending changes…'),
            ] + $export_steps + [
                route('upgrade', ['step' => 'Download']) => I18N::translate('Download %s…', e($download_url)),
                route('upgrade', ['step' => 'Unzip'])    => I18N::translate('Unzip %s to a temporary folder…', e(basename($download_url))),
                route('upgrade', ['step' => 'Copy'])     => I18N::translate('Copy files…'),
            ];
    }

    /**
     * @return Response
     */
    private function wizardStepCheck(): Response
    {
        $latest_version = $this->upgrade_service->latestVersion();

        if ($latest_version === '') {
            return $this->failure(I18N::translate('No upgrade information is available.'));
        }

        if (version_compare(Webtrees::VERSION, $latest_version) >= 0) {
            return $this->failure(I18N::translate('This is the latest version of webtrees. No upgrade is available.'));
        }

        /* I18N: %s is a version number, such as 1.2.3 */
        return $this->success(I18N::translate('Upgrade to webtrees %s.', e($latest_version)));
    }

    /**
     * @return Response
     */
    private function wizardStepPending(): Response
    {
        $changes_exist = DB::table('change')
            ->where('status', '=', 'pending')
            ->exists();

        if (!$changes_exist) {
            return $this->success(I18N::translate('There are no pending changes.'));
        }

        $route   = route('show-pending');
        $message = I18N::translate('You should accept or reject all pending changes before upgrading.');
        $message .= ' <a href="' . e($route) . '">' . I18N::translate('Pending changes') . '</a>';

        return $this->failure($message);
    }

    /**
     * @param Tree $tree
     *
     * @return Response
     */
    private function wizardStepExport(Tree $tree): Response
    {
        $filename = WT_DATA_DIR . $tree->name() . date('-Y-m-d') . '.ged';

        try {
            $stream = fopen($filename, 'w');

            if ($stream !== false) {
                $tree->exportGedcom($stream);
                fclose($stream);

                return $this->success(I18N::translate('The family tree has been exported to %s.', e($filename)));
            }
        } catch (Throwable $ex) {
            // Can't write to the data folder.
        }

        return $this->failure(I18N::translate('The file %s could not be created.', e($filename)));
    }

    /**
     * @return Response
     */
    private function wizardStepDownload(): Response
    {
        try {
            $download_url = $this->upgrade_service->downloadUrl();
            $zip_file     = WT_DATA_DIR . basename($download_url);
            $zip_stream   = fopen($zip_file, 'w');
            
            if ($zip_stream === false) {
                throw new Exception('Cannot read ZIP file: ' . $zip_file);
            }

            $start_time   = microtime(true);
            $client       = new Client();

            $response = $client->get($download_url, self::GUZZLE_OPTIONS);
            $stream   = $response->getBody();

            while (!$stream->eof()) {
                fwrite($zip_stream, $stream->read(65536));
            }

            $stream->close();
            fclose($zip_stream);
            $zip_size = filesize($zip_file);
            $end_time = microtime(true);

            if ($zip_size > 0) {
                $kb      = I18N::number(intdiv($zip_size + 1023, 1024));
                $seconds = I18N::number($end_time - $start_time, 2);

                /* I18N: %1$s is a number of KB, %2$s is a (fractional) number of seconds */
                return $this->success(I18N::translate('%1$s KB were downloaded in %2$s seconds.', $kb, $seconds));
            }

            if (!in_array('ssl', stream_get_transports())) {
                // Guess why we might have failed...
                return $this->failure(I18N::translate('This server does not support secure downloads using HTTPS.'));
            }

            return $this->failure('');
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }
    }

    /**
     * @return Response
     */
    private function wizardStepUnzip(): Response
    {
        $download_url   = $this->upgrade_service->downloadUrl();
        $zip_file       = WT_DATA_DIR . basename($download_url);
        $tmp_folder     = WT_DATA_DIR . basename($download_url, '.zip');
        $src_filesystem = new Filesystem(new ZipArchiveAdapter($zip_file, null, 'webtrees'));
        $paths          = $src_filesystem->listContents('', true);
        $paths          = array_filter($paths, function (array $file): bool {
            return $file['type'] === 'file';
        });

        $start_time = microtime(true);

        // The Flysystem/ZipArchiveAdapter is very slow, taking over a second per file.
        // So we do this step using the native PHP library.

        $zip = new ZipArchive();
        if ($zip->open($zip_file)) {
            $zip->extractTo($tmp_folder);
            $zip->close();
            echo 'ok';
        } else {
            echo 'failed';
        }

        $end_time = microtime(true);
        $seconds  = I18N::number($end_time - $start_time, 2);
        $count    = count($paths);

        /* I18N: …from the .ZIP file, %2$s is a (fractional) number of seconds */
        return $this->success(I18N::plural('%1$s file was extracted in %2$s seconds.', '%1$s files were extracted in %2$s seconds.', $count, $count, $seconds));
    }

    /**
     * @return Response
     */
    private function wizardStepCopy(): Response
    {
        $download_url   = $this->upgrade_service->downloadUrl();
        $src_filesystem = new Filesystem(new Local(WT_DATA_DIR . basename($download_url, '.zip') . '/webtrees'));
        $dst_filesystem = new Filesystem(new Local(WT_ROOT));
        $paths          = $src_filesystem->listContents('', true);
        $paths          = array_filter($paths, function (array $file): bool {
            return $file['type'] === 'file';
        });

        $lock_file_text = I18N::translate('This website is being upgraded. Try again in a few minutes.');
        $dst_filesystem->put(self::LOCK_FILE, $lock_file_text);

        foreach ($paths as $path) {
            $dst_filesystem->put($path['path'], $src_filesystem->read($path['path']));

            if ($this->timeout_service->isTimeNearlyUp()) {
                return $this->failure(I18N::translate('The server’s time limit has been reached.'));
            }
        }

        $dst_filesystem->delete(self::LOCK_FILE);

        // Delete the temporary files - if there is enough time.
        foreach ($paths as $path) {
            $src_filesystem->delete($path['path']);

            if (($this->timeout_service->isTimeNearlyUp())) {
                break;
            }
        }

        return $this->success(I18N::translate('The upgrade is complete.'));
    }

    /**
     * @param string $message
     *
     * @return Response
     */
    private function success(string $message): Response
    {
        return new Response(self::SUCCESS . $message);
    }

    /**
     * @param string $message
     *
     * @return Response
     */
    private function failure(string $message): Response
    {
        return new Response(self::FAILURE . $message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
