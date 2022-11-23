<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryMg;

/**
 * Class LanguageMg - Representation of the Malagasy language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMg extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'mg';
    }

    public function defaultTerritory()
    {
        return new TerritoryMg();
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }
}
