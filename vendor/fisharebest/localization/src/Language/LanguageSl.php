<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule10;
use Fisharebest\Localization\Territory\TerritorySi;

/**
 * Class LanguageSl - Representation of the Slovenian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSl extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'sl';
    }

    public function defaultTerritory()
    {
        return new TerritorySi();
    }

    public function pluralRule()
    {
        return new PluralRule10();
    }
}
