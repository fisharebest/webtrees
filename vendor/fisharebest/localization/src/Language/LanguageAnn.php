<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryNg;

/**
 * Class LanguageAnn - Representation of the Obolo language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageAnn extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ann';
    }

    public function defaultTerritory()
    {
        return new TerritoryNg();
    }
}
