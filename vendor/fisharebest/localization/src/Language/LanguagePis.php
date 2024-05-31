<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritorySb;

/**
 * Class LanguageEn - Representation of the English language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguagePis extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'pis';
    }

    public function defaultTerritory()
    {
        return new TerritorySb();
    }
}
