<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryEt;

/**
 * Class LanguageAa - Representation of the Afar language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageAa extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'aa';
    }

    public function defaultTerritory()
    {
        return new TerritoryEt();
    }
}
