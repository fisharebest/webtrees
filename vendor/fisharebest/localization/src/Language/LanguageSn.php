<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryZw;

/**
 * Class LanguageSn - Representation of the Shona language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSn extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'sn';
    }

    public function defaultTerritory()
    {
        return new TerritoryZw();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
