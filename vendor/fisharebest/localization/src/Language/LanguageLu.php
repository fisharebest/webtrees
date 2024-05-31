<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCd;

/**
 * Class LanguageLu - Representation of the Luba-Katanga language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageLu extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'lu';
    }

    public function defaultTerritory()
    {
        return new TerritoryCd();
    }
}
