<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use HTMLPurifier;
use HTMLPurifier_AttrDef_Enum;
use HTMLPurifier_Config;
use HTMLPurifier_HTMLDefinition;

use function assert;

/**
 * Filter/sanitize HTML
 */
class HtmlService
{
    /**
     * Take some dirty HTML (as provided by the user), and clean it before
     * we save/display it.
     *
     * @param string $html
     *
     * @return string
     */
    public function sanitize(string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();

        $config->set('Cache.DefinitionImpl', null);

        $config->set('HTML.TidyLevel', 'none'); // Only XSS cleaning now

        // Remove the default maximum width/height for images.  This enables percentage values.
        $config->set('CSS.MaxImgLength', null);

        // Allow id attributes.
        $config->set('Attr.EnableID', true);

        $def = $config->getHTMLDefinition(true);
        assert($def instanceof HTMLPurifier_HTMLDefinition);

        // Allow link targets.
        $def->addAttribute('a', 'target', new HTMLPurifier_AttrDef_Enum(['_blank', '_self', '_target', '_top']));

        // Allow image maps.
        $def->addAttribute('img', 'usemap', 'CDATA');

        $map = $def->addElement('map', 'Block', 'Flow', 'Common', [
            'name'  => 'CDATA',
            'id'    => 'ID',
            'title' => 'CDATA',
        ]);

        $map->excludes = ['map' => true];

        $area = $def->addElement('area', 'Block', 'Empty', 'Common', [
            'name'      => 'CDATA',
            'id'        => 'ID',
            'alt'       => 'Text',
            'coords'    => 'CDATA',
            'accesskey' => 'Character',
            'nohref'    => new HTMLPurifier_AttrDef_Enum(['nohref']),
            'href'      => 'URI',
            'shape'     => new HTMLPurifier_AttrDef_Enum(['rect', 'circle', 'poly', 'default']),
            'tabindex'  => 'Number',
        ]);

        $area->excludes = ['area' => true];

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($html);
    }
}
