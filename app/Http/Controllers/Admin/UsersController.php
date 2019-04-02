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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Mail;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use const WT_BASE_URL;

/**
 * Controller for user administration.
 */
class UsersController extends AbstractAdminController
{
    private const SECONDS_PER_DAY = 24 * 60 * 60;

    /**
     * @var ModuleService
     */
    private $module_service;

    /**
     * @var UserService
     */
    private $user_service;

    /**
     * UsersController constructor.
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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function cleanup(ServerRequestInterface $request): ResponseInterface
    {
        $months = (int) ($request->getQueryParams()['months'] ?? 6);

        $inactive_threshold   = time() - $months * 30 * self::SECONDS_PER_DAY;
        $unverified_threshold = time() - 7 * self::SECONDS_PER_DAY;

        $users = $this->user_service->all();

        $inactive_users = $users->filter(static function (UserInterface $user) use ($inactive_threshold): bool {
            if ($user->getPreference('sessiontime') === '0') {
                $datelogin = (int) $user->getPreference('reg_timestamp');
            } else {
                $datelogin = (int) $user->getPreference('sessiontime');
            }

            return $datelogin < $inactive_threshold && $user->getPreference('verified');
        });

        $unverified_users = $users->filter(static function (UserInterface $user) use ($unverified_threshold): bool {
            if ($user->getPreference('sessiontime') === '0') {
                $datelogin = (int) $user->getPreference('reg_timestamp');
            } else {
                $datelogin = (int) $user->getPreference('sessiontime');
            }

            return $datelogin < $unverified_threshold && !$user->getPreference('verified');
        });

        $options = $this->monthOptions();

        $title = I18N::translate('Delete inactive users');

        return $this->viewResponse('admin/users-cleanup', [
            'months'           => $months,
            'options'          => $options,
            'title'            => $title,
            'inactive_users'   => $inactive_users,
            'unverified_users' => $unverified_users,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function cleanupAction(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->user_service->all() as $user) {
            if ((bool) $request->getParsedBody()['del_' . $user->id()]) {
                Log::addAuthenticationLog('Deleted user: ' . $user->userName());
                $this->user_service->delete($user);

                FlashMessages::addMessage(I18N::translate('The user %s has been deleted.', e($user->userName())), 'success');
            }
        }

        $url = route('admin-users-cleanup');

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, UserInterface $user): ResponseInterface
    {
        $filter = $request->getQueryParams()['filter'] ?? '';

        $all_users = $this->user_service->all();

        $page_size = (int) $user->getPreference(' admin_users_page_size', '10');

        $title = I18N::translate('User administration');

        return $this->viewResponse('admin/users', [
            'all_users' => $all_users,
            'filter'    => $filter,
            'page_size' => $page_size,
            'title'     => $title,
        ]);
    }

    /**
     * @param DatatablesService      $datatables_service
     * @param ServerRequestInterface $request
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function data(DatatablesService $datatables_service, ServerRequestInterface $request, UserInterface $user): ResponseInterface
    {
        $installed_languages = [];
        foreach (I18N::installedLocales() as $installed_locale) {
            $installed_languages[$installed_locale->languageTag()] = $installed_locale->endonym();
        }

        $query = DB::table('user')
            ->leftJoin('user_setting AS us1', static function (JoinClause $join): void {
                $join
                    ->on('us1.user_id', '=', 'user.user_id')
                    ->where('us1.setting_name', '=', 'language');
            })
            ->leftJoin('user_setting AS us2', static function (JoinClause $join): void {
                $join
                    ->on('us2.user_id', '=', 'user.user_id')
                    ->where('us2.setting_name', '=', 'reg_timestamp');
            })
            ->leftJoin('user_setting AS us3', static function (JoinClause $join): void {
                $join
                    ->on('us3.user_id', '=', 'user.user_id')
                    ->where('us3.setting_name', '=', 'sessiontime');
            })
            ->leftJoin('user_setting AS us4', static function (JoinClause $join): void {
                $join
                    ->on('us4.user_id', '=', 'user.user_id')
                    ->where('us4.setting_name', '=', 'verified');
            })
            ->leftJoin('user_setting AS us5', static function (JoinClause $join): void {
                $join
                    ->on('us5.user_id', '=', 'user.user_id')
                    ->where('us5.setting_name', '=', 'verified_by_admin');
            })
            ->where('user.user_id', '>', '0')
            ->select([
                'user.user_id AS edit_menu', // Hidden column
                'user.user_id',
                'user_name',
                'real_name',
                'email',
                'us1.setting_value AS language',
                'us2.setting_value AS registered_at_sort', // Hidden column
                'us2.setting_value AS registered_at',
                'us3.setting_value AS active_at_sort', // Hidden column
                'us3.setting_value AS active_at',
                'us4.setting_value AS verified',
                'us5.setting_value AS verified_by_admin',
            ]);

        $search_columns = ['user_name', 'real_name', 'email'];
        $sort_columns   = [];

        $callback = static function (stdClass $row) use ($installed_languages, $user): array {
            if ($row->user_id !== $user->id()) {
                $admin_options = '<div class="dropdown-item"><a href="#" onclick="return masquerade(' . $row->user_id . ')">' . view('icons/user') . ' ' . I18N::translate('Masquerade as this user') . '</a></div>' . '<div class="dropdown-item"><a href="#" data-confirm="' . I18N::translate('Are you sure you want to delete “%s”?', e($row->user_name)) . '" onclick="delete_user(this.dataset.confirm, ' . $row->user_id . ');">' . view('icons/delete') . ' ' . I18N::translate('Delete') . '</a></div>';
            } else {
                // Do not delete ourself!
                $admin_options = '';
            }

            // Link to send email to other users.
            if ($row->user_id != $user->id()) {
                $row->email = '<a href="' . e(route('message', ['to' => $row->user_name])) . '">' . e($row->email) . '</a>';
            } else {
                $row->email = e($row->email);
            }

            $datum = [
                '<div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" id="edit-user-button-' . $row->user_id . '" aria-haspopup="true" aria-expanded="false">' . view('icons/edit') . ' <span class="caret"></span></button><div class="dropdown-menu" aria-labelledby="edit-user-button-' . $row->user_id . '"><div class="dropdown-item"><a href="' . e(route('admin-users-edit', ['user_id' => $row->user_id])) . '">' . view('icons/edit') . ' ' . I18N::translate('Edit') . '</a></div><div class="divider"></div><div class="dropdown-item"><a href="' . e(route('user-page-user-edit', ['user_id' => $row->user_id])) . '">' . view('icons/block') . ' ' . I18N::translate('Change the blocks on this user’s “My page”') . '</a></div>' . $admin_options . '</div></div>',
                $row->user_id,
                '<span dir="auto">' . e($row->user_name) . '</span>',
                '<span dir="auto">' . e($row->real_name) . '</span>',
                $row->email,
                $installed_languages[$row->language] ?? $row->language,
                $row->registered_at,
                $row->registered_at ? view('components/datetime-diff', ['timestamp' => Carbon::createFromTimestamp((int) $row->registered_at)]) : '',
                $row->active_at,
                $row->active_at ? view('components/datetime-diff', ['timestamp' => Carbon::createFromTimestamp((int) $row->active_at)]) : I18N::translate('Never'),
                $row->verified ? I18N::translate('yes') : I18N::translate('no'),
                $row->verified_by_admin ? I18N::translate('yes') : I18N::translate('no'),
            ];

            // Highlight old registrations.
            if (date('U') - $datum[6] > 604800 && !$datum[10]) {
                $datum[7] = '<span class="text-danger">' . $datum[7] . '</span>';
            }

            return $datum;
        };

        return $datatables_service->handle($request, $query, $search_columns, $sort_columns, $callback);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $email     = $request->getQueryParams()['email'] ?? '';
        $real_name = $request->getQueryParams()['real_name'] ?? '';
        $username  = $request->getQueryParams()['username'] ?? '';
        $title     = I18N::translate('Add a user');

        return $this->viewResponse('admin/users-create', [
            'email'     => $email,
            'real_name' => $real_name,
            'title'     => $title,
            'username'  => $username,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        $user_id = (int) $request->getQueryParams()['user_id'];
        $user    = $this->user_service->find($user_id);

        if ($user === null) {
            throw new NotFoundHttpException(I18N::translate('%1$s does not exist.', 'user_id:' . $user_id));
        }

        return $this->viewResponse('admin/users-edit', [
            'contact_methods' => FunctionsEdit::optionsContactMethods(),
            'default_locale'  => WT_LOCALE,
            'locales'         => I18N::installedLocales(),
            'roles'           => $this->roles(),
            'trees'           => Tree::getAll(),
            'theme_options'   => $this->themeOptions(),
            'title'           => I18N::translate('Edit the user'),
            'user'            => $user,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function save(ServerRequestInterface $request): ResponseInterface
    {
        $username  = $request->getParsedBody()['username'];
        $real_name = $request->getParsedBody()['real_name'];
        $email     = $request->getParsedBody()['email'];
        $password  = $request->getParsedBody()['password'];

        $errors = false;
        if ($this->user_service->findByUserName($username)) {
            FlashMessages::addMessage(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.'));
            $errors = true;
        }

        if ($this->user_service->findByEmail($email)) {
            FlashMessages::addMessage(I18N::translate('Duplicate email address. A user with that email already exists.'));
            $errors = true;
        }

        if ($errors) {
            $url = route('admin-users-create', [
                'email'     => $email,
                'real_name' => $real_name,
                'username'  => $username,
            ]);

            return redirect($url);
        }

        $new_user = $this->user_service->create($username, $real_name, $email, $password)
            ->setPreference('verified', '1')
            ->setPreference('language', WT_LOCALE)
            ->setPreference('timezone', Site::getPreference('TIMEZONE'))
            ->setPreference('reg_timestamp', date('U'))
            ->setPreference('sessiontime', '0');

        Log::addAuthenticationLog('User ->' . $username . '<- created');

        $url = route('admin-users-edit', [
            'user_id' => $new_user->id(),
        ]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request, UserInterface $user): ResponseInterface
    {
        $user_id        = (int) $request->getParsedBody()['user_id'];
        $username       = $request->getParsedBody()['username'];
        $real_name      = $request->getParsedBody()['real_name'];
        $email          = $request->getParsedBody()['email'];
        $password       = $request->getParsedBody()['password'];
        $theme          = $request->getParsedBody()['theme'];
        $language       = $request->getParsedBody()['language'];
        $timezone       = $request->getParsedBody()['timezone'];
        $contact_method = $request->getParsedBody()['contact_method'];
        $comment        = $request->getParsedBody()['comment'];
        $auto_accept    = (bool) $request->getParsedBody()['auto_accept'];
        $canadmin       = (bool) $request->getParsedBody()['canadmin'];
        $visible_online = (bool) $request->getParsedBody()['visible_online'];
        $verified       = (bool) $request->getParsedBody()['verified'];
        $approved       = (bool) $request->getParsedBody()['approved'];

        $edit_user = $this->user_service->find($user_id);

        if ($edit_user === null) {
            throw new NotFoundHttpException(I18N::translate('%1$s does not exist', 'user_id:' . $user_id));
        }

        // We have just approved a user.  Tell them
        if ($edit_user->getPreference('verified_by_admin') !== '1' && $approved) {
            I18N::init($edit_user->getPreference('language'));

            Mail::send(
                Auth::user(),
                $edit_user,
                Auth::user(),
                /* I18N: %s is a server name/URL */
                I18N::translate('New user at %s', WT_BASE_URL),
                view('emails/approve-user-text', ['user' => $edit_user, 'site_url' => WT_BASE_URL]),
                view('emails/approve-user-html', ['user' => $edit_user, 'site_url' => WT_BASE_URL])
            );
        }

        $edit_user
            ->setRealName($real_name)
            ->setPreference('theme', $theme)
            ->setPreference('language', $language)
            ->setPreference('TIMEZONE', $timezone)
            ->setPreference('contactmethod', $contact_method)
            ->setPreference('comment', $comment)
            ->setPreference('auto_accept', (string) $auto_accept)
            ->setPreference('visibleonline', (string) $visible_online)
            ->setPreference('verified', (string) $verified)
            ->setPreference('verified_by_admin', (string) $approved);

        if ($password !== '') {
            $edit_user->setPassword($password);
        }

        // We cannot change our own admin status. Another admin will need to do it.
        if ($edit_user->id() !== $user->id()) {
            $edit_user->setPreference('canadmin', $canadmin ? '1' : '0');
        }

        foreach (Tree::getAll() as $tree) {
            $path_length = (int) $request->getParsedBody()['RELATIONSHIP_PATH_LENGTH' . $tree->id()];
            $gedcom_id   = $request->getParsedBody()['gedcomid' . $tree->id()];
            $can_edit    = $request->getParsedBody()['canedit' . $tree->id()];

            // Do not allow a path length to be set if the individual ID is not
            if ($gedcom_id === '') {
                $path_length = 0;
            }

            $tree->setUserPreference($edit_user, 'gedcomid', $gedcom_id);
            $tree->setUserPreference($edit_user, 'canedit', $can_edit);
            $tree->setUserPreference($edit_user, 'RELATIONSHIP_PATH_LENGTH', (string) $path_length);
        }

        if ($edit_user->email() !== $email && $this->user_service->findByEmail($email) instanceof User) {
            FlashMessages::addMessage(I18N::translate('Duplicate email address. A user with that email already exists.') . $email, 'danger');

            return redirect(route('admin-users-edit', ['user_id' => $edit_user->id()]));
        }

        if ($edit_user->userName() !== $username && $this->user_service->findByUserName($username) instanceof User) {
            FlashMessages::addMessage(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.'), 'danger');

            return redirect(route('admin-users-edit', ['user_id' => $edit_user->id()]));
        }

        $edit_user
            ->setEmail($email)
            ->setUserName($username);

        return redirect(route('admin-users'));
    }

    /**
     * @return string[]
     */
    private function roles(): array
    {
        return [
            /* I18N: Listbox entry; name of a role */
            'none'   => I18N::translate('Visitor'),
            /* I18N: Listbox entry; name of a role */
            'access' => I18N::translate('Member'),
            /* I18N: Listbox entry; name of a role */
            'edit'   => I18N::translate('Editor'),
            /* I18N: Listbox entry; name of a role */
            'accept' => I18N::translate('Moderator'),
            /* I18N: Listbox entry; name of a role */
            'admin'  => I18N::translate('Manager'),
        ];
    }

    /**
     * Delete users older than this.
     *
     * @return string[]
     */
    private function monthOptions(): array
    {
        return [
            3  => I18N::number(3),
            6  => I18N::number(6),
            9  => I18N::number(9),
            12 => I18N::number(12),
            18 => I18N::number(18),
            24 => I18N::number(24),
        ];
    }

    /**
     * @return Collection
     * @return string[]
     */
    private function themeOptions(): Collection
    {
        return $this->module_service
            ->findByInterface(ModuleThemeInterface::class)
            ->map($this->module_service->titleMapper())
            ->prepend(I18N::translate('<default theme>'), '');
    }
}
