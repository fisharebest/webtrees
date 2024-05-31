<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryDe;

/**
 * Class LanguageEn - Representation of the North Frisian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageFrr extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'frr';
    }

    public function defaultTerritory()
    {
        return new TerritoryDe();
    }
}
