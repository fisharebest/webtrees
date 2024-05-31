<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LanguageEbu - Representation of the Embu language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageEbu extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ebu';
    }

    public function defaultTerritory()
    {
        return new TerritoryKe();
    }
}
