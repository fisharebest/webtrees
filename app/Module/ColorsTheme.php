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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function asset;
use function response;
use function uasort;

/**
 * The colors theme.
 */
class ColorsTheme extends CloudsTheme
{
    // If no valid palette has been selected, use this one.
    private const DEFAULT_PALETTE = 'ash';

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a theme. */
        return I18N::translate('colors');
    }

    /**
     * Generate a list of items for the user menu.
     *
     * @param Tree|null $tree
     *
     * @return Menu[]
     */
    public function userMenu(?Tree $tree): array
    {
        return array_filter([
            $this->menuPendingChanges($tree),
            $this->menuMyPages($tree),
            $this->menuThemes(),
            $this->menuPalette(),
            $this->menuLanguages(),
            $this->menuLogin(),
            $this->menuLogout(),
        ]);
    }

    /**
     * Switch to a new palette
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postPaletteAction(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');
        assert($user instanceof UserInterface);

        $palette = $request->getQueryParams()['palette'];
        assert(array_key_exists($palette, $this->palettes()));

        $user->setPreference('themecolor', $palette);

        // If we are the admin, then use our selection as the site default.
        if (Auth::isAdmin($user)) {
            Site::setPreference('DEFAULT_COLOR_PALETTE', $palette);
        }

        Session::put('palette', $palette);

        return response();
    }

    /**
     * A list of CSS files to include for this page.
     *
     * @return string[]
     */
    public function stylesheets(): array
    {
        return [
            asset('css/colors.min.css'),
            asset('css/colors/' . $this->palette() . '.min.css'),
        ];
    }

    /**
     * Create a menu of palette options
     *
     * @return Menu
     */
    protected function menuPalette(): Menu
    {
        /* I18N: A colour scheme */
        $menu = new Menu(I18N::translate('Palette'), '#', 'menu-color');

        $palette = $this->palette();

        foreach ($this->palettes() as $palette_id => $palette_name) {
            $url = route('module', ['module' => $this->name(), 'action' => 'Palette', 'palette' => $palette_id]);

            $submenu = new Menu(
                $palette_name,
                '#',
                'menu-color-' . $palette_id . ($palette === $palette_id ? ' active' : ''),
                [
                    'data-post-url' => $url,
                ]
            );

            $menu->addSubmenu($submenu);
        }

        return $menu;
    }

    /**
     * @return string[]
     */
    private function palettes(): array
    {
        $palettes = [
            /* I18N: The name of a colour-scheme */
            'aquamarine'       => I18N::translate('Aqua Marine'),
            /* I18N: The name of a colour-scheme */
            'ash'              => I18N::translate('Ash'),
            /* I18N: The name of a colour-scheme */
            'belgianchocolate' => I18N::translate('Belgian Chocolate'),
            /* I18N: The name of a colour-scheme */
            'bluelagoon'       => I18N::translate('Blue Lagoon'),
            /* I18N: The name of a colour-scheme */
            'bluemarine'       => I18N::translate('Blue Marine'),
            /* I18N: The name of a colour-scheme */
            'coffeeandcream'   => I18N::translate('Coffee and Cream'),
            /* I18N: The name of a colour-scheme */
            'coldday'          => I18N::translate('Cold Day'),
            /* I18N: The name of a colour-scheme */
            'greenbeam'        => I18N::translate('Green Beam'),
            /* I18N: The name of a colour-scheme */
            'mediterranio'     => I18N::translate('Mediterranio'),
            /* I18N: The name of a colour-scheme */
            'mercury'          => I18N::translate('Mercury'),
            /* I18N: The name of a colour-scheme */
            'nocturnal'        => I18N::translate('Nocturnal'),
            /* I18N: The name of a colour-scheme */
            'olivia'           => I18N::translate('Olivia'),
            /* I18N: The name of a colour-scheme */
            'pinkplastic'      => I18N::translate('Pink Plastic'),
            /* I18N: The name of a colour-scheme */
            'sage'             => I18N::translate('Sage'),
            /* I18N: The name of a colour-scheme */
            'shinytomato'      => I18N::translate('Shiny Tomato'),
            /* I18N: The name of a colour-scheme */
            'tealtop'          => I18N::translate('Teal Top'),
        ];

        uasort($palettes, '\Fisharebest\Webtrees\I18N::strcasecmp');

        return $palettes;
    }

    /**
     * @return string
     */
    private function palette(): string
    {
        // If we are logged in, use our preference
        $palette = Auth::user()->getPreference('themecolor', '');

        // If not logged in or no preference, use one we selected earlier in the session.
        if ($palette === '') {
            $palette = Session::get('palette', '');
        }

        // We haven't selected one this session? Use the site default
        if ($palette === '') {
            $palette = Site::getPreference('DEFAULT_COLOR_PALETTE', self::DEFAULT_PALETTE);
        }

        return $palette;
    }
}
