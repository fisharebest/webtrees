<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryUg;

/**
 * Class LanguageNyn - Representation of the Nyankole language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageNyn extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'nyn';
    }

    public function defaultTerritory()
    {
        return new TerritoryUg();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
