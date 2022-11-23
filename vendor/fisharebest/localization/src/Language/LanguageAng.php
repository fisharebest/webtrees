<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryGb;

/**
 * Class LanguageAng - Representation of the Anglo-Saxon / Old-English language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageAng extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ang';
    }

    public function defaultTerritory()
    {
        return new TerritoryGb();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
