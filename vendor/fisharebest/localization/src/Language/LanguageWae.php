<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryCh;

/**
 * Class LanguageWae - Representation of the Walser language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageWae extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'wae';
    }

    public function defaultTerritory()
    {
        return new TerritoryCh();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
