<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleManx;
use Fisharebest\Localization\Territory\TerritoryIm;

/**
 * Class LanguageGv - Representation of the Manx language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageGv extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'gv';
    }

    public function defaultTerritory()
    {
        return new TerritoryIm();
    }

    public function pluralRule()
    {
        return new PluralRuleManx();
    }
}
