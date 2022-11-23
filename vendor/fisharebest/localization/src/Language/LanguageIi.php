<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryCn;

/**
 * Class LanguageIi - Representation of the Sichuan Yi language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageIi extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ii';
    }

    public function defaultTerritory()
    {
        return new TerritoryCn();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
