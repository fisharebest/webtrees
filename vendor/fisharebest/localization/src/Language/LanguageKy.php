<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryKg;

/**
 * Class LanguageKy - Representation of the Kirghiz language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKy extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ky';
    }

    public function defaultTerritory()
    {
        return new TerritoryKg();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
