<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryBr;

/**
 * Class LanguageKgp - Representation of the Kaingang language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKgp extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'kgp';
    }

    public function defaultTerritory()
    {
        return new TerritoryBr();
    }
}
