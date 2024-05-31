<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Territory\TerritoryNo;

/**
 * Class LanguageSe - Representation of the Northern Sami language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSe extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'se';
    }

    public function defaultTerritory()
    {
        return new TerritoryNo();
    }

    public function pluralRule()
    {
        return new PluralRuleOneTwoOther();
    }
}
