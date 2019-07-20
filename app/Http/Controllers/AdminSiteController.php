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
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Controller for site administration.
 */
class AdminSiteController extends AbstractBaseController
{
    /** @var string */
    protected $layout = 'layouts/administration';

    /**
     * @var ModuleService
     */
    private $module_service;

    /**
     * @var UserService
     */
    private $user_service;

    /**
     * AdminSiteController constructor.
     *
     * @param ModuleService $module_service
     * @param UserService   $user_service
     */
    public function __construct(ModuleService $module_service, UserService $user_service)
    {
        $this->module_service = $module_service;
        $this->user_service   = $user_service;
    }

    /**
     * Show old user files in the data folder.
     *
     * @param ServerRequestInterface $request
     * @param FilesystemInterface    $filesystem
     *
     * @return ResponseInterface
     */
    public function cleanData(ServerRequestInterface $request, FilesystemInterface $filesystem): ResponseInterface
    {
        $protected = [
            '.htaccess',
            '.gitignore',
            'index.php',
            'config.ini.php',
        ];

        if ($request->getAttribute('dbtype') === 'sqlite') {
            $protected[] = $request->getAttribute('dbname') . '.sqlite';
        }

        // Protect the media folders
        foreach (Tree::getAll() as $tree) {
            $media_directory = $tree->getPreference('MEDIA_DIRECTORY');
            [$folder] = explode('/', $media_directory);

            $protected[] = $folder;
        }

        // List the top-level contents of the data folder
        $entries = array_map(static function (array $content) {
            return $content['path'];
        }, $filesystem->listContents());

        return $this->viewResponse('admin/clean-data', [
            'title'     => I18N::translate('Clean up data folder'),
            'entries'   => $entries,
            'protected' => $protected,
        ]);
    }

    /**
     * Delete old user files in the data folder.
     *
     * @param ServerRequestInterface $request
     * @param FilesystemInterface    $filesystem
     *
     * @return ResponseInterface
     */
    public function cleanDataAction(ServerRequestInterface $request, FilesystemInterface $filesystem): ResponseInterface
    {
        $to_delete = $request->getParsedBody()['to_delete'] ?? [];
        $to_delete = array_filter($to_delete);

        foreach ($to_delete as $path) {
            $metadata = $filesystem->getMetadata($path);

            if ($metadata === false) {
                // Already deleted?
                continue;
            }

            if ($metadata['type'] === 'dir') {
                try {
                    $filesystem->deleteDir($path);

                    FlashMessages::addMessage(I18N::translate('The folder %s has been deleted.', e($path)), 'success');
                } catch (Exception $ex) {
                    FlashMessages::addMessage(I18N::translate('The folder %s could not be deleted.', e($path)), 'danger');
                }
            }

            if ($metadata['type'] === 'file') {
                try {
                    $filesystem->delete($path);

                    FlashMessages::addMessage(I18N::translate('The file %s has been deleted.', e($path)), 'success');
                } catch (Exception $ex) {
                    FlashMessages::addMessage(I18N::translate('The file %s could not be deleted.', e($path)), 'danger');
                }
            }
        }

        return redirect(route('admin-clean-data'));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function logs(ServerRequestInterface $request): ResponseInterface
    {
        $earliest = DB::table('log')->min('log_time');
        $latest   = DB::table('log')->max('log_time');

        $earliest = $earliest ? Carbon::make($earliest) : Carbon::now();
        $latest   = $latest ? Carbon::make($latest) : Carbon::now();

        $earliest = $earliest->toDateString();
        $latest   = $latest->toDateString();

        $params   = $request->getQueryParams();
        $action   = $params['action'] ?? '';
        $from     = $params['from'] ?? $earliest;
        $to       = $params['to'] ?? $latest;
        $type     = $params['type'] ?? '';
        $text     = $params['text'] ?? '';
        $ip       = $params['ip'] ?? '';
        $username = $params['username'] ?? '';
        $gedc     = $params['gedc'] ?? '';

        $from = max($from, $earliest);
        $to   = min(max($from, $to), $latest);

        $user_options = ['' => ''];
        foreach ($this->user_service->all() as $tmp_user) {
            $user_options[$tmp_user->userName()] = $tmp_user->userName();
        }

        $tree_options = ['' => ''] + Tree::getNameList();

        $title = I18N::translate('Website logs');

        return $this->viewResponse('admin/site-logs', [
            'action'       => $action,
            'earliest'     => $earliest,
            'from'         => $from,
            'gedc'         => $gedc,
            'ip'           => $ip,
            'latest'       => $latest,
            'tree_options' => $tree_options,
            'title'        => $title,
            'to'           => $to,
            'text'         => $text,
            'type'         => $type,
            'username'     => $username,
            'user_options' => $user_options,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param DatatablesService      $datatables_service
     *
     * @return ResponseInterface
     */
    public function logsData(ServerRequestInterface $request, DatatablesService $datatables_service): ResponseInterface
    {
        $query = $this->logsQuery($request->getQueryParams());

        return $datatables_service->handle($request, $query, [], [], static function (stdClass $row): array {
            return [
                $row->log_id,
                Carbon::make($row->log_time)->local()->format('Y-m-d H:i:s'),
                $row->log_type,
                '<span dir="auto">' . e($row->log_message) . '</span>',
                '<span dir="auto">' . e($row->ip_address) . '</span>',
                '<span dir="auto">' . e($row->user_name) . '</span>',
                '<span dir="auto">' . e($row->gedcom_name) . '</span>',
            ];
        });
    }

    /**
     * Generate a query for filtering the site log.
     *
     * @param string[] $params
     *
     * @return Builder
     */
    private function logsQuery(array $params): Builder
    {
        $from     = $params['from'];
        $to       = $params['to'];
        $type     = $params['type'];
        $text     = $params['text'];
        $ip       = $params['ip'];
        $username = $params['username'];
        $gedc     = $params['gedc'];

        $query = DB::table('log')
            ->leftJoin('user', 'user.user_id', '=', 'log.user_id')
            ->leftJoin('gedcom', 'gedcom.gedcom_id', '=', 'log.gedcom_id')
            ->select(['log.*', new Expression("COALESCE(user_name, '<none>') AS user_name"), new Expression("COALESCE(gedcom_name, '<none>') AS gedcom_name")]);

        if ($from !== '') {
            $query->where('log_time', '>=', $from);
        }

        if ($to !== '') {
            // before end of the day
            $query->where('log_time', '<', Carbon::make($to)->addDay());
        }

        if ($type !== '') {
            $query->where('log_type', '=', $type);
        }

        if ($text) {
            $query->whereContains('log_message', $text);
        }

        if ($ip) {
            $query->whereContains('ip_address', $ip);
        }

        if ($username) {
            $query->whereContains('user_name', $ip);
        }

        if ($gedc) {
            $query->where('gedcom_name', '=', $gedc);
        }

        return $query;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function logsDelete(ServerRequestInterface $request): ResponseInterface
    {
        $this->logsQuery($request->getParsedBody())->delete();

        return response();
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function logsExport(ServerRequestInterface $request): ResponseInterface
    {
        $content = $this->logsQuery($request->getQueryParams())
            ->orderBy('log_id')
            ->get()
            ->map(static function (stdClass $row): string {
                return
                    '"' . $row->log_time . '",' .
                    '"' . $row->log_type . '",' .
                    '"' . str_replace('"', '""', $row->log_message) . '",' .
                    '"' . $row->ip_address . '",' .
                    '"' . str_replace('"', '""', $row->user_name) . '",' .
                    '"' . str_replace('"', '""', $row->gedcom_name) . '"' .
                    "\n";
            })
            ->implode('');

        return response($content, StatusCodeInterface::STATUS_OK, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="webtrees-logs.csv"',
        ]);
    }

    /**
     * @return ResponseInterface
     */
    public function mailForm(): ResponseInterface
    {
        $mail_ssl_options       = $this->mailSslOptions();
        $mail_transport_options = $this->mailTransportOptions();

        $title = I18N::translate('Sending email');

        return $this->viewResponse('admin/site-mail', [
            'mail_ssl_options'       => $mail_ssl_options,
            'mail_transport_options' => $mail_transport_options,
            'title'                  => $title,
        ]);
    }

    /**
     * A list SSL modes (e.g. for an edit control).
     *
     * @return string[]
     */
    private function mailSslOptions(): array
    {
        return [
            'none' => I18N::translate('none'),
            /* I18N: Secure Sockets Layer - a secure communications protocol*/
            'ssl'  => I18N::translate('ssl'),
            /* I18N: Transport Layer Security - a secure communications protocol */
            'tls'  => I18N::translate('tls'),
        ];
    }

    /**
     * A list SSL modes (e.g. for an edit control).
     *
     * @return string[]
     */
    private function mailTransportOptions(): array
    {
        $options = [
            /* I18N: "sendmail" is the name of some mail software */
            'sendmail' => I18N::translate('Use sendmail to send messages'),
            'external' => I18N::translate('Use SMTP to send messages'),
        ];

        if (!function_exists('proc_open')) {
            unset($options['sendmail']);
        }

        return $options;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mailSave(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getParsedBody();

        Site::setPreference('SMTP_ACTIVE', $params['SMTP_ACTIVE']);
        Site::setPreference('SMTP_FROM_NAME', $params['SMTP_FROM_NAME']);
        Site::setPreference('SMTP_HOST', $params['SMTP_HOST']);
        Site::setPreference('SMTP_PORT', $params['SMTP_PORT']);
        Site::setPreference('SMTP_AUTH', $params['SMTP_AUTH']);
        Site::setPreference('SMTP_AUTH_USER', $params['SMTP_AUTH_USER']);
        Site::setPreference('SMTP_SSL', $params['SMTP_SSL']);
        Site::setPreference('SMTP_HELO', $params['SMTP_HELO']);
        if ($params['SMTP_AUTH_PASS'] !== '') {
            Site::setPreference('SMTP_AUTH_PASS', $params['SMTP_AUTH_PASS']);
        }

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
        $url = route('admin-control-panel');

        return redirect($url);
    }

    /**
     * @return ResponseInterface
     */
    public function preferencesForm(): ResponseInterface
    {
        $all_themes = $this->themeOptions();

        $title = I18N::translate('Website preferences');

        return $this->viewResponse('admin/site-preferences', [
            'all_themes'         => $all_themes,
            'max_execution_time' => (int) get_cfg_var('max_execution_time'),
            'title'              => $title,
        ]);
    }

    /**
     * @return Collection
     */
    private function themeOptions(): Collection
    {
        return $this->module_service
            ->findByInterface(ModuleThemeInterface::class)
            ->map($this->module_service->titleMapper());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function preferencesSave(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getParsedBody();

        $INDEX_DIRECTORY = $params['INDEX_DIRECTORY'];
        if (substr($INDEX_DIRECTORY, -1) !== '/') {
            $INDEX_DIRECTORY .= '/';
        }
        if (File::mkdir($INDEX_DIRECTORY)) {
            Site::setPreference('INDEX_DIRECTORY', $INDEX_DIRECTORY);
        } else {
            FlashMessages::addMessage(I18N::translate('The folder %s does not exist, and it could not be created.', e($INDEX_DIRECTORY)), 'danger');
        }

        Site::setPreference('THEME_DIR', $params['THEME_DIR']);
        Site::setPreference('ALLOW_CHANGE_GEDCOM', $params['ALLOW_CHANGE_GEDCOM']);
        Site::setPreference('TIMEZONE', $params['TIMEZONE']);

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
        $url = route('admin-control-panel');

        return redirect($url);
    }

    /**
     * @return ResponseInterface
     */
    public function registrationForm(): ResponseInterface
    {
        $title = I18N::translate('Sign-in and registration');

        $registration_text_options = $this->registrationTextOptions();

        return $this->viewResponse('admin/site-registration', [
            'registration_text_options' => $registration_text_options,
            'title'                     => $title,
        ]);
    }

    /**
     * A list of registration rules (e.g. for an edit control).
     *
     * @return string[]
     */
    private function registrationTextOptions(): array
    {
        return [
            0 => I18N::translate('No predefined text'),
            1 => I18N::translate('Predefined text that states all users can request a user account'),
            2 => I18N::translate('Predefined text that states admin will decide on each request for a user account'),
            3 => I18N::translate('Predefined text that states only family members can request a user account'),
            4 => I18N::translate('Choose user defined welcome text typed below'),
        ];
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function registrationSave(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getParsedBody();

        Site::setPreference('WELCOME_TEXT_AUTH_MODE', $params['WELCOME_TEXT_AUTH_MODE']);
        Site::setPreference('WELCOME_TEXT_AUTH_MODE_' . WT_LOCALE, $params['WELCOME_TEXT_AUTH_MODE_4']);
        Site::setPreference('USE_REGISTRATION_MODULE', $params['USE_REGISTRATION_MODULE']);
        Site::setPreference('SHOW_REGISTER_CAUTION', $params['SHOW_REGISTER_CAUTION']);

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
        $url = route('admin-control-panel');

        return redirect($url);
    }

    /**
     * Show the server information page.
     *
     * @return ResponseInterface
     */
    public function serverInformation(): ResponseInterface
    {
        ob_start();
        phpinfo(INFO_ALL & ~INFO_CREDITS & ~INFO_LICENSE);
        $phpinfo = ob_get_clean();
        preg_match('%<body>(.*)</body>%s', $phpinfo, $matches);
        $phpinfo = $matches[1];

        return $this->viewResponse('admin/server-information', [
            'title'   => I18N::translate('Server information'),
            'phpinfo' => $phpinfo,
        ]);
    }
}
