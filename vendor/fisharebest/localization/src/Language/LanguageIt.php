<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryIt;

/**
 * Class LanguageIt - Representation of the Italian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageIt extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'it';
    }

    public function defaultTerritory()
    {
        return new TerritoryIt();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
