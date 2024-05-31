<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Territory\TerritoryFi;

/**
 * Class LanguageSma - Representation of the Southern Sami language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSma extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'sma';
    }

    public function defaultTerritory()
    {
        return new TerritoryFi();
    }

    public function pluralRule()
    {
        return new PluralRuleOneTwoOther();
    }
}
