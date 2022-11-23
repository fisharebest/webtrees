<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryTz;

/**
 * Class LanguageAsa - Representation of the Asu (Tanzania) language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageAsa extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'asa';
    }

    public function defaultTerritory()
    {
        return new TerritoryTz();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
