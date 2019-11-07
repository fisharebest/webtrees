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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use DateTimeZone;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_combine;

/**
 * Edit user account details.
 */
class AccountEdit implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');

        $user = $request->getAttribute('user');
        assert($user instanceof User);

        if ($tree instanceof Tree) {
            $my_individual_record = Individual::getInstance($tree->getUserPreference(Auth::user(), User::PREF_TREE_ACCOUNT_XREF), $tree);
            $default_individual   = Individual::getInstance($tree->getUserPreference(Auth::user(), User::PREF_TREE_DEFAULT_XREF), $tree);
        } else {
            $my_individual_record = null;
            $default_individual   = null;
        }

        $show_delete_option = $user->getPreference(User::PREF_IS_ADMINISTRATOR) !== '1';
        $timezone_ids       = DateTimeZone::listIdentifiers();
        $timezones          = array_combine($timezone_ids, $timezone_ids);
        $title              = I18N::translate('My account');

        return $this->viewResponse('edit-account-page', [
            'contact_methods'      => FunctionsEdit::optionsContactMethods(),
            'default_individual'   => $default_individual,
            'installed_languages'  => $this->installedLanguages(),
            'my_individual_record' => $my_individual_record,
            'show_delete_option'   => $show_delete_option,
            'timezones'            => $timezones,
            'title'                => $title,
            'tree'                 => $tree,
            'user'                 => $user,
        ]);
    }

    /**
     * A list of installed languages (e.g. for an edit control).
     *
     * @return string[]
     */
    private function installedLanguages(): array
    {
        $languages = [];
        foreach (I18N::installedLocales() as $locale) {
            $languages[$locale->languageTag()] = $locale->endonym();
        }

        return $languages;
    }
}
