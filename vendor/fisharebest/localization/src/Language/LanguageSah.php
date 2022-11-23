<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryRu;

/**
 * Class LanguageSah - Representation of the Yakut language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSah extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'sah';
    }

    public function defaultTerritory()
    {
        return new TerritoryRu();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
