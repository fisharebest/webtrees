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
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;

/**
 * Class ContactsFooterModule - provide a link to the site owner.
 */
class ContactsFooterModule extends AbstractModule implements ModuleFooterInterface
{
    use ModuleFooterTrait;

    /**
     * @var UserService
     */
    private $user_service;

    /**
     * Dependency injection.
     *
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * How should this module be labelled on tabs, footers, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Contact information');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Hit counters” module */
        return I18N::translate('A link to the site contacts.');
    }

    /**
     * The default position for this footer.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultFooterOrder(): int
    {
        return 2;
    }

    /**
     * A footer, to be added at the bottom of every page.
     *
     * @param Tree|null $tree
     *
     * @return string
     */
    public function getFooter(?Tree $tree): string
    {
        if ($tree === null) {
            return '';
        }

        $contact_user   = $this->user_service->find((int) $tree->getPreference('CONTACT_USER_ID'));
        $webmaster_user = $this->user_service->find((int) $tree->getPreference('WEBMASTER_USER_ID'));

        if ($contact_user instanceof User && $contact_user === $webmaster_user) {
            return view('modules/contact-links/footer', [
                'contact_links' => $this->contactLinkEverything($contact_user),
            ]);
        }

        if ($contact_user instanceof User && $webmaster_user instanceof User) {
            return view('modules/contact-links/footer', [
                'contact_links' => $this->contactLinkGenealogy($contact_user) . '<br>' . $this->contactLinkTechnical($webmaster_user),
            ]);
        }

        if ($contact_user instanceof User) {
            return view('modules/contact-links/footer', [
                'contact_links' => $this->contactLinkGenealogy($contact_user),
            ]);
        }

        if ($webmaster_user instanceof User) {
            return view('modules/contact-links/footer', [
                'contact_links' => $this->contactLinkTechnical($webmaster_user),
            ]);
        }

        return '';
    }

    /**
     * Create contact link for both technical and genealogy support.
     *
     * @param User $user
     *
     * @return string
     */
    public function contactLinkEverything(User $user): string
    {
        return I18N::translate('For technical support or genealogy questions contact %s.', $this->user_service->contactLink($user));
    }

    /**
     * Create contact link for genealogy support.
     *
     * @param User $user
     *
     * @return string
     */
    public function contactLinkGenealogy(User $user): string
    {
        return I18N::translate('For help with genealogy questions contact %s.', $this->user_service->contactLink($user));
    }

    /**
     * Create contact link for technical support.
     *
     * @param User $user
     *
     * @return string
     */
    public function contactLinkTechnical(User $user): string
    {
        return I18N::translate('For technical support and information contact %s.', $this->user_service->contactLink($user));
    }
}
