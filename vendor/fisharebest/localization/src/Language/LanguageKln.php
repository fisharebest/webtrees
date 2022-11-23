<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LanguageKln - Representation of the Kalenjin language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKln extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'kln';
    }

    public function defaultTerritory()
    {
        return new TerritoryKe();
    }
}
