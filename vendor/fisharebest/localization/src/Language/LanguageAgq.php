<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageAgq - Representation of the Aghem language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LanguageAgq extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'agq';
    }

    public function defaultTerritory()
    {
        return new TerritoryCm();
    }
}
