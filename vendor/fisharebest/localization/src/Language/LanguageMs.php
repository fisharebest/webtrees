<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryMy;

/**
 * Class LanguageMs - Representation of the Malay language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMs extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ms';
    }

    public function defaultTerritory()
    {
        return new TerritoryMy();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
