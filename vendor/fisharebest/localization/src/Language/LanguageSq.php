<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryAl;

/**
 * Class LanguageSq - Representation of the Albanian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSq extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'sq';
    }

    public function defaultTerritory()
    {
        return new TerritoryAl();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
