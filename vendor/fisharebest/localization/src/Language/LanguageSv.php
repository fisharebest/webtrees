<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritorySe;

/**
 * Class LanguageSv - Representation of the Swedish language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSv extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'sv';
    }

    public function defaultTerritory()
    {
        return new TerritorySe();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
