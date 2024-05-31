<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule7;
use Fisharebest\Localization\Territory\TerritoryBa;

/**
 * Class LanguageBs - Representation of the Bosnian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageBs extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'bs';
    }

    public function defaultTerritory()
    {
        return new TerritoryBa();
    }

    public function pluralRule()
    {
        return new PluralRule7();
    }
}
