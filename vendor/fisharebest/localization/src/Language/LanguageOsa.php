<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryUs;

/**
 * Class LanguageOsa - Representation of the Osage language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageOsa extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'osa';
    }

    public function defaultTerritory()
    {
        return new TerritoryUs();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
