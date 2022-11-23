<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryFr;

/**
 * Class LanguageBr - Representation of the Breton language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageBr extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'br';
    }

    public function defaultTerritory()
    {
        return new TerritoryFr();
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }
}
