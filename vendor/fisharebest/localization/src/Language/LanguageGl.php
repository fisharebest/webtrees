<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryEs;

/**
 * Class LanguageGl - Representation of the Galician language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageGl extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'gl';
    }

    public function defaultTerritory()
    {
        return new TerritoryEs();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
