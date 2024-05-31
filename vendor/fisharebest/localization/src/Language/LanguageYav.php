<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageYav - Representation of the Yangben language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageYav extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'yav';
    }

    public function defaultTerritory()
    {
        return new TerritoryCm();
    }
}
