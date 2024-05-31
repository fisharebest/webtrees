<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritorySz;

/**
 * Class LanguageSs - Representation of the Swati language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSs extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ss';
    }

    public function defaultTerritory()
    {
        return new TerritorySz();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
