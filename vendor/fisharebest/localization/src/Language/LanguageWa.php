<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryBe;

/**
 * Class LanguageWa - Representation of the Walloon language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageWa extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'wa';
    }

    public function defaultTerritory()
    {
        return new TerritoryBe();
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }
}
