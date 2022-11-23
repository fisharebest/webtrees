<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryNl;

/**
 * Class LanguageNl - Representation of the Dutch language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageNl extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'nl';
    }

    public function defaultTerritory()
    {
        return new TerritoryNl();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
