<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryZa;

/**
 * Class LanguageTs - Representation of the Tsonga language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageTs extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ts';
    }

    public function defaultTerritory()
    {
        return new TerritoryZa();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
