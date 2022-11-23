<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryZw;

/**
 * Class LanguageNd - Representation of the North Ndebele language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageNd extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'nd';
    }

    public function defaultTerritory()
    {
        return new TerritoryZw();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
