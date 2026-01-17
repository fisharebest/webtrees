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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function asset;
use function is_string;
use function response;
use function uasort;

/**
 * The colors theme.
 */
class ColorsTheme extends CloudsTheme implements ModuleGlobalInterface
{
    use ModuleGlobalTrait;

    // If no valid palette has been selected, use this one.
    private const string DEFAULT_PALETTE = 'ash';

    private const CSS = [
        'aquamarine'       => ':root{--color-1:#007e94;--color-2:#085360;--color-3:#00a9c7;--color-4:#dcf2f9;--color-5:#ffffff;--color-6:#ffffff;}',
        'ash'              => ':root{--color-1:#5d6779;--color-2:#2a2b2d;--color-3:#9da5b4;--color-4:#ededee;--color-5:#ffffff;--color-6:#ffffff;}',
        'belgianchocolate' => ':root{--color-1:#971702;--color-2:#311900;--color-3:#af2604;--color-4:#f6edd5;--color-5:#ffffff;--color-6:#ffffff;}',
        'bluelagoon'       => ':root{--color-1:#5ab5ee;--color-2:#2385c2;--color-3:#5ab5ee;--color-4:#e6f5ff;--color-5:#ffffff;--color-6:#ffffff;}',
        'bluemarine'       => ':root{--color-1:#6598cb;--color-2:#98badd;--color-3:#6598cb;--color-4:#e0e7ff;--color-5:#ffffff;--color-6:#ffffff;}',
        'coffeeandcream'   => ':root{--color-1:#d4c7a7;--color-2:#93724f;--color-3:#93724f;--color-4:#f4ead1;--color-5:#553e2f;--color-6:#ffffff;}',
        'coldday'          => ':root{--color-1:#1a1575;--color-2:#4d91ff;--color-3:#5997e3;--color-4:#e6e1ff;--color-5:#ffffff;--color-6:#ffffff;}',
        'greenbeam'        => ':root{--color-1:#03961e;--color-2:#7be000;--color-3:#04af23;--color-4:#e6ffc7;--color-5:#ffffff;--color-6:#ffffff;}',
        'mediterranio'     => ':root{--color-1:#a30f42;--color-2:#fc6d1d;--color-3:#d23014;--color-4:#fef9dc;--color-5:#ffffff;--color-6:#ffffff;}',
        'mercury'          => ':root{--color-1:#d4d4d4;--color-2:#a9adbc;--color-3:#c6c8d2;--color-4:#f0f2f5;--color-5:#707070;--color-6:#707070;}',
        'nocturnal'        => ':root{--color-1:#0a2352;--color-2:#9fa8d5;--color-3:#6a78be;--color-4:#e0e1f0;--color-5:#ffffff;--color-6:#ffffff;}',
        'olivia'           => ':root{--color-1:#7db323;--color-2:#b5d52a;--color-3:#7db323;--color-4:#eef9dc;--color-5:#ffffff;--color-6:#ffffff;}',
        'pinkplastic'      => ':root{--color-1:#f41063;--color-2:#f391c6;--color-3:#f75993;--color-4:#fbdaed;--color-5:#ffffff;--color-6:#ffffff;}',
        'sage'             => ':root{--color-1:#767647;--color-2:#ccccaa;--color-3:#ccccaa;--color-4:#eeeedd;--color-5:#ffffff;--color-6:#333333;}',
        'shinytomato'      => ':root{--color-1:#f21107;--color-2:#a1443a;--color-3:#f96058;--color-4:#f0eaf0;--color-5:#ffffff;--color-6:#ffffff;}',
        'tealtop'          => ':root{--color-1:#34775a;--color-2:#52bf90;--color-3:#51b389;--color-4:#d2f4e6;--color-5:#ffffff;--color-6:#ffffff;}',
    ];

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
     * @return array<Menu>
     */
    public function userMenu(Tree|null $tree): array
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
        $user    = Validator::attributes($request)->user();
        $palette = Validator::queryParams($request)->isInArrayKeys($this->palettes())->string('palette');

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
     * @return array<string>
     */
    public function stylesheets(): array
    {
        return [
            asset('css/colors.min.css'),
        ];
    }

    public function headContent(): string
    {
        return '<style>' . self::CSS[$this->palette()] . '</style>';
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
                    'data-wt-post-url' => $url,
                ]
            );

            $menu->addSubmenu($submenu);
        }

        return $menu;
    }

    /**
     * @return array<string>
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

        uasort($palettes, I18N::comparator());

        return $palettes;
    }

    /**
     * @return string
     */
    private function palette(): string
    {
        // If we are logged in, use our preference
        $palette = Auth::user()->getPreference('themecolor');

        // If not logged in or no preference, use one we selected earlier in the session.
        if ($palette === '') {
            $palette = Session::get('palette');
            $palette = is_string($palette) ? $palette : '';
        }

        // We haven't selected one this session? Use the site default
        if ($palette === '') {
            $palette = Site::getPreference('DEFAULT_COLOR_PALETTE');
        }

        if ($palette === '') {
            $palette = self::DEFAULT_PALETTE;
        }

        return $palette;
    }
}
