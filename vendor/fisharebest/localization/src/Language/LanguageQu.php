<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryPe;

/**
 * Class LanguageQu - Representation of the Quechua language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LanguageQu extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'qu';
    }

    public function defaultTerritory()
    {
        return new TerritoryPe();
    }
}
