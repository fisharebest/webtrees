<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageKsf - Representation of the Bafia language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKsf extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ksf';
    }

    public function defaultTerritory()
    {
        return new TerritoryCm();
    }
}
