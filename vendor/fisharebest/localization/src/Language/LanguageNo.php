<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryNo;

/**
 * Class LanguageNo - Representation of the Norwegian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageNo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'no';
    }

    public function defaultTerritory()
    {
        return new TerritoryNo();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
