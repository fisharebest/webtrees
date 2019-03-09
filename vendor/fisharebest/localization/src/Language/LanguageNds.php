<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryDe;

/**
 * Class LanguageNds - Representation of the Low German language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LanguageNds extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'nds';
    }

    public function defaultTerritory()
    {
        return new TerritoryDe();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
