<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryCv;

/**
 * Class LanguageKea - Representation of the Kabuverdianu language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKea extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'kea';
    }

    public function defaultTerritory()
    {
        return new TerritoryCv();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
