<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryMn;

/**
 * Class LanguageMn - Representation of the Mongolian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMn extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'mn';
    }

    public function defaultTerritory()
    {
        return new TerritoryMn();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
