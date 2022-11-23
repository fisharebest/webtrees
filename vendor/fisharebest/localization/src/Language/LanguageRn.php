<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryBi;

/**
 * Class LanguageRn - Representation of the Rundi language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageRn extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'rn';
    }

    public function defaultTerritory()
    {
        return new TerritoryBi();
    }
}
