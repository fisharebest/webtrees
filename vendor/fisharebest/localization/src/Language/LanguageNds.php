<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryDe;

/**
 * Class LanguageNds - Representation of the Low German language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageNds extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'nds';
    }

    public function defaultTerritory()
    {
        return new TerritoryDe();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
