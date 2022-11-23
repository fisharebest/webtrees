<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryMz;

/**
 * Class LanguageMgh - Representation of the Makhuwa-Meetto language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMgh extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'mgh';
    }

    public function defaultTerritory()
    {
        return new TerritoryMz();
    }
}
