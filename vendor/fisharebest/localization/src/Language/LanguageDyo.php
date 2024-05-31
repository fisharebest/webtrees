<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritorySn;

/**
 * Class LanguageDyo - Representation of the Jola-Fonyi language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageDyo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'dyo';
    }

    public function defaultTerritory()
    {
        return new TerritorySn();
    }
}
