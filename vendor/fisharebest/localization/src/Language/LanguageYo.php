<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryNg;

/**
 * Class LanguageYo - Representation of the Yoruba language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageYo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'yo';
    }

    public function defaultTerritory()
    {
        return new TerritoryNg();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
