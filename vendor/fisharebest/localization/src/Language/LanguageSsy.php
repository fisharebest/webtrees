<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryEr;

/**
 * Class LanguageSsy - Representation of the Saho language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSsy extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ssy';
    }

    public function defaultTerritory()
    {
        return new TerritoryEr();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
