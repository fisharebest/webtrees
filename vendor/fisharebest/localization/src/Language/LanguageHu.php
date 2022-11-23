<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryHu;

/**
 * Class LanguageHu - Representation of the Hungarian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageHu extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'hu';
    }

    public function defaultTerritory()
    {
        return new TerritoryHu();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
