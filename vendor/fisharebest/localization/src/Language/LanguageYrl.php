<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryBr;

/**
 * Class LanguageYrl - Representation of the Kaingang language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageYrl extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'yrl';
    }

    public function defaultTerritory()
    {
        return new TerritoryBr();
    }
}
