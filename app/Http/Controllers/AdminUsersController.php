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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for user administration.
 */
class AdminUsersController extends AbstractBaseController
{
    const SECONDS_PER_DAY = 24 * 60 * 60;

    /** @var string */
    protected $layout = 'layouts/administration';

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function cleanup(Request $request): Response
    {
        $months = (int) $request->get('months', 6);

        $inactive_threshold   = time() - $months * 30 * self::SECONDS_PER_DAY;
        $unverified_threshold = time() - 7 * self::SECONDS_PER_DAY;

        $users = User::all();

        $inactive_users = array_filter($users, function (User $user) use ($inactive_threshold): bool {
            if ($user->getPreference('sessiontime') === '0') {
                $datelogin = (int) $user->getPreference('reg_timestamp');
            } else {
                $datelogin = (int) $user->getPreference('sessiontime');
            }

            return $datelogin < $inactive_threshold && $user->getPreference('verified');
        });

        $unverified_users = array_filter($users, function (User $user) use ($unverified_threshold): bool {
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
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function cleanupAction(Request $request): RedirectResponse
    {
        foreach (User::all() as $user) {
            if ((bool) $request->get('del_' . $user->getUserId())) {
                Log::addAuthenticationLog('Deleted user: ' . $user->getUserName());
                $user->delete();

                FlashMessages::addMessage(I18N::translate('The user %s has been deleted.', e($user->getUserName())), 'success');
            }
        }

        $url = route('admin-users-cleanup');

        return new RedirectResponse($url);
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return Response
     */
    public function index(Request $request, User $user): Response
    {
        $filter = $request->get('filter', '');

        $all_users = User::all();

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
     * @param Request $request
     *
     * @param User    $user
     *
     * @return JsonResponse
     */
    public function data(Request $request, User $user): JsonResponse
    {
        $search = $request->get('search')['value'];
        $start  = (int) $request->get('start');
        $length = (int) $request->get('length');
        $order  = $request->get('order', []);
        $draw   = (int) $request->get('draw');

        $sql_select =
            "SELECT SQL_CALC_FOUND_ROWS u.user_id, user_name, real_name, email, us1.setting_value AS language, us2.setting_value AS registered_at, us3.setting_value AS active_at, us4.setting_value AS verified, us5.setting_value AS verified_by_admin" .
            " FROM `##user` u" .
            " LEFT JOIN `##user_setting` us1 ON (u.user_id=us1.user_id AND us1.setting_name='language')" .
            " LEFT JOIN `##user_setting` us2 ON (u.user_id=us2.user_id AND us2.setting_name='reg_timestamp')" .
            " LEFT JOIN `##user_setting` us3 ON (u.user_id=us3.user_id AND us3.setting_name='sessiontime')" .
            " LEFT JOIN `##user_setting` us4 ON (u.user_id=us4.user_id AND us4.setting_name='verified')" .
            " LEFT JOIN `##user_setting` us5 ON (u.user_id=us5.user_id AND us5.setting_name='verified_by_admin')" .
            " WHERE u.user_id > 0";

        $args = [];

        if ($search) {
            $sql_select .= " AND (user_name LIKE CONCAT('%', :search_1, '%') OR real_name LIKE CONCAT('%', :search_2, '%') OR email LIKE CONCAT('%', :search_3, '%'))";
            $args['search_1'] = $search;
            $args['search_2'] = $search;
            $args['search_3'] = $search;
        }

        if ($order) {
            $sql_select .= " ORDER BY ";
            foreach ($order as $key => $value) {
                if ($key > 0) {
                    $sql_select .= ',';
                }
                // Columns in datatables are numbered from zero.
                // Columns in MySQL are numbered starting with one.
                switch ($value['dir']) {
                    case 'asc':
                        $sql_select .= (1 + $value['column']) . " ASC ";
                        break;
                    case 'desc':
                        $sql_select .= (1 + $value['column']) . " DESC ";
                        break;
                }
            }
        } else {
            $sql_select = " ORDER BY 1 ASC";
        }

        if ($length) {
            $user->setPreference('admin_users_page_size', $length);
            $sql_select .= " LIMIT :limit OFFSET :offset";
            $args['limit']  = $length;
            $args['offset'] = $start;
        }

        $rows = Database::prepare($sql_select)->execute($args)->fetchAll();

        // Total filtered/unfiltered rows
        $recordsFiltered = (int) Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
        $recordsTotal    = (int) Database::prepare("SELECT COUNT(*) FROM `##user` WHERE user_id > 0")->fetchOne();

        $installed_languages = [];
        foreach (I18N::installedLocales() as $installed_locale) {
            $installed_languages[$installed_locale->languageTag()] = $installed_locale->endonym();
        }

        // Reformat various columns for display
        $data = [];
        foreach ($rows as $row) {
            $user_id   = $row->user_id;
            $user_name = $row->user_name;

            if ($user_id != $user->getUserId()) {
                $admin_options = '<div class="dropdown-item"><a href="#" onclick="return masquerade(' . $user_id . ')">' . view('icons/user') . ' ' . I18N::translate('Masquerade as this user') . '</a></div>' . '<div class="dropdown-item"><a href="#" data-confirm="' . I18N::translate('Are you sure you want to delete “%s”?', e($user_name)) . '" onclick="delete_user(this.dataset.confirm, ' . $user_id . ');">' . view('icons/delete') . ' ' . I18N::translate('Delete') . '</a></div>';
            } else {
                // Do not delete ourself!
                $admin_options = '';
            }

            $datum = [
                '<div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" id="edit-user-button-' . $user_id . '" aria-haspopup="true" aria-expanded="false">' . view('icons/edit') . ' <span class="caret"></span></button><div class="dropdown-menu" aria-labelledby="edit-user-button-' . $user_id . '"><div class="dropdown-item"><a href="' . e(route('admin-users-edit', ['user_id' => $user_id])) . '">' . view('icons/edit') . ' ' . I18N::translate('Edit') . '</a></div><div class="divider"></div><div class="dropdown-item"><a href="' . e(route('user-page-user-edit', ['user_id' => $user_id])) . '">' . view('icons/block') . ' ' . I18N::translate('Change the blocks on this user’s “My page”') . '</a></div>' . $admin_options . '</div></div>',
                $user_id,
                '<span dir="auto">' . e($user_name) . '</span>',
                '<span dir="auto">' . e($row->user_name) . '</span>',
                e($row->email),
                $installed_languages[$row->language] ?? $row->language,
                $row->registered_at,
                $row->registered_at > 0 ? FunctionsDate::formatTimestamp($row->registered_at + WT_TIMESTAMP_OFFSET) : '',
                $row->active_at,
                $row->active_at > 0 ? FunctionsDate::formatTimestamp($row->active_at + WT_TIMESTAMP_OFFSET) : I18N::translate('Never'),
                $row->verified ? I18N::translate('yes') : I18N::translate('no'),
                $row->verified_by_admin ? I18N::translate('yes') : I18N::translate('no'),
            ];

            // Link to send email to other users.
            if ($user_id != $user->getUserId()) {
                $datum[4] = '<a href="' . e(route('message', ['to'  => $datum[2],
                                                              'url' => route('admin-users'),
                    ])) . '">' . $datum[4] . '</a>';
            }

            // Highlight old registrations.
            if (date('U') - $datum[6] > 604800 && !$datum[10]) {
                $datum[7] = '<span class="red">' . $datum[7] . '</span>';
            }

            $data[] = $datum;
        }

        // See http://www.datatables.net/usage/server-side
        return new JsonResponse([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $email     = $request->get('email', '');
        $real_name = $request->get('real_name', '');
        $username  = $request->get('username', '');
        $title     = I18N::translate('Add a user');

        return $this->viewResponse('admin/users-create', [
            'email'     => $email,
            'real_name' => $real_name,
            'title'     => $title,
            'username'  => $username,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $user_id = (int) $request->get('user_id');
        $user    = User::find($user_id);

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
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function save(Request $request): RedirectResponse
    {
        $username  = $request->get('username');
        $real_name = $request->get('real_name');
        $email     = $request->get('email');
        $pass1     = $request->get('pass1');
        $pass2     = $request->get('pass2');

        $errors = false;
        if (User::findByUserName($username)) {
            FlashMessages::addMessage(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.'));
            $errors = true;
        }

        if (User::findByEmail($email)) {
            FlashMessages::addMessage(I18N::translate('Duplicate email address. A user with that email already exists.'));
            $errors = true;
        }

        if ($pass1 !== $pass2) {
            FlashMessages::addMessage(I18N::translate('The passwords do not match.'));
            $errors = true;
        }

        if ($errors) {
            $url = route('admin-users-create', [
                'email'     => $email,
                'real_name' => $real_name,
                'username'  => $username,
            ]);

            return new RedirectResponse($url);
        }

        $new_user = User::create($username, $real_name, $email, $pass1)
            ->setPreference('verified', '1')
            ->setPreference('language', WT_LOCALE)
            ->setPreference('timezone', Site::getPreference('TIMEZONE'))
            ->setPreference('reg_timestamp', date('U'))
            ->setPreference('sessiontime', '0');

        Log::addAuthenticationLog('User ->' . $username . '<- created');

        $url = route('admin-users-edit', [
            'user_id' => $new_user->getUserId(),
        ]);

        return new RedirectResponse($url);
    }


    /**
     * @param Request $request
     * @param User    $user
     *
     * @return RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $user_id        = (int) $request->get('user_id');
        $username       = $request->get('username');
        $real_name      = $request->get('real_name');
        $email          = $request->get('email');
        $pass1          = $request->get('pass1');
        $theme          = $request->get('theme');
        $language       = $request->get('language');
        $timezone       = $request->get('timezone');
        $contact_method = $request->get('contact_method');
        $comment        = $request->get('comment');
        $auto_accept    = (bool) $request->get('auto_accept');
        $canadmin       = (bool) $request->get('canadmin');
        $visible_online = (bool) $request->get('visible_online');
        $verified       = (bool) $request->get('verified');
        $approved       = (bool) $request->get('approved');

        $edit_user = User::find($user_id);

        if ($edit_user === null) {
            throw new NotFoundHttpException(I18N::translate('%1$s does not exist', 'user_id:' . $user_id));
        }

        $edit_user
            ->setRealName($real_name)
            ->setEmail($email)
            ->setUserName($username)
            ->setPreference('theme', $theme)
            ->setPreference('language', $language)
            ->setPreference('TIMEZONE', $timezone)
            ->setPreference('contactmethod', $contact_method)
            ->setPreference('comment', $comment)
            ->setPreference('auto_accept', (string) $auto_accept)
            ->setPreference('visibleonline', (string) $visible_online)
            ->setPreference('verified', (string) $verified)
            ->setPreference('verified_by_admin', (string) $approved);

        if ($pass1 !== '') {
            $edit_user->setPassword($pass1);
        }

        // We cannot change our own admin status. Another admin will need to do it.
        if ($edit_user->getUserId() !== $user->getUserId()) {
            $edit_user->setPreference('canadmin', $canadmin ? '1' : '0');
        }

        foreach (Tree::getAll() as $tree) {
            $path_length = (int) $request->get('RELATIONSHIP_PATH_LENGTH' . $tree->getTreeId());
            $gedcom_id   = $request->get('gedcomid' . $tree->getTreeId(), '');
            $can_edit    = $request->get('canedit' . $tree->getTreeId(), '');

            // Do not allow a path length to be set if the individual ID is not
            if ($gedcom_id === '') {
                $path_length = 0;
            }

            $tree->setUserPreference($edit_user, 'gedcomid', $gedcom_id);
            $tree->setUserPreference($edit_user, 'canedit', $can_edit);
            $tree->setUserPreference($edit_user, 'RELATIONSHIP_PATH_LENGTH', (string) $path_length);
        }

        $url = route('admin-users');

        return new RedirectResponse($url);
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
     * @return string[]
     */
    private function themeOptions(): array
    {
        return ['' => I18N::translate('<default theme>')] + Theme::themeNames();
    }
}
