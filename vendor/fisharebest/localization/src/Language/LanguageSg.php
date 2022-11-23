<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryCf;

/**
 * Class LanguageSg - Representation of the Sango language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSg extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'sg';
    }

    public function defaultTerritory()
    {
        return new TerritoryCf();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
