<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryMw;

/**
 * Class LanguageNy - Representation of the Chewa language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageNy extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ny';
    }

    public function defaultTerritory()
    {
        return new TerritoryMw();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
