<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCd;

/**
 * Class LanguageSwc - Representation of the Congo Swahili language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSwc extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'swc';
    }

    public function defaultTerritory()
    {
        return new TerritoryCd();
    }
}
