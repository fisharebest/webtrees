<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryMl;

/**
 * Class LanguageKhq - Representation of the Koyra Chiini Songhay language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKhq extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'khq';
    }

    public function defaultTerritory()
    {
        return new TerritoryMl();
    }
}
