<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Territory\TerritoryNa;

/**
 * Class LanguageNaq - Representation of the Nama (Namibia) language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageNaq extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'naq';
    }

    public function defaultTerritory()
    {
        return new TerritoryNa();
    }

    public function pluralRule()
    {
        return new PluralRuleOneTwoOther();
    }
}
