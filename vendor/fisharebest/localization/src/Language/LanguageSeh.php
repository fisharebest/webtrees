<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryMz;

/**
 * Class LanguageSeh - Representation of the Sena language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSeh extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'seh';
    }

    public function defaultTerritory()
    {
        return new TerritoryMz();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
