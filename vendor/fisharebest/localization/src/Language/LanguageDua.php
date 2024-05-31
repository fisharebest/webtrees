<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageDua - Representation of the Duala language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageDua extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'dua';
    }

    public function defaultTerritory()
    {
        return new TerritoryCm();
    }
}
