<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryCd;

/**
 * Class LanguageLn - Representation of the Lingala language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageLn extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ln';
    }

    public function defaultTerritory()
    {
        return new TerritoryCd();
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }
}
