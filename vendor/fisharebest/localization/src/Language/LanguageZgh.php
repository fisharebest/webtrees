<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryMa;

/**
 * Class LanguageZgh - Representation of the Standard Moroccan Tamazight language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageZgh extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'zgh';
    }

    public function defaultTerritory()
    {
        return new TerritoryMa();
    }
}
