<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryBj;

/**
 * Class LanguageGuw - Representation of the Gun language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageGuw extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'guw';
    }

    public function defaultTerritory()
    {
        return new TerritoryBj();
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }
}
