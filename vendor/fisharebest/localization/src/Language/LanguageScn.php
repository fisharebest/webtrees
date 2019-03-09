<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryIt;

/**
 * Class LanguageScn - Representation of the Sicilian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LanguageScn extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'scn';
    }

    public function defaultTerritory()
    {
        return new TerritoryIt();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
