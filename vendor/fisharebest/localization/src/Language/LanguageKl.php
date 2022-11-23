<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryGl;

/**
 * Class LanguageKl - Representation of the Kalaallisut language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKl extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'kl';
    }

    public function defaultTerritory()
    {
        return new TerritoryGl();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
