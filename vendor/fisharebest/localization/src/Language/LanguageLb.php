<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryLu;

/**
 * Class LanguageLb - Representation of the Luxembourgish language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageLb extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'lb';
    }

    public function defaultTerritory()
    {
        return new TerritoryLu();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
