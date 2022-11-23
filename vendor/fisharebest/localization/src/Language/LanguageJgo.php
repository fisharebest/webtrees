<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageJgo - Representation of the Ngomba language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageJgo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'jgo';
    }

    public function defaultTerritory()
    {
        return new TerritoryCm();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
