<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritorySo;

/**
 * Class LanguageSo - Representation of the Somali language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'so';
    }

    public function defaultTerritory()
    {
        return new TerritorySo();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
