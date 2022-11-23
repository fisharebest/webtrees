<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule6;
use Fisharebest\Localization\Territory\TerritoryLt;

/**
 * Class LanguageLt - Representation of the Lithuanian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageLt extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'lt';
    }

    public function defaultTerritory()
    {
        return new TerritoryLt();
    }

    public function pluralRule()
    {
        return new PluralRule6();
    }
}
