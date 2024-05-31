<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageMua - Representation of the Mundang language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMua extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'mua';
    }

    public function defaultTerritory()
    {
        return new TerritoryCm();
    }
}
