<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageEwo - Representation of the Ewondo language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageEwo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ewo';
    }

    public function defaultTerritory()
    {
        return new TerritoryCm();
    }
}
