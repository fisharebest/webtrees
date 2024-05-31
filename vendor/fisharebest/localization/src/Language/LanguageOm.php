<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryEt;

/**
 * Class LanguageOm - Representation of the Oromo language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageOm extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'om';
    }

    public function defaultTerritory()
    {
        return new TerritoryEt();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
