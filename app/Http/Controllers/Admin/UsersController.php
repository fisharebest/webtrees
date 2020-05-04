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
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

use function e;
use function route;

/**
 * Controller for user administration.
 */
class UsersController extends AbstractAdminController
{
    /** @var DatatablesService */
    private $datatables_service;

    /** @var EmailService */
    private $email_service;

    /** @var ModuleService */
    private $module_service;

    /** @var UserService */
    private $user_service;

    /** @var TreeService */
    private $tree_service;

    /**
     * UsersController constructor.
     *
     * @param DatatablesService $datatables_service
     * @param EmailService      $email_service
     * @param ModuleService     $module_service
     * @param TreeService       $tree_service
     * @param UserService       $user_service
     */
    public function __construct(
        DatatablesService $datatables_service,
        EmailService $email_service,
        ModuleService $module_service,
        TreeService $tree_service,
        UserService $user_service
    ) {
        $this->datatables_service = $datatables_service;
        $this->email_service      = $email_service;
        $this->module_service     = $module_service;
        $this->tree_service       = $tree_service;
        $this->user_service       = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');

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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function data(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');

        $languages = $this->module_service->findByInterface(ModuleLanguageInterface::class, true)
            ->mapWithKeys(static function (ModuleLanguageInterface $module): array {
                $locale = $module->locale();

                return [$locale->languageTag() => $locale->endonym()];
            });

        $query = DB::table('user')
            ->leftJoin('user_setting AS us1', static function (JoinClause $join): void {
                $join
                    ->on('us1.user_id', '=', 'user.user_id')
                    ->where('us1.setting_name', '=', 'language');
            })
            ->leftJoin('user_setting AS us2', static function (JoinClause $join): void {
                $join
                    ->on('us2.user_id', '=', 'user.user_id')
                    ->where('us2.setting_name', '=', User::PREF_TIMESTAMP_REGISTERED);
            })
            ->leftJoin('user_setting AS us3', static function (JoinClause $join): void {
                $join
                    ->on('us3.user_id', '=', 'user.user_id')
                    ->where('us3.setting_name', '=', User::PREF_TIMESTAMP_ACTIVE);
            })
            ->leftJoin('user_setting AS us4', static function (JoinClause $join): void {
                $join
                    ->on('us4.user_id', '=', 'user.user_id')
                    ->where('us4.setting_name', '=', User::PREF_IS_EMAIL_VERIFIED);
            })
            ->leftJoin('user_setting AS us5', static function (JoinClause $join): void {
                $join
                    ->on('us5.user_id', '=', 'user.user_id')
                    ->where('us5.setting_name', '=', User::PREF_IS_ACCOUNT_APPROVED);
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

        $callback = function (stdClass $row) use ($languages, $user): array {
            $row_user = $this->user_service->find($row->user_id);
            $datum = [
                view('admin/users-table-options', ['row' => $row, 'self' => $user, 'user' => $row_user]),
                $row->user_id,
                '<span dir="auto">' . e($row->user_name) . '</span>',
                '<span dir="auto">' . e($row->real_name) . '</span>',
                '<a href="mailto:' . e($row->email) . '">' . e($row->email) . '</a>',
                $languages->get($row->language, $row->language),
                $row->registered_at,
                $row->registered_at ? view('components/datetime-diff', ['timestamp' => Carbon::createFromTimestamp((int) $row->registered_at)]) : '',
                $row->active_at,
                $row->active_at ? view('components/datetime-diff', ['timestamp' => Carbon::createFromTimestamp((int) $row->active_at)]) : I18N::translate('Never'),
                $row->verified ? I18N::translate('yes') : I18N::translate('no'),
                $row->verified_by_admin ? I18N::translate('yes') : I18N::translate('no'),
            ];

            // Highlight old registrations.
            if (!$datum[10] && date('U') - $datum[6] > 604800) {
                $datum[7] = '<span class="text-danger">' . $datum[7] . '</span>';
            }

            return $datum;
        };

        return $this->datatables_service->handleQuery($request, $query, $search_columns, $sort_columns, $callback);
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
            throw new HttpNotFoundException(I18N::translate('%1$s does not exist.', 'user_id:' . $user_id));
        }

        $languages = $this->module_service->findByInterface(ModuleLanguageInterface::class, true, true)
            ->mapWithKeys(static function (ModuleLanguageInterface $module): array {
                $locale = $module->locale();

                return [$locale->languageTag() => $locale->endonym()];
            });

        return $this->viewResponse('admin/users-edit', [
            'contact_methods'  => FunctionsEdit::optionsContactMethods(),
            'default_language' => I18N::languageTag(),
            'languages'        => $languages->all(),
            'roles'            => $this->roles(),
            'trees'            => $this->tree_service->all(),
            'theme_options'    => $this->themeOptions(),
            'title'            => I18N::translate('Edit the user'),
            'user'             => $user,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function save(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getParsedBody();

        $username  = $params['username'];
        $real_name = $params['real_name'];
        $email     = $params['email'];
        $password  = $params['password'];

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

        $new_user = $this->user_service->create($username, $real_name, $email, $password);
        $new_user->setPreference(User::PREF_IS_EMAIL_VERIFIED, '1');
        $new_user->setPreference(User::PREF_LANGUAGE, I18N::languageTag());
        $new_user->setPreference(User::PREF_TIME_ZONE, Site::getPreference('TIMEZONE', 'UTC'));
        $new_user->setPreference(User::PREF_TIMESTAMP_REGISTERED, date('U'));
        $new_user->setPreference(User::PREF_TIMESTAMP_ACTIVE, '0');

        Log::addAuthenticationLog('User ->' . $username . '<- created');

        $url = route('admin-users-edit', [
            'user_id' => $new_user->id(),
        ]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');

        $params = (array) $request->getParsedBody();

        $user_id        = (int) $params['user_id'];
        $username       = $params['username'];
        $real_name      = $params['real_name'];
        $email          = $params['email'];
        $password       = $params['password'];
        $theme          = $params['theme'];
        $language       = $params['language'];
        $timezone       = $params['timezone'];
        $contact_method = $params['contact-method'];
        $comment        = $params['comment'];
        $auto_accept    = (bool) ($params[User::PREF_AUTO_ACCEPT_EDITS] ?? '');
        $canadmin       = (bool) ($params[User::PREF_IS_ADMINISTRATOR] ?? '');
        $visible_online = (bool) ($params['visible-online'] ?? '');
        $verified       = (bool) ($params[User::PREF_IS_EMAIL_VERIFIED] ?? '');
        $approved       = (bool) ($params['approved'] ?? '');

        $edit_user = $this->user_service->find($user_id);

        if ($edit_user === null) {
            throw new HttpNotFoundException(I18N::translate('%1$s does not exist', 'user_id:' . $user_id));
        }

        // We have just approved a user.  Tell them
        if ($approved && $edit_user->getPreference(User::PREF_IS_ACCOUNT_APPROVED) !== '1') {
            I18N::init($edit_user->getPreference(User::PREF_LANGUAGE));

            $base_url = $request->getAttribute('base_url');

            $this->email_service->send(
                new SiteUser(),
                $edit_user,
                Auth::user(),
                /* I18N: %s is a server name/URL */
                I18N::translate('New user at %s', $base_url),
                view('emails/approve-user-text', ['user' => $edit_user, 'base_url' => $base_url]),
                view('emails/approve-user-html', ['user' => $edit_user, 'base_url' => $base_url])
            );
        }

        $edit_user->setRealName($real_name);
        $edit_user->setPreference(User::PREF_THEME, $theme);
        $edit_user->setPreference(User::PREF_LANGUAGE, $language);
        $edit_user->setPreference(User::PREF_TIME_ZONE, $timezone);
        $edit_user->setPreference(User::PREF_CONTACT_METHOD, $contact_method);
        $edit_user->setPreference(User::PREF_NEW_ACCOUNT_COMMENT, $comment);
        $edit_user->setPreference(User::PREF_AUTO_ACCEPT_EDITS, (string) $auto_accept);
        $edit_user->setPreference(User::PREF_IS_VISIBLE_ONLINE, (string) $visible_online);
        $edit_user->setPreference(User::PREF_IS_EMAIL_VERIFIED, (string) $verified);
        $edit_user->setPreference(User::PREF_IS_ACCOUNT_APPROVED, (string) $approved);

        if ($password !== '') {
            $edit_user->setPassword($password);
        }

        // We cannot change our own admin status. Another admin will need to do it.
        if ($edit_user->id() !== $user->id()) {
            $edit_user->setPreference(User::PREF_IS_ADMINISTRATOR, $canadmin ? '1' : '');
        }

        foreach ($this->tree_service->all() as $tree) {
            $path_length = (int) $params['RELATIONSHIP_PATH_LENGTH' . $tree->id()];
            $gedcom_id   = $params['gedcomid' . $tree->id()] ?? '';
            $can_edit    = $params['canedit' . $tree->id()] ?? '';

            // Do not allow a path length to be set if the individual ID is not
            if ($gedcom_id === '') {
                $path_length = 0;
            }

            $tree->setUserPreference($edit_user, User::PREF_TREE_ACCOUNT_XREF, $gedcom_id);
            $tree->setUserPreference($edit_user, User::PREF_TREE_ROLE, $can_edit);
            $tree->setUserPreference($edit_user, User::PREF_TREE_PATH_LENGTH, (string) $path_length);
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
            User::ROLE_VISITOR   => I18N::translate('Visitor'),
            /* I18N: Listbox entry; name of a role */
            User::ROLE_MEMBER => I18N::translate('Member'),
            /* I18N: Listbox entry; name of a role */
            User::ROLE_EDITOR   => I18N::translate('Editor'),
            /* I18N: Listbox entry; name of a role */
            User::ROLE_MODERATOR => I18N::translate('Moderator'),
            /* I18N: Listbox entry; name of a role */
            User::ROLE_MANAGER  => I18N::translate('Manager'),
        ];
    }

    /**
     * @return Collection<string>
     */
    private function themeOptions(): Collection
    {
        return $this->module_service
            ->findByInterface(ModuleThemeInterface::class)
            ->map($this->module_service->titleMapper())
            ->prepend(I18N::translate('<default theme>'), '');
    }
}
