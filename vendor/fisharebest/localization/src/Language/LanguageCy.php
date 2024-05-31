<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleWelsh;
use Fisharebest\Localization\Territory\TerritoryGb;

/**
 * Class LanguageCy - Representation of the Welsh language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageCy extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'cy';
    }

    public function defaultTerritory()
    {
        return new TerritoryGb();
    }

    public function pluralRule()
    {
        return new PluralRuleWelsh();
    }
}
