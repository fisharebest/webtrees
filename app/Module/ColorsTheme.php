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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * The colors theme.
 */
class ColorsTheme extends CloudsTheme
{
    protected const PERSON_BOX_CLASSES = [
        'M' => 'person_box',
        'F' => 'person_boxF',
        'U' => 'person_boxNN',
    ];

    /** @var string[] A list of color palettes */
    protected $palettes;

    /** @var string Which of the color palettes to use on this page */
    protected $palette;

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a theme. */
        return I18N::translate('colors');
    }

    /**
     * @param Request   $request
     * @param Tree|null $tree The current tree (if there is one).
     */
    public function __construct(Request $request, ?Tree $tree)
    {
        parent::__construct($request, $tree);

        $this->palettes = [
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
        uasort($this->palettes, '\Fisharebest\Webtrees\I18N::strcasecmp');

        // If we've selected a new palette, and we are logged in, set this value as a default.
        if (isset($_GET['themecolor']) && array_key_exists($_GET['themecolor'], $this->palettes)) {
            // Request to change color
            $this->palette = $_GET['themecolor'];
            Auth::user()->setPreference('themecolor', $this->palette);
            if (Auth::isAdmin()) {
                Site::setPreference('DEFAULT_COLOR_PALETTE', $this->palette);
            }
            unset($_GET['themecolor']);
            // Rember that we have selected a value
            Session::put('subColor', $this->palette);
        }
        // If we are logged in, use our preference
        $this->palette = Auth::user()->getPreference('themecolor');
        // If not logged in or no preference, use one we selected earlier in the session?
        if (!$this->palette) {
            $this->palette = Session::get('subColor');
        }
        // We haven't selected one this session? Use the site default
        if (!$this->palette) {
            $this->palette = Site::getPreference('DEFAULT_COLOR_PALETTE');
        }
        // Make sure our selected palette actually exists
        if (!array_key_exists($this->palette, $this->palettes)) {
            $this->palette = 'ash';
        }
    }

    /**
     * Generate a list of items for the user menu.
     *
     * @return Menu[]
     */
    public function secondaryMenu(): array
    {
        return array_filter([
            $this->menuPendingChanges(),
            $this->menuMyPages(),
            $this->menuThemes(),
            $this->menuPalette(),
            $this->menuLanguages(),
            $this->menuLogin(),
            $this->menuLogout(),
        ]);
    }

    /**
     * Create a menu of palette options
     *
     * @return Menu|null
     */
    public function menuPalette()
    {
        if ($this->tree !== null && Site::getPreference('ALLOW_USER_THEMES') === '1' && $this->tree->getPreference('ALLOW_THEME_DROPDOWN') === '1') {
            /* I18N: A colour scheme */
            $menu = new Menu(I18N::translate('Palette'), '#', 'menu-color');

            foreach ($this->palettes as $palette_id => $palette_name) {
                $url = $this->request->getRequestUri();
                $url = preg_replace('/&themecolor=[a-z]+/', '', $url);
                $url .= '&themecolor=' . $palette_id;

                $menu->addSubmenu(new Menu(
                    $palette_name,
                    '#',
                    'menu-color-' . $palette_id . ($this->palette === $palette_id ? ' active' : ''),
                    [
                        'onclick' => 'document.location=\'' . $url . '\'',
                    ]
                ));
            }

            return $menu;
        }

        return null;
    }

    /**
     * A list of CSS files to include for this page.
     *
     * @return string[]
     */
    public function stylesheets(): array
    {
        return [
            'themes/_common/css-2.0.0/style.css',
            'themes/clouds/css-2.0.0/style.css',
            'themes/colors/css-2.0.0/style.css',
            'themes/colors/css-2.0.0/palette/' . $this->palette . '.css',
        ];
    }
}
