<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleZeroOneOther;
use Fisharebest\Localization\Territory\TerritoryDe;

/**
 * Class LanguageKsh - Representation of the Kölsch language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKsh extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ksh';
    }

    public function defaultTerritory()
    {
        return new TerritoryDe();
    }

    public function pluralRule()
    {
        return new PluralRuleZeroOneOther();
    }
}
