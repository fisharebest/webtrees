<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule5;
use Fisharebest\Localization\Territory\TerritoryRo;

/**
 * Class LanguageRo - Representation of the Romanian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageRo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ro';
    }

    public function defaultTerritory()
    {
        return new TerritoryRo();
    }

    public function pluralRule()
    {
        return new PluralRule5();
    }
}
